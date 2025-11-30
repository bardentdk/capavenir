<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { Dialog, DialogPanel, Combobox, ComboboxInput, ComboboxOptions, ComboboxOption } from '@headlessui/vue'
import { MagnifyingGlassIcon, UserIcon, CalendarIcon, DocumentPlusIcon } from '@heroicons/vue/24/outline'

const isOpen = ref(false)
const query = ref('')

// Liste des actions (Statique pour l'instant, tu pourras y injecter tes clients via props plus tard)
const actions = [
  { id: 1, name: 'Nouvelle Intervention', url: '/interventions/create', icon: DocumentPlusIcon },
  { id: 2, name: 'Nouveau Frais', url: '/expenses/create', icon: DocumentPlusIcon },
  { id: 3, name: 'Mon Planning', url: '/planning', icon: CalendarIcon },
  { id: 4, name: 'Liste Bénéficiaires', url: '/clients', icon: UserIcon },
  { id: 5, name: 'Comptabilité', url: '/accounting', icon: DocumentPlusIcon },
]

const filteredItems = computed(() =>
  query.value === ''
    ? actions
    : actions.filter((item) => item.name.toLowerCase().includes(query.value.toLowerCase()))
)

const onSelect = (item) => {
    if (item) {
        router.visit(item.url)
        closeModal()
    }
}

const closeModal = () => { isOpen.value = false; query.value = '' }
const openModal = () => { isOpen.value = true }

// Gestion du raccourci clavier Ctrl+K
const onKeydown = (event) => {
    if (event.key === 'k' && (event.metaKey || event.ctrlKey)) {
        event.preventDefault()
        isOpen.value = !isOpen.value
    }
}

onMounted(() => window.addEventListener('keydown', onKeydown))
onUnmounted(() => window.removeEventListener('keydown', onKeydown))
</script>

<template>
  <Dialog :open="isOpen" @close="closeModal" class="relative z-50">
    <div class="fixed inset-0 bg-gray-900/30 backdrop-blur-sm" aria-hidden="true" />

    <div class="fixed inset-0 overflow-y-auto p-4 sm:p-6 md:p-20 mt-10">
      <DialogPanel class="mx-auto max-w-xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white dark:bg-slate-800 shadow-2xl ring-1 ring-black ring-opacity-5 transition-all">
        <Combobox @update:modelValue="onSelect">
          <div class="relative">
            <MagnifyingGlassIcon class="pointer-events-none absolute top-3.5 left-4 h-5 w-5 text-gray-400" aria-hidden="true" />
            <ComboboxInput
                class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 dark:text-white placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                placeholder="Rechercher une action..."
                @change="query = $event.target.value"
                autoFocus
            />
          </div>

          <ComboboxOptions v-if="filteredItems.length > 0" static class="max-h-72 scroll-py-2 overflow-y-auto py-2 text-sm text-gray-800 dark:text-gray-200">
            <ComboboxOption v-for="item in filteredItems" :key="item.id" :value="item" as="template" v-slot="{ active }">
              <li :class="['cursor-default select-none px-4 py-2 flex items-center gap-3', active ? 'bg-sky-600 text-white' : '']">
                <component :is="item.icon" class="h-5 w-5" :class="active ? 'text-white' : 'text-gray-400'" />
                {{ item.name }}
              </li>
            </ComboboxOption>
          </ComboboxOptions>

          <p v-if="query !== '' && filteredItems.length === 0" class="p-4 text-sm text-gray-500">Aucun résultat trouvé.</p>
        </Combobox>
      </DialogPanel>
    </div>
  </Dialog>
</template>