<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DatePicker from '@/Components/DatePicker.vue';

const props = defineProps({
    client: Object // Optionnel (présent seulement en mode édition)
});

const isEditing = !!props.client;

const form = useForm({
    first_name: props.client?.first_name || '',
    last_name: props.client?.last_name || '',
    birth_date: props.client?.birth_date ? props.client.birth_date.split('T')[0] : '', // Format YYYY-MM-DD
    address: props.client?.address || '',
    medical_info: props.client?.medical_info || '',
    is_active: props.client?.is_active ?? true,
});

const submit = () => {
    if (isEditing) {
        form.put(`/clients/${props.client.id}`);
    } else {
        form.post('/clients');
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Modifier Dossier' : 'Nouveau Dossier'" />
    <AppLayout>
        <div class="max-w-2xl mx-auto">
            <h1 class="text-xl font-bold text-slate-900 mb-6">{{ isEditing ? 'Modifier le dossier' : 'Créer un dossier bénéficiaire' }}</h1>

            <div class="bg-white shadow-sm ring-1 ring-slate-900/5 rounded-xl p-6 sm:p-8">
                <form @submit.prevent="submit" class="space-y-6">

                    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-900">Prénom</label>
                            <input type="text" v-model="form.first_name" class="px-3 py-3 mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                            <div v-if="form.errors.first_name" class="text-red-500 text-xs mt-1">{{ form.errors.first_name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-900">Nom</label>
                            <input type="text" v-model="form.last_name" class="px-3 py-3 mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                            <div v-if="form.errors.last_name" class="text-red-500 text-xs mt-1">{{ form.errors.last_name }}</div>
                        </div>
                    </div>

                    <!-- <div>
                        <label class="block text-sm font-medium text-slate-900">Date de naissance</label>
                        <input type="date" v-model="form.birth_date" class="px-3 py-3 mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                    </div> -->
                    <div>
                        <DatePicker
                            label="Date de naissance"
                            v-model="form.birth_date"
                            placeholder="JJ MMMM AAAA"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-900">Adresse principale</label>
                        <input type="text" v-model="form.address" class="px-3 py-3 mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-900">Informations Médicales / Alertes</label>
                        <textarea v-model="form.medical_info" rows="3" placeholder="Allergies, traitement, régime..." class="px-3 py-3 mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm"></textarea>
                    </div>

                    <div v-if="isEditing" class="flex items-center gap-2">
                        <input type="checkbox" v-model="form.is_active" id="active" class="px-3 py-3 h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-600">
                        <label for="active" class="text-sm font-medium text-slate-900">Dossier Actif</label>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                        <Link href="/clients" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50">Annuler</Link>
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500 disabled:opacity-50">
                            {{ isEditing ? 'Mettre à jour' : 'Créer le dossier' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AppLayout>
</template>