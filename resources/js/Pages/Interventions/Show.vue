<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    CalendarIcon,
    MapPinIcon,
    CheckBadgeIcon,
    PencilSquareIcon,
    DocumentArrowDownIcon,
    ArrowLeftIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    intervention: Object,
    can_validate: Boolean
});

// Fonction pour formater les dates proprement
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString('fr-FR', {
        weekday: 'long', day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit'
    });
};

const validateIntervention = () => {
    if (confirm('Êtes-vous sûr de vouloir valider cette intervention ? Elle ne sera plus modifiable.')) {
        router.patch(`/interventions/${props.intervention.id}/validate`);
    }
};
</script>

<template>
    <Head :title="`Intervention - ${intervention.client.first_name}`" />

    <AppLayout>
        <div class="mb-6">
            <Link href="/interventions" class="inline-flex items-center text-sm text-slate-500 hover:text-sky-600">
                <ArrowLeftIcon class="w-4 h-4 mr-1"/>
                Retour à la liste
            </Link>
        </div>

        <div class="max-w-4xl mx-auto">

            <div class="md:flex md:items-center md:justify-between mb-6">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Intervention : {{ intervention.client.first_name }} {{ intervention.client.last_name }}
                    </h2>
                    <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                        <div class="mt-2 flex items-center text-sm text-slate-500">
                            <CalendarIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-slate-400" />
                            {{ formatDate(intervention.start_at) }}
                        </div>
                        <div class="mt-2 flex items-center text-sm text-slate-500">
                            <MapPinIcon class="mr-1.5 h-5 w-5 flex-shrink-0 text-slate-400" />
                            {{ intervention.location_type }}
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex flex-shrink-0 md:ml-4 md:mt-0 gap-3">
                    <a :href="`/interventions/${intervention.id}/pdf`" target="_blank" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition-colors">
                        <DocumentArrowDownIcon class="-ml-0.5 mr-1.5 h-5 w-5 text-slate-400" />
                        Télécharger PDF
                    </a>

                    <button
                        v-if="can_validate"
                        @click="validateIntervention"
                        type="button"
                        class="inline-flex items-center rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
                        <CheckBadgeIcon class="-ml-0.5 mr-1.5 h-5 w-5" />
                        Valider & Clôturer
                    </button>

                    <span v-else class="inline-flex items-center rounded-md bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-500 cursor-not-allowed">
                        <CheckBadgeIcon class="-ml-0.5 mr-1.5 h-5 w-5" />
                        Déjà validé
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white shadow-sm ring-1 ring-slate-900/5 rounded-xl p-6">
                        <h3 class="text-base font-semibold leading-7 text-slate-900">Détails</h3>
                        <dl class="mt-4 space-y-4 text-sm leading-6">
                            <div>
                                <dt class="text-slate-500">Type</dt>
                                <dd class="font-medium text-slate-900">{{ intervention.intervention_type }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Durée</dt>
                                <dd class="font-medium text-slate-900">{{ intervention.duration_minutes }} min</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Statut</dt>
                                <dd class="mt-1">
                                    <span v-if="intervention.status === 'validated'" class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Validé</span>
                                    <span v-else class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">Brouillon</span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-slate-50 ring-1 ring-slate-900/5 rounded-xl p-6 opacity-75">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Vos notes originales</h3>
                        <p class="text-sm text-slate-600 italic whitespace-pre-wrap">{{ intervention.raw_notes }}</p>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white shadow-md ring-1 ring-slate-900/5 rounded-xl overflow-hidden">
                        <div class="border-b border-slate-200 bg-slate-50 px-6 py-4 flex items-center justify-between">
                            <h3 class="font-semibold text-slate-900">Compte rendu d'intervention</h3>
                            <span class="text-xs text-slate-500">Généré par IA</span>
                        </div>
                        <div class="p-8 prose prose-slate max-w-none">
                            <div class="whitespace-pre-wrap text-slate-700 leading-relaxed font-normal">
                                {{ intervention.ai_report || "Aucun rapport généré." }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>