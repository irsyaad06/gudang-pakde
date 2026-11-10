<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AppIcon from '@/Components/AppIcon.vue';
import ListingCard from '@/Components/ListingCard.vue';
import ListingPagination from '@/Components/ListingPagination.vue';

const props = defineProps({ listings: { type: Object, required: true }, categories: { type: Array, required: true }, filters: { type: Object, required: true } });
const search = ref(props.filters.search || '');
const category = ref(props.filters.category || '');
const condition = ref(props.filters.condition || '');
const loading = ref(false);
const filtersOpen = ref(!!(props.filters.category || props.filters.condition));
const hasFilters = computed(() => !!(props.filters.search || props.filters.category || props.filters.condition));
const filterCount = computed(() => [props.filters.category, props.filters.condition].filter(Boolean).length);
const activeQuery = computed(() => Object.fromEntries(Object.entries(props.filters).filter(([, value]) => value)));

watch(() => props.filters, (filters) => {
    search.value = filters.search || '';
    category.value = filters.category || '';
    condition.value = filters.condition || '';
    if (filters.category || filters.condition) filtersOpen.value = true;
});

const apply = () => {
    const query = Object.fromEntries(Object.entries({ search: search.value.trim(), category: category.value, condition: condition.value }).filter(([, value]) => value));
    router.get(route('catalog.index'), query, {
        preserveState: true,
        preserveScroll: true,
        onStart: () => { loading.value = true; },
        onFinish: () => { loading.value = false; },
    });
};
const reset = () => { search.value = ''; category.value = ''; condition.value = ''; apply(); };
</script>

<template>
    <Head title="Katalog Barang" />
    <AppLayout>
        <div class="min-w-0 rounded-[24px] bg-surface px-3 py-5 sm:p-6 lg:rounded-[28px] lg:p-7">
            <div class="flex flex-col gap-5 border-b border-border pb-5 xl:flex-row xl:items-center xl:justify-between">
                <div class="flex min-w-0 items-center gap-3 px-1 sm:px-0">
                    <h1 class="text-xl font-bold tracking-tight sm:text-2xl">Katalog barang</h1>
                    <span v-if="listings.total != null" class="shrink-0 rounded-full border border-border px-2.5 py-1 text-xs font-medium tabular-nums text-ink-600">{{ listings.total }} barang</span>
                </div>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <form role="search" aria-label="Cari barang di katalog" class="flex min-h-11 w-full min-w-0 items-center rounded-xl border border-border bg-surface pl-3 focus-within:border-focus focus-within:ring-2 focus-within:ring-focus sm:w-72" @submit.prevent="apply">
                        <label for="catalog-search" class="sr-only">Cari barang</label>
                        <input id="catalog-search" v-model="search" type="search" name="search" maxlength="200" placeholder="Cari apa pakde?" class="min-h-11 min-w-0 flex-1 rounded-l-xl border-0 bg-transparent px-0 text-sm placeholder:text-ink-500 focus:ring-0" />
                        <button type="submit" :disabled="loading" aria-label="Cari barang" class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-ink-600 hover:text-ink-900 disabled:opacity-50"><AppIcon name="search" class="h-5 w-5" /></button>
                    </form>
                    <button type="button" :aria-expanded="filtersOpen" aria-controls="catalog-filters" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-border px-4 text-sm font-semibold hover:bg-canvas" :class="filterCount ? 'bg-canvas' : 'bg-surface'" @click="filtersOpen = !filtersOpen">
                        <AppIcon name="filter" class="h-[18px] w-[18px]" />Filter
                        <span v-if="filterCount" class="flex h-5 w-5 items-center justify-center rounded-full bg-ink-900 text-xs text-white">{{ filterCount }}</span>
                    </button>
                    <Link :href="route('listings.create')" class="ml-auto inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-ink-900 px-4 text-sm font-semibold text-white hover:bg-ink-900/90 sm:ml-0"><AppIcon name="plus" class="h-[18px] w-[18px]" />Pasang Barang</Link>
                </div>
            </div>

            <form v-show="filtersOpen" id="catalog-filters" aria-label="Filter kategori dan kondisi" class="mt-5 grid gap-3 rounded-2xl bg-photo p-3 sm:flex sm:flex-wrap sm:items-end sm:p-4" @submit.prevent="apply">
                <div class="min-w-0 sm:min-w-52">
                    <label for="catalog-category" class="mb-1.5 block text-xs font-semibold text-ink-600">Kategori</label>
                    <select id="catalog-category" v-model="category" name="category" class="min-h-11 w-full rounded-xl border-border bg-surface py-2 text-sm focus:border-focus focus:ring-focus" :disabled="loading" @change="apply">
                        <option value="">Semua kategori</option>
                        <option v-for="item in categories" :key="item.id" :value="item.slug">{{ item.name }}</option>
                    </select>
                </div>
                <div class="min-w-0 sm:min-w-48">
                    <label for="catalog-condition" class="mb-1.5 block text-xs font-semibold text-ink-600">Kondisi barang</label>
                    <select id="catalog-condition" v-model="condition" name="condition" class="min-h-11 w-full rounded-xl border-border bg-surface py-2 text-sm focus:border-focus focus:ring-focus" :disabled="loading" @change="apply">
                        <option value="">Semua kondisi</option>
                        <option value="new">Baru</option>
                        <option value="like_new">Seperti Baru</option>
                        <option value="good">Bagus</option>
                        <option value="fair">Cukup</option>
                    </select>
                </div>
                <button v-if="hasFilters" type="button" :disabled="loading" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl px-3 text-sm font-semibold text-ink-600 underline underline-offset-4 hover:text-ink-900 disabled:opacity-50 sm:ml-auto" @click="reset"><AppIcon name="close" class="h-4 w-4" />Hapus filter</button>
            </form>

            <section aria-labelledby="catalog-heading" :aria-busy="loading" class="pt-5 sm:pt-6">
                <div class="mb-4 flex min-h-6 flex-wrap items-center justify-between gap-2 px-1 sm:mb-5 sm:px-0">
                    <h2 id="catalog-heading" class="min-w-0 break-words text-sm font-semibold text-ink-600">{{ hasFilters ? 'Hasil pencarian' : 'Barang terbaru' }}<span v-if="filters.search" class="font-normal"> untuk “{{ filters.search }}”</span></h2>
                    <button v-if="hasFilters && !filtersOpen" type="button" :disabled="loading" class="min-h-11 rounded-lg text-xs font-semibold underline underline-offset-4 disabled:opacity-50" @click="reset">Hapus filter</button>
                    <span role="status" class="text-xs text-ink-500">{{ loading ? 'Memuat barang…' : '' }}</span>
                </div>
                <div v-if="listings.data?.length" class="grid grid-cols-2 gap-3 motion-safe:transition-opacity md:grid-cols-3 lg:grid-cols-4 lg:gap-5 xl:gap-6" :class="loading ? 'opacity-60' : ''">
                    <ListingCard v-for="listing in listings.data" :key="listing.id" :listing="listing" />
                </div>
                <div v-else class="rounded-2xl border border-dashed border-border px-5 py-16 text-center sm:py-24">
                    <div aria-hidden="true" class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-photo text-ink-500"><AppIcon :name="hasFilters ? 'search' : 'box'" class="h-7 w-7" /></div>
                    <h3 class="text-lg font-bold">{{ hasFilters ? 'Tidak ada barang yang cocok' : 'Belum ada barang' }}</h3>
                    <p class="mx-auto mt-2 max-w-xs text-sm leading-relaxed text-ink-600">{{ hasFilters ? 'Coba kata kunci lain atau hapus filter untuk melihat lebih banyak barang.' : 'Barang yang dipasang oleh komunitas akan muncul di sini.' }}</p>
                    <button v-if="hasFilters" type="button" :disabled="loading" class="mt-6 min-h-11 rounded-xl bg-ink-900 px-5 text-sm font-semibold text-white hover:bg-ink-900/90 disabled:opacity-50" @click="reset">Hapus filter</button>
                    <Link v-else :href="route('listings.create')" class="mt-6 inline-flex min-h-11 items-center gap-2 rounded-xl bg-ink-900 px-5 text-sm font-semibold text-white hover:bg-ink-900/90"><AppIcon name="plus" class="h-4 w-4" />Pasang Barang</Link>
                </div>
                <ListingPagination :paginator="listings" :query="activeQuery" />
            </section>
        </div>
    </AppLayout>
</template>
