<script setup>
import { computed, nextTick, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    maxWidth: { type: String, default: '2xl' },
    closeable: { type: Boolean, default: true },
    titleId: { type: String, required: true },
    descriptionId: { type: String, default: undefined },
});
const emit = defineEmits(['close']);
const dialog = ref(null);
const showSlot = ref(props.show);
let returnFocus = null;
let closeTimer = null;

watch(() => props.show, async (show) => {
    clearTimeout(closeTimer);
    if (show) {
        returnFocus = document.activeElement;
        showSlot.value = true;
        await nextTick();
        document.body.style.overflow = 'hidden';
        if (!dialog.value?.open) dialog.value?.showModal();
    } else {
        document.body.style.overflow = '';
        closeTimer = setTimeout(() => {
            dialog.value?.close();
            showSlot.value = false;
            returnFocus?.focus?.();
        }, 200);
    }
}, { immediate: true });

const close = () => {
    if (props.closeable) emit('close');
};
const onCancel = (event) => {
    event.preventDefault();
    close();
};
onUnmounted(() => {
    clearTimeout(closeTimer);
    document.body.style.overflow = '';
});
const maxWidthClass = computed(() => ({ sm: 'sm:max-w-sm', md: 'sm:max-w-md', lg: 'sm:max-w-lg', xl: 'sm:max-w-xl', '2xl': 'sm:max-w-2xl' })[props.maxWidth]);
</script>

<template>
    <dialog ref="dialog" :aria-labelledby="titleId" :aria-describedby="descriptionId" class="z-50 m-0 min-h-full min-w-full overflow-y-auto bg-transparent backdrop:bg-transparent" @cancel="onCancel">
        <div class="fixed inset-0 z-50 grid items-center overflow-y-auto px-4 py-6 sm:px-6" scroll-region>
            <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-show="show" class="fixed inset-0 bg-ink-900/40" aria-hidden="true" @click="close" />
            </Transition>
            <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to-class="opacity-100 translate-y-0 sm:scale-100" leave-active-class="ease-in duration-200" leave-from-class="opacity-100 translate-y-0 sm:scale-100">
                <div v-show="show" class="relative mx-auto w-full transform overflow-hidden rounded-[2rem] border border-border bg-surface shadow-xl" :class="maxWidthClass">
                    <slot v-if="showSlot" />
                </div>
            </Transition>
        </div>
    </dialog>
</template>
