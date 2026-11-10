<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="min-h-12 min-w-0 rounded-2xl border-border bg-surface px-4 text-sm text-ink-900 placeholder:text-ink-500 focus:border-brand-600 focus:ring-brand-600 aria-[invalid=true]:border-danger disabled:cursor-not-allowed disabled:bg-canvas disabled:opacity-60"
        v-model="model"
        ref="input"
    />
</template>
