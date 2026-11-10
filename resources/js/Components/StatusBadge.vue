<script setup>
import AppIcon from '@/Components/AppIcon.vue';

defineProps({
    status: { type: String, required: true, validator: (value) => ['available', 'sold'].includes(value) },
});
</script>

<template>
    <span
        class="inline-flex items-center justify-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold shadow-sm backdrop-blur-sm transition-colors"
        :class="status === 'sold' ? 'bg-danger text-white' : 'bg-emerald-600 text-white'"
    >
        <span aria-hidden="true" class="h-1.5 w-1.5 shrink-0 rounded-full bg-white" />
        {{ status === 'sold' ? 'Terjual' : 'Tersedia' }}
    </span>
</template>
