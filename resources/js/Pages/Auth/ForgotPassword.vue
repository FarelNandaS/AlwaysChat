<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Lupa Kata Sandi" />

        <!-- Header Branding / Salam -->
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
                Lupa Kata Sandi?
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
            </p>
        </div>

        <!-- Notification Status -->
        <div v-if="status" class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-xs font-medium text-green-700">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Email Input -->
            <div>
                <InputLabel for="email" value="Alamat Email" class="text-slate-700 font-medium text-xs" />

                <div class="relative mt-1">
                    <TextInput
                        id="email"
                        type="email"
                        class="w-full px-3.5 py-2 text-sm border-slate-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="nama@email.com"
                    />
                </div>

                <InputError class="mt-1.5 text-xs" :message="form.errors.email" />
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <PrimaryButton
                    class="w-full py-2.5 flex justify-center items-center font-semibold text-xs tracking-wide bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg shadow-sm transition-all duration-150"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Mengirim Tautan...</span>
                    <span v-else>Kirim Tautan Reset Password</span>
                </PrimaryButton>
            </div>
        </form>

        <!-- Footer Link Kembali ke Login -->
        <div class="mt-6 text-center border-t border-slate-100 pt-4">
            <p class="text-xs text-slate-500">
                Ingat kata sandi Anda?
                <Link :href="route('login')" class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors ml-0.5">
                    Kembali ke halaman masuk
                </Link>
            </p>
        </div>
    </GuestLayout>
</template>