<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppIcon from '@/Components/AppIcon.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import ConditionBadge from '@/Components/ConditionBadge.vue';
import { formatPrice } from '@/Utils/listing.js';

const props = defineProps({ listing: { type: Object, required: true } });

const photoCount = computed(() => {
    return props.listing.images_count ?? props.listing.images?.length ?? (props.listing.image_url ? 1 : 0);
});
</script>

<template>
    <article
        class="group flex min-w-0 flex-col overflow-hidden rounded-3xl border border-border bg-surface p-3 shadow-sm transition-all duration-300 ease-out hover:-translate-y-1 hover:border-ink-900/25 hover:shadow-[0_10px_25px_-5px_rgba(0,0,0,0.08),0_8px_10px_-6px_rgba(0,0,0,0.04)]"
    >
        <!-- Kontainer Gambar dengan Status Badge di Kiri Atas -->
        <div class="relative overflow-hidden rounded-2xl bg-photo">
            <Link
                :href="route('listings.show', listing.id)"
                :aria-label="`Lihat detail ${listing.title}`"
                class="block aspect-[4/3] overflow-hidden focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-[-4px] focus-visible:outline-brand-600"
            >
                <img
                    v-if="listing.image_url"
                    :src="listing.image_url"
                    :alt="`Foto ${listing.title}`"
                    class="h-full w-full object-contain p-2 sm:p-3 transition-transform duration-500 ease-out group-hover:scale-105"
                    loading="lazy"
                    decoding="async"
                />
                <div v-else class="flex h-full flex-col items-center justify-center gap-2 p-4 text-center text-xs text-ink-500">
                    <AppIcon name="image" class="h-8 w-8" />
                    <span>Foto belum tersedia</span>
                </div>
            </Link>

            <!-- Status Barang di Atas Gambar (Kiri Atas) -->
            <div class="pointer-events-none absolute left-2.5 top-2.5 z-10 sm:left-3 sm:top-3">
                <StatusBadge :status="listing.status" />
            </div>

            <!-- Jumlah Foto (di Kanan Bawah) -->
            <span
                v-if="photoCount > 1"
                class="pointer-events-none absolute bottom-2.5 right-2.5 z-10 flex items-center gap-1 rounded-md bg-ink-900/70 px-1.5 py-0.5 text-[10px] font-semibold text-white shadow-sm backdrop-blur-sm"
                :title="`${photoCount} foto`"
            >
                <AppIcon name="image" class="h-3 w-3" />
                {{ photoCount }}
            </span>
        </div>

        <!-- Konten Info Barang -->
        <div class="flex flex-1 flex-col px-1 pb-1 pt-4 sm:px-2">
            <!-- Kategori & Badge Kondisi Barang -->
            <div class="flex flex-wrap items-center gap-2">
                <span v-if="listing.category?.name" class="text-xs font-medium text-ink-500">
                    {{ listing.category.name }}
                </span>
                <ConditionBadge :condition="listing.item_condition" />
            </div>

            <!-- Judul Barang -->
            <h3 class="mt-2.5 line-clamp-2 break-words text-base font-semibold leading-snug tracking-tight text-ink-900 transition-colors duration-200 group-hover:text-brand-600 sm:text-lg">
                <Link :href="route('listings.show', listing.id)" class="hover:underline">
                    {{ listing.title }}
                </Link>
            </h3>

            <!-- Harga -->
            <p class="mt-2 break-words text-lg font-bold tracking-tight text-ink-900 sm:text-xl">
                {{ formatPrice(listing.price) }}
            </p>

            <!-- Lokasi Wilayah -->
            <p class="mb-4 mt-2 flex items-start gap-1.5 break-words text-xs text-ink-600 sm:text-sm">
                <AppIcon name="location" class="mt-0.5 h-3.5 w-3.5 shrink-0 text-ink-500" />
                <span class="line-clamp-1 min-w-0 break-words">{{ listing.location }}</span>
            </p>

            <!-- Tombol Aksi: Lihat Barang & Hubungi Penjual -->
            <div class="mt-auto border-t border-border pt-3">
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('listings.show', listing.id)"
                        class="inline-flex min-h-10 flex-1 items-center justify-center rounded-full border border-border bg-white px-3 text-xs font-semibold text-ink-900 transition-colors hover:bg-canvas sm:text-sm"
                    >
                        Lihat Barang
                    </Link>

                    <a
                        v-if="listing.whatsapp_url"
                        :href="listing.whatsapp_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-ink-900 text-white transition-colors hover:bg-black focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ink-900"
                        title="Hubungi penjual via WhatsApp"
                        aria-label="Hubungi penjual via WhatsApp"
                    >
                        <AppIcon name="whatsapp" class="h-4 w-4" />
                    </a>
                    <button
                        v-else
                        type="button"
                        disabled
                        class="inline-flex h-10 w-10 shrink-0 cursor-not-allowed items-center justify-center rounded-full bg-ink-900/50 text-white opacity-60"
                        :title="listing.status === 'sold' ? 'Barang sudah terjual' : 'Kontak WhatsApp belum tersedia'"
                        aria-label="Kontak WhatsApp tidak tersedia"
                    >
                        <AppIcon name="whatsapp" class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </article>
</template>
