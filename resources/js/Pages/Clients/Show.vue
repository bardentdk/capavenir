<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import {
    UserIcon,
    FolderOpenIcon,
    DocumentIcon,
    ArrowDownTrayIcon,
    TrashIcon,
    PlusIcon,
    PencilSquareIcon,
    ArrowLeftIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    client: Object
});

const activeTab = ref('documents'); // On ouvre direct sur les docs, c'est souvent ce qu'on cherche

// --- GESTION UPLOAD ---
const fileInput = ref(null);
const uploadForm = useForm({
    client_id: props.client.id,
    file: null,
    name: ''
});

const triggerUpload = () => fileInput.value.click();

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    uploadForm.file = file;
    // On envoie direct ou on peut demander le nom. Ici on envoie direct pour fluidité.
    uploadForm.post('/documents', {
        preserveScroll: true,
        onSuccess: () => {
            uploadForm.reset();
            // Petit reset du champ file html
            fileInput.value.value = null;
        }
    });
};

// --- GESTION SUPPRESSION ---
const deleteDocument = (doc) => {
    if (confirm('Voulez-vous vraiment supprimer ce document ?')) {
        router.delete(`/documents/${doc.id}`, { preserveScroll: true });
    }
};

// Fonction utilitaire pour l'icone selon l'extension
const getFileIcon = (filename) => {
    const ext = filename.split('.').pop().toLowerCase();
    // On pourrait retourner des icones différentes selon pdf, jpg, doc...
    // Pour l'instant icône générique propre.
    return DocumentIcon;
};
</script>

<template>
    <Head :title="client.first_name + ' ' + client.last_name" />

    <AppLayout>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <Link href="/clients" class="p-2 rounded-full bg-white border border-slate-200 text-slate-500 hover:text-(--color-capavenir) transition-colors">
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                        {{ client.first_name }} {{ client.last_name }}
                        <span v-if="!client.is_active" class="px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-500 border border-slate-200">Archivé</span>
                    </h1>
                    <p class="text-slate-500 text-sm">Dossier bénéficiaire #{{ client.id }}</p>
                </div>
            </div>

            <Link :href="`/clients/${client.id}/edit`" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors shadow-sm">
                <PencilSquareIcon class="w-4 h-4 mr-2" />
                Modifier infos
            </Link>
        </div>

        <div class="border-b border-slate-200 mb-6">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">

                <button @click="activeTab = 'infos'"
                    :class="[
                        activeTab === 'infos'
                            ? 'border-(--color-capavenir) text-(--color-capavenir)'
                            : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300',
                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors'
                    ]">
                    <UserIcon class="w-5 h-5" />
                    Informations
                </button>

                <button @click="activeTab = 'documents'"
                    :class="[
                        activeTab === 'documents'
                            ? 'border-(--color-capavenir) text-(--color-capavenir)'
                            : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300',
                        'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors'
                    ]">
                    <FolderOpenIcon class="w-5 h-5" />
                    GED / Documents
                    <span class="bg-slate-100 text-slate-600 py-0.5 px-2.5 rounded-full text-xs ml-2">{{ client.documents.length }}</span>
                </button>

            </nav>
        </div>

        <div v-if="activeTab === 'infos'" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 animate-fade-in">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-8">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-slate-500">Nom complet</dt>
                    <dd class="mt-1 text-sm text-slate-900 font-semibold">{{ client.first_name }} {{ client.last_name }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-slate-500">Date de naissance</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ new Date(client.birth_date).toLocaleDateString() }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-slate-500">Adresse</dt>
                    <dd class="mt-1 text-sm text-slate-900">{{ client.address }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-slate-500">Informations médicales / Notes</dt>
                    <dd class="mt-1 text-sm text-slate-900 bg-slate-50 p-3 rounded-lg border border-slate-100">
                        {{ client.medical_info || 'Aucune information saisie.' }}
                    </dd>
                </div>
            </dl>
        </div>

        <div v-if="activeTab === 'documents'" class="animate-fade-in">

            <div
                @click="triggerUpload"
                class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center hover:border-(--color-capavenir) hover:bg-pink-50/30 transition-all cursor-pointer group mb-6"
            >
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 group-hover:bg-white mb-3">
                    <PlusIcon class="h-6 w-6 text-slate-500 group-hover:text-(--color-capavenir)" />
                </div>
                <h3 class="text-sm font-semibold text-slate-900">Ajouter un document</h3>
                <p class="text-xs text-slate-500 mt-1">PDF, Images (Max 10Mo)</p>
                <input type="file" ref="fileInput" class="hidden" @change="handleFileChange" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
            </div>

            <div v-if="client.documents.length > 0" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <ul role="list" class="divide-y divide-slate-100">
                    <li v-for="doc in client.documents" :key="doc.id" class="flex items-center justify-between gap-x-6 px-6 py-5 hover:bg-slate-50 transition-colors">
                        <div class="flex min-w-0 gap-x-4">
                            <div class="h-10 w-10 flex-none rounded-lg bg-pink-50 flex items-center justify-center">
                                <DocumentIcon class="h-6 w-6 text-(--color-capavenir)" />
                            </div>
                            <div class="min-w-0 flex-auto">
                                <p class="text-sm font-semibold leading-6 text-slate-900">{{ doc.name }}</p>
                                <p class="mt-1 truncate text-xs leading-5 text-slate-500">Ajouté le {{ new Date(doc.created_at).toLocaleDateString() }}</p>
                            </div>
                        </div>
                        <div class="flex flex-none items-center gap-x-4">
                            <a :href="`/storage/${doc.path}`" target="_blank" class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 flex items-center gap-1">
                                <ArrowDownTrayIcon class="w-4 h-4 text-slate-500" />
                                <span class="hidden sm:inline">Ouvrir</span>
                            </a>

                            <button @click="deleteDocument(doc)" class="text-slate-400 hover:text-red-600 transition-colors p-1">
                                <TrashIcon class="w-5 h-5" />
                            </button>
                        </div>
                    </li>
                </ul>
            </div>

            <div v-else class="text-center py-10">
                <FolderOpenIcon class="mx-auto h-12 w-12 text-slate-300" />
                <h3 class="mt-2 text-sm font-semibold text-slate-900">Aucun document</h3>
                <p class="mt-1 text-sm text-slate-500">Commencez par ajouter un fichier ci-dessus.</p>
            </div>

        </div>

    </AppLayout>
</template>