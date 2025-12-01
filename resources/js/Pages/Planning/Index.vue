<script setup>
import { ref, watch, reactive, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import frLocale from '@fullcalendar/core/locales/fr';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot, Listbox, ListboxButton, ListboxOptions, ListboxOption } from '@headlessui/vue';
import DatePicker from '@/Components/DatePicker.vue';
import { PlusIcon, CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    events: Array,
    users: Array,
    selectedUserId: Number,
    canEdit: Boolean
});

// --- VARIABLES UI ---
const dateStart = ref('');
const timeStart = ref('');
const dateEnd = ref('');
const timeEnd = ref('');

// --- CALENDRIER ---
const currentEvents = ref(props.events);

const calendarOptions = reactive({
    plugins: [ dayGridPlugin, timeGridPlugin, interactionPlugin ],
    initialView: 'timeGridWeek',
    locale: frLocale,
    headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
    slotMinTime: "07:00:00",
    slotMaxTime: "20:00:00",
    allDaySlot: false,
    height: 'auto',
    events: currentEvents,
    nowIndicator: true,
    selectable: props.canEdit,
    longPressDelay: 500,

    // Clic sur une case vide
    select: (info) => {
        if (props.canEdit) openModal(info.startStr, info.endStr);
    },

    // Clic sur un événement existant
    eventClick: (info) => {
        if (props.canEdit && confirm(`Supprimer "${info.event.title}" ?`)) {
            router.delete(`/planning/${info.event.id}`, { preserveScroll: true });
        }
    }
});

// Mise à jour temps réel des events
watch(() => props.events, (newEvents) => {
    currentEvents.value = newEvents;
}, { deep: true });

// --- FILTRE VUE (Voir l'agenda de...) ---
const selectedUser = ref(props.selectedUserId);
watch(selectedUser, (newVal) => {
    router.get('/planning', { user_id: newVal }, { preserveState: true, preserveScroll: true });
});

// --- FORMULAIRE ---
const isOpen = ref(false);
const form = useForm({
    user_ids: [], // Tableau d'IDs pour le multi-select
    title: '',
    start_at: '',
    end_at: '',
    type: 'intervention',
    description: '',
    recurrence: 'none',
    recurrence_end: ''
});

// Computed pour afficher joliment les noms sélectionnés dans le bouton
const selectedUserNames = computed(() => {
    if (form.user_ids.length === 0) return 'Sélectionner les participants...';
    if (form.user_ids.length === props.users.length) return 'Toute l\'équipe';

    const selectedNames = props.users
        .filter(u => form.user_ids.includes(u.id))
        .map(u => u.name);

    if (selectedNames.length > 2) return `${selectedNames[0]}, ${selectedNames[1]} +${selectedNames.length - 2}`;
    return selectedNames.join(', ');
});

const openModal = (startStr, endStr) => {
    const startDateObj = new Date(startStr);
    const endDateObj = new Date(endStr);

    // Remplissage DatePicker + Time Input
    dateStart.value = startDateObj.toISOString().split('T')[0];
    dateEnd.value = endDateObj.toISOString().split('T')[0];
    timeStart.value = startDateObj.toTimeString().slice(0, 5);
    timeEnd.value = endDateObj.toTimeString().slice(0, 5);

    // Par défaut, on sélectionne l'utilisateur qu'on regarde actuellement
    // Sauf si on regarde "Tout le monde" (cas rare selon ta logique)
    form.user_ids = [selectedUser.value];

    isOpen.value = true;
};

// Fonction helper pour créer event maintenant (Mobile FAB)
const createEventNow = () => {
    const now = new Date();
    now.setMinutes(0, 0, 0);
    now.setHours(now.getHours() + 1);

    const end = new Date(now);
    end.setHours(end.getHours() + 1);

    const offset = now.getTimezoneOffset() * 60000;
    const localStart = new Date(now.getTime() - offset).toISOString().slice(0, -1);
    const localEnd = new Date(end.getTime() - offset).toISOString().slice(0, -1);

    openModal(localStart, localEnd);
};

const closeModal = () => {
    isOpen.value = false;
    form.reset();
    dateStart.value = '';
    timeStart.value = '';
};

const submitEvent = () => {
    // Reconstitution des dates complètes
    if (dateStart.value && timeStart.value) form.start_at = `${dateStart.value} ${timeStart.value}:00`;
    if (dateEnd.value && timeEnd.value) form.end_at = `${dateEnd.value} ${timeEnd.value}:00`;

    form.post('/planning', {
        preserveScroll: true,
        onSuccess: () => closeModal()
    });
};
</script>

<template>
    <Head title="Planning Équipe" />
    <AppLayout>

        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Agenda</h1>
                <p class="text-slate-500 text-sm">Planning des interventions et disponibilités.</p>
            </div>

            <!-- Filtre Compta -->
            <div v-if="canEdit && users.length > 0" class="flex items-center gap-2 bg-white p-1.5 rounded-lg border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider pl-2">Voir :</span>
                <select v-model="selectedUser" class="border-0 py-1.5 pl-2 pr-8 text-sm font-medium text-slate-700 focus:ring-0 cursor-pointer bg-transparent">
                    <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
                </select>
            </div>
        </div>

        <!-- CALENDRIER -->
        <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 overflow-hidden calendar-wrapper mb-20 sm:mb-0">
            <FullCalendar :options="calendarOptions" />
        </div>

        <!-- BOUTON FLOTTANT MOBILE -->
        <button
            v-if="canEdit"
            @click="createEventNow"
            class="fixed bottom-8 right-8 z-40 p-4 bg-sky-600 text-white rounded-full shadow-xl shadow-sky-900/30 hover:scale-110 active:scale-95 transition-all focus:outline-none focus:ring-4 focus:ring-sky-300 sm:hidden"
            title="Ajouter un événement"
        >
            <PlusIcon class="h-8 w-8" />
        </button>

        <!-- MODAL -->
        <TransitionRoot appear :show="isOpen" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-50">
                <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-black/25 backdrop-blur-sm" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center">
                        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95" enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                            <DialogPanel class="w-full max-w-2xl transform overflow-visible rounded-2xl bg-white p-8 text-left align-middle shadow-2xl transition-all border border-slate-100">

                                <DialogTitle as="h3" class="text-xl font-bold leading-6 text-slate-900 mb-6 flex items-center gap-2">
                                    <span class="w-2 h-6 bg-sky-500 rounded-full"></span>
                                    Nouveau Créneau
                                </DialogTitle>

                                <form @submit.prevent="submitEvent" class="space-y-6">

                                    <!-- MULTI-SELECT PARTICIPANTS -->
                                    <div v-if="canEdit && users.length > 0" class="relative">
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Participants</label>
                                        <Listbox v-model="form.user_ids" multiple>
                                            <div class="relative mt-1">
                                                <ListboxButton class="relative w-full cursor-default rounded-md bg-white py-2.5 pl-3 pr-10 text-left border border-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-600 sm:text-sm shadow-sm">
                                                    <span class="block truncate font-medium text-slate-700">{{ selectedUserNames }}</span>
                                                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-slate-400" aria-hidden="true" />
                                                    </span>
                                                </ListboxButton>

                                                <transition leave-active-class="transition duration-100 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
                                                    <ListboxOptions class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm z-50">
                                                        <ListboxOption v-for="person in users" :key="person.id" :value="person.id" as="template" v-slot="{ active, selected }">
                                                            <li :class="[active ? 'bg-sky-50 text-sky-900' : 'text-slate-900', 'relative cursor-default select-none py-2 pl-10 pr-4']">
                                                                <span :class="[selected ? 'font-medium' : 'font-normal', 'block truncate']">{{ person.name }}</span>
                                                                <span v-if="selected" class="absolute inset-y-0 left-0 flex items-center pl-3 text-sky-600">
                                                                    <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                                                </span>
                                                            </li>
                                                        </ListboxOption>
                                                    </ListboxOptions>
                                                </transition>
                                            </div>
                                        </Listbox>
                                        <p v-if="form.errors.user_ids" class="text-red-500 text-xs mt-1">{{ form.errors.user_ids }}</p>
                                    </div>

                                    <!-- TITRE -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Titre</label>
                                        <input type="text" v-model="form.title" class="block w-full rounded-md border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-sm" placeholder="Ex: Réunion...">
                                        <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
                                    </div>

                                    <!-- DATES (Harmonie DatePicker) -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Début</label>
                                            <div class="flex gap-2">
                                                <div class="flex-1"><DatePicker v-model="dateStart" placeholder="Date" /></div>
                                                <div class="w-24"><input type="time" v-model="timeStart" class="block w-full rounded-md border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-sky-600 sm:text-sm text-center"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Fin</label>
                                            <div class="flex gap-2">
                                                <div class="flex-1"><DatePicker v-model="dateEnd" placeholder="Date" /></div>
                                                <div class="w-24"><input type="time" v-model="timeEnd" class="block w-full rounded-md border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-sky-600 sm:text-sm text-center"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- TYPE -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Type</label>
                                        <select v-model="form.type" class="block w-full rounded-md border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-sm">
                                            <option value="intervention">Intervention Client 🟢</option>
                                            <option value="reunion">Réunion / Synthèse 🔵</option>
                                            <option value="formation">Formation / Autre 🟣</option>
                                        </select>
                                    </div>

                                    <!-- NOTES -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Notes</label>
                                        <textarea v-model="form.description" rows="2" class="block w-full rounded-md border-0 py-2 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-sm"></textarea>
                                    </div>

                                    <!-- RÉCURRENCE -->
                                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="block text-xs font-bold text-indigo-800 uppercase">Répéter ?</label>
                                            <select v-model="form.recurrence" class="text-sm rounded border-indigo-200 py-1 px-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                                <option value="none">Non, une fois</option>
                                                <option value="daily">Tous les jours</option>
                                                <option value="weekly">Toutes les semaines</option>
                                                <option value="monthly">Tous les mois</option>
                                            </select>
                                        </div>
                                        <div v-if="form.recurrence !== 'none'" class="animate-fade-in mt-3">
                                            <label class="block text-xs font-bold text-indigo-800 uppercase mb-1">Jusqu'au</label>
                                            <DatePicker v-model="form.recurrence_end" placeholder="Date de fin" />
                                        </div>
                                    </div>

                                    <!-- BOUTONS -->
                                    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-slate-100">
                                        <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50 focus:outline-none">Annuler</button>
                                        <button type="submit" :disabled="form.processing" class="px-6 py-2 text-sm font-bold text-white bg-sky-600 rounded-md hover:bg-sky-700 focus:outline-none shadow-sm shadow-sky-200">
                                            {{ form.processing ? 'Ajout...' : 'Ajouter au planning' }}
                                        </button>
                                    </div>
                                </form>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </AppLayout>
</template>

<style>
/* --- FULLCALENDAR CSS (Apple Style) --- */
.fc-theme-standard td, .fc-theme-standard th { border-color: #f1f5f9; }
.fc-scrollgrid { border: none !important; }
.fc-col-header-cell-cushion { color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; padding: 10px 0 !important; }
.fc-day-today { background-color: transparent !important; }
.fc-day-today .fc-col-header-cell-cushion { color: #0ea5e9; }
.fc-button-primary { background-color: white !important; color: #475569 !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); font-weight: 600 !important; text-transform: capitalize; border-radius: 8px !important; padding: 6px 16px !important; }
.fc-button-primary:hover { background-color: #f8fafc !important; }
.fc-button-active { background-color: #f1f5f9 !important; color: #0f172a !important; }
.fc-toolbar-title { font-size: 1.25rem !important; font-weight: 700 !important; color: #1e293b; text-transform: capitalize; }
.fc-event { border: none !important; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-radius: 6px !important; padding: 2px 4px; font-size: 0.8rem; font-weight: 600; cursor: pointer; }
.fc-event-main { color: inherit; }
.fc-timegrid-now-indicator-line { border-color: #ef4444; border-width: 2px; }
.fc-timegrid-now-indicator-arrow { border-color: #ef4444; border-width: 6px; }
</style>