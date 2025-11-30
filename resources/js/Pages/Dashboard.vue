<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    PlusIcon,
    BanknotesIcon,
    UserGroupIcon,
    ChartPieIcon,
    UserPlusIcon,
    ArrowRightIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    stats: Array,
    shortcuts: Array,
    role: String,
    user: Object,
});
// Mapping des icônes pour les afficher dynamiquement
const iconMap = {
    PlusIcon,
    BanknotesIcon,
    UserGroupIcon,
    ChartPieIcon,
    UserPlusIcon
};


</script>

<template>
    <Head title="Tableau de bord" />

    <AppLayout>
        <div class="mb-8 overflow-hidden">
            <h1 class="text-2xl font-bold text-slate-800">Bonjour
                <span class="text-(--color-capavenir) italic">{{user.name}}</span>
                ! 👋
            </h1>
            <p class="text-slate-500 mt-1">
                Voici ce qui se passe sur
                <span v-if="role === 'educator'">votre activité</span>
                <span v-else>la structure</span>
                ce mois-ci.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div v-for="(stat, index) in stats" :key="index"
                class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                <div class="text-slate-500 text-xs font-bold uppercase tracking-wider">{{ stat.label }}</div>
                <div :class="['mt-2 text-3xl font-bold', stat.color]">
                    {{ stat.value }}
                </div>
            </div>
        </div>

        <h2 class="text-lg font-bold text-slate-900 mb-4">Accès Rapide</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <Link v-for="(action, index) in shortcuts" :key="index" :href="action.url"
                class="group relative flex items-center gap-x-6 rounded-lg p-4 bg-white border border-slate-200 hover:bg-slate-50 transition-colors">

                <div :class="['flex h-11 w-11 flex-none items-center justify-center rounded-lg', action.color]">
                    <component :is="iconMap[action.icon]" class="h-6 w-6 text-white" aria-hidden="true" />
                </div>

                <div class="flex-auto">
                    <span class="block font-semibold text-slate-900">
                        {{ action.label }}
                        <span class="absolute inset-0" />
                    </span>
                </div>

                <ArrowRightIcon class="h-5 w-5 text-slate-400 group-hover:text-slate-600" />
            </Link>
        </div>

        <div class="mt-12 bg-(--color-capavenir-dark) rounded-lg p-4 border border-(--color-capavenir) flex items-center gap-3">
            <div class="text-pink-100 mt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-8 h-8">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-medium text-pink-100 font-serif">Astuce du jour</h3>
                <p class="text-sm text-pink-100 mt-1">
                    N'oubliez pas de valider vos interventions en fin de semaine pour faciliter le travail de la comptabilité.
                </p>
            </div>
        </div>

    </AppLayout>
</template>