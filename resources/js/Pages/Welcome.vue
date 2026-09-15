<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="AlwaysChat - Realtime Messaging" />

    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-slate-50 selection:bg-indigo-500 selection:text-white">
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
            <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="font-semibold text-slate-600 hover:text-indigo-600 focus:outline focus:outline-2 focus:rounded-sm focus:outline-indigo-500">
                Dashboard
            </Link>

            <template v-else>
                <Link :href="route('login')" class="font-semibold text-slate-600 hover:text-indigo-600 focus:outline focus:outline-2 focus:rounded-sm focus:outline-indigo-500">
                    Log in
                </Link>

                <Link v-if="canRegister" :href="route('register')" class="ml-4 font-semibold text-slate-600 hover:text-indigo-600 focus:outline focus:outline-2 focus:rounded-sm focus:outline-indigo-500">
                    Register
                </Link>
            </template>
        </div>

        <div class="max-w-7xl mx-auto mt-10 p-6 lg:p-8">
            <div class="flex flex-col items-center justify-center text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-indigo-600 shadow-xl shadow-indigo-200 mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>

                <h1 class="text-5xl font-extrabold text-slate-900 tracking-tight sm:text-6xl">
                    Koneksi Tanpa Batas dengan <span class="text-indigo-600">AlwaysChat.</span>
                </h1>
                
                <p class="mt-6 text-lg leading-8 text-slate-600 max-w-2xl">
                    Aplikasi pesan instan berbasis web dengan teknologi WebSocket. Rasakan pengalaman chat yang benar-benar realtime, cepat, dan aman.
                </p>

                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <Link :href="route('register')" class="rounded-full bg-indigo-600 px-8 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all active:scale-95">
                        Mulai Chatting Gratis
                    </Link>
                    <a href="#features" class="text-sm font-semibold leading-6 text-slate-900 hover:text-indigo-600 transition-colors">
                        Pelajari Fitur <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>

            <div class="mt-16 flow-root sm:mt-24">
                <div class="-m-2 rounded-xl bg-slate-900/5 p-2 ring-1 ring-inset ring-slate-900/10 lg:-m-4 lg:rounded-2xl lg:p-4">
                    <div class="bg-white rounded-lg shadow-2xl overflow-hidden border border-slate-200 aspect-[16/9] flex items-center justify-center text-slate-400 italic">
                        [ Screenshot / Preview Interface AlwaysChat ]
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* Kamu bisa menambahkan custom font atau animasi di sini jika perlu */
</style>