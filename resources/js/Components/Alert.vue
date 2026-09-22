<script>
import { reactive } from 'vue';

const alertState = reactive({
    alerts: []
});

const removeAlert = (id) => {
    alertState.alerts = alertState.alerts.filter(a => a.id !== id);
};

const triggerAlert = (type = 'default', title, message = '', duration = 4000) => {
    const id = Date.now() + Math.random();
    alertState.alerts.push({ id, type, title, message });

    if (duration > 0) {
        setTimeout(() => {
            removeAlert(id);
        }, duration);
    }
};

export const alert = Object.assign(
    (title, message, duration) => triggerAlert('default', title, message, duration),
    {
        show: (title, message, duration) => triggerAlert('default', title, message, duration),
        success: (title, message, duration) => triggerAlert('success', title, message, duration),
        warning: (title, message, duration) => triggerAlert('warning', title, message, duration),
        error: (title, message, duration) => triggerAlert('error', title, message, duration),
    }
);
</script>

<script setup>
const getStyles = (type) => {
    switch (type) {
        case 'success':
            return {
                border: 'border-emerald-200 bg-emerald-50 text-emerald-900',
                iconBg: 'bg-emerald-500 text-white',
                iconPath: 'M5 13l4 4L19 7'
            };
        case 'warning':
            return {
                border: 'border-amber-200 bg-amber-50 text-amber-900',
                iconBg: 'bg-amber-500 text-white',
                iconPath: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
            };
        case 'error':
            return {
                border: 'border-rose-200 bg-rose-50 text-rose-900',
                iconBg: 'bg-rose-500 text-white',
                iconPath: 'M6 18L18 6M6 6l12 12'
            };
        case 'default':
        default:
            return {
                border: 'border-slate-200 bg-white text-slate-800 shadow-slate-200/50',
                iconBg: 'bg-indigo-600 text-white',
                iconPath: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
            };
    }
};
</script>

<template>
    <div class="fixed top-5 left-1/2 -translate-x-1/2 z-[9999] w-full max-w-sm px-4 space-y-2 pointer-events-none">
        <TransitionGroup
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform -translate-y-6 opacity-0 scale-95"
            enter-to-class="transform translate-y-0 opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="transform translate-y-0 opacity-100 scale-100"
            leave-to-class="transform -translate-y-4 opacity-0 scale-95"
        >
            <div
                v-for="item in alertState.alerts"
                :key="item.id"
                class="pointer-events-auto flex items-start gap-3 p-3.5 rounded-xl border shadow-lg backdrop-blur-sm transition-all"
                :class="getStyles(item.type).border"
            >
                <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center mt-0.5" :class="getStyles(item.type).iconBg">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="getStyles(item.type).iconPath" />
                    </svg>
                </div>

                <div class="flex-1 min-w-0 pr-1">
                    <h4 class="text-xs font-semibold leading-snug">{{ item.title }}</h4>
                    <p v-if="item.message" class="text-[11px] opacity-80 mt-0.5 leading-relaxed">{{ item.message }}</p>
                </div>

                <button
                    @click="removeAlert(item.id)"
                    class="flex-shrink-0 p-1 text-slate-400 hover:text-slate-600 rounded-lg transition-colors"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>