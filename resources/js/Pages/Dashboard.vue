<script setup>
import { alert } from '@/Components/Alert.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { decryptMessage, encryptMessage } from '@/Utils/CryptoHelper';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    conversations: {
        type: Array,
        default: () => []
    }
});

const isAddModalOpen = ref(false);

const AddForm = useForm({
    email: '',
    chat: '',
    ciphertext: '',
    iv: '',
});

const page = usePage();
const currentUserId = page.props.auth.user.id;

const activeChat = ref(null);
const activeChannel = ref(null);
const messageContainer = ref(null);

const messages = ref([]);
const chats = ref([]);
const onlineUserId = ref([]);

const newMessageText = ref('');
const searchChat = ref('');

const isLoadingMessages = ref(false);
const isFetchingKey = ref(false);
const isSendingMessage = ref(false);
const isLoadingChat = ref(true);

const scrollToBottom = () => {
    nextTick(() => {
        if (messageContainer.value) {
            messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
        }
    })
}

const updateAndReorderSidebar = (conversationId, newPlaintext, timeFormatted) => {
    const chatIndex = chats.value.findIndex(c => c.id === conversationId);
    if (chatIndex !== -1) {
        const targetChat = chats.value[chatIndex];
        targetChat.lastMsg = newPlaintext;
        if (timeFormatted) {
            targetChat.time = timeFormatted;
        }

        chats.value.splice(chatIndex, 1);
        chats.value.unshift(targetChat);
    }
}

const syncOnlineStatus = () => {
    chats.value.forEach(chat => {
        const recipientId = chat.recipient_id;
        chat.online = onlineUserId.value.includes(recipientId);
    })

    if (activeChat.value) {
        const activeRecipientId = activeChat.value.recipient_id;
        activeChat.value.online = onlineUserId.value.includes(activeRecipientId);
    }
}

const decryptConversationsList = async (list) => {
    return Promise.all(list.map(async (chat) => {
        if (chat.lastMsg && chat.iv && chat.public_key && chat.lastMsg !== "Belum ada pesan") {
            try {
                const plaintext = await decryptMessage(chat.public_key, chat.lastMsg, chat.iv);
                return { ...chat, lastMsg: plaintext }
            } catch (error) {
                console.error('Gagal encrypt pesan:', error);
                return { ...chat, lastMsg: '[Pesan Terencrypt]' }
            }
        }
        return chat;
    }))
}

const loadChat = async (conversation) => {
    const myPrivateKey = localStorage.getItem('my_private_key') ?? null;

    if (!myPrivateKey) {
        await new Promise(resolve => setTimeout(resolve, 500));
    }

    chats.value = await decryptConversationsList(conversation);

    isLoadingChat.value = false;
}

onMounted(() => {
    if (window.Echo && currentUserId) {
        window.Echo.private(`user.${currentUserId}`).listen('.message.sent', async (e) => {
            const newMsg = e.message;

            const targetChat = chats.value.find(c => c.id === newMsg.conversation_id);
            if (targetChat) {
                try {
                    const plaintext = await decryptMessage(targetChat.public_key, newMsg.ciphertext, newMsg.iv);

                    updateAndReorderSidebar(newMsg.conversation_id, plaintext, newMsg.created_at);

                    if (!activeChat.value || activeChat.value.id !== newMsg.conversation_id) {
                        targetChat.has_unread = true;
                    }
                } catch (error) {
                    console.error('Gagal decrypt pesan incoming global:', error);
                }
            }
        });

        window.Echo.join('online').here((users) => {
            onlineUserId.value = users.map(u => u.id);
            syncOnlineStatus();
        }).joining((user) => {
            if (!onlineUserId.value.includes(user.id)) {
                onlineUserId.value.push(user.id);
                syncOnlineStatus();
            }
        }).leaving((user) => {
            onlineUserId.value = onlineUserId.value.filter(id => id !== user.id);
            syncOnlineStatus();
        }).error((error) => {
            console.error('Online channel error:', error);
        })
    }
});

onUnmounted(() => {
    if (window.Echo) {
        if (currentUserId) {
            window.Echo.leave(`user.${currentUserId}`);
        }
        window.Echo.leave('online');
    }
});

watch(() => props.conversations, async (newVal) => {
    await loadChat(newVal);
}, { immediate: true });

watch(() => chats.value, () => {
    syncOnlineStatus();
}, { deep: true });

const openAddModal = () => {
    isAddModalOpen.value = true;
}

const closeAddModal = () => {
    isAddModalOpen.value = false;
    AddForm.reset();
    AddForm.clearErrors();
}

const handleAddChat = async () => {
    if (!AddForm.email.trim() || !AddForm.chat.trim()) return;

    const rawPlainText = AddForm.chat;

    const existingChat = chats.value.find(c => c.email.toLowerCase() === AddForm.email.toLowerCase());

    if (existingChat) {
        selectChat(existingChat);
        newMessageText.value = AddForm.chat;
        closeAddModal();
        return;
    }

    let recipientPublicKey = existingChat?.public_key;

    if (!recipientPublicKey) {
        isFetchingKey.value = true;

        try {
            const response = await axios.get(route('api.user.public-key', { email: AddForm.email }));
            recipientPublicKey = response.data.public_key;
        } catch (error) {
            console.error('Gagal menemukan public key:', error);
            AddForm.setError('email', 'Pengguna dengan email tersebut tidak ditemukan.');
            return;
        } finally {
            isFetchingKey.value = false;
        }
    }

    try {
        const { ciphertext, iv } = await encryptMessage(recipientPublicKey, rawPlainText);
        AddForm.ciphertext = ciphertext;
        AddForm.iv = iv;
    } catch (error) {
        console.error("Gagal encrypt:", error);
        alert.error('Gagal mengamankan pesan pastikan private key tersimpan di browser.')
        return;
    }

    AddForm.post(route('api.add-conversation'), {
        preserveScroll: true,
        onSuccess: (page) => {
            const newConv = page.props.flash.conversation;

            if (newConv) {
                newConv.lastMsg = rawPlainText;

                const existingIndex = chats.value.findIndex(c => c.id === newConv.id);
                if (existingIndex) {
                    chats.value.splice(existingIndex, 1);
                }

                chats.value.unshift(newConv);
                selectChat(newConv);
            }

            closeAddModal();
        },
    })
}

const filteredChat = computed(() => {
    if (!searchChat.value.trim()) return chats.value;

    return chats.value.filter(c => c.name.toLowerCase().includes(searchChat.value.toLowerCase()));
})

const sendMessage = async () => {
    if (!newMessageText.value || !activeChat.value) return;

    const rawPlainText = newMessageText.value;
    isSendingMessage.value = true;

    try {
        const { ciphertext, iv } = await encryptMessage(activeChat.value.public_key, rawPlainText);

        const response = await axios.post(route('api.send-message'), {
            conversation_id: activeChat.value.id,
            ciphertext: ciphertext,
            iv: iv,
        });

        const newMsgData = response.data.message;

        messages.value.push({
            ...newMsgData,
            plaintext: rawPlainText,
        });

        scrollToBottom();

        updateAndReorderSidebar(activeChat.value.id, rawPlainText, newMsgData.created_at || 'Baru Saja');

        newMessageText.value = '';
    } catch (error) {
        console.error('Gagal mengirim pesan:', error);
        alert.error('Gagal mengirim pesan.')
    } finally {
        isSendingMessage.value = false;
    }
}

const selectChat = async (chat) => {
    if (activeChat.value) {
        window.Echo.leave(`chat.${activeChat.value.id}`);
    }

    activeChat.value = chat
    messages.value = [];
    isLoadingMessages.value = true;

    chat.has_unread = false;

    window.history.pushState({}, '', `/dashboard?chat=${chat.id}`)

    try {
        const response = await axios.get(route('api.getMessages', { conversation: chat.id }));
        const rawMessage = response.data;

        messages.value = await Promise.all(rawMessage.map(async (msg) => {
            try {
                const plaintext = await decryptMessage(chat.public_key, msg.ciphertext, msg.iv);
                return { ...msg, plaintext }
            } catch (error) {
                console.error('Gagal mendecrypt pesan:', error);
                return { ...msg, plaintext: '[Pesan gagal terdecrypt]' }
            }
        }))
    } catch (error) {
        console.error('Gagal mengambil pesan:', error);
    } finally {
        isLoadingMessages.value = false;
        scrollToBottom();
    }

    activeChannel.value = window.Echo.private(`chat.${chat.id}`).listen('.message.sent', async (e) => {
        const newMsg = e.message;

        if (newMsg.sender_id !== currentUserId) {
            try {
                const plaintext = await decryptMessage(chat.public_key, newMsg.ciphertext, newMsg.iv);
                messages.value.push({
                    ...newMsg,
                    plaintext: plaintext,
                    has_unread: false,
                });
            } catch (error) {
                messages.value.push({
                    ...newMsg,
                    plaintext: "[Gagal deksripsi pesan real-time]",
                    has_unread: false,
                });
            }

            try {
                await axios.post(route('api.mark-as-read', {conversation: newMsg.conversation_id}));
            } catch (error) {
                console.error('Gagal memperbarui status baca pesan:', error);
            }

            scrollToBottom();
            updateAndReorderSidebar(chat.id, message.value[message.value.length - 1].plaintext, newMsg.created_at);
        }
    });

    try {
        await axios.post(route('api.mark-as-read', {conversation: chat.id}));
    } catch (error) {
        console.error('Gagal memperbarui status baca pesan:', error);
    }
}

const outChat = () => {
    if (activeChat.value) {
        window.Echo.leave(`chat.${activeChat.value.id}`);
    }

    activeChat.value = null
    messages.value = [];
    window.history.pushState({}, '', `/dashboard`);
}

const logout = () => {
    localStorage.removeItem('my_private_key');

    router.post(route('logout'));
}
</script>

<template>

    <Head title="AlwaysChat - Dashboard" />

    <AuthenticatedLayout>
        <div class="flex h-[100vh] overflow-hidden bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-200">

            <aside
                class="w-full md:w-96 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col transition-all duration-300 ease-in-out"
                :class="{ 'hidden md:flex': activeChat, 'flex': !activeChat }">
                <div class="p-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="p-2 flex items-center justify-between">
                        <h3 class="font-bold text-2xl text-slate-900 dark:text-slate-100">Chats</h3>
                        <div class="flex items-center justify-center gap-1 text-slate-600 dark:text-slate-300">
                            <button class="hover:text-indigo-600 dark:hover:text-indigo-400" @click="openAddModal"><svg width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 12.0002H18.0007M12.0002 6V18.0007" stroke="currentColor"
                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <Dropdown>
                                <template #trigger>
                                    <button class="hover:text-indigo-600 dark:hover:text-indigo-400 flex items-center"><svg class="w-5 h-5" fill="none"
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
                                    <DropdownLink :href="route('settings')">
                                        Settings
                                    </DropdownLink>

                                    <div class="border-t border-slate-200 dark:border-slate-700" />

                                    <DropdownLink href="#" as="button" @click.prevent="logout">
                                        Logout
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                    <div class="relative">
                        <input type="text" placeholder="Cari percakapan..." v-model="searchChat"
                            class="w-full pl-10 pr-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 transition-all placeholder-slate-400 dark:placeholder-slate-500" />
                        <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                <div v-if="isLoadingChat" class="p-4 h-full flex justify-center items-center text-center text-slate-400 dark:text-slate-500 text-sm">
                    Memuat...
                </div>
                
                <div v-else-if="chats.length == 0" class="p-4 h-full flex justify-center items-center text-center text-slate-400 dark:text-slate-500 text-sm">
                    Belum ada pesan, mulai percakapan baru.
                </div>

                <div v-if="filteredChat.length == 0 && searchChat.trim()" class="p-4 h-full flex justify-center items-center text-center text-slate-400 dark:text-slate-500 text-sm">
                    Chat tidak ditemukan, silahkan buat percakapan baru.
                </div>

                <div v-else class="flex-1 overflow-y-auto">
                    <div v-for="chat in filteredChat" :key="chat.id"
                        class="flex items-center gap-4 p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors border-b border-slate-50 dark:border-slate-800/40"
                        @click="selectChat(chat)">
                        <div class="relative">
                            <div
                                class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-950 flex items-center justify-center font-bold text-indigo-600 dark:text-indigo-400">
                                {{ chat.name.charAt(0) }}
                            </div>
                            <span v-if="chat.online"
                                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-slate-900 rounded-full"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">{{ chat.name }}</h3>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500">{{ chat.time }}</span>
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ chat.lastMsg }}</p>
                                <span v-if="chat.has_unread"
                                    class="w-2.5 h-2.5 bg-indigo-600 dark:bg-indigo-500 rounded-full flex-shrink-0 ml-2">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <main v-if="activeChat"
                class="flex-1 flex flex-col bg-white dark:bg-slate-900 w-full h-full absolute md:relative inset-0 md:inset-auto z-20 md:z-auto transition-all duration-300 ease-in-out">
                <header
                    class="h-16 px-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-white/80 dark:bg-slate-900/80 backdrop-blur-md z-10">
                    <div class="flex items-center gap-3">
                        <button class="hover:text-indigo-600 dark:hover:text-indigo-400 text-slate-600 dark:text-slate-300" @click="outChat">
                            <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M3.57813 12.4981C3.5777 12.6905 3.65086 12.8831 3.79761 13.0299L9.7936 19.0301C10.0864 19.3231 10.5613 19.3233 10.8543 19.0305C11.1473 18.7377 11.1474 18.2629 10.8546 17.9699L6.13418 13.2461L20.3295 13.2461C20.7437 13.2461 21.0795 12.9103 21.0795 12.4961C21.0795 12.0819 20.7437 11.7461 20.3295 11.7461L6.14168 11.7461L10.8546 7.03016C11.1474 6.73718 11.1473 6.2623 10.8543 5.9695C10.5613 5.6767 10.0864 5.67685 9.79362 5.96984L3.84392 11.9233C3.68134 12.0609 3.57812 12.2664 3.57812 12.4961L3.57813 12.4981Z"
                                    fill="currentColor" />
                            </svg>
                        </button>
                        <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-800">
                            <div
                                class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-950 flex items-center justify-center font-bold text-indigo-600 dark:text-indigo-400">
                                {{ activeChat.name.charAt(0) }}
                            </div>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ activeChat.name }}</h2>
                            <p v-if="activeChat.online"
                                class="text-[10px] text-green-500 font-medium uppercase tracking-wider">
                                Online</p>
                            <p v-else class="text-[10px] text-slate-400 dark:text-slate-500 font-medium uppercase tracking-wider">Offline</p>
                        </div>
                    </div>
                    <div class="flex gap-4 text-slate-400 dark:text-slate-500">
                        <button class="hover:text-indigo-600 dark:hover:text-indigo-400"><svg class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg></button>
                        <button class="hover:text-indigo-600 dark:hover:text-indigo-400"><svg class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                </path>
                            </svg></button>
                    </div>
                </header>

                <div ref="messageContainer" class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50/50 dark:bg-slate-950/40">
                    <div v-if="isLoadingMessages" class="flex justify-center items-center h-full text-slate-400 dark:text-slate-500">
                        <p class="text-sm">Memuat pesan...</p>
                    </div>

                    <div v-else-if="messages.length === 0"
                        class="flex justify-center items-center h-full text-slate-400 dark:text-slate-500">
                        <p class="text-sm">Belum ada percakapan. Mulai kirim pesan!</p>
                    </div>

                    <template v-else>
                        <div v-for="msg in messages" :key="msg.id" class="flex items-end gap-2"
                            :class="{ 'justify-end': msg.sender_id === currentUserId }">

                            <div v-if="msg.sender_id !== currentUserId"
                                class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/60 p-3 rounded-2xl rounded-bl-none shadow-sm max-w-md">
                                <p class="text-sm text-slate-700 dark:text-slate-200 break-words">{{ msg.plaintext || msg.ciphertext }}</p>
                                <span class="text-[9px] text-slate-400 dark:text-slate-500 mt-1 block">{{ msg.created_at }}</span>
                            </div>

                            <div v-else
                                class="bg-indigo-600 dark:bg-indigo-500 p-3 rounded-2xl rounded-br-none shadow-md shadow-indigo-100 dark:shadow-none max-w-md">
                                <p class="text-sm text-white break-words">{{ msg.plaintext || msg.ciphertext }}</p>
                                <span class="text-[9px] text-indigo-200 mt-1 block text-right">{{ msg.created_at
                                    }}</span>
                            </div>
                        </div>
                    </template>
                </div>

                <footer class="p-4 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800">
                    <form @submit.prevent="sendMessage"
                        class="max-w-4xl mx-auto flex items-center gap-3 bg-slate-100 dark:bg-slate-800 rounded-2xl px-4 py-2 focus-within:ring-2 focus-within:ring-indigo-500 transition-all">
                        <button type="button" class="text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </button>
                        <input v-model="newMessageText" type="text" placeholder="Tulis pesan..."
                            :disabled="isSendingMessage"
                            class="flex-1 bg-transparent border-none focus:ring-0 text-sm text-slate-700 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 disabled:opacity-50" />
                        <button type="submit" :disabled="isSendingMessage || !newMessageText.trim()"
                            class="bg-indigo-600 dark:bg-indigo-500 text-white p-2 rounded-xl hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-all active:scale-95 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </form>
                </footer>
            </main>

            <main v-else class="flex-1 hidden md:flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-950 text-slate-400 dark:text-slate-500">
                <p>Pilih salah satu chat untuk memulai perpesanan</p>
            </main>

            <Modal :show="isAddModalOpen" @close="closeAddModal" max-width="md">
                <div class="p-6 bg-white dark:bg-slate-900">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 mb-4">
                        Mulai Chat Baru
                    </h2>

                    <form @submit.prevent="handleAddChat">
                        <div>
                            <InputLabel for="email" value="Email Pengguna*" class="dark:text-slate-300" />

                            <TextInput id="email" type="email" class="mt-1 block w-full dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700" v-model="AddForm.email"
                                placeholder="user@gmail.com" required autofocus />

                            <InputError class="mt-2" :message="AddForm.errors.email" />
                        </div>

                        <div class="mt-2">
                            <InputLabel for="chat" value="Chat*" class="dark:text-slate-300" />

                            <TextInput id="chat" type="text" class="mt-1 block w-full dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700" v-model="AddForm.chat"
                                placeholder="Ketik Pesan..." required autofocus />

                            <InputError class="mt-2" :message="AddForm.errors.chat" />
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <SecondaryButton @click="closeAddModal">
                                Batal
                            </SecondaryButton>

                            <PrimaryButton :disabled="AddForm.processing">
                                <span v-if="isFetchingKey">Mencari User...</span>
                                <span v-else-if="AddForm.processing">Memproses...</span>
                                <span v-else>Cari & Chat</span>
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </Modal>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #e2e8f0;
    border-radius: 10px;
}

:deep(.dark) .overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #334155;
}
</style>