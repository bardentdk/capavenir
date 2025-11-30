<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { PlusIcon, BanknotesIcon, TruckIcon, PaperClipIcon } from '@heroicons/vue/24/outline';

defineProps({ expenses: Object });
</script>

<template>
    <Head title="Mes Frais" />
    <AppLayout>
        <div class="sm:flex sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Notes de Frais</h1>
                <p class="mt-1 text-sm text-slate-500">Déclarez vos achats et indemnités kilométriques.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <Link href="/expenses/create" class="inline-flex items-center justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500">
                    <PlusIcon class="-ml-0.5 mr-1.5 h-5 w-5" />
                    Nouvelle Note
                </Link>
            </div>
        </div>

        <div class="bg-white shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl overflow-hidden">
            <ul role="list" class="divide-y divide-slate-100">
                <li v-for="expense in expenses.data" :key="expense.id" class="flex items-center justify-between gap-x-6 px-4 py-5 hover:bg-slate-50 sm:px-6">
                    <div class="flex min-w-0 gap-x-4">
                        <div class="h-12 w-12 flex-none rounded-full flex items-center justify-center"
                             :class="expense.type === 'mileage' ? 'bg-orange-100 text-orange-600' : 'bg-purple-100 text-purple-600'">
                            <TruckIcon v-if="expense.type === 'mileage'" class="h-6 w-6" />
                            <BanknotesIcon v-else class="h-6 w-6" />
                        </div>
                        <div class="min-w-0 flex-auto">
                            <p class="text-sm font-semibold leading-6 text-slate-900">{{ expense.description }}</p>
                            <p class="mt-1 flex text-xs leading-5 text-slate-500">
                                {{ expense.date_formatted }}
                                <a v-if="expense.proof_url" :href="expense.proof_url" target="_blank" class="ml-2 inline-flex items-center text-sky-600 hover:underline">
                                    <PaperClipIcon class="w-3 h-3 mr-1"/> Justificatif
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="hidden sm:flex sm:flex-col sm:items-end">
                        <p class="text-sm leading-6 text-slate-900 font-bold">{{ expense.amount }}</p>
                        <div v-if="expense.status === 'pending'" class="mt-1 text-xs leading-5 text-amber-500 font-medium">En attente</div>
                        <div v-if="expense.status === 'approved'" class="mt-1 text-xs leading-5 text-emerald-500 font-medium">Validé</div>
                        <div v-if="expense.status === 'rejected'" class="mt-1 text-xs leading-5 text-red-500 font-medium">Rejeté</div>
                    </div>
                </li>
                 <li v-if="expenses.data.length === 0" class="px-6 py-10 text-center text-slate-500">
                    Aucun frais déclaré pour le moment.
                </li>
            </ul>
        </div>
    </AppLayout>
</template>