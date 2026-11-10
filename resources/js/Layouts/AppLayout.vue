<script setup>
import { computed, ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AppIcon from '@/Components/AppIcon.vue';
import BrandMark from '@/Components/BrandMark.vue';

const page = usePage();
const menuOpen = ref(false);
const headerSearch = ref(page.props.filters?.search || '');
const isCatalog = computed(() => page.component === 'Catalog/Index' || page.component === 'Listings/Show');
const isMine = computed(() => ['Listings/MyListings', 'Listings/Edit'].includes(page.component));
const isCreate = computed(() => page.component === 'Listings/Create');
const accountButton = ref(null);

watch(() => page.url, () => { menuOpen.value = false; });
watch(() => page.props.filters?.search, (search) => { headerSearch.value = search || ''; });

const closeMenu = () => {
    menuOpen.value = false;
    accountButton.value?.focus();
};
const searchCatalog = () => {
    const query = Object.fromEntries(Object.entries({
        category: page.props.filters?.category,
        condition: page.props.filters?.condition,
        search: headerSearch.value.trim(),
    }).filter(([, value]) => value));
    router.get(route('catalog.index'), query);
    menuOpen.value = false;
};
</script>

<template>
    <div class="flex min-h-screen flex-col bg-canvas font-sans text-ink-900">
        <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-2 focus:z-50 focus:rounded-xl focus:bg-surface focus:px-4 focus:py-3">Lewati ke konten</a>
        <header class="relative z-20 mx-auto w-full max-w-[1440px] px-3 pb-5 pt-4 sm:px-6 sm:pb-6 sm:pt-6 lg:px-8">
            <div class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-3 lg:grid-cols-[auto_minmax(0,1fr)_auto] lg:gap-5">
                <Link :href="route('catalog.index')" class="inline-flex min-h-16 w-fit items-center gap-2.5 rounded-full border border-white bg-surface px-4 pr-5 sm:gap-3 sm:px-5 sm:pr-7" aria-label="Gudang Pakde — katalog">
                    <BrandMark class="h-10 w-10 shrink-0" />
                    <span class="text-[15px] font-bold tracking-tight sm:text-lg">Gudang Pakde</span>
                </Link>

                <nav aria-label="Navigasi utama" class="order-last col-span-2 flex min-w-0 items-center justify-center gap-1 rounded-full border border-white bg-surface p-1.5 lg:order-none lg:col-span-1 lg:justify-self-center">
                    <Link :href="route('catalog.index')" :aria-current="isCatalog ? 'page' : undefined" class="inline-flex min-h-11 flex-1 items-center justify-center gap-1.5 whitespace-nowrap rounded-full px-2 text-xs font-semibold sm:gap-2 sm:px-4 sm:text-sm lg:flex-none xl:px-5" :class="isCatalog ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-canvas hover:text-ink-900'">
                        <AppIcon name="grid" class="h-[18px] w-[18px] shrink-0" />
                        Katalog
                    </Link>
                    <Link v-if="page.props.auth?.user" :href="route('listings.mine')" :aria-current="isMine ? 'page' : undefined" class="inline-flex min-h-11 flex-1 items-center justify-center gap-1.5 whitespace-nowrap rounded-full px-2 text-xs font-semibold sm:gap-2 sm:px-3 sm:text-sm lg:flex-none xl:px-5" :class="isMine ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-canvas hover:text-ink-900'">
                        <AppIcon name="box" class="h-[18px] w-[18px] shrink-0" />
                        Barang Saya
                    </Link>
                    <Link :href="route('listings.create')" :aria-current="isCreate ? 'page' : undefined" class="inline-flex min-h-11 flex-1 items-center justify-center gap-1.5 whitespace-nowrap rounded-full px-2 text-xs font-semibold sm:gap-2 sm:px-4 sm:text-sm lg:flex-none xl:px-5" :class="isCreate ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-canvas hover:text-ink-900'">
                        <AppIcon name="plus" class="h-[18px] w-[18px] shrink-0" />
                        Pasang<span class="hidden xl:inline"> Barang</span>
                    </Link>
                </nav>

                <div class="relative flex min-h-16 items-center gap-2 justify-self-end rounded-full border border-white bg-surface p-2 lg:pl-4" @keydown.esc.stop.prevent="closeMenu" @focusout="!$event.currentTarget.contains($event.relatedTarget) && (menuOpen = false)">
                    <form role="search" aria-label="Cari di katalog" class="hidden items-center rounded-lg border-r border-border pr-3 focus-within:ring-2 focus-within:ring-focus lg:flex" @submit.prevent="searchCatalog">
                        <label for="header-search" class="sr-only">Cari barang</label>
                        <input id="header-search" v-model="headerSearch" type="search" name="search" maxlength="200" placeholder="Cari barang..." class="min-h-11 w-24 min-w-0 rounded-lg border-0 bg-transparent px-1 text-sm placeholder:text-ink-500 focus:ring-0 xl:w-36" />
                        <button type="submit" aria-label="Cari barang" class="inline-flex h-11 w-9 shrink-0 items-center justify-center rounded-full text-ink-600 hover:text-ink-900"><AppIcon name="search" class="h-5 w-5" /></button>
                    </form>
                    <button ref="accountButton" type="button" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-full px-1 text-sm font-semibold sm:pr-2" :aria-expanded="menuOpen" aria-controls="account-navigation" aria-label="Menu akun" @click="menuOpen = !menuOpen">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-canvas"><AppIcon name="user" class="h-5 w-5" /></span>
                        <span class="hidden max-w-24 truncate xl:block">{{ page.props.auth?.user?.name || 'Akun' }}</span>
                        <AppIcon name="chevron-down" class="hidden h-4 w-4 text-ink-500 sm:block" />
                    </button>
                    <nav v-if="menuOpen" id="account-navigation" aria-label="Navigasi akun" class="absolute right-0 top-full z-30 mt-2 grid w-56 gap-1 rounded-2xl border border-border bg-surface p-2 shadow-sm">
                        <template v-if="page.props.auth?.user">
                            <p class="truncate border-b border-border px-3 py-3 text-sm font-semibold">{{ page.props.auth.user.name }}</p>
                            <Link :href="route('profile.edit')" class="inline-flex min-h-11 items-center gap-3 rounded-xl px-3 text-sm font-medium hover:bg-canvas"><AppIcon name="settings" class="h-[18px] w-[18px]" />Profil & akun</Link>
                            <Link :href="route('listings.mine')" class="inline-flex min-h-11 items-center gap-3 rounded-xl px-3 text-sm font-medium hover:bg-canvas"><AppIcon name="box" class="h-[18px] w-[18px]" />Barang Saya</Link>
                            <Link :href="route('logout')" method="post" as="button" class="inline-flex min-h-11 items-center gap-3 rounded-xl px-3 text-left text-sm font-medium hover:bg-canvas"><AppIcon name="logout" class="h-[18px] w-[18px]" />Keluar</Link>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="inline-flex min-h-11 items-center gap-3 rounded-xl bg-ink-900 px-3 text-sm font-semibold text-white"><AppIcon name="user" class="h-[18px] w-[18px]" />Masuk</Link>
                            <Link :href="route('register')" class="inline-flex min-h-11 items-center rounded-xl px-3 text-sm font-medium hover:bg-canvas">Buat akun</Link>
                        </template>
                    </nav>
                </div>
            </div>
        </header>

        <div v-if="page.props.flash?.success" role="status" class="mx-auto mb-4 w-full max-w-[1440px] px-3 sm:px-6 lg:px-8">
            <p class="flex items-start gap-3 rounded-2xl border border-success/10 bg-success-soft p-4 text-sm font-medium text-success"><AppIcon name="check" class="h-5 w-5 shrink-0" />{{ page.props.flash.success }}</p>
        </div>
        <main id="main-content" class="mx-auto w-full max-w-[1440px] flex-1 px-3 sm:px-6 lg:px-8"><slot /></main>
        <footer class="mx-auto flex w-full max-w-[1440px] flex-wrap items-center justify-between gap-2 px-5 py-7 text-xs text-ink-500 sm:px-8 lg:px-10">
            <div class="flex items-center gap-2">
                <BrandMark class="h-5 w-5 shrink-0" />
                <span class="font-semibold text-ink-600">Gudang Pakde</span>
            </div>
            <span>Barang bekas, kesempatan baru.</span>
        </footer>
    </div>
</template>
