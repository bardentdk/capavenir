<script setup>
import { useForm, Head } from '@inertiajs/vue3'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <Head title="Connexion" />

  <div class="min-h-screen flex items-center justify-center bg-slate-100 p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">

      <div class="bg-slate-900 p-8 text-center">
        <h1 class="text-2xl font-bold text-white tracking-wide">SocialCare</h1>
        <p class="text-slate-400 text-sm mt-2">Gestion des interventions éducatives</p>
      </div>

      <div class="p-8">
        <form @submit.prevent="submit" class="space-y-6">

          <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Adresse e-mail</label>
            <input v-model="form.email" type="email" id="email" required autofocus
              class="mt-1 block w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm shadow-sm placeholder-slate-400
              focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" />
            <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-slate-700">Mot de passe</label>
            <input v-model="form.password" type="password" id="password" required
              class="mt-1 block w-full px-3 py-2 bg-white border border-slate-300 rounded-md text-sm shadow-sm placeholder-slate-400
              focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500" />
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center">
              <input v-model="form.remember" type="checkbox" class="h-4 w-4 text-sky-600 focus:ring-sky-500 border-gray-300 rounded" />
              <span class="ml-2 text-sm text-slate-600">Se souvenir de moi</span>
            </label>
            <a href="#" class="text-sm text-sky-600 hover:text-sky-500">Oublié ?</a>
          </div>

          <div>
            <button type="submit" :disabled="form.processing"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors disabled:opacity-50">
              <span v-if="form.processing">Connexion...</span>
              <span v-else>Se connecter</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>