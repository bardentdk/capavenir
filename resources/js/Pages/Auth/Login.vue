<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { EnvelopeIcon, LockClosedIcon, EyeIcon, EyeSlashIcon, ArrowRightIcon } from '@heroicons/vue/24/outline';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Connexion à l'espace sécurisé" />

    <div class="min-h-screen w-full flex bg-white overflow-hidden">

        <div class="hidden lg:flex w-1/2 relative overflow-hidden bg-slate-900">
            <img
                src="https://www.autismeinfoservice.fr/sites/default/files/2019-03/scolarite_et_soins_autisme_0.jpg"
                class="absolute inset-0 w-full h-full object-cover opacity-60"
                alt="Background"
            />
            <!-- <img
                src="https://images.unsplash.com/photo-1542038784456-1ea8e935640e?q=80&w=2070&auto=format&fit=crop"
                class="absolute inset-0 w-full h-full object-cover opacity-60"
                alt="Background"
            /> -->

            <div class="absolute inset-0 bg-gradient-to-br from-[#881337]/80 to-[#4c0519]/90 mix-blend-multiply"></div>

            <div class="relative z-10 flex flex-col justify-between p-16 w-full text-white">
                <div class="flex items-center gap-3">
                    <!-- <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-lg flex items-center justify-center border border-white/30">
                        <span class="font-serif font-bold text-xl">C</span>
                    </div>
                    <span class="text-lg font-medium tracking-wider uppercase opacity-90">Cap Avenir</span> -->
                    <img
                        src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=768,fit=crop,q=95/AwvDBJ4r6bcKnbVp/logo-cap-avenir-fini-mePb61Q7O5TpkZQ3.png"
                        alt="Logo Cap Avenir"
                        class="h-32 grayscale-200 mb-6 object-contain drop-shadow-sm hover:scale-105 transition-transform duration-500"
                    />
                </div>

                <div class="space-y-6">
                    <h2 class="z-[999] text-4xl md:text-6xl font-serif font-semibold leading-tight">
                      <span class="">
                        Accompagner,<br>
                        Soutenir,<br>
                      </span>
                    </h2>
                    <h2 class="text-4xl md:text-6xl font-serif font-semibold leading-tight">
                        <span class="z-[0] bg-linear-to-r from-(--color-capavenir) to-[#881337] p-2  text-shadow text-shadow-white font-itc bold text-8xl">Construire demain.</span>
                    </h2>
                    <p class="text-rose-100/80 text-lg max-w-md font-light leading-relaxed">
                        Bienvenue sur votre outil de gestion dédié. Simplifiez vos démarches administratives pour vous concentrer sur l'essentiel : l'humain.
                    </p>
                </div>

                <div class="text-sm text-rose-200/60 font-light">
                    © 2025 Cap Avenir - Tous droits réservés.
                </div>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-8 sm:p-12 lg:p-24 bg-white relative">

            <div class="absolute top-0 right-0 w-64 h-64 bg-rose-50 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 opacity-50 pointer-events-none"></div>

            <div class="w-full max-w-md space-y-8 z-10">

                <div class="text-center">
                    <img
                        src="https://assets.zyrosite.com/cdn-cgi/image/format=auto,w=768,fit=crop,q=95/AwvDBJ4r6bcKnbVp/logo-cap-avenir-fini-mePb61Q7O5TpkZQ3.png"
                        alt="Logo Cap Avenir"
                        class="h-24 mx-auto mb-6 object-contain drop-shadow-sm hover:scale-105 transition-transform duration-500"
                    />
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900">
                        Connexion à votre espace
                    </h2>
                    <p class="mt-2 text-sm text-slate-500">
                        Saisissez vos identifiants pour accéder au portail.
                    </p>
                </div>

                <form @submit.prevent="submit" class="mt-8 space-y-6">

                    <div class="space-y-5">
                        <div class="group">
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Adresse E-mail</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <EnvelopeIcon class="h-5 w-5 text-slate-400 group-focus-within:text-[#881337] transition-colors" />
                                </div>
                                <input
                                    v-model="form.email"
                                    id="email"
                                    name="email"
                                    type="email"
                                    autocomplete="email"
                                    required
                                    class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#881337]/20 focus:border-[#881337] transition-all shadow-sm bg-slate-50 focus:bg-white"
                                    placeholder="exemple@cap-avenir.com"
                                />
                            </div>
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                        </div>

                        <div class="group">
                            <div class="flex items-center justify-between mb-1">
                                <label for="password" class="block text-sm font-medium text-slate-700">Mot de passe</label>
                                <div class="text-sm">
                                    <a href="#" class="font-medium text-[#881337] hover:text-[#be185d] transition-colors">
                                        Oublié ?
                                    </a>
                                </div>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <LockClosedIcon class="h-5 w-5 text-slate-400 group-focus-within:text-[#881337] transition-colors" />
                                </div>
                                <input
                                    v-model="form.password"
                                    id="password"
                                    name="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    autocomplete="current-password"
                                    required
                                    class="block w-full pl-10 pr-10 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#881337]/20 focus:border-[#881337] transition-all shadow-sm bg-slate-50 focus:bg-white"
                                    placeholder="••••••••"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                                >
                                    <EyeIcon v-if="!showPassword" class="h-5 w-5" />
                                    <EyeSlashIcon v-else class="h-5 w-5" />
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input
                            v-model="form.remember"
                            id="remember-me"
                            name="remember-me"
                            type="checkbox"
                            class="h-4 w-4 text-[#881337] focus:ring-[#881337] border-slate-300 rounded cursor-pointer"
                        />
                        <label for="remember-me" class="ml-2 block text-sm text-slate-600 cursor-pointer select-none">
                            Se souvenir de moi
                        </label>
                    </div>

                    <div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="hover:cursor-pointer group relative w-full flex justify-center py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-[#881337] hover:bg-[#be185d] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#881337] transition-all duration-200 shadow-lg shadow-[#881337]/30 hover:shadow-[#881337]/50 disabled:opacity-70 disabled:cursor-not-allowed overflow-hidden"
                        >
                            <span class="absolute right-4 flex items-center opacity-0 group-hover:opacity-100 group-hover:translate-x-0 -translate-x-2 transition-all duration-200">
                                <ArrowRightIcon class="w-5 h-5" />
                            </span>

                            <span class="group-hover:-translate-x-2 transition-transform duration-200">
                                {{ form.processing ? 'Connexion en cours...' : 'Se connecter' }}
                            </span>
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center text-xs text-slate-400">
                    Problème d'accès ? Contactez le <a href="mailto:admin@cap-avenir.re" class="text-slate-600 underline hover:text-[#881337]">support technique</a>.
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Petit ajustement pour la police Serif si elle n'est pas chargée globalement */
.font-serif {
    font-family: 'Playfair Display', ui-serif, Georgia, Cambria, "Times New Roman", Times, serif;
}
</style>