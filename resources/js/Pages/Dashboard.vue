<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Tableau de bord" />

    <AppLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Tableau de bord</h1>
            <p class="text-slate-500">Bienvenue sur votre espace de gestion.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
                <div class="text-slate-500 text-sm font-medium uppercase">Interventions ce mois</div>
                <div class="mt-2 text-3xl font-bold text-slate-800">0</div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100">
                <div class="text-slate-500 text-sm font-medium uppercase">Heures validées</div>
                <div class="mt-2 text-3xl font-bold text-sky-600">0h</div>
            </div>
        </div>
    </AppLayout>
</template>