<script setup>
import AppIcon from '@/Components/AppIcon.vue';

defineProps({
    status: { type: String, required: true, validator: (value) => ['available', 'sold'].includes(value) },
});
</script>

<template>
    <span class="inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold" :class="status === 'sold' ? 'bg-sold text-warning' : 'bg-success-soft text-success'">
        <AppIcon v-if="status === 'available'" name="check" class="h-3.5 w-3.5 shrink-0" />
        <span v-else aria-hidden="true" class="h-1.5 w-1.5 shrink-0 rounded-full bg-current" />
        {{ status === 'sold' ? 'Terjual' : 'Tersedia' }}
    </span>
</template>
