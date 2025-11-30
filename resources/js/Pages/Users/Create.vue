<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ roles: Array });

const form = useForm({
    name: '',
    email: '',
    role: 'educator', // Défaut
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/users');
};
</script>

<template>
    <Head title="Nouveau Collaborateur" />
    <AppLayout>
        <div class="max-w-xl mx-auto">
            <h1 class="text-xl font-bold text-slate-900 mb-6">Créer un compte</h1>

            <div class="bg-white shadow-sm ring-1 ring-slate-900/5 rounded-xl p-6 sm:p-8">
                <form @submit.prevent="submit" class="space-y-6">

                    <div>
                        <label class="block text-sm font-medium text-slate-900">Nom complet</label>
                        <input type="text" v-model="form.name" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                        <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-900">Adresse E-mail</label>
                        <input type="email" v-model="form.email" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                        <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-900">Rôle / Accès</label>
                        <select v-model="form.role" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                            <option v-for="role in roles" :key="role.value" :value="role.value">
                                {{ role.label }}
                            </option>
                        </select>
                        <div v-if="form.errors.role" class="text-red-500 text-xs mt-1">{{ form.errors.role }}</div>
                    </div>

                    <div class="border-t border-slate-100 pt-4 mt-4">
                        <p class="text-xs text-slate-500 mb-3">Définissez un mot de passe provisoire. L'utilisateur pourra le changer plus tard (fonction à venir) ou vous pourrez le réinitialiser.</p>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-900">Mot de passe</label>
                                <input type="password" v-model="form.password" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                                <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-900">Confirmation</label>
                                <input type="password" v-model="form.password_confirmation" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <Link href="/users" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50">Annuler</Link>
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500 disabled:opacity-50">
                            Créer le compte
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AppLayout>
</template>