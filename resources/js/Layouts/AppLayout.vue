<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AppIcon from '@/Components/AppIcon.vue';
import BrandMark from '@/Components/BrandMark.vue';
import ToastContainer from '@/Components/ToastContainer.vue';
import FloatingDocsButton from '@/Components/FloatingDocsButton.vue';

const page = usePage();
const menuOpen = ref(false);
const headerSearch = ref(page.props.filters?.search || '');
const accountButton = ref(null);
const navContainer = ref(null);
const isScrolled = ref(false);

const handleScroll = () => {
    isScrolled.value = typeof window !== 'undefined' && window.scrollY > 8;
};

// Active tab evaluation
const isCatalog = computed(() => page.component === 'Catalog/Index' || page.component === 'Listings/Show');
const isMine = computed(() => ['Listings/MyListings', 'Listings/Edit'].includes(page.component));
const isCreate = computed(() => page.component === 'Listings/Create');

const currentActiveTab = computed(() => {
    if (isCatalog.value) return 'catalog';
    if (isMine.value) return 'mine';
    if (isCreate.value) return 'create';
    return null;
});

// Retrieve previous tab snapshot from sessionStorage (valid if navigated within 3 seconds)
let prevNav = null;
try {
    const raw = sessionStorage.getItem('gudang_pakde_nav_prev');
    if (raw) {
        const parsed = JSON.parse(raw);
        if (Date.now() - parsed.time < 3000) {
            prevNav = parsed;
        }
    }
} catch (e) {}

const hasTabTransition = prevNav && prevNav.tab && currentActiveTab.value && prevNav.tab !== currentActiveTab.value;

// visualActiveTab controls which tab has white text, cross-fading in sync with the sliding pill
const visualActiveTab = ref(
    hasTabTransition ? prevNav.tab : currentActiveTab.value
);

const pillStyle = ref(
    hasTabTransition
        ? {
              left: `${prevNav.left}px`,
              width: `${prevNav.width}px`,
              top: `${prevNav.top}px`,
              height: `${prevNav.height}px`,
              opacity: 1,
              transition: 'none',
          }
        : { opacity: 0 }
);

const saveNavSnapshot = (tab, el) => {
    if (!el) return;
    try {
        sessionStorage.setItem(
            'gudang_pakde_nav_prev',
            JSON.stringify({
                tab,
                left: el.offsetLeft,
                width: el.offsetWidth,
                top: el.offsetTop,
                height: el.offsetHeight,
                time: Date.now(),
            })
        );
    } catch (e) {}
};

const animateToTab = (tabName, withTransition = true) => {
    if (!navContainer.value) return;
    if (!tabName) {
        visualActiveTab.value = null;
        pillStyle.value = { ...pillStyle.value, opacity: 0 };
        return;
    }

    const targetEl = navContainer.value.querySelector(`[data-tab="${tabName}"]`);
    if (!targetEl) return;

    visualActiveTab.value = tabName;
    pillStyle.value = {
        left: `${targetEl.offsetLeft}px`,
        width: `${targetEl.offsetWidth}px`,
        top: `${targetEl.offsetTop}px`,
        height: `${targetEl.offsetHeight}px`,
        opacity: 1,
        transition: withTransition ? 'all 420ms cubic-bezier(0.25, 1, 0.5, 1)' : 'none',
    };

    saveNavSnapshot(tabName, targetEl);
};

const onTabClick = (tabName, event) => {
    if (!navContainer.value) return;

    // Save snapshot of current tab before page unmounts
    const currentTab = visualActiveTab.value || currentActiveTab.value;
    const currentEl = navContainer.value.querySelector(`[data-tab="${currentTab}"]`);
    if (currentEl) {
        saveNavSnapshot(currentTab, currentEl);
    }

    // Immediately start sliding the pill in the current view
    animateToTab(tabName, true);
};

let resizeObserver = null;

onMounted(() => {
    nextTick(() => {
        if (!navContainer.value) return;

        const targetTab = currentActiveTab.value;
        if (!targetTab) {
            pillStyle.value = { ...pillStyle.value, opacity: 0 };
            return;
        }

        const targetEl = navContainer.value.querySelector(`[data-tab="${targetTab}"]`);
        if (!targetEl) return;

        if (hasTabTransition) {
            // Step 1: Ensure initial position corresponds to previous tab in new DOM
            const fromEl = navContainer.value.querySelector(`[data-tab="${prevNav.tab}"]`);
            if (fromEl) {
                pillStyle.value = {
                    left: `${fromEl.offsetLeft}px`,
                    width: `${fromEl.offsetWidth}px`,
                    top: `${fromEl.offsetTop}px`,
                    height: `${fromEl.offsetHeight}px`,
                    opacity: 1,
                    transition: 'none',
                };
            }

            // Force reflow
            void navContainer.value.offsetHeight;

            // Step 2: In the next animation frame, smoothly slide to the target tab
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    animateToTab(targetTab, true);
                });
            });
        } else {
            // Initial direct placement
            animateToTab(targetTab, false);
        }
    });

    if (typeof window !== 'undefined') {
        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll();
    }

    if (typeof ResizeObserver !== 'undefined' && navContainer.value) {
        resizeObserver = new ResizeObserver(() => {
            if (currentActiveTab.value) {
                animateToTab(currentActiveTab.value, false);
            }
        });
        resizeObserver.observe(navContainer.value);
    }
});

onBeforeUnmount(() => {
    if (typeof window !== 'undefined') {
        window.removeEventListener('scroll', handleScroll);
    }
    if (resizeObserver) {
        resizeObserver.disconnect();
    }
});

