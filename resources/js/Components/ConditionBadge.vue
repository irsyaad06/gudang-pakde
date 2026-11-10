<script setup>
import { computed } from 'vue';
import { conditionLabel } from '@/Utils/listing.js';

const props = defineProps({
    condition: {
        type: String,
        required: true,
    },
});

const config = computed(() => {
    switch (props.condition) {
        case 'new':
            return {
                label: 'Baru',
                badgeClass: 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                dotClass: 'bg-emerald-500',
            };
        case 'like_new':
            return {
                label: 'Seperti Baru',
                badgeClass: 'bg-sky-50 text-sky-700 border-sky-200/80',
                dotClass: 'bg-sky-500',
            };
        case 'good':
            return {
                label: 'Bagus',
                badgeClass: 'bg-amber-50 text-amber-700 border-amber-200/80',
                dotClass: 'bg-amber-500',
            };
        case 'fair':
            return {
                label: 'Cukup',
                badgeClass: 'bg-slate-100 text-slate-700 border-slate-200/80',
                dotClass: 'bg-slate-400',
            };
        default:
            return {
                label: conditionLabel(props.condition),
                badgeClass: 'bg-photo text-ink-600 border-border',
                dotClass: 'bg-ink-500',
            };
    }
});
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 rounded-md border px-2 py-0.5 text-[11px] font-semibold tracking-tight"
        :class="config.badgeClass"
    >
        <span class="h-1.5 w-1.5 shrink-0 rounded-full" :class="config.dotClass" aria-hidden="true" />
        {{ config.label }}
    </span>
</template>
