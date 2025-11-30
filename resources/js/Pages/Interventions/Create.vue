<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import axios from 'axios';
import { SparklesIcon } from '@heroicons/vue/24/solid';
import DatePicker from '@/Components/DatePicker.vue';

const props = defineProps({
    clients: Array
});

// Variables locales pour l'UI (Date/Heure séparées)
const dateDebut = ref('');
const heureDebut = ref('');
const dateFin = ref('');
const heureFin = ref('');

// État IA
const isGenerating = ref(false);

const form = useForm({
    client_id: '',
    start_at: '',
    end_at: '',
    intervention_type: 'Accompagnement éducatif',
    location_type: 'Domicile',
    raw_notes: '',
    ai_report: '',
});

// Fonction IA
const generateAiReport = async () => {
    if (!form.raw_notes || !form.client_id) {
        alert("Veuillez sélectionner un bénéficiaire et saisir quelques notes d'abord.");
        return;
    }
    isGenerating.value = true;
    try {
        const response = await axios.post('/interventions/generate-ai', {
            raw_notes: form.raw_notes,
            intervention_type: form.intervention_type,
            client_id: form.client_id
        });
        form.ai_report = response.data.report;
    } catch (error) {
        console.error(error);
        alert("Erreur IA. Voir console.");
    } finally {
        isGenerating.value = false;
    }
};

// Soumission
const submit = () => {
    // Reconstitution des Datetime pour Laravel
    if(dateDebut.value && heureDebut.value) {
        form.start_at = `${dateDebut.value} ${heureDebut.value}:00`;
    }
    if(dateFin.value && heureFin.value) {
        form.end_at = `${dateFin.value} ${heureFin.value}:00`;
    }

    // Auto-fill fin si vide
    if (!dateFin.value && dateDebut.value) {
         form.end_at = `${dateDebut.value} ${heureFin.value || '18:00'}:00`;
    }

    form.post('/interventions');
};
</script>

<template>
    <Head title="Nouvelle Intervention" />

    <AppLayout>
        <div class="max-w-3xl mx-auto">
            <h1 class="text-xl font-bold text-slate-900 mb-6">Saisir une intervention</h1>

            <div class="bg-white shadow-sm ring-1 ring-slate-900/5 rounded-xl p-6 sm:p-8">
                <form @submit.prevent="submit" class="space-y-8"> <div>
                        <label class="block text-sm font-medium leading-6 text-slate-900">Bénéficiaire</label>
                        <select v-model="form.client_id" class="mt-2 block w-full rounded-md border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-sm sm:leading-6">
                            <option value="" disabled>Choisir un enfant...</option>
                            <option v-for="client in clients" :key="client.id" :value="client.id">
                                {{ client.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.client_id" class="mt-2 text-sm text-red-600">{{ form.errors.client_id }}</p>
                    </div>

                    <div class="border-t border-slate-100 pt-6"></div>

                    <div>
                        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Horaires</h3>
                        <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium leading-6 text-slate-900 mb-1">Début</label>
                                <div class="flex gap-2">
                                    <div class="flex-1">
                                        <DatePicker v-model="dateDebut" placeholder="Date" />
                                    </div>
                                    <div class="w-28">
                                        <input type="time" v-model="heureDebut" class="block w-full rounded-md border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-sky-600 sm:text-sm">
                                    </div>
                                </div>
                                <p v-if="form.errors.start_at" class="text-xs text-red-600 mt-1">{{ form.errors.start_at }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium leading-6 text-slate-900 mb-1">Fin</label>
                                <div class="flex gap-2">
                                    <div class="flex-1">
                                        <DatePicker v-model="dateFin" :placeholder="dateDebut ? 'Même jour' : 'Date'" />
                                    </div>
                                    <div class="w-28">
                                        <input type="time" v-model="heureFin" class="block w-full rounded-md border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-sky-600 sm:text-sm">
                                    </div>
                                </div>
                                <p v-if="form.errors.end_at" class="text-xs text-red-600 mt-1">{{ form.errors.end_at }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-4">Contexte</h3>
                        <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium leading-6 text-slate-900">Type d'intervention</label>
                                <select v-model="form.intervention_type" class="mt-2 block w-full rounded-md border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-sky-600 sm:text-sm">
                                    <option>Accompagnement éducatif</option>
                                    <option>Entretien familial</option>
                                    <option>Synthèse / Réunion</option>
                                    <option>Transport</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium leading-6 text-slate-900">Lieu</label>
                                <select v-model="form.location_type" class="mt-2 block w-full rounded-md border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-sky-600 sm:text-sm">
                                    <option>Domicile</option>
                                    <option>École / Établissement</option>
                                    <option>Extérieur</option>
                                    <option>Bureau</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-6"></div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-medium leading-6 text-slate-900">Notes Brutes</label>
                                <span class="text-xs text-slate-400">Vos mots-clés</span>
                            </div>
                            <textarea v-model="form.raw_notes" rows="8" placeholder="- Activité..." class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-sky-600 sm:text-sm"></textarea>

                            <div class="mt-3 text-right">
                                <button type="button" @click="generateAiReport" :disabled="isGenerating || !form.raw_notes"
                                    class="inline-flex items-center gap-x-1.5 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50 transition-all">
                                    <SparklesIcon v-if="!isGenerating" class="-ml-0.5 h-5 w-5" />
                                    <svg v-else class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    {{ isGenerating ? 'Rédaction IA...' : 'Générer le rapport' }}
                                </button>
                            </div>
                        </div>

                        <div class="relative">
                            <label class="block text-sm font-medium leading-6 text-slate-900 mb-2">Compte Rendu Final</label>
                            <div class="relative">
                                <textarea v-model="form.ai_report" rows="12" class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-indigo-600 sm:text-sm bg-slate-50"></textarea>
                                <div v-if="!form.ai_report && !isGenerating" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <p class="text-sm text-slate-400 italic">Le rapport apparaîtra ici...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-x-6 border-t border-slate-900/10 pt-6">
                        <button type="button" class="text-sm font-semibold leading-6 text-slate-900" @click="$inertia.visit('/interventions')">Annuler</button>
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500 disabled:opacity-50">
                            {{ form.processing ? 'Enregistrement...' : 'Valider & Enregistrer' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AppLayout>
</template>