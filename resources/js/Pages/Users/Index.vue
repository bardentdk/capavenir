<script setup>
import { Head, Link, router } from '@inertiajs/vue3'; // Ajout router
import AppLayout from '@/Layouts/AppLayout.vue';
import { UserPlusIcon, PencilSquareIcon, TrashIcon, ClockIcon } from '@heroicons/vue/24/outline'; // Ajout icones

defineProps({ users: Object });

// ... tes fonctions getRoleBadge et formatRole inchangées ...
const getRoleBadge = (role) => {
    switch(role) {
        case 'admin': return 'bg-purple-100 text-purple-700 border-purple-200';
        case 'accountant': return 'bg-orange-100 text-orange-700 border-orange-200';
        case 'educator': return 'bg-sky-100 text-sky-700 border-sky-200';
        default: return 'bg-slate-100 text-slate-600';
    }
};

const formatRole = (role) => {
    const map = {
        'admin': 'Administrateur',
        'accountant': 'Comptabilité',
        'educator': 'Éducateur',
    };
    return map[role] || role;
};

// Fonction suppression
const deleteUser = (user) => {
    if(confirm(`Voulez-vous vraiment supprimer ${user.name} ? Cette action est irréversible.`)) {
        router.delete(`/users/${user.id}`);
    }
};
</script>

<template>
    <Head title="Gestion de l'équipe" />
    <AppLayout>
        <div class="sm:flex sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Équipe</h1>
                <p class="mt-1 text-sm text-slate-500">Gérez les comptes d'accès à l'application.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <Link href="/users/create" class="inline-flex items-center justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500 transition-colors">
                    <UserPlusIcon class="-ml-0.5 mr-1.5 h-5 w-5" />
                    Ajouter un membre
                </Link>
            </div>
        </div>

        <div class="bg-white shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Rôle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Dernière connexion</th>
                        <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <tr v-for="user in users.data" :key="user.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div v-if="user?.avatar_path" class="w-10 h-10 rounded-full bg-cover bg-center border-2 border-white shadow-sm"
                                     :style="{ backgroundImage: `url('${user.avatar_path}')` }">
                                </div>
                                <div v-else class="w-10 h-10 rounded-full bg-gradient-to-br from-(--color-capavenir) to-slate-400 flex items-center justify-center text-xs font-bold text-white border-2 border-white shadow-sm">
                                    {{ user?.name?.charAt(0) || 'U' }}
                                </div>
                                <div class="ml-3 font-medium text-slate-900">{{ user.name }}</div>
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ user.email }}</td>

                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span :class="['px-2 py-1 text-xs font-medium rounded-full border', getRoleBadge(user.role)]">
                                {{ formatRole(user.role) }}
                            </span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            <div class="flex items-center gap-1.5">
                                <ClockIcon class="w-4 h-4 text-slate-400" />
                                <span>{{ user.last_login }}</span>
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <Link :href="`/users/${user.id}/edit`" class="text-sky-600 hover:text-sky-900 mr-4 transition-colors" title="Modifier">
                                <PencilSquareIcon class="w-5 h-5 inline" />
                            </Link>
                            <button @click="deleteUser(user)" class="text-red-400 hover:text-red-600 transition-colors" title="Supprimer">
                                <TrashIcon class="w-5 h-5 inline" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="users.data.length > 0" class="border-t border-slate-200 px-4 py-3 flex justify-between bg-white rounded-b-xl">
             </div>
    </AppLayout>
</template>