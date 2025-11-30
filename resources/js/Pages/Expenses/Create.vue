<script setup>
  import { useForm, Head } from '@inertiajs/vue3';
  import AppLayout from '@/Layouts/AppLayout.vue';
  import { ref } from 'vue';

  const props = defineProps({ clients: Array });

  // Type de frais sélectionné (mileage ou purchase)
  const currentTab = ref('mileage');

  const form = useForm({
      type: 'mileage',
      expense_date: new Date().toISOString().split('T')[0], // Date aujourd'hui par défaut
      client_id: '',

      // Champs Kilomètres
      start_address: '',
      end_address: '',
      distance_km: '',

      // Champs Achat
      amount: '',
      proof: null, // Fichier
  });

  const submit = () => {
      form.type = currentTab.value;
      form.post('/expenses', {
          forceFormData: true, // IMPORTANT pour l'upload de fichier
      });
  };

  const handleFile = (e) => {
      form.proof = e.target.files[0];
  };
</script>

<template>
    <Head title="Nouveau Frais" />
    <AppLayout>
        <div class="max-w-xl mx-auto">
            <h1 class="text-xl font-bold text-slate-900 mb-6">Déclarer un frais</h1>

            <div class="grid sm:grid-cols-2 gap-2 p-1 bg-slate-200 rounded-lg mb-6">
                <button
                    @click="currentTab = 'mileage'"
                    :class="[
                        'py-2.5 text-sm font-medium rounded-md transition-all',
                        currentTab === 'mileage' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'
                    ]">
                    🚗 Indemnité Kilométrique
                </button>
                <button
                    @click="currentTab = 'purchase'"
                    :class="[
                        'py-2.5 text-sm font-medium rounded-md transition-all',
                        currentTab === 'purchase' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'
                    ]">
                    💶 Achat / Autre
                </button>
            </div>

            <div class="bg-white shadow-sm ring-1 ring-slate-900/5 rounded-xl p-6">
                <form @submit.prevent="submit" class="space-y-6">

                    <div>
                        <label class="block text-sm font-medium text-slate-900">Date</label>
                        <input type="date" v-model="form.expense_date" class="px-3 py-3 mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-900">Bénéficiaire (Optionnel)</label>
                        <select v-model="form.client_id" class="px-3 py-3 mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                            <option value="">Aucun / Frais général</option>
                            <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>

                    <div v-if="currentTab === 'mileage'" class="space-y-4 animate-fade-in">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-900">Départ</label>
                                <input type="text" v-model="form.start_address" placeholder="Bureau, Domicile..." class="px-3 py-3 mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                                <p v-if="form.errors.start_address" class="text-red-500 text-xs mt-1">{{ form.errors.start_address }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-900">Arrivée</label>
                                <input type="text" v-model="form.end_address" placeholder="Adresse client..." class="px-3 py-3 mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                                <p v-if="form.errors.end_address" class="text-red-500 text-xs mt-1">{{ form.errors.end_address }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-900">Distance (km)</label>
                            <div class="relative mt-1 rounded-md shadow-sm">
                                <input type="number" step="0.1" v-model="form.distance_km" class="px-3 py-3 block w-full rounded-md border-slate-300 pr-12 focus:border-sky-500 focus:ring-sky-500 sm:text-sm" placeholder="0.0">
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-slate-500 sm:text-sm">km</span>
                                </div>
                            </div>
                             <p v-if="form.errors.distance_km" class="text-red-500 text-xs mt-1">{{ form.errors.distance_km }}</p>
                        </div>

                        <div class="bg-blue-50 p-3 rounded-md text-sm text-blue-700">
                            Info : Le barème appliqué sera de <strong>0.50 € / km</strong>.
                        </div>
                    </div>

                    <div v-if="currentTab === 'purchase'" class="space-y-4 animate-fade-in">
                        <div>
                            <label class="block text-sm font-medium text-slate-900">Montant total</label>
                            <div class="relative mt-1 rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-slate-500 sm:text-sm">€</span>
                                </div>
                                <input type="number" step="0.01" v-model="form.amount" class="px-3 py-3 block w-full rounded-md border-slate-300 pl-7 pr-12 focus:border-sky-500 focus:ring-sky-500 sm:text-sm" placeholder="0.00">
                            </div>
                             <p v-if="form.errors.amount" class="text-red-500 text-xs mt-1">{{ form.errors.amount }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-900">Justificatif (Photo/PDF)</label>
                            <input type="file" @change="handleFile" class="px-3 py-3  mt-2 block w-full text-sm text-slate-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-sky-50 file:text-sky-700
                                hover:file:bg-sky-100
                            "/>
                             <p v-if="form.errors.proof" class="text-red-500 text-xs mt-1">{{ form.errors.proof }}</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500 disabled:opacity-50">
                            {{ form.processing ? 'Envoi...' : 'Enregistrer le frais' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AppLayout>
</template>