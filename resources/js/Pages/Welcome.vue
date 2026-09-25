<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const isDark = ref(false);

onMounted(() => {
    isDark.value = document.documentElement.classList.contains('dark');
});

const toggleTheme = () => {
    isDark.value = !isDark.value;
    const newTheme = isDark.value ? 'dark' : 'light';
    
    localStorage.setItem('theme', newTheme);
    
    if (isDark.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};
</script>

<template>
    <Head title="AlwaysChat - Realtime Messaging" />

    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 selection:bg-indigo-500 selection:text-white transition-colors duration-200">

        <div class="fixed top-0 right-0 p-6 flex items-center gap-4 z-10">
            <button
                @click="toggleTheme"
                type="button"
                class="p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 shadow-sm transition-all duration-150"
                title="Beralih Tema"
            >
                <svg v-if="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>

            <div v-if="canLogin" class="flex items-center gap-4">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="font-semibold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 focus:outline-none">
                    Dashboard
                </Link>

                <template v-else>
                    <Link :href="route('login')" class="font-semibold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 focus:outline-none">
                        Log in
                    </Link>

                    <Link v-if="canRegister" :href="route('register')" class="font-semibold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 focus:outline-none">
                        Register
                    </Link>
                </template>
            </div>
        </div>

        <div class="max-w-7xl mx-auto mt-16 sm:mt-10 p-6 lg:p-8">
            <div class="flex flex-col items-center justify-center text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-indigo-600 dark:bg-indigo-500 shadow-xl shadow-indigo-200 dark:shadow-none mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>

                <h1 class="text-5xl font-extrabold text-slate-900 dark:text-slate-50 tracking-tight sm:text-6xl">
                    Koneksi Tanpa Batas dengan <span class="text-indigo-600 dark:text-indigo-400">AlwaysChat.</span>
                </h1>

                <p class="mt-6 text-lg leading-8 text-slate-600 dark:text-slate-400 max-w-2xl">
                    Aplikasi pesan instan berbasis web dengan teknologi WebSocket. Rasakan pengalaman chat yang benar-benar realtime, cepat, dan aman.
                </p>

                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <Link :href="route('register')" class="rounded-full bg-indigo-600 dark:bg-indigo-500 px-8 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-200 dark:shadow-none hover:bg-indigo-500 dark:hover:bg-indigo-600 transition-all active:scale-95">
                        Mulai Chatting Gratis
                    </Link>
                    <a href="#features" class="text-sm font-semibold leading-6 text-slate-900 dark:text-slate-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        Pelajari Fitur <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>

            <div class="mt-16 flow-root sm:mt-24">
                <div class="-m-2 rounded-xl bg-slate-900/5 dark:bg-slate-100/5 p-2 ring-1 ring-inset ring-slate-900/10 dark:ring-slate-100/10 lg:-m-4 lg:rounded-2xl lg:p-4">
                    <div class="bg-white dark:bg-slate-900 rounded-lg shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-800 aspect-[16/9] flex items-center justify-center text-slate-400 dark:text-slate-500 italic transition-colors">
                        [ Screenshot / Preview Interface AlwaysChat ]
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>