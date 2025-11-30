<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { ChevronLeftIcon, ChevronRightIcon, CalendarIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    modelValue: {
        type: String,
        required: true // Format attendu : "YYYY-MM" (ex: "2025-11")
    }
});

const emit = defineEmits(['update:modelValue', 'change']);

const isOpen = ref(false);
const containerRef = ref(null);

// Parsing de la date initiale
const currentYear = ref(parseInt(props.modelValue.split('-')[0]));
const currentMonthIndex = computed(() => parseInt(props.modelValue.split('-')[1]) - 1);

const months = [
    'Janvier', 'Février', 'Mars', 'Avril',
    'Mai', 'Juin', 'Juillet', 'Août',
    'Septembre', 'Octobre', 'Novembre', 'Décembre'
];

// Navigation Année
const prevYear = () => currentYear.value--;
const nextYear = () => currentYear.value++;

// Sélection Mois
const selectMonth = (index) => {
    // Formatage "01", "02", ... "11"
    const formattedMonth = String(index + 1).padStart(2, '0');
    const newValue = `${currentYear.value}-${formattedMonth}`;

    emit('update:modelValue', newValue);
    emit('change', newValue); // Pour déclencher le rechargement Inertia
    isOpen.value = false;
};

// Fermeture au clic dehors
const handleClickOutside = (e) => {
    if (containerRef.value && !containerRef.value.contains(e.target)) {
        isOpen.value = false;
    }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));

// Label affiché dans le bouton
const formattedLabel = computed(() => {
    const [y, m] = props.modelValue.split('-');
    return `${months[parseInt(m) - 1]} ${y}`;
});
</script>

<template>
    <div class="relative inline-block text-left" ref="containerRef">

        <button
            @click="isOpen = !isOpen"
            type="button"
            class="inline-flex items-center gap-x-2 w-full justify-between rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-sky-600"
        >
            <CalendarIcon class="h-5 w-5 text-slate-400" aria-hidden="true" />
            <span class="capitalize min-w-[100px] text-left">{{ formattedLabel }}</span>
        </button>

        <div v-if="isOpen" class="absolute right-0 z-50 mt-2 w-72 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none animate-fade-in">
            <div class="p-4">

                <div class="flex items-center justify-between mb-4">
                    <button @click="prevYear" class="p-1 hover:bg-slate-100 rounded-full text-slate-500 hover:text-sky-600">
                        <ChevronLeftIcon class="h-5 w-5" />
                    </button>
                    <span class="text-lg font-bold text-slate-800">{{ currentYear }}</span>
                    <button @click="nextYear" class="p-1 hover:bg-slate-100 rounded-full text-slate-500 hover:text-sky-600">
                        <ChevronRightIcon class="h-5 w-5" />
                    </button>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <button
                        v-for="(month, index) in months"
                        :key="month"
                        @click="selectMonth(index)"
                        :class="[
                            'px-2 py-2 text-sm rounded-md transition-colors font-medium',
                            // Si c'est le mois sélectionné (et la bonne année)
                            (index === currentMonthIndex && currentYear === parseInt(props.modelValue.split('-')[0]))
                                ? 'bg-sky-600 text-white shadow-sm'
                                : 'text-slate-600 hover:bg-sky-50 hover:text-sky-700'
                        ]"
                    >
                        {{ month.substring(0, 3) }}. </button>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex justify-center">
                    <button
                        @click="() => {
                            const now = new Date();
                            currentYear = now.getFullYear();
                            selectMonth(now.getMonth());
                        }"
                        class="text-xs text-sky-600 font-semibold hover:text-sky-800"
                    >
                        Revenir à ce mois
                    </button>
                </div>

            </div>
        </div>
    </div>
</template>