<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { alert } from '@/Components/Alert.vue';

const selectedTheme = ref('light');

const themes = [
    {
        id: 'light',
        name: 'Terang (Light)',
        description: 'Tampilan bersih dengan latar belakang cerah yang nyaman di tempat terang.',
        icon: 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z'
    },
    {
        id: 'dark',
        name: 'Gelap (Dark)',
        description: 'Tampilan gelap yang mengurangi ketegangan mata saat digunakan di malam hari.',
        icon: 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z'
    },
    {
        id: 'system',
        name: 'Sesuai Sistem',
        description: 'Mengikuti pengaturan mode gelap/terang perangkat Anda secara otomatis.',
        icon: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'
    }
];

onMounted(() => {
    const savedTheme = localStorage.getItem('theme') || 'light';
    selectedTheme.value = savedTheme;
});

const saveSettings = () => {
    localStorage.setItem('theme', selectedTheme.value);

    if (selectedTheme.value === 'dark' || (selectedTheme.value === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    alert.success('Berhasil!', 'Pengaturan tema telah diperbarui.');
};
</script>

<template>
    <Head title="Pengaturan Tema" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">
                        Pengaturan
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Kelola informasi akun, kata sandi, dan keamanan aplikasi Anda
                    </p>
                </div>
                <Link
                    :href="route('dashboard')"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/60 transition-colors shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Dashboard
                </Link>
            </div>
        </template>

        <div class="py-8 bg-slate-100/50 dark:bg-slate-950 transition-colors duration-200 min-h-[calc(100vh-4rem)]">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm transition-colors duration-200">
                    <section class="max-w-xl">
                        <header>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">
                                Tema Tampilan
                            </h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                Pilih gaya visual antarmuka aplikasi AlwaysChat sesuai dengan kenyamanan mata Anda.
                            </p>
                        </header>

                        <div class="mt-6 space-y-3">
                            <div
                                v-for="theme in themes"
                                :key="theme.id"
                                @click="selectedTheme = theme.id"
                                class="relative flex items-center p-4 border rounded-xl cursor-pointer transition-all duration-150"
                                :class="selectedTheme === theme.id 
                                    ? 'border-indigo-600 bg-indigo-50/40 dark:bg-indigo-950/30 ring-1 ring-indigo-600 dark:ring-indigo-500' 
                                    : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 hover:bg-slate-50/50 dark:hover:bg-slate-800/40'"
                            >
                                <div 
                                    class="p-2.5 rounded-lg mr-4 transition-colors"
                                    :class="selectedTheme === theme.id 
                                        ? 'bg-indigo-600 text-white' 
                                        : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" :d="theme.icon" />
                                    </svg>
                                </div>

                                <div class="flex-1 pr-4">
                                    <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                        {{ theme.name }}
                                    </h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ theme.description }}
                                    </p>
                                </div>

                                <div class="flex items-center">
                                    <div 
                                        class="w-4 h-4 rounded-full border flex items-center justify-center transition-colors"
                                        :class="selectedTheme === theme.id 
                                            ? 'border-indigo-600 bg-indigo-600 dark:border-indigo-500 dark:bg-indigo-500' 
                                            : 'border-slate-300 dark:border-slate-600'"
                                    >
                                        <div v-if="selectedTheme === theme.id" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center gap-4">
                            <PrimaryButton 
                                @click="saveSettings"
                                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 active:bg-indigo-800 text-xs font-semibold rounded-lg shadow-sm transition-colors"
                            >
                                Simpan Perubahan
                            </PrimaryButton>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>