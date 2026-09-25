<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { encryptPrivateKeyWithPassword, generateKeyPair } from '@/Utils/CryptoHelper';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    public_key: '',
    encrypted_private_key: '',
    salt: '',
    iv: '',
});

const submit = async () => {
    try {
        const { publicKeyJwk, privateKeyJwk } = await generateKeyPair();

        const encryptedPayload = await encryptPrivateKeyWithPassword(privateKeyJwk, form.password);

        form.public_key = JSON.stringify(publicKeyJwk);
        form.encrypted_private_key = encryptedPayload.encrypted_private_key;
        form.salt = encryptedPayload.salt;
        form.iv = encryptedPayload.iv;

        localStorage.setItem('my_private_key', JSON.stringify(privateKeyJwk));

        form.post(route('register'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    } catch (error) {
        console.error('Gagal memproses kunci kriptografi saat registrasi:', error);
    }
};
</script>

<template>
    <GuestLayout>
        <Head title="Daftar Akun" />

        <!-- Header Branding / Salam -->
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-100 tracking-tight">
                Buat Akun Baru
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Bergabunglah dengan AlwaysChat untuk menikmati perpesanan yang aman & terenkripsi.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Nama Lengkap Input -->
            <div>
                <InputLabel for="name" value="Nama Lengkap" class="text-slate-700 dark:text-slate-300 font-medium text-xs" />

                <div class="relative mt-1">
                    <TextInput
                        id="name"
                        type="text"
                        class="w-full px-3.5 py-2 text-sm border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Nama Lengkap Anda"
                    />
                </div>

                <InputError class="mt-1.5 text-xs" :message="form.errors.name" />
            </div>

            <!-- Email Input -->
            <div>
                <InputLabel for="email" value="Alamat Email" class="text-slate-700 dark:text-slate-300 font-medium text-xs" />

                <div class="relative mt-1">
                    <TextInput
                        id="email"
                        type="email"
                        class="w-full px-3.5 py-2 text-sm border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        placeholder="nama@email.com"
                    />
                </div>

                <InputError class="mt-1.5 text-xs" :message="form.errors.email" />
            </div>

            <!-- Password Input -->
            <div>
                <InputLabel for="password" value="Kata Sandi" class="text-slate-700 dark:text-slate-300 font-medium text-xs" />

                <div class="relative mt-1">
                    <TextInput
                        id="password"
                        type="password"
                        class="w-full px-3.5 py-2 text-sm border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="Minimal 8 karakter"
                    />
                </div>

                <InputError class="mt-1.5 text-xs" :message="form.errors.password" />
            </div>

            <!-- Password Confirmation Input -->
            <div>
                <InputLabel for="password_confirmation" value="Konfirmasi Kata Sandi" class="text-slate-700 dark:text-slate-300 font-medium text-xs" />

                <div class="relative mt-1">
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="w-full px-3.5 py-2 text-sm border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Ulangi kata sandi"
                    />
                </div>

                <InputError class="mt-1.5 text-xs" :message="form.errors.password_confirmation" />
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <PrimaryButton
                    class="w-full py-2.5 flex justify-center items-center font-semibold text-xs tracking-wide bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 active:bg-indigo-800 text-white rounded-lg shadow-sm transition-all duration-150"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Membuat Akun & Kunci Kriptografi...</span>
                    <span v-else>Daftar Akun</span>
                </PrimaryButton>
            </div>
        </form>

        <!-- Footer Link ke Halaman Login -->
        <div class="mt-6 text-center border-t border-slate-100 dark:border-slate-800 pt-4">
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Sudah memiliki akun?
                <Link :href="route('login')" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors ml-0.5">
                    Masuk di sini
                </Link>
            </p>
        </div>
    </GuestLayout>
</template>