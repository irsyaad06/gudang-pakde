<script setup>
import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppIcon from '@/Components/AppIcon.vue';
import { useToast } from '@/Composables/useToast.js';

const page = usePage();
const { toasts, remove, success, error, info } = useToast();

let lastKey = '';

watch(
    () => [
        page.props.flash?.success,
        page.props.flash?.error,
        page.props.flash?.info,
        page.props.flash?.status,
    ],
    ([successMsg, errorMsg, infoMsg, statusMsg]) => {
        const key = `${successMsg || ''}:::${errorMsg || ''}:::${infoMsg || ''}:::${statusMsg || ''}`;
        if (key === '::::::' || key === lastKey) return;
        lastKey = key;

        if (successMsg) {
            success(successMsg, 'Berhasil');
        }
        if (errorMsg) {
            error(errorMsg, 'Perhatian');
        }
        if (infoMsg) {
            info(infoMsg, 'Info');
        }
        if (statusMsg) {
            const readable = statusMsg === 'verification-link-sent'
                ? 'Tautan verifikasi baru telah dikirim ke email Anda.'
                : statusMsg;
            info(readable, 'Status');
        }
    },
    { immediate: true }
);

const typeConfig = (type) => {
    switch (type) {
        case 'error':
            return {
                borderClass: 'border-red-200/90',
                iconBg: 'bg-red-50 text-danger border-red-100',
                iconName: 'close',
                defaultTitle: 'Gagal',
            };
        case 'warning':
            return {
                borderClass: 'border-amber-200/90',
                iconBg: 'bg-amber-50 text-amber-600 border-amber-100',
                iconName: 'filter',
                defaultTitle: 'Peringatan',
            };
        case 'info':
            return {
                borderClass: 'border-brand-200/90',
                iconBg: 'bg-brand-50 text-brand-600 border-brand-100',
                iconName: 'box',
                defaultTitle: 'Informasi',
            };
        case 'success':
        default:
            return {
                borderClass: 'border-emerald-200/90',
                iconBg: 'bg-emerald-50 text-emerald-600 border-emerald-100',
                iconName: 'check',
                defaultTitle: 'Berhasil',
            };
    }
};
</script>

<template>
    <aside
        aria-live="assertive"
        class="pointer-events-none fixed right-4 top-4 z-[9999] flex w-full max-w-sm flex-col gap-2.5 sm:right-6 sm:top-6"
    >
        <TransitionGroup
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0 translate-x-4"
            move-class="transition ease-in-out duration-300"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                role="status"
                class="pointer-events-auto flex items-start gap-3 rounded-2xl border bg-surface/95 p-4 shadow-[0_12px_30px_-6px_rgba(0,0,0,0.12),0_4px_10px_-2px_rgba(0,0,0,0.06)] backdrop-blur-md"
                :class="typeConfig(toast.type).borderClass"
            >
                <div
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl border"
                    :class="typeConfig(toast.type).iconBg"
                >
                    <AppIcon :name="typeConfig(toast.type).iconName" class="h-4 w-4" />
                </div>

                <div class="min-w-0 flex-1 pt-0.5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-ink-500">
                        {{ toast.title || typeConfig(toast.type).defaultTitle }}
                    </p>
                    <p class="mt-0.5 text-sm font-medium leading-snug text-ink-900 break-words">
                        {{ toast.message }}
                    </p>
                </div>

                <button
                    type="button"
                    class="ml-1 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-ink-400 transition-colors hover:bg-canvas hover:text-ink-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600"
                    aria-label="Tutup notifikasi"
                    @click="remove(toast.id)"
                >
                    <AppIcon name="close" class="h-3.5 w-3.5" />
                </button>
            </div>
        </TransitionGroup>
    </aside>
</template>
