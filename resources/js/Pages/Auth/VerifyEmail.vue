<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Verifikasi Email" />

        <!-- Header Branding / Salam -->
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
                Verifikasi Email Anda
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Terima kasih telah mendaftar di AlwaysChat!
            </p>
        </div>

        <div class="mb-5 text-xs text-slate-600 leading-relaxed text-center bg-slate-50 p-3.5 rounded-lg border border-slate-100">
            Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan ke email Anda. Jika Anda tidak menerima email tersebut, kami dengan senang hati akan mengirimkannya kembali.
        </div>

        <!-- Notification Status -->
        <div
            class="mb-5 p-3 rounded-lg bg-green-50 border border-green-200 text-xs font-medium text-green-700 text-center"
            v-if="verificationLinkSent"
        >
            Tautan verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div class="flex flex-col gap-3">
                <PrimaryButton
                    class="w-full py-2.5 flex justify-center items-center font-semibold text-xs tracking-wide bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg shadow-sm transition-all duration-150"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Mengirim Ulang Email...</span>
                    <span v-else>Kirim Ulang Email Verifikasi</span>
                </PrimaryButton>

                <div class="text-center pt-2 border-t border-slate-100">
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors"
                    >
                        Keluar / Log Out
                    </Link>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>