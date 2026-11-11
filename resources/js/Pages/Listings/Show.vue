<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AppIcon from '@/Components/AppIcon.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import ConditionBadge from '@/Components/ConditionBadge.vue';
import { conditionLabel, formatPrice } from '@/Utils/listing.js';

const props = defineProps({ listing: { type: Object, required: true } });

const galleryImages = computed(() => {
    if (props.listing.images && props.listing.images.length > 0) {
        return props.listing.images;
    }
    if (props.listing.image_url) {
        return [{ id: 'primary', url: props.listing.image_url }];
    }
    return [];
});

const activeImage = ref(galleryImages.value[0]?.url || props.listing.image_url || null);
const imageLoaded = ref(false);

watch(galleryImages, (images) => {
    if (!activeImage.value || !images.some(img => img.url === activeImage.value)) {
        activeImage.value = images[0]?.url || props.listing.image_url || null;
        imageLoaded.value = false;
    }
}, { immediate: true });

const selectImage = (url) => {
    if (activeImage.value !== url) {
        imageLoaded.value = false;
        activeImage.value = url;
    }
};
</script>

<template>
    <Head :title="listing.title" />
    <AppLayout>
        <div class="min-w-0 rounded-[24px] bg-surface p-4 sm:p-6 lg:rounded-[28px] lg:p-7">
            <nav aria-label="Jejak navigasi" class="mb-5 flex min-w-0 items-center gap-3 border-b border-border pb-5 text-xs text-ink-600 sm:mb-7 sm:text-sm">
                <Link :href="route('catalog.index')" class="inline-flex min-h-11 shrink-0 items-center gap-2 rounded-xl border border-border px-3 font-semibold text-ink-900 hover:bg-canvas"><AppIcon name="arrow-left" class="h-4 w-4" />Katalog</Link>
                <span aria-hidden="true" class="text-ink-500">/</span>
                <span class="truncate" aria-current="page">{{ listing.title }}</span>
            </nav>

            <article class="grid min-w-0 items-start gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.85fr)] lg:gap-8 xl:gap-10">
                <div class="min-w-0 lg:col-start-1 lg:row-start-1">
                    <div class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-photo sm:rounded-[20px]">
                        <!-- Skeleton Shimmer Placeholder while main image loads -->
                        <div
                            v-if="activeImage && !imageLoaded"
                            class="absolute inset-0 z-0 flex items-center justify-center bg-neutral-100"
                        >
                            <div class="pointer-events-none absolute inset-0 -translate-x-full animate-shimmer bg-gradient-to-r from-transparent via-white/60 to-transparent" />
                            <AppIcon name="image" class="h-12 w-12 text-neutral-300" />
                        </div>

                        <img
                            v-if="activeImage"
                            :src="activeImage"
                            :alt="`Foto ${listing.title}`"
                            class="h-full w-full object-contain p-5 sm:p-8 transition-opacity duration-300"
                            :class="imageLoaded ? 'opacity-100' : 'opacity-0'"
                            fetchpriority="high"
                            @load="imageLoaded = true"
                        />
                        <div v-else class="flex h-full flex-col items-center justify-center gap-3 text-sm text-ink-500"><AppIcon name="image" class="h-12 w-12" />Foto belum tersedia</div>
                        <span v-if="galleryImages.length > 1" class="absolute bottom-3 right-3 flex items-center gap-1.5 rounded-full bg-ink-900/70 px-2.5 py-1 text-xs font-semibold text-white shadow-sm backdrop-blur-sm">
                            <AppIcon name="image" class="h-3.5 w-3.5" />
                            {{ galleryImages.findIndex(i => i.url === activeImage) + 1 }} / {{ galleryImages.length }}
                        </span>
                    </div>

                    <!-- Kotak-kotak thumbnail dibawah untuk menampilkan gambar selanjutnya dan merujuk ke gambar tersebut -->
                    <div v-if="galleryImages.length > 1" class="mt-3.5 flex flex-wrap items-center gap-2.5 sm:mt-4 sm:gap-3" role="tablist" aria-label="Galeri foto barang">
                        <button
                            v-for="(img, idx) in galleryImages"
                            :key="img.id || idx"
                            type="button"
                            role="tab"
                            :aria-selected="activeImage === img.url"
                            :aria-label="`Pilih foto ${idx + 1} dari ${galleryImages.length}`"
                            @click="selectImage(img.url)"
                            class="group relative h-16 w-16 sm:h-20 sm:w-20 shrink-0 overflow-hidden rounded-xl border-2 bg-photo p-1 transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600"
                            :class="activeImage === img.url ? 'border-brand-600 ring-2 ring-brand-600/30 opacity-100 shadow-sm' : 'border-border opacity-70 hover:opacity-100 hover:border-ink-900/40'"
                        >
                            <img :src="img.url" :alt="`Thumbnail foto ${idx + 1}`" class="h-full w-full object-contain transition-transform duration-200 group-hover:scale-105" />
                        </button>
                    </div>
                </div>

                <div class="min-w-0 lg:col-start-2 lg:row-span-2 lg:row-start-1">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <Link v-if="listing.category?.slug" :href="route('catalog.index', { category: listing.category.slug })" class="inline-flex min-h-11 items-center rounded-lg text-sm font-medium text-ink-600 underline-offset-4 hover:underline">{{ listing.category.name }}</Link>
                        <p v-else-if="listing.category?.name" class="text-sm font-medium text-ink-600">{{ listing.category.name }}</p>
                        <StatusBadge :status="listing.status" />
                    </div>
                    <h1 class="mt-3 break-words text-2xl font-bold leading-tight tracking-tight sm:text-3xl xl:text-[34px]">{{ listing.title }}</h1>
                    <p class="mt-5 break-words text-3xl font-bold leading-tight tracking-tight sm:mt-6 xl:text-4xl">{{ formatPrice(listing.price) }}</p>

                    <dl class="mt-7 grid grid-cols-2 gap-4 border-y border-border py-5 text-sm sm:mt-8 sm:py-6">
                        <div class="min-w-0">
                            <dt class="mb-2 text-xs text-ink-600">Kondisi barang</dt>
                            <dd class="break-words font-semibold">
                                <ConditionBadge :condition="listing.item_condition" />
                            </dd>
                        </div>
                        <div class="min-w-0 border-l border-border pl-4">
                            <dt class="mb-2 text-xs text-ink-600">Lokasi barang</dt>
                            <dd class="flex items-start gap-1.5 font-semibold"><AppIcon name="location" class="mt-px h-4 w-4 shrink-0 text-ink-500" /><span class="min-w-0 break-words">{{ listing.location }}</span></dd>
                        </div>
                    </dl>

                    <section aria-labelledby="seller-heading" class="mt-6 rounded-2xl border border-border p-4 sm:mt-7 sm:p-5">
                        <div class="flex items-center gap-3">
                            <span aria-hidden="true" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-canvas text-ink-600"><AppIcon name="user" class="h-6 w-6" /></span>
                            <div class="min-w-0">
                                <h2 id="seller-heading" class="text-xs text-ink-600">Dipasang oleh</h2>
                                <p class="mt-1 break-words text-base font-semibold">{{ listing.user?.name || 'Tidak tersedia' }}</p>
                            </div>
                        </div>
                        <div class="mt-5">
                            <template v-if="listing.status === 'sold'">
                                <button type="button" disabled class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-canvas px-3 text-sm font-semibold text-ink-500">Barang sudah terjual</button>
                                <p class="mt-3 rounded-xl bg-sold p-3 text-xs leading-relaxed text-warning">Barang ini sudah terjual. Kontak penjual tidak tersedia.</p>
                            </template>
                            <template v-else-if="listing.whatsapp_url">
                                <a :href="listing.whatsapp_url" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-ink-900 px-3 text-center text-sm font-semibold text-white transition-colors duration-200 hover:bg-[#25D366]">
                                    <AppIcon name="whatsapp" class="h-5 w-5 shrink-0" />
                                    <span>Hubungi via WhatsApp</span>
                                    <span class="sr-only">(buka tab baru)</span>
                                </a>
                                <p class="mt-3 text-center text-xs leading-relaxed text-ink-600">Percakapan terbuka di WhatsApp. Transaksi diatur langsung dengan penjual.</p>
                            </template>
                            <p v-else class="rounded-xl bg-photo p-4 text-sm leading-relaxed text-ink-600">Kontak penjual belum tersedia.</p>
                        </div>
                    </section>
                </div>

                <section aria-labelledby="description-heading" class="min-w-0 border-t border-border pt-6 lg:col-start-1 lg:row-start-2">
                    <h2 id="description-heading" class="text-lg font-bold tracking-tight">Tentang barang ini</h2>
                    <p class="mt-4 whitespace-pre-wrap break-words text-sm leading-7 text-ink-600">{{ listing.description }}</p>
                </section>
            </article>
        </div>
    </AppLayout>
</template>
