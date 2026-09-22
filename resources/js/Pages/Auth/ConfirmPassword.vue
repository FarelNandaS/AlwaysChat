<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Konfirmasi Kata Sandi" />

        <!-- Header Branding / Salam -->
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
                Konfirmasi Keamanan
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Ini adalah area sensitif aplikasi AlwaysChat. Silakan konfirmasi kata sandi Anda sebelum melanjutkan.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Input Password -->
            <div>
                <InputLabel for="password" value="Kata Sandi" class="text-slate-700 font-medium text-xs" />

                <div class="relative mt-1">
                    <TextInput
                        id="password"
                        type="password"
                        class="w-full px-3.5 py-2 text-sm border-slate-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        autofocus
                        placeholder="••••••••"
                    />
                </div>

                <InputError class="mt-1.5 text-xs" :message="form.errors.password" />
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <PrimaryButton
                    class="w-full py-2.5 flex justify-center items-center font-semibold text-xs tracking-wide bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg shadow-sm transition-all duration-150"
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Memverifikasi...</span>
                    <span v-else>Konfirmasi Kata Sandi</span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>