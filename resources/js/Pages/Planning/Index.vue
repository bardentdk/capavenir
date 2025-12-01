<script setup>
  import { ref, watch, reactive } from 'vue';
  import { Head, useForm, router } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import FullCalendar from '@fullcalendar/vue3';
  import dayGridPlugin from '@fullcalendar/daygrid';
  import timeGridPlugin from '@fullcalendar/timegrid';
  import interactionPlugin from '@fullcalendar/interaction';
  import frLocale from '@fullcalendar/core/locales/fr';
  import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
  import DatePicker from '@/Components/DatePicker.vue';
  import { PlusIcon } from '@heroicons/vue/24/outline'; // <--- AJOUT ICONE

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

  // --- GESTION CALENDRIER (REACTIF) ---
  const calendarOptions = reactive({
      plugins: [ dayGridPlugin, timeGridPlugin, interactionPlugin ],
      initialView: 'timeGridWeek',
      locale: frLocale,
      headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      slotMinTime: "07:00:00",
      slotMaxTime: "20:00:00",
      allDaySlot: false,
      height: 'auto',
      events: props.events,
      nowIndicator: true,
      selectable: props.canEdit,

      // Pour le mobile : délai avant de considérer un "long press" comme une sélection
      longPressDelay: 500,

      select: (info) => {
          if (!props.canEdit) return;
          openModal(info.startStr, info.endStr);
      },

      eventClick: (info) => {
          if (!props.canEdit) return;
          if(confirm(`Supprimer "${info.event.title}" ?`)) {
              router.delete(`/planning/${info.event.id}`);
          }
      }
  });

  watch(() => props.events, (newEvents) => {
      calendarOptions.events = newEvents;
  });

  // --- FILTRE EDUCATEUR ---
  const selectedUser = ref(props.selectedUserId);
  watch(selectedUser, (newVal) => {
      router.get('/planning', { user_id: newVal }, { preserveState: true, preserveScroll: true });
  });

  // --- MODAL & FORMULAIRE ---
  const isOpen = ref(false);
  const form = useForm({
      user_id: props.selectedUserId,
      title: '',
      start_at: '',
      end_at: '',
      type: 'intervention',
      description: ''
  });

  const openModal = (startStr, endStr) => {
      const startDateObj = new Date(startStr);
      const endDateObj = new Date(endStr);

      dateStart.value = startDateObj.toISOString().split('T')[0];
      dateEnd.value = endDateObj.toISOString().split('T')[0];
      timeStart.value = startDateObj.toTimeString().slice(0, 5);
      timeEnd.value = endDateObj.toTimeString().slice(0, 5);

      form.user_id = selectedUser.value;
      isOpen.value = true;
  };

  // --- NOUVELLE FONCTION : CRÉATION RAPIDE (MOBILE) ---
  const createEventNow = () => {
      // On calcule l'heure suivante pour pré-remplir
      const now = new Date();
      now.setMinutes(0, 0, 0); // Reset minutes
      now.setHours(now.getHours() + 1); // Heure suivante

      const end = new Date(now);
      end.setHours(end.getHours() + 1); // Durée 1h par défaut

      // On utilise toISOString pour simuler ce que FullCalendar envoie
      // Attention au fuseau horaire local, petite astuce pour garder l'heure locale :
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
      if (dateStart.value && timeStart.value) {
          form.start_at = `${dateStart.value} ${timeStart.value}:00`;
      }
      if (dateEnd.value && timeEnd.value) {
          form.end_at = `${dateEnd.value} ${timeEnd.value}:00`;
      }

      form.post('/planning', {
          onSuccess: () => {
              closeModal();
          },
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

        <!-- FAB (FLOATING ACTION BUTTON) POUR MOBILE -->
        <!-- Ce bouton s'affiche toujours en bas à droite, par-dessus tout -->
        <button
            v-if="canEdit"
            @click="createEventNow"
            class="fixed bottom-8 right-8 z-40 p-4 bg-(--color-capavenir) text-white rounded-full shadow-xl shadow-pink-900/30 hover:scale-110 active:scale-95 transition-all focus:outline-none focus:ring-4 focus:ring-pink-300"
            title="Ajouter un événement"
        >
            <PlusIcon class="h-8 w-8" />
        </button>

        <!-- MODAL AJOUT -->
        <TransitionRoot appear :show="isOpen" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-50">
                <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-black/25 backdrop-blur-sm" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center">
                        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95" enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                            <DialogPanel class="w-full max-w-lg transform overflow-hidden rounded-2xl bg-white p-8 text-left align-middle shadow-2xl transition-all border border-slate-100">

                                <DialogTitle as="h3" class="text-xl font-bold leading-6 text-slate-900 mb-6 flex items-center gap-2">
                                    <span class="w-2 h-6 bg-sky-500 rounded-full"></span>
                                    Nouveau Créneau
                                </DialogTitle>

                                <form @submit.prevent="submitEvent" class="space-y-6">

                                    <!-- Pour QUI ? -->
                                    <div v-if="canEdit && users.length > 0" class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Pour qui ?</label>
                                        <select v-model="form.user_id" class="block w-full rounded-md border-0 py-2 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-sm bg-white">
                                            <option v-for="u in users" :key="u.id" :value="u.id">
                                                {{ u.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- TITRE -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Titre de l'événement</label>
                                        <input type="text" v-model="form.title" class="block w-full rounded-md border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-sm" placeholder="Ex: Intervention Domicile...">
                                        <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
                                    </div>

                                    <!-- DATES -->
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
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Type d'activité</label>
                                        <select v-model="form.type" class="block w-full rounded-md border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-sm">
                                            <option value="intervention">Intervention Client 🟢</option>
                                            <option value="reunion">Réunion / Synthèse 🔵</option>
                                            <option value="formation">Formation / Autre 🟣</option>
                                        </select>
                                    </div>

                                    <!-- NOTES -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Notes (Optionnel)</label>
                                        <textarea v-model="form.description" rows="3" class="block w-full rounded-md border-0 py-2 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-sm" placeholder="Détails supplémentaires..."></textarea>
                                    </div>

                                    <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-slate-100">
                                        <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500">
                                            Annuler
                                        </button>
                                        <button type="submit" :disabled="form.processing" class="px-6 py-2 text-sm font-bold text-white bg-sky-600 rounded-md hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-600 shadow-sm transition-all transform active:scale-95">
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
  /* CSS FullCalendar inchangé */
  .fc-theme-standard td, .fc-theme-standard th { border-color: #f1f5f9; }
  .fc-scrollgrid { border: none !important; }
  .fc-col-header-cell-cushion { color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; padding: 10px 0 !important; }
  .fc-day-today { background-color: transparent !important; }
  .fc-day-today .fc-col-header-cell-cushion { color: #0ea5e9; }
  .fc-button-primary { background-color: white !important; color: #475569 !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); font-weight: 600 !important; text-transform: capitalize; border-radius: 8px !important; padding: 6px 16px !important; }
  .fc-button-primary:hover { background-color: #f8fafc !important; }
  .fc-button-active { background-color: #f1f5f9 !important; color: #0f172a !important; }
  .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 700 !important; color: #1e293b; text-transform: capitalize; }
  .fc-event { border: none !important; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-radius: 6px !important; padding: 2px 4px; font-size: 0.8rem; font-weight: 600; }
  .fc-event-main { color: inherit; }
  .fc-timegrid-now-indicator-line { border-color: #ef4444; border-width: 2px; }
  .fc-timegrid-now-indicator-arrow { border-color: #ef4444; border-width: 6px; }
</style>