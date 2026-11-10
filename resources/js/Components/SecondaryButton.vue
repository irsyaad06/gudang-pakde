<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex min-h-12 items-center justify-center gap-2 rounded-full border border-border bg-surface px-5 py-3 text-sm font-semibold text-ink-900 transition-colors hover:bg-canvas focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600 disabled:cursor-not-allowed disabled:opacity-50"
    >
        <slot />
    </button>
</template>
