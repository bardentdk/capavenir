<script setup>
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { CheckCircleIcon, XCircleIcon, EyeIcon } from '@heroicons/vue/24/outline';
import MonthPicker from '@/Components/MonthPicker.vue';

const props = defineProps({
    pendingExpenses: Array,
    payrollSummary: Array,
    history: Array,
    currentMonth: String
});

const rejectionReason = ref('');
const rejectingId = ref(null);

// Changer de mois
const updateMonth = (newValue) => {
    // router.get('/accounting', { month: e.target.value }, { preserveState: true });
    router.get('/accounting', { month: newValue }, { preserveState: true });
};

// Actions Frais
const approve = (id) => {
    if(confirm('Valider ce frais pour remboursement ?')) {
        router.patch(`/accounting/expenses/${id}/approve`);
    }
};

const openRejectModal = (id) => {
    rejectingId.value = id;
    rejectionReason.value = '';
};

const confirmReject = () => {
    if (!rejectionReason.value) return;
    router.patch(`/accounting/expenses/${rejectingId.value}/reject`, {
        reason: rejectionReason.value
    }, {
        onSuccess: () => rejectingId.value = null
    });
};
</script>

<template>
    <Head title="Espace Comptabilité" />
    <AppLayout>
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-slate-900">Suivi & Validation Paie</h1>

            <div class="flex items-center gap-3 bg-white p-1.5 rounded-lg border border-slate-200 shadow-sm">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider pl-2">Période :</span>

                <MonthPicker
                    :model-value="currentMonth"
                    @change="updateMonth"
                />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                    <h2 class="font-semibold text-slate-800">Synthèse des heures (Validées)</h2>
                    <p class="text-xs text-slate-500">Base de calcul pour les fiches de paie</p>
                </div>
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Éducateur</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Total Heures</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Volume</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <tr v-for="user in payrollSummary" :key="user.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ user.name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-slate-600">
                                <span class="font-bold text-slate-800">{{ user.total_hours }}h {{ user.total_minutes }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-slate-400">
                                {{ user.raw_minutes }} min
                            </td>
                        </tr>
                        <tr v-if="payrollSummary.length === 0">
                            <td colspan="3" class="px-6 py-4 text-center text-sm text-slate-500 italic">Aucune donnée validée pour ce mois.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                <div class="bg-orange-50 px-6 py-4 border-b border-orange-100 flex justify-between items-center">
                    <div>
                        <h2 class="font-semibold text-orange-900">Notes de frais en attente</h2>
                        <p class="text-xs text-orange-700">Action requise avant remboursement</p>
                    </div>
                    <span class="bg-orange-200 text-orange-800 text-xs font-bold px-2 py-1 rounded-full">{{ pendingExpenses.length }}</span>
                </div>

                <div class="overflow-y-auto max-h-[500px] flex-1">
                    <ul class="divide-y divide-slate-100">
                        <li v-for="expense in pendingExpenses" :key="expense.id" class="p-4 hover:bg-slate-50 transition-colors">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ expense.user_name }}</p>
                                    <p class="text-xs text-slate-500">{{ expense.date }} - {{ expense.description }}</p>

                                    <a v-if="expense.proof_url" :href="expense.proof_url" target="_blank" class="mt-2 inline-flex items-center text-xs text-sky-600 hover:underline">
                                        <EyeIcon class="w-3 h-3 mr-1"/> Voir le justificatif
                                    </a>
                                </div>
                                <div class="text-right">
                                    <span class="block text-lg font-bold text-slate-900">{{ expense.amount }} €</span>
                                    <span v-if="expense.type === 'mileage'" class="text-xs text-slate-400 uppercase">Kilométrique</span>
                                    <span v-else class="text-xs text-slate-400 uppercase">Achat</span>
                                </div>
                            </div>

                            <div class="mt-3 flex justify-end gap-2" v-if="rejectingId !== expense.id">
                                <button @click="openRejectModal(expense.id)" class="inline-flex items-center px-2 py-1 bg-white border border-slate-300 rounded text-xs font-medium text-slate-700 hover:bg-slate-50">
                                    <XCircleIcon class="w-4 h-4 mr-1 text-red-500"/> Rejeter
                                </button>
                                <button @click="approve(expense.id)" class="inline-flex items-center px-2 py-1 bg-emerald-600 border border-transparent rounded text-xs font-medium text-white hover:bg-emerald-700 shadow-sm">
                                    <CheckCircleIcon class="w-4 h-4 mr-1"/> Valider
                                </button>
                            </div>

                            <div v-if="rejectingId === expense.id" class="mt-3 bg-red-50 p-3 rounded-md animate-fade-in">
                                <label class="block text-xs font-medium text-red-800 mb-1">Motif du rejet :</label>
                                <input v-model="rejectionReason" type="text" class="block w-full text-xs rounded-md border-red-300 focus:border-red-500 focus:ring-red-500" placeholder="Ex: Justificatif illisible...">
                                <div class="mt-2 flex justify-end gap-2">
                                    <button @click="rejectingId = null" class="text-xs text-slate-600 hover:text-slate-800">Annuler</button>
                                    <button @click="confirmReject" class="text-xs bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Confirmer le rejet</button>
                                </div>
                            </div>
                        </li>
                        <li v-if="pendingExpenses.length === 0" class="p-8 text-center text-slate-500 italic">
                            Aucun frais en attente de validation. Tout est à jour ! 👏
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                <h2 class="font-semibold text-slate-800">Historique des Frais ({{ currentMonth }})</h2>
                <p class="text-xs text-slate-500">Liste complète des demandes traitées et en cours pour la période.</p>
            </div>
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Éducateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Type & Détail</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Montant</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase">Preuve</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <tr v-for="item in history" :key="item.id" class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ item.date }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ item.user_name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            <span class="block font-medium text-slate-700">{{ item.type_label }}</span>
                            <span class="text-xs truncate max-w-xs block">{{ item.description }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-slate-800">{{ item.amount }} €</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span v-if="item.status === 'approved'" class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Validé</span>
                            <span v-else-if="item.status === 'rejected'" class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Rejeté</span>
                            <span v-else class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">En attente</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <a v-if="item.proof_url" :href="item.proof_url" target="_blank" class="text-sky-600 hover:text-sky-900 flex justify-center" title="Voir le justificatif">
                                <EyeIcon class="w-5 h-5" />
                            </a>
                            <span v-else class="text-slate-300">-</span>
                        </td>
                    </tr>
                    <tr v-if="history.length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500 italic">
                            Aucun historique de frais pour ce mois-ci.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>