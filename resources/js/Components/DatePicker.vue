<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { ChevronLeftIcon, ChevronRightIcon, CalendarDaysIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    modelValue: [String, Date], // Accepte "YYYY-MM-DD" ou Date object
    label: String,
    placeholder: { type: String, default: 'Sélectionner une date' }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const containerRef = ref(null);

// Gestion de la date interne
const currentDate = ref(new Date()); // Pour la navigation (Mois affiché)
const selectedDate = ref(null);      // La date réellement sélectionnée

// Initialisation depuis la prop
watch(() => props.modelValue, (newVal) => {
    if (newVal) {
        const d = new Date(newVal);
        if (!isNaN(d)) {
            selectedDate.value = d;
            currentDate.value = new Date(d); // On centre le calendrier dessus
        }
    }
}, { immediate: true });

const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
const dayNames = ['Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa', 'Di'];

// Calcul des jours à afficher
const daysInMonth = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();
    const firstDayOfMonth = new Date(year, month, 1).getDay(); // 0 = Dimanche
    const daysInCurrentMonth = new Date(year, month + 1, 0).getDate();

    // Ajustement pour commencer le Lundi (Lundi=0 dans notre boucle, mais getDay() renvoie Dimanche=0)
    // getDay(): Su=0, Mo=1... Sa=6.  On veut Mo=0... Su=6
    let offset = firstDayOfMonth === 0 ? 6 : firstDayOfMonth - 1;

    let days = [];
    // Jours vides avant le 1er
    for (let i = 0; i < offset; i++) days.push(null);
    // Jours du mois
    for (let i = 1; i <= daysInCurrentMonth; i++) days.push(new Date(year, month, i));

    return days;
});

const formattedHeader = computed(() => {
    return `${monthNames[currentDate.value.getMonth()]} ${currentDate.value.getFullYear()}`;
});

const formattedValue = computed(() => {
    if (!selectedDate.value) return '';
    return selectedDate.value.toLocaleDateString('fr-FR', {
        day: '2-digit', month: 'long', year: 'numeric'
    });
});

const prevMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1);
};

const nextMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1);
};

const selectDate = (date) => {
    if (!date) return;
    selectedDate.value = date;

    // On émet au format YYYY-MM-DD pour Laravel
    // Astuce pour éviter le décalage timezone :
    const offset = date.getTimezoneOffset();
    const localDate = new Date(date.getTime() - (offset*60*1000));
    emit('update:modelValue', localDate.toISOString().split('T')[0]);

    isOpen.value = false;
};

const isSelected = (date) => {
    if (!date || !selectedDate.value) return false;
    return date.toDateString() === selectedDate.value.toDateString();
};

const isToday = (date) => {
    if (!date) return false;
    return date.toDateString() === new Date().toDateString();
};

// Fermeture click outside
const handleClickOutside = (e) => {
    if (containerRef.value && !containerRef.value.contains(e.target)) {
        isOpen.value = false;
    }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>

<template>
    <div class="relative w-full" ref="containerRef">
        <label v-if="label" class="block text-sm font-medium text-slate-900 mb-1">{{ label }}</label>

        <button
            type="button"
            @click="isOpen = !isOpen"
            class="relative w-full cursor-default rounded-md bg-white py-2.5 pl-3 pr-10 text-left text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-600 sm:text-sm sm:leading-6 hover:bg-slate-50"
        >
            <span class="block truncate" :class="!formattedValue ? 'text-slate-400' : ''">
                {{ formattedValue || placeholder }}
            </span>
            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                <CalendarDaysIcon class="h-5 w-5 text-slate-400" aria-hidden="true" />
            </span>
        </button>

        <div v-if="isOpen" class="absolute z-50 mt-1 w-full min-w-[300px] overflow-auto rounded-md bg-white py-4 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none animate-fade-in sm:text-sm">

            <div class="flex items-center justify-between px-4 mb-4">
                <button @click.stop="prevMonth" type="button" class="p-1.5 hover:bg-slate-100 rounded-full text-slate-600">
                    <ChevronLeftIcon class="w-5 h-5"/>
                </button>
                <div class="font-bold text-slate-800 capitalize">{{ formattedHeader }}</div>
                <button @click.stop="nextMonth" type="button" class="p-1.5 hover:bg-slate-100 rounded-full text-slate-600">
                    <ChevronRightIcon class="w-5 h-5"/>
                </button>
            </div>

            <div class="grid grid-cols-7 text-center text-xs font-semibold text-slate-400 mb-2 px-2">
                <div v-for="day in dayNames" :key="day">{{ day }}</div>
            </div>

            <div class="grid grid-cols-7 gap-1 px-2">
                <button
                    v-for="(date, idx) in daysInMonth"
                    :key="idx"
                    type="button"
                    @click="selectDate(date)"
                    :disabled="!date"
                    :class="[
                        'h-9 w-9 mx-auto flex items-center justify-center rounded-full text-sm transition-colors',
                        !date ? 'invisible' : '',
                        isSelected(date) ? 'bg-sky-600 text-white font-bold' : 'hover:bg-slate-100 text-slate-700',
                        isToday(date) && !isSelected(date) ? 'text-sky-600 font-bold ring-1 ring-sky-200' : ''
                    ]"
                >
                    {{ date ? date.getDate() : '' }}
                </button>
            </div>
        </div>
    </div>
</template>