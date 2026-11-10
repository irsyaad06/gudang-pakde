<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-if="message" role="alert">
        <p class="text-sm leading-relaxed text-danger">
            {{ message }}
        </p>
    </div>
</template>
