<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
  HomeIcon,
  UsersIcon,
  ClipboardDocumentCheckIcon,
  BanknotesIcon,
  Bars3Icon,
  XMarkIcon,
  CalendarDaysIcon,
  UserGroupIcon,
  ChartPieIcon,
  ChevronDoubleRightIcon,
  ArrowRightOnRectangleIcon,
  BellIcon
} from '@heroicons/vue/24/outline';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import CommandPalette from '@/Components/CommandPalette.vue';
import NotificationMenu from '@/Components/NotificationMenu.vue';

const showingNavigationDropdown = ref(false)
const page = usePage()
const user = computed(() => page.props.auth?.user)

const navigation = [
  { name: 'Tableau de bord', href: '/dashboard', icon: HomeIcon },
  { name: 'Mes Interventions', href: '/interventions', icon: ClipboardDocumentCheckIcon },
  { name: 'Mes Frais', href: '/expenses', icon: BanknotesIcon },
  { name: 'Bénéficiaires', href: '/clients', icon: UsersIcon },
  { name: 'Comptabilité', href: '/accounting', icon: ChartPieIcon },
  { name: 'Agenda', href: '/planning', icon: CalendarDaysIcon },
  { name: 'Équipe', href: '/users', icon: UserGroupIcon },
]

const isUrl = (...urls) => {
  let currentUrl = page.url.substr(1)
  if (urls[0] === '') return currentUrl === ''
  return urls.filter((url) => currentUrl.startsWith(url)).length
}
</script>

<template>
  <div class="h-screen bg-(--color-capavenir)/10 flex overflow-hidden font-serif">
    <div class="text-(--color-capavenir-dark) absolute top-0 right-5 flex items-center">
      <div class="flex items-center">
        <span class="font-itc text-xl mr-1 ">C</span>
        <span class="font-serif italic text-xs">onsulter mes notifications </span>
        <ChevronDoubleRightIcon class="w-5" />
      </div>
      <NotificationMenu />
    </div>
    <CommandPalette />
    <div v-if="showingNavigationDropdown" class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden backdrop-blur-sm" @click="showingNavigationDropdown = false"></div>

    <div :class="[
        showingNavigationDropdown ? 'translate-x-0' : '-translate-x-full',
        'fixed inset-y-0 left-0 z-50 w-76 bg-white text-(--color-capavenir) transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shadow-xl lg:shadow-none flex flex-col h-full'
      ]">

      <div class="flex items-center justify-between h-16 bg-(--color-capavenir-dark) border-b border-slate-800 shrink-0 px-4">
        <div class="flex justify-center items-center align-middle gap-2">
            <img
              src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=768,fit=crop,q=95/AwvDBJ4r6bcKnbVp/logo-cap-avenir-fini-mePb61Q7O5TpkZQ3.png"
              alt="Logo Cap Avenir"
              class="h-12 w-auto object-contain"
            />
            <div class="flex items-baseline">
                <span class="font-itc text-3xl text-white">C</span>
                <span class="text-xl font-bold tracking-wide text-white font-serif">ap Avenir</span>
            </div>
        </div>
        <button @click="showingNavigationDropdown = false" class="lg:hidden text-slate-400 hover:text-white">
          <XMarkIcon class="w-6 h-6" />
        </button>
      </div>

      <nav class="mt-6 px-3 space-y-1 flex-1 overflow-y-auto">
        <Link v-for="item in navigation" :key="item.name" :href="item.href"
          :class="[
            isUrl(item.href.substring(1))
              ? 'bg-(--color-capavenir-dark) text-white shadow-md'
              : 'text-(--color-capavenir) hover:bg-(--color-capavenir) hover:text-white',
            'group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200'
          ]">
          <component :is="item.icon"
            :class="[
              isUrl(item.href.substring(1)) ? 'text-white' : 'text-(--color-capavenir) group-hover:text-white',
              'mr-3 flex-shrink-0 h-6 w-6'
            ]" aria-hidden="true" />
          {{ item.name }}
        </Link>
      </nav>

      <div class="border-t border-slate-800 p-4 bg-(--color-capavenir-dark) shrink-0">
        <div class="flex items-center text-(--color-capavenir-dark)">
            <Link href="/profile" class="flex items-center flex-1 min-w-0 hover:opacity-80 transition-opacity">
                <div v-if="user?.avatar" class="w-8 h-8 rounded-full bg-cover bg-center border border-slate-600"
                     :style="'background-image: url(\'' + user.avatar + '\');'">
                </div>
                <div v-else class="w-8 h-8 rounded-full bg-(--color-capavenir) flex items-center justify-center text-xs font-bold text-white border border-(--color-capavenir-dark)">
                    {{ user?.name?.charAt(0) || 'U' }}
                </div>

                <div class="ml-3 overflow-hidden">
                    <p class="text-sm font-medium text-white truncate">{{ user?.name || 'Utilisateur' }}</p>
                    <p class="text-xs text-pink-300 truncate">Mon compte</p>
                </div>
            </Link>

            <ThemeToggle />
            <Link href="/logout" method="post" as="button" class="ml-2 text-slate-400 hover:text-white" title="Déconnexion">
              <ArrowRightOnRectangleIcon class="w-5 h-5" />
            </Link>
        </div>
      </div>
    </div>

    <div class="flex-1 flex flex-col h-full overflow-hidden">
      <header class="flex-shrink-0 sticky top-0 z-10 flex items-center justify-between h-16 px-4 bg-white dark:bg-slate-900 shadow-sm lg:hidden border-b border-slate-200 dark:border-slate-700">
          <button @click="showingNavigationDropdown = true" class="text-slate-500 dark:text-slate-200">
              <Bars3Icon class="w-6 h-6" />
          </button>

          <div class="flex items-center gap-3">
              <!-- <ThemeToggle class="text-(--color-capavenir-dark)" /> -->
              <NotificationMenu />
          </div>
      </header>

      <!-- <header class="flex-shrink-0 sticky top-0 z-10 flex items-center justify-between h-16 px-4 bg-white shadow-sm lg:hidden border-b border-slate-200">
        <button @click="showingNavigationDropdown = true" class="text-slate-500 hover:text-slate-700">
          <Bars3Icon class="w-6 h-6" />
        </button>
        <span class="font-bold text-slate-700 flex items-center justify-center">
          <img src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=768,fit=crop,q=95/AwvDBJ4r6bcKnbVp/logo-cap-avenir-fini-mePb61Q7O5TpkZQ3.png" alt="" class="h-12 w-auto"/>
        </span>
        <div class="w-6"></div>
      </header> -->

      <main class="flex-1 py-8 px-4 sm:px-8 bg-pink-100 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>