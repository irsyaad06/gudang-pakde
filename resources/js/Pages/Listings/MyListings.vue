<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AppIcon from '@/Components/AppIcon.vue';
import OwnedListings from '@/Components/OwnedListings.vue';

defineProps({ listings: { type: Object, required: true } });
</script>

<template>
    <Head title="Barang Saya" />
    <AppLayout>
        <div class="min-w-0 rounded-[24px] bg-surface px-3 py-5 sm:p-6 lg:rounded-[28px] lg:p-7">
        <header class="mb-6 flex flex-col items-start justify-between gap-5 sm:flex-row sm:items-end">
            <div><h1 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">Barang Saya</h1><p class="mt-2 text-sm leading-relaxed text-ink-600">Semua barangmu, satu tempat untuk mengelolanya.</p></div>
            <Link :href="route('listings.create')" class="inline-flex min-h-12 shrink-0 items-center justify-center gap-2 rounded-full bg-ink-900 px-6 text-sm font-semibold text-white hover:bg-ink-600"><AppIcon name="plus" class="h-5 w-5" />Pasang Barang</Link>
        </header>
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3 border-b border-border pb-5"><h2 class="inline-flex min-h-11 items-center gap-2 rounded-full bg-ink-900 px-5 text-sm font-semibold text-white">Semua barang <span class="rounded-full bg-white/15 px-2 py-0.5 text-xs">{{ listings.total ?? listings.data.length }}</span></h2><p class="text-sm text-ink-500">Terbaru lebih dahulu</p></div>
        <OwnedListings :listings="listings" />
        </div>
    </AppLayout>
</template>