watch(() => page.url, () => {
    menuOpen.value = false;
    handleScroll();
    nextTick(() => {
        if (currentActiveTab.value) {
            animateToTab(currentActiveTab.value, true);
        }
    });
});
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
        <ToastContainer />
        <FloatingDocsButton />
        <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-2 focus:z-50 focus:rounded-xl focus:bg-surface focus:px-4 focus:py-3">Lewati ke konten</a>
        <header class="sticky top-0 z-30 w-full" style="view-transition-name: app-header;">
            <!-- Feathered gradient blur background layer behind navbar (fades to opacity 0 at bottom) -->
            <div
                aria-hidden="true"
                class="pointer-events-none absolute inset-x-0 -top-2 -bottom-7 z-0 transition-opacity duration-300 ease-out"
                :class="isScrolled ? 'opacity-100' : 'opacity-0'"
                style="
                    backdrop-filter: blur(18px);
                    -webkit-backdrop-filter: blur(18px);
                    background: linear-gradient(to bottom, rgba(243, 243, 245, 0.94) 0%, rgba(243, 243, 245, 0.7) 50%, rgba(243, 243, 245, 0) 100%);
                    mask-image: linear-gradient(to bottom, black 0%, black 50%, transparent 100%);
                    -webkit-mask-image: linear-gradient(to bottom, black 0%, black 50%, transparent 100%);
                "
            />
            <div class="relative z-10 mx-auto w-full max-w-[1440px] px-3 pb-4 pt-3.5 sm:px-6 sm:pb-5 sm:pt-4 lg:px-8">
                <div class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-3 lg:grid-cols-[auto_minmax(0,1fr)_auto] lg:gap-5">
                <Link :href="route('catalog.index')" class="inline-flex min-h-16 w-fit items-center gap-2.5 rounded-full border border-white bg-surface px-4 pr-5 sm:gap-3 sm:px-5 sm:pr-7" aria-label="Gudang Pakde — katalog">
                    <BrandMark class="h-10 w-10 shrink-0" />
                    <span class="text-[15px] font-bold tracking-tight sm:text-lg">Gudang Pakde</span>
                </Link>

                <nav ref="navContainer" aria-label="Navigasi utama" class="relative order-last col-span-2 flex min-w-0 items-center justify-center gap-1 rounded-full border border-white bg-surface p-1.5 lg:order-none lg:col-span-1 lg:justify-self-center">
                    <!-- Sliding active indicator pill -->
                    <div
                        aria-hidden="true"
                        class="pointer-events-none absolute rounded-full bg-ink-900 shadow-sm"
                        :style="pillStyle"
                    />

                    <Link
                        :href="route('catalog.index')"
                        :aria-current="isCatalog ? 'page' : undefined"
                        data-tab="catalog"
                        class="relative z-10 inline-flex min-h-11 flex-1 items-center justify-center gap-1.5 whitespace-nowrap rounded-full px-2 text-xs font-semibold transition-colors duration-300 sm:gap-2 sm:px-4 sm:text-sm lg:flex-none xl:px-5"
                        :class="visualActiveTab === 'catalog' ? 'text-white' : 'text-ink-600 hover:text-ink-900'"
                        @click="onTabClick('catalog', $event)"
                    >
                        <AppIcon name="grid" class="h-[18px] w-[18px] shrink-0" />
                        <span>Katalog</span>
                    </Link>

                    <Link
                        v-if="page.props.auth?.user"
                        :href="route('listings.mine')"
                        :aria-current="isMine ? 'page' : undefined"
                        data-tab="mine"
                        class="relative z-10 inline-flex min-h-11 flex-1 items-center justify-center gap-1.5 whitespace-nowrap rounded-full px-2 text-xs font-semibold transition-colors duration-300 sm:gap-2 sm:px-3 sm:text-sm lg:flex-none xl:px-5"
                        :class="visualActiveTab === 'mine' ? 'text-white' : 'text-ink-600 hover:text-ink-900'"
                        @click="onTabClick('mine', $event)"
                    >
                        <AppIcon name="box" class="h-[18px] w-[18px] shrink-0" />
                        <span>Barang Saya</span>
                    </Link>

                    <Link
                        :href="route('listings.create')"
                        :aria-current="isCreate ? 'page' : undefined"
                        data-tab="create"
                        class="relative z-10 inline-flex min-h-11 flex-1 items-center justify-center gap-1.5 whitespace-nowrap rounded-full px-2 text-xs font-semibold transition-colors duration-300 sm:gap-2 sm:px-4 sm:text-sm lg:flex-none xl:px-5"
                        :class="visualActiveTab === 'create' ? 'text-white' : 'text-ink-600 hover:text-ink-900'"
                        @click="onTabClick('create', $event)"
                    >
                        <AppIcon name="plus" class="h-[18px] w-[18px] shrink-0" />
                        <span>Pasang<span class="hidden xl:inline"> Barang</span></span>
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
            </div>
        </header>

        <main id="main-content" class="mx-auto w-full max-w-[1440px] flex-1 px-3 sm:px-6 lg:px-8" style="view-transition-name: app-main;">
            <div class="page-content-wrapper min-h-full w-full">
                <slot />
            </div>
        </main>
        <footer class="mx-auto flex w-full max-w-[1440px] flex-wrap items-center justify-between gap-4 px-5 py-7 text-xs text-ink-500 sm:px-8 lg:px-10">
            <div class="flex items-center gap-2">
                <BrandMark class="h-5 w-5 shrink-0" />
                <span class="font-semibold text-ink-600">Gudang Pakde</span>
            </div>
            <div class="flex items-center gap-4">
                <Link :href="route('docs.index')" class="font-medium text-ink-600 transition hover:text-ink-900 underline underline-offset-4">Dokumentasi Teknis</Link>
                <span>Barang bekas, kesempatan baru.</span>
            </div>
        </footer>
    </div>
</template>
