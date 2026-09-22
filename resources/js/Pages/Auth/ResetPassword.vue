<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Atur Ulang Kata Sandi" />

        <!-- Header Branding / Salam -->
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
                Atur Ulang Kata Sandi
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Masukkan kata sandi baru Anda untuk memulihkan akses ke akun AlwaysChat.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Email Input -->
            <div>
                <InputLabel for="email" value="Alamat Email" class="text-slate-700 font-medium text-xs" />

                <div class="relative mt-1">
                    <TextInput
                        id="email"
                        type="email"
                        class="w-full px-3.5 py-2 text-sm border-slate-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50/50"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="nama@email.com"
                    />
                </div>

                <InputError class="mt-1.5 text-xs" :message="form.errors.email" />
            </div>

            <!-- Password Baru Input -->
            <div>
                <InputLabel for="password" value="Kata Sandi Baru" class="text-slate-700 font-medium text-xs" />

                <div class="relative mt-1">
                    <TextInput
                        id="password"
                        type="password"
                        class="w-full px-3.5 py-2 text-sm border-slate-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="Minimal 8 karakter"
                    />
                </div>

                <InputError class="mt-1.5 text-xs" :message="form.errors.password" />
            </div>

            <!-- Konfirmasi Password Baru Input -->
            <div>
                <InputLabel for="password_confirmation" value="Konfirmasi Kata Sandi Baru" class="text-slate-700 font-medium text-xs" />

                <div class="relative mt-1">
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="w-full px-3.5 py-2 text-sm border-slate-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Ulangi kata sandi baru"
                    />
                </div>

                <InputError class="mt-1.5 text-xs" :message="form.errors.password_confirmation" />
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <PrimaryButton
                    class="w-full py-2.5 flex justify-center items-center font-semibold text-xs tracking-wide bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg shadow-sm transition-all duration-150"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Menyimpan Kata Sandi...</span>
                    <span v-else>Atur Ulang Kata Sandi</span>
                </PrimaryButton>
            </div>
        </form>

        <!-- Footer Link Kembali ke Login -->
        <div class="mt-6 text-center border-t border-slate-100 pt-4">
            <p class="text-xs text-slate-500">
                Batal me-reset?
                <Link :href="route('login')" class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors ml-0.5">
                    Kembali ke halaman masuk
                </Link>
            </p>
        </div>
    </GuestLayout>
</template>