<script setup>
import { computed } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue';
import { BellIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';

const page = usePage();
const notifications = computed(() => page.props.auth?.notifications || []);
const hasUnread = computed(() => notifications.value.length > 0);

const markAsRead = () => {
    router.post('/notifications/mark-as-read', {}, {
        preserveScroll: true,
        preserveState: true // On garde l'état pour que l'UI se mette à jour proprement
    });
};
</script>

<template>
    <Popover class="relative">
        <PopoverButton class="relative p-2 text-slate-500 hover:text-slate-700 dark:text-slate-300 dark:hover:text-white focus:outline-none transition-colors">
            <BellIcon class="w-6 h-6" />
            <span v-if="hasUnread" class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
            </span>
        </PopoverButton>

        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-1 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-1 opacity-0"
        >
            <PopoverPanel class="absolute right-0 z-50 mt-2 w-80 transform px-4 sm:px-0 lg:max-w-3xl">
                <div class="overflow-hidden rounded-lg shadow-lg ring-1 ring-black ring-opacity-5">
                    <div class="bg-white dark:bg-slate-800 p-4">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium text-slate-900 dark:text-white">Notifications</span>
                            <button v-if="hasUnread" @click="markAsRead" class="text-xs text-sky-600 hover:text-sky-500 dark:text-sky-400">
                                Tout marquer comme lu
                            </button>
                        </div>

                        <div v-if="!hasUnread" class="text-center py-6 text-slate-500 dark:text-slate-400 text-sm">
                            <CheckCircleIcon class="w-8 h-8 mx-auto mb-2 text-slate-300" />
                            Rien à signaler !
                        </div>

                        <div v-else class="space-y-3 max-h-64 overflow-y-auto">
                            <div v-for="notif in notifications" :key="notif.id" class="relative flex gap-3 p-2 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                <div class="flex-shrink-0 mt-1">
                                    <span class="h-2 w-2 rounded-full bg-sky-500 block"></span>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-700 dark:text-slate-200">
                                        {{ notif.data.message || 'Nouvelle notification' }}
                                    </p>
                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ new Date(notif.created_at).toLocaleDateString() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </PopoverPanel>
        </transition>
    </Popover>
</template>