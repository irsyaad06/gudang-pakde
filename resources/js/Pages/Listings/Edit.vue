<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AppIcon from '@/Components/AppIcon.vue';
import ListingForm from '@/Components/ListingForm.vue';

defineProps({ categories: { type: Array, required: true }, listing: { type: Object, required: true } });
</script>

<template>
    <Head :title="`Edit ${listing.title}`" />
    <AppLayout>
        <div class="mb-4 flex flex-wrap items-center gap-2 text-sm text-ink-500"><Link :href="route('listings.mine')" class="inline-flex min-h-11 items-center gap-2 rounded-full pr-3 font-medium text-ink-600 hover:text-ink-900"><AppIcon name="arrow-left" class="h-4 w-4" />Barang Saya</Link><span aria-hidden="true">/</span><span>Edit Barang</span></div>
        <header class="mb-6"><h1 class="text-2xl font-semibold tracking-tight text-ink-900 sm:text-3xl">Edit barang</h1><p class="mt-2 max-w-2xl text-sm leading-relaxed text-ink-600">Perbarui detail barangmu. Foto saat ini tetap tersimpan jika tidak diganti.</p></header>
        <ListingForm :categories="categories" :listing="listing" />
    </AppLayout>
</template>
