<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    contentClasses: {
        type: String,
        default: 'py-1 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/60',
    },
});

const open = ref(false);
const triggerRef = ref(null);
const dropdownStyle = ref({});

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

const updatePosition = () => {
    if (!triggerRef.value) return;
    const rect = triggerRef.value.getBoundingClientRect();
    const widthPx = props.width === '48' ? 192 : 192; // Default w-48 (12rem = 192px)

    let left = rect.right - widthPx;
    if (props.align === 'left') {
        left = rect.left;
    }

    dropdownStyle.value = {
        position: 'fixed',
        top: `${rect.bottom + 6}px`,
        left: `${left}px`,
        zIndex: 9999,
    };
};

watch(open, async (newVal) => {
    if (newVal) {
        await nextTick();
        updatePosition();
        window.addEventListener('scroll', updatePosition, true);
        window.addEventListener('resize', updatePosition);
    } else {
        window.removeEventListener('scroll', updatePosition, true);
        window.removeEventListener('resize', updatePosition);
    }
});

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    window.removeEventListener('scroll', updatePosition, true);
    window.removeEventListener('resize', updatePosition);
});

const widthClass = computed(() => {
    return {
        48: 'w-48',
    }[props.width.toString()] || 'w-48';
});
</script>

<template>
    <div class="inline-block" ref="triggerRef">
        <div @click="open = !open" class="flex items-center justify-center cursor-pointer">
            <slot name="trigger" />
        </div>

        <Teleport to="body">
            <!-- Overlay transparan -->
            <div
                v-if="open"
                class="fixed inset-0 z-[9998]"
                @click="open = false"
            ></div>

            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div
                    v-if="open"
                    class="rounded-xl shadow-lg"
                    :class="[widthClass]"
                    :style="dropdownStyle"
                    @click="open = false"
                >
                    <div
                        class="rounded-xl ring-1 ring-black ring-opacity-5 dark:ring-white dark:ring-opacity-10 overflow-hidden"
                        :class="contentClasses"
                    >
                        <slot name="content" />
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>