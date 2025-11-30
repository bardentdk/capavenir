<script setup>
import { ref, onMounted } from 'vue';
import { SunIcon, MoonIcon } from '@heroicons/vue/24/outline';

const isDark = ref(false);

const toggleTheme = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

onMounted(() => {
    // Check préférence utilisateur ou système
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
});
</script>

<template>
    <button @click="toggleTheme" class="p-2 rounded-full text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 transition-colors">
        <SunIcon v-if="isDark" class="w-6 h-6" />
        <MoonIcon v-else class="w-6 h-6" />
    </button>
</template>