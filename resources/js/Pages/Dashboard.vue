<script setup>
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// Data dummy untuk testing UI
const chats = ref([
    { id: 1, name: 'Budi Santoso', lastMsg: 'P, besok jadi ketemuan?', time: '14:20', online: true },
    { id: 2, name: 'Siti Aminah', lastMsg: 'Filenya sudah saya kirim ya', time: '12:05', online: false },
    { id: 3, name: 'Grup Mabar', lastMsg: 'Gass login!', time: 'Yesterday', online: true },
]);

const logout = () => {
    localStorage.removeItem('my_private_key');

    router.post(route('logout'));
}
</script>

<template>

    <Head title="AlwaysChat - Dashboard" />

    <AuthenticatedLayout>
        <div class="flex h-[100vh] overflow-hidden bg-slate-100">

            <aside class="w-80 md:w-96 bg-white border-r border-slate-200 flex-col hidden md:flex">
                <div class="p-4 border-b border-slate-100">
                    <div class="p-2 flex items-center justify-between">
                        <h3 class="font-bold text-2xl">Chats</h3>
                        <div class="flex items-center justify-center">
                            <button class="hover:text-indigo-600"><svg width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 12.0002H18.0007M12.0002 6V18.0007" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <Dropdown>
                                <template #trigger>
                                    <button class="hover:text-indigo-600"><svg class="w-5 h-5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                            </path>
                                        </svg></button>
                                </template>
                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">
                                        Profile saya
                                    </DropdownLink>

                                    <div class="border-t border-gray-200" />

                                    <DropdownLink href="#" as="button" @click.prevent="logout">
                                        Logout
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                    <div class="relative">
                        <input type="text" placeholder="Cari pesan atau teman..."
                            class="w-full pl-10 pr-4 py-2 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 transition-all" />
                        <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto">
                    <div v-for="chat in chats" :key="chat.id"
                        class="flex items-center gap-4 p-4 hover:bg-slate-50 cursor-pointer transition-colors border-b border-slate-50">
                        <div class="relative">
                            <div
                                class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center font-bold text-indigo-600">
                                {{ chat.name.charAt(0) }}
                            </div>
                            <span v-if="chat.online"
                                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h3 class="text-sm font-semibold text-slate-900 truncate">{{ chat.name }}</h3>
                                <span class="text-[10px] text-slate-400">{{ chat.time }}</span>
                            </div>
                            <p class="text-xs text-slate-500 truncate">{{ chat.lastMsg }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            <main class="flex-1 flex flex-col bg-white">
                <header
                    class="h-16 px-6 border-b border-slate-200 flex items-center justify-between bg-white/80 backdrop-blur-md z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-slate-200"></div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Budi Santoso</h2>
                            <p class="text-[10px] text-green-500 font-medium uppercase tracking-wider">Online</p>
                        </div>
                    </div>
                    <div class="flex gap-4 text-slate-400">
                        <button class="hover:text-indigo-600"><svg class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg></button>
                        <button class="hover:text-indigo-600"><svg class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                </path>
                            </svg></button>
                    </div>
                </header>

                <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50/50">
                    <div class="flex items-end gap-2">
                        <div class="bg-white border border-slate-200 p-3 rounded-2xl rounded-bl-none shadow-sm">
                            <p class="text-sm text-slate-700">P, besok jadi ketemuan di cafe biasa?</p>
                            <span class="text-[9px] text-slate-400 mt-1 block">14:20</span>
                        </div>
                    </div>

                    <div class="flex items-end justify-end gap-2">
                        <div class="bg-indigo-600 p-3 rounded-2xl rounded-br-none shadow-md shadow-indigo-100">
                            <p class="text-sm text-white">Jadi dong! Jam 10 pagi ya.</p>
                            <span class="text-[9px] text-indigo-200 mt-1 block text-right">14:21</span>
                        </div>
                    </div>
                </div>

                <footer class="p-4 bg-white border-t border-slate-200">
                    <div
                        class="max-w-4xl mx-auto flex items-center gap-3 bg-slate-100 rounded-2xl px-4 py-2 focus-within:ring-2 focus-within:ring-indigo-500 transition-all">
                        <button class="text-slate-400 hover:text-indigo-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </button>
                        <input type="text" placeholder="Tulis pesan..."
                            class="flex-1 bg-transparent border-none focus:ring-0 text-sm text-slate-700" />
                        <button
                            class="bg-indigo-600 text-white p-2 rounded-xl hover:bg-indigo-700 transition-all active:scale-95 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </div>
                </footer>
            </main>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Custom scrollbar agar lebih minimalis */
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #e2e8f0;
    border-radius: 10px;
}
</style>