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
  ArrowRightOnRectangleIcon,
  BellIcon,
  ChevronRightIcon
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
  <div class="h-screen bg-slate-50 flex overflow-hidden font-serif">

    <CommandPalette />

    <div v-if="showingNavigationDropdown" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden transition-opacity" @click="showingNavigationDropdown = false"></div>

    <div :class="[
        showingNavigationDropdown ? 'translate-x-0' : '-translate-x-full',
        'fixed inset-y-0 left-0 z-50 w-80 bg-white transition-transform duration-300 ease-out lg:static lg:translate-x-0 shadow-2xl lg:shadow-xl lg:shadow-slate-200/50 flex flex-col h-full border-r border-slate-100'
      ]">

      <div class="h-24 flex items-center px-8 shrink-0">
        <div class="flex items-center gap-3">
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-(--color-capavenir) to-(--color-capavenir-dark) rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                <img
                  src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=768,fit=crop,q=95/AwvDBJ4r6bcKnbVp/logo-cap-avenir-fini-mePb61Q7O5TpkZQ3.png"
                  alt="Logo"
                  class="relative h-12 w-auto object-contain transform transition duration-500 hover:scale-110"
                />
            </div>
            <div class="flex flex-col justify-center">
                <span class="text-2xl font-bold tracking-tight text-slate-800 font-serif leading-none">Cap Avenir</span>
                <span class="text-[10px] uppercase tracking-widest text-(--color-capavenir) font-sans font-semibold mt-1">Manager</span>
            </div>
        </div>
        <button @click="showingNavigationDropdown = false" class="lg:hidden ml-auto text-slate-400 hover:text-slate-600">
          <XMarkIcon class="w-6 h-6" />
        </button>
      </div>

      <nav class="flex-1 px-4 mt-4 space-y-2 overflow-y-auto scrollbar-hide">
        <div v-for="item in navigation" :key="item.name">
            <Link :href="item.href"
              :class="[
                isUrl(item.href.substring(1))
                  ? 'bg-gradient-to-r from-(--color-capavenir) to-(--color-capavenir-dark) text-white shadow-lg shadow-pink-900/20 translate-x-1'
                  : 'text-slate-500 hover:bg-pink-50 hover:text-(--color-capavenir)',
                'group flex items-center justify-between px-4 py-3 text-sm font-medium rounded-2xl transition-all duration-300 ease-in-out'
              ]">

              <div class="flex items-center">
                  <component :is="item.icon"
                    :class="[
                      isUrl(item.href.substring(1)) ? 'text-white' : 'text-slate-400 group-hover:text-(--color-capavenir)',
                      'mr-3 flex-shrink-0 h-5 w-5 transition-colors duration-300'
                    ]" aria-hidden="true" />
                  <span class="tracking-wide">{{ item.name }}</span>
              </div>

              <ChevronRightIcon
                v-if="!isUrl(item.href.substring(1))"
                class="w-4 h-4 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300 text-(--color-capavenir)/50"
              />
            </Link>
        </div>
      </nav>

      <div class="p-4 shrink-0">
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 flex items-center justify-between group hover:border-(--color-capavenir)/30 hover:bg-pink-50/50 transition-all duration-300">
            <Link href="/profile" class="flex items-center gap-3 flex-1 min-w-0">
                <div class="relative">
                    <div v-if="user?.avatar" class="w-10 h-10 rounded-full bg-cover bg-center border-2 border-white shadow-sm"
                         :style="'background-image: url(\'' + user.avatar + '\');'">
                    </div>
                    <div v-else class="w-10 h-10 rounded-full bg-gradient-to-br from-(--color-capavenir) to-slate-400 flex items-center justify-center text-xs font-bold text-white border-2 border-white shadow-sm">
                        {{ user?.name?.charAt(0) || 'U' }}
                    </div>
                    <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                </div>

                <div class="flex flex-col overflow-hidden">
                    <p class="text-sm font-bold text-slate-800 truncate group-hover:text-(--color-capavenir) transition-colors">{{ user?.name || 'Utilisateur' }}</p>
                    <p class="text-[10px] text-slate-500 uppercase tracking-wider font-sans">Mon Compte</p>
                </div>
            </Link>

            <div class="flex items-center border-l border-slate-200 pl-3 ml-2 gap-2">
                <!-- <ThemeToggle class="w-5 h-5 text-slate-400 hover:text-(--color-capavenir)" /> -->

                <Link href="/logout" method="post" as="button" class="text-slate-400 hover:text-red-500 transition-colors" title="Déconnexion">
                  <ArrowRightOnRectangleIcon class="w-5 h-5" />
                </Link>
            </div>
        </div>
      </div>
    </div>

    <div class="flex-1 flex flex-col h-full overflow-hidden relative">

        <header class="flex-shrink-0 z-20 px-4 sm:px-8 pt-4 pb-0 flex justify-between items-center bg-transparent pointer-events-none">

            <button @click="showingNavigationDropdown = true" class="text-slate-500 lg:hidden pointer-events-auto bg-white p-2 rounded-lg shadow-sm border border-slate-100">
                <Bars3Icon class="w-6 h-6" />
            </button>

            <div class="hidden lg:block"></div>

            <div class="flex items-center gap-4 pointer-events-auto">
                <div class="hidden md:flex items-center text-(--color-capavenir-dark) bg-white/80 backdrop-blur-md px-4 py-1.5 rounded-full shadow-sm border border-white/50">
                    <span class="font-itc text-xl mr-1">C</span>
                    <span class="font-serif italic text-sm">onsulter mes notifications</span>
                </div>

                <div class="bg-white rounded-full shadow-lg shadow-slate-200/50 p-1 border border-slate-50">
                    <NotificationMenu />
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto px-4 sm:px-8 py-6 scroll-smooth">
            <div class="mt-2">
                <slot />
            </div>
        </main>
    </div>
  </div>
</template>

<style scoped>
/* Masquer la scrollbar de la nav tout en gardant le scroll */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>