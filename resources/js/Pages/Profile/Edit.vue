<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Pengaturan Profil" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">
                        Profil
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

        <div class="py-8 bg-slate-50/50 dark:bg-slate-950 min-h-[calc(100vh-65px)] transition-colors duration-200">
            <div class="max-w-4xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
                
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden transition-colors duration-200">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/40">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Informasi Pengguna</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Perbarui nama pengguna dan alamat email akun Anda.</p>
                    </div>
                    <div class="p-6">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl"
                        />
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-xl shadow-sm overflow-hidden transition-colors duration-200">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/40">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Pembaruan Kata Sandi</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pastikan akun Anda menggunakan kata sandi yang kuat untuk menjaga keamanan.</p>
                    </div>
                    <div class="p-6">
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 border border-red-100 dark:border-red-900/40 rounded-xl shadow-sm overflow-hidden transition-colors duration-200">
                    <div class="p-6 border-b border-red-50 dark:border-red-900/30 bg-red-50/20 dark:bg-red-950/20">
                        <h3 class="text-base font-semibold text-red-600 dark:text-red-400">Hapus Akun</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Setelah akun Anda dihapus, semua sumber daya dan data terkait akan dihapus secara permanen.</p>
                    </div>
                    <div class="p-6">
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>