<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { MagnifyingGlassIcon, PlusIcon, PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { debounce } from 'lodash'; // Lodash est inclus par défaut dans Laravel Vite souvent, sinon npm install lodash

const props = defineProps({
    clients: Object,
    filters: Object
});

const search = ref(props.filters.search || '');

// Recherche réactive avec délai (debounce)
watch(search, (value) => {
    router.get('/clients', { search: value }, { preserveState: true, replace: true });
});

const deleteClient = (client) => {
    if(confirm(`Supprimer le dossier de ${client.name} ?`)) {
        router.delete(`/clients/${client.id}`);
    }
}
</script>

<template>
    <Head title="Bénéficiaires" />
    <AppLayout>
        <div class="sm:flex sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Bénéficiaires</h1>
                <p class="mt-1 text-sm text-slate-500">Gestion des dossiers usagers.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-4">
                <Link href="/clients/create" class="inline-flex items-center justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500">
                    <PlusIcon class="-ml-0.5 mr-1.5 h-5 w-5" />
                    Nouveau Dossier
                </Link>
            </div>
        </div>

        <div class="mb-4 max-w-md">
            <div class="relative rounded-md shadow-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <MagnifyingGlassIcon class="h-5 w-5 text-slate-400" />
                </div>
                <input v-model="search" type="text" class="px-3 py-3 block w-full sm:w-full rounded-md border-slate-300 pl-10 focus:border-sky-500 focus:ring-sky-500 sm:text-sm" placeholder="Rechercher par nom...">
            </div>
        </div>

        <div class="bg-white shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Âge</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Adresse</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Statut</th>
                        <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <tr v-for="client in clients.data" :key="client.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <Link :href="`/clients/${client.id}`" class="text-sm font-medium text-slate-900 hover:text-(--color-capavenir) transition-colors font-bold">
                                {{ client.name }}
                            </Link>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ client.age }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 max-w-xs truncate">{{ client.address }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span v-if="client.is_active" class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Actif</span>
                            <span v-else class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-800">Archivé</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <Link :href="`/clients/${client.id}/edit`" class="text-sky-600 hover:text-sky-900 mr-4">
                                <PencilSquareIcon class="w-5 h-5 inline" />
                            </Link>
                            <button @click="deleteClient(client)" class="text-red-600 hover:text-red-900">
                                <TrashIcon class="w-5 h-5 inline" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>