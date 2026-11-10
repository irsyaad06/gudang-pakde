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
        class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-start text-sm font-medium leading-5 text-ink-900 transition-colors hover:bg-canvas focus:bg-canvas focus:outline-none"
    >
        <slot />
    </Link>
</template>
