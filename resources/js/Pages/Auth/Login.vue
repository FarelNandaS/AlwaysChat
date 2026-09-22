<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { decryptPrivateKeyWithPassword } from '@/Utils/CryptoHelper';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onSuccess: async (page) => {
            const user = page.props.auth.user;

            if (user && user.encrypted_private_key) {
                try {
                    const privateKeyJwk = await decryptPrivateKeyWithPassword(
                        { 
                            encrypted_private_key: user.encrypted_private_key, 
                            salt: user.salt, 
                            iv: user.iv 
                        }, 
                        form.password
                    );

                    localStorage.setItem('my_private_key', JSON.stringify(privateKeyJwk));
                    console.log('Private key berhasil dipulihkan di device ini!');
                } catch (error) {
                    console.error('Gagal memulihkan Private Key:', error);
                }
            }
        },
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Masuk" />

        <!-- Header Branding / Salam -->
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
                Selamat Datang Kembali!
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Masuk ke akun AlwaysChat Anda untuk melanjutkan percakapan.
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

            <!-- Password Input -->
            <div>
                <div class="flex items-center justify-between">
                    <InputLabel for="password" value="Kata Sandi" class="text-slate-700 font-medium text-xs" />
                    
                    <Link 
                        v-if="canResetPassword" 
                        :href="route('password.request')"
                        class="text-xs font-medium text-indigo-600 hover:text-indigo-500 transition-colors"
                    >
                        Lupa kata sandi?
                    </Link>
                </div>

                <div class="relative mt-1">
                    <TextInput 
                        id="password" 
                        type="password" 
                        class="w-full px-3.5 py-2 text-sm border-slate-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                        v-model="form.password" 
                        required
                        autocomplete="current-password" 
                        placeholder="••••••••"
                    />
                </div>

                <InputError class="mt-1.5 text-xs" :message="form.errors.password" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center cursor-pointer">
                    <Checkbox name="remember" v-model:checked="form.remember" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                    <span class="ms-2 text-xs text-slate-600 font-medium select-none">Ingat saya di perangkat ini</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <PrimaryButton 
                    class="w-full py-2.5 flex justify-center items-center font-semibold text-xs tracking-wide bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg shadow-sm transition-all duration-150" 
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }" 
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Memproses...</span>
                    <span v-else>Masuk ke Akun</span>
                </PrimaryButton>
            </div>
        </form>

        <!-- Footer Link ke Halaman Register -->
        <div class="mt-6 text-center border-t border-slate-100 pt-4">
            <p class="text-xs text-slate-500">
                Belum memiliki akun?
                <Link :href="route('register')" class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors ml-0.5">
                    Daftar sekarang
                </Link>
            </p>
        </div>
    </GuestLayout>
</template>