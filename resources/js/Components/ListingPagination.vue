<script setup>
import { Link } from '@inertiajs/vue3';
import AppIcon from '@/Components/AppIcon.vue';

defineProps({ paginator: { type: Object, required: true }, query: { type: Object, default: () => ({}) } });
</script>

<template>
    <nav v-if="paginator.last_page > 1" aria-label="Halaman daftar barang" class="mt-7 flex flex-wrap items-center justify-between gap-3 border-t border-border pt-5 text-sm sm:mt-9">
        <span class="w-full text-center text-xs text-ink-600 sm:w-auto sm:text-left sm:text-sm">Halaman <span aria-current="page" class="font-semibold text-ink-900">{{ paginator.current_page }}</span> dari {{ paginator.last_page }}</span>
        <div class="flex w-full items-center justify-between gap-2 sm:w-auto">
            <Link v-if="paginator.current_page > 1" :href="route(route().current(), { ...query, page: paginator.current_page - 1 })" preserve-scroll class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-border bg-surface px-3 text-xs font-semibold hover:bg-canvas sm:px-4 sm:text-sm"><AppIcon name="arrow-left" class="h-4 w-4" />Sebelumnya</Link>
            <span v-else aria-disabled="true" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-border px-3 text-xs text-ink-500 sm:px-4 sm:text-sm"><AppIcon name="arrow-left" class="h-4 w-4" />Sebelumnya</span>
            <Link v-if="paginator.current_page < paginator.last_page" :href="route(route().current(), { ...query, page: paginator.current_page + 1 })" preserve-scroll class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-ink-900 px-3 text-xs font-semibold text-white hover:bg-ink-900/90 sm:px-4 sm:text-sm">Berikutnya<AppIcon name="arrow-right" class="h-4 w-4" /></Link>
            <span v-else aria-disabled="true" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-border px-3 text-xs text-ink-500 sm:px-4 sm:text-sm">Berikutnya<AppIcon name="arrow-right" class="h-4 w-4" /></span>
        </div>
    </nav>
</template>
