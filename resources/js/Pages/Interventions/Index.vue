<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { PlusIcon, ClockIcon, MapPinIcon } from '@heroicons/vue/20/solid';

defineProps({
    interventions: Object, // Pagination object
});
</script>

<template>
    <Head title="Mes Interventions" />

    <AppLayout>
        <div class="sm:flex sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Interventions</h1>
                <p class="mt-1 text-sm text-slate-500">Gérez votre planning et vos comptes rendus.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <Link href="/interventions/create" class="inline-flex items-center justify-center rounded-md bg-(--color-capavenir) px-4 py-2 text-sm transition duration-300 font-semibold text-white shadow-sm hover:bg-(--color-capavenir-dark) focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600">
                    <PlusIcon class="-ml-0.5 mr-1.5 h-5 w-5" aria-hidden="true" />
                    Nouvelle Saisie
                </Link>
            </div>
        </div>

        <div class="bg-white shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl overflow-hidden">
            <ul role="list" class="divide-y divide-slate-100">

                <li v-for="intervention in interventions.data" :key="intervention.id" class="relative flex justify-between gap-x-6 px-4 py-5 hover:bg-slate-50 sm:px-6 transition-colors">
                    <div class="flex min-w-0 gap-x-4">
                        <div class="h-12 w-12 flex-none rounded-full bg-sky-100 flex items-center justify-center text-sky-600 font-bold text-lg">
                            {{ intervention.client_name.charAt(0) }}
                        </div>

                        <div class="min-w-0 flex-auto">
                            <p class="text-sm font-semibold leading-6 text-slate-900">
                                <Link :href="`/interventions/${intervention.id}`" class="focus:outline-none">
                                    <span class="absolute inset-x-0 -top-px bottom-0" />
                                    {{ intervention.client_name }}
                                </Link>
                            </p>
                            <div class="mt-1 flex text-xs leading-5 text-slate-500 gap-x-2">
                                <span class="flex items-center">
                                    <ClockIcon class="h-4 w-4 mr-1 text-slate-400"/>
                                    {{ intervention.start_at_formatted }} - {{ intervention.end_at_time }}
                                </span>
                                <span class="bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-md font-medium">
                                    {{ intervention.type }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-x-4">
                        <div class="hidden sm:flex sm:flex-col sm:items-end">
                            <p class="text-sm leading-6 text-slate-900 font-medium">{{ intervention.duration }}</p>

                            <div v-if="intervention.status === 'draft'" class="mt-1 flex items-center text-xs leading-5 text-amber-500">
                                <div class="flex-none rounded-full bg-amber-500/20 p-1">
                                    <div class="h-1.5 w-1.5 rounded-full bg-amber-500" />
                                </div>
                                <div class="ml-1.5">Brouillon</div>
                            </div>
                            <div v-else class="mt-1 flex items-center text-xs leading-5 text-emerald-500">
                                <div class="flex-none rounded-full bg-emerald-500/20 p-1">
                                    <div class="h-1.5 w-1.5 rounded-full bg-emerald-500" />
                                </div>
                                <div class="ml-1.5">Validé</div>
                            </div>
                        </div>
                        <svg class="h-5 w-5 flex-none text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </li>

                <li v-if="interventions.data.length === 0" class="px-6 py-10 text-center text-slate-500">
                    Aucune intervention pour le moment. Commencez par en créer une !
                </li>

            </ul>

            <!-- <div v-if="interventions.data.length > 0" class="border-t border-slate-200 px-4 py-3 sm:px-6 flex justify-between">
                <Link :href="interventions.prev_page_url" :class="{'opacity-50 pointer-events-none': !interventions.prev_page_url}" class="text-sm text-slate-600 hover:text-sky-600 font-medium">Précédent</Link>
                <Link :href="interventions.next_page_url" :class="{'opacity-50 pointer-events-none': !interventions.next_page_url}" class="text-sm text-slate-600 hover:text-sky-600 font-medium">Suivant</Link>
            </div> -->
            <div v-if="interventions.data.length > 0" class="border-t border-slate-200 px-4 py-3 sm:px-6 flex justify-between">

                <Link
                    v-if="interventions.prev_page_url"
                    :href="interventions.prev_page_url"
                    class="text-sm text-slate-600 hover:text-sky-600 font-medium">
                    Précédent
                </Link>
                <span v-else class="text-sm text-slate-300 cursor-not-allowed font-medium">
                    Précédent
                </span>

                <Link
                    v-if="interventions.next_page_url"
                    :href="interventions.next_page_url"
                    class="text-sm text-slate-600 hover:text-sky-600 font-medium">
                    Suivant
                </Link>
                <span v-else class="text-sm text-slate-300 cursor-not-allowed font-medium">
                    Suivant
                </span>

            </div>
        </div>
    </AppLayout>
</template>