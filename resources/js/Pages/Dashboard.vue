<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Beranda" />

    <AppLayout>
        <h1 class="text-2xl font-bold">Selamat datang!</h1>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        Lihat barang terbaru atau kelola barang yang kamu pasang. <Link :href="route('catalog.index')" class="font-semibold text-brand-800 underline">Jelajahi barang</Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
