<script setup>
import { useForm, Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { CameraIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    user: Object,
});

const photoInput = ref(null);
const photoPreview = ref(null);

// Formulaire Infos Générales
const form = useForm({
    _method: 'POST', // Astuce pour l'upload de fichiers en modification
    name: props.user.name,
    email: props.user.email,
    photo: null,
});

// Formulaire Mot de Passe
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updateProfileInformation = () => {
    form.post('/profile', {
        preserveScroll: true,
        onSuccess: () => {
            // Optionnel : clear input file
        },
    });
};

const updatePassword = () => {
    passwordForm.put('/profile/password', {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];
    if (!photo) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(photo);
    form.photo = photo;
};
</script>

<template>
    <Head title="Mon Profil" />
    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <h1 class="text-2xl font-bold text-slate-900">Paramètres du compte</h1>

            <div class="bg-white shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6 sm:p-8">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-slate-900">Profil Public</h3>
                        <p class="mt-1 text-sm text-slate-500">Mettez à jour votre photo et vos informations personnelles.</p>
                    </div>

                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <form @submit.prevent="updateProfileInformation" class="space-y-6">

                            <div class="col-span-6 sm:col-span-4">
                                <label class="block text-sm font-medium text-slate-700">Photo</label>

                                <div class="mt-2 flex items-center gap-x-3">
                                    <div v-if="photoPreview" class="h-16 w-16 rounded-full bg-cover bg-center bg-no-repeat shadow-inner"
                                         :style="'background-image: url(\'' + photoPreview + '\');'">
                                    </div>

                                    <div v-else-if="user.avatar" class="h-16 w-16 rounded-full bg-cover bg-center bg-no-repeat shadow-inner"
                                         :style="'background-image: url(\'' + user.avatar + '\');'">
                                    </div>

                                    <div v-else class="h-16 w-16 rounded-full bg-slate-200 flex items-center justify-center text-xl font-bold text-slate-500">
                                        {{ user.name.charAt(0) }}
                                    </div>

                                    <input ref="photoInput" type="file" class="hidden" @change="updatePhotoPreview">

                                    <button type="button" @click="selectNewPhoto" class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">
                                        Changer
                                    </button>
                                </div>
                                <p v-if="form.errors.photo" class="mt-2 text-sm text-red-600">{{ form.errors.photo }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700">Nom complet</label>
                                <input v-model="form.name" type="text" class="px-3 py-3 mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                                <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700">Adresse Email</label>
                                <input v-model="form.email" type="email" class="px-3 py-3 mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                                <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">{{ form.errors.email }}</p>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" :disabled="form.processing" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500 disabled:opacity-50">
                                    Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6 sm:p-8">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-slate-900">Sécurité</h3>
                        <p class="mt-1 text-sm text-slate-500">Assurez-vous d'utiliser un mot de passe long et unique.</p>
                    </div>

                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <form @submit.prevent="updatePassword" class="space-y-6">

                            <div>
                                <label class="block text-sm font-medium text-slate-700">Mot de passe actuel</label>
                                <input v-model="passwordForm.current_password" type="password" class="px-3 py-3 mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                                <p v-if="passwordForm.errors.current_password" class="mt-2 text-sm text-red-600">{{ passwordForm.errors.current_password }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700">Nouveau mot de passe</label>
                                <input v-model="passwordForm.password" type="password" class="px-3 py-3 mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                                <p v-if="passwordForm.errors.password" class="mt-2 text-sm text-red-600">{{ passwordForm.errors.password }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700">Confirmer le nouveau mot de passe</label>
                                <input v-model="passwordForm.password_confirmation" type="password" class="px-3 py-3 mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" :disabled="passwordForm.processing" class="rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 disabled:opacity-50">
                                    Mettre à jour le mot de passe
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>