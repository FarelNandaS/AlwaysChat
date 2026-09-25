<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2.5 text-start text-sm leading-5 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white focus:outline-none focus:bg-slate-100 dark:focus:bg-slate-700/50 transition duration-150 ease-in-out cursor-pointer"
    >
        <slot />
    </Link>
</template>