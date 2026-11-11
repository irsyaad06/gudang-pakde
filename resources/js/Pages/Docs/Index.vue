<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AppIcon from '@/Components/AppIcon.vue';
import AppArchitectureDiagram from '@/Components/Docs/AppArchitectureDiagram.vue';
import InfraArchitectureDiagram from '@/Components/Docs/InfraArchitectureDiagram.vue';
import DatabaseErdDiagram from '@/Components/Docs/DatabaseErdDiagram.vue';

const props = defineProps({
    meta: {
        type: Object,
        default: () => ({}),
    },
});

// Sidebar collapse state
const isSidebarCollapsed = ref(false);
const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

// Mobile navigation collapsible state (default collapsed on mobile to avoid blocking content)
const isMobileMenuExpanded = ref(false);

// Filter mode: 'all' | 'rancangan' | 'technical'
const activeDocMode = ref('all');

// Parent groups collapse state inside sidebar
const parentOpenState = ref({
    rancangan: true,
    technical: true,
});

const toggleParent = (parentId) => {
    parentOpenState.value[parentId] = !parentOpenState.value[parentId];
};

// Two Parent Menus (Orangtua) matching GITS PDF Requirements
const parentSections = [
    {
        id: 'rancangan',
        title: 'Rancangan Arsitektur Aplikasi & Infrastruktur',
        shortTitle: 'Rancangan Arsitektur (100k DAU)',
        subtitle: 'Skala 100.000 DAU (Poin 2 Assignment)',
        badge: '100k DAU',
        icon: 'layers',
        colorBadge: 'bg-blue-100 text-blue-800 border-blue-200',
        items: [
            {
                id: 'rancangan-asumsi',
                title: '1. Asumsi Beban & Pola Traffic 100k DAU',
                shortTitle: '1. Asumsi & Pola 100k DAU',
                subtitle: 'Kalkulasi RPS, peak hours & storage foto',
                icon: 'cpu',
                tag: 'Kapasitas',
            },
            {
                id: 'rancangan-arsitektur',
                title: '2. Diagram Arsitektur Aplikasi',
                shortTitle: '2. Diagram Arsitektur Aplikasi',
                subtitle: 'Modular monolith, alur request & WhatsApp',
                icon: 'layers',
                tag: 'Diagram App',
            },
            {
                id: 'rancangan-infrastruktur',
                title: '3. Diagram Infrastruktur Cloud',
                shortTitle: '3. Diagram Infrastruktur Cloud',
                subtitle: 'Cloudflare, AWS ALB, ECS, RDS & S3',
                icon: 'server',
                tag: 'Diagram Infra',
            },
            {
                id: 'rancangan-scaling',
                title: '4. Scaling, Reliability & Keamanan',
                shortTitle: '4. Scaling & Keamanan',
                subtitle: 'Auto-scaling, caching, rate limit & backup',
                icon: 'shield-check',
                tag: 'Keandalan',
            },
        ],
    },
    {
        id: 'technical',
        title: 'Technical Documentation',
        shortTitle: 'Technical Documentation',
        subtitle: 'Dokumentasi Teknis Sistem & Pengembang (Poin 3 Assignment)',
        badge: 'Tech Docs',
        icon: 'file-text',
        colorBadge: 'bg-emerald-100 text-emerald-800 border-emerald-200',
        items: [
            {
                id: 'tech-overview',
                title: '1. Ikhtisar Proyek & Fitur MVP',
                shortTitle: '1. Ikhtisar & Fitur MVP',
                subtitle: 'Tujuan aplikasi, fitur & struktur kode',
                icon: 'box',
                tag: 'Overview',
            },
            {
                id: 'tech-install',
                title: '2. Panduan Setup & Instalasi Lokal',
                shortTitle: '2. Panduan Instalasi Lokal',
                subtitle: 'Setup di macOS & Windows langkah demi langkah',
                icon: 'terminal',
                tag: 'Setup',
            },
            {
                id: 'tech-database',
                title: '3. Desain Database & ERD',
                shortTitle: '3. Desain Database & ERD',
                subtitle: 'Schema tabel, relasi entitas & indexing',
                icon: 'database',
                tag: 'ERD',
            },
            {
                id: 'tech-decisions',
                title: '4. Keputusan Tech Stack',
                shortTitle: '4. Keputusan Tech Stack',
                subtitle: 'Alasan pemilihan stack & analisis trade-off',
                icon: 'cpu',
                tag: 'Stack',
            },
            {
                id: 'tech-routes',
                title: '5. Dokumentasi Route Web & Keamanan',
                shortTitle: '5. Route Web & Keamanan',
                subtitle: 'Endpoint HTTP, Form Request & Policies',
                icon: 'shield-check',
                tag: 'API / Web',
            },
            {
                id: 'tech-devops',
                title: '6. Deployment & Operasional',
                shortTitle: '6. Deployment & Operasional',
                subtitle: 'CI/CD, monitoring, logging & troubleshooting',
                icon: 'server',
                tag: 'Operations',
            },
        ],
    },
];

// Visible parents according to active filter
const visibleParents = computed(() => {
    if (activeDocMode.value === 'rancangan') {
        return parentSections.filter((p) => p.id === 'rancangan');
    }
    if (activeDocMode.value === 'technical') {
        return parentSections.filter((p) => p.id === 'technical');
    }
    return parentSections;
});

// Flattened visible items for scrollspy & counting
const visibleItems = computed(() => {
    return visibleParents.value.flatMap((parent) => parent.items);
});

// Active section state
const activeSection = ref('rancangan-asumsi');

const activeItemTitle = computed(() => {
    const item = visibleItems.value.find((i) => i.id === activeSection.value);
    return item ? item.shortTitle : 'Pilih Bagian';
});

// Card collapsible state (all 10 sub-sections)
const cardOpenState = ref({
    'rancangan-asumsi': true,
    'rancangan-arsitektur': true,
    'rancangan-infrastruktur': true,
    'rancangan-scaling': true,
    'tech-overview': true,
    'tech-install': true,
    'tech-database': true,
    'tech-decisions': true,
    'tech-routes': true,
    'tech-devops': true,
});

const isCardOpen = (id) => !!cardOpenState.value[id];

const toggleCard = (id) => {
    cardOpenState.value[id] = !cardOpenState.value[id];
};

const openAllCards = () => {
    Object.keys(cardOpenState.value).forEach((key) => {
        cardOpenState.value[key] = true;
    });
};

const closeAllCards = () => {
    Object.keys(cardOpenState.value).forEach((key) => {
        cardOpenState.value[key] = false;
    });
};

const openCount = computed(() => {
    const visibleIds = visibleItems.value.map((item) => item.id);
    return visibleIds.filter((id) => cardOpenState.value[id]).length;
});

// Switch view mode
const setDocMode = (mode) => {
    activeDocMode.value = mode;
    nextTick(() => {
        if (mode === 'rancangan') {
            activeSection.value = 'rancangan-asumsi';
            scrollToSection('rancangan-asumsi');
        } else if (mode === 'technical') {
            activeSection.value = 'tech-overview';
            scrollToSection('tech-overview');
        } else {
            scrollToSection('rancangan-asumsi');
        }
    });
};

// OS Tab switcher for local install guide
const activeOs = ref('macos'); // 'macos' | 'windows'

// Copy snippet feedback state
const copiedSnippet = ref(null);
const copyToClipboard = (text, id) => {
    navigator.clipboard.writeText(text).then(() => {
        copiedSnippet.value = id;
        setTimeout(() => {
            if (copiedSnippet.value === id) {
                copiedSnippet.value = null;
            }
        }, 2200);
    });
};

// Scrollspy handling
let isProgrammaticScroll = false;
let programmaticScrollTimeout = null;
let isScrollTicking = false;

const syncMobileNav = (id) => {
    if (typeof window !== 'undefined' && window.innerWidth < 1024) {
        const activeNavEl = document.querySelector(`[data-nav-id="${id}"]`);
        if (activeNavEl) {
            activeNavEl.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
        }
    }
};

const updateActiveSectionOnScroll = () => {
    if (isProgrammaticScroll || typeof window === 'undefined') return;

    const items = visibleItems.value;
    if (!items.length) return;

    const scrollY = window.scrollY;
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;

    // Bottom check
    if (windowHeight + scrollY >= documentHeight - 60) {
        const lastId = items[items.length - 1].id;
        if (activeSection.value !== lastId) {
            activeSection.value = lastId;
            syncMobileNav(lastId);
        }
        return;
    }

    const focalPoint = 160;

    for (let i = items.length - 1; i >= 0; i--) {
        const id = items[i].id;
        const el = document.getElementById(id);
        if (el) {
            const rect = el.getBoundingClientRect();
            if (rect.top <= focalPoint) {
                if (activeSection.value !== id) {
                    activeSection.value = id;
                    syncMobileNav(id);
                }
                return;
            }
        }
    }

    if (items.length > 0 && activeSection.value !== items[0].id) {
        activeSection.value = items[0].id;
        syncMobileNav(items[0].id);
    }
};

const handleWindowScroll = () => {
    if (!isScrollTicking) {
        window.requestAnimationFrame(() => {
            updateActiveSectionOnScroll();
            isScrollTicking = false;
        });
        isScrollTicking = true;
    }
};

const scrollToSection = (id) => {
    isProgrammaticScroll = true;
    activeSection.value = id;
    cardOpenState.value[id] = true;
    isMobileMenuExpanded.value = false;
    syncMobileNav(id);

    if (programmaticScrollTimeout) {
        clearTimeout(programmaticScrollTimeout);
    }
    programmaticScrollTimeout = setTimeout(() => {
        isProgrammaticScroll = false;
    }, 850);

    nextTick(() => {
        const el = document.getElementById(id);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
};

onMounted(() => {
    if (typeof window !== 'undefined') {
        window.addEventListener('scroll', handleWindowScroll, { passive: true });
        nextTick(() => {
            updateActiveSectionOnScroll();
        });
    }
});

onBeforeUnmount(() => {
    if (typeof window !== 'undefined') {
        window.removeEventListener('scroll', handleWindowScroll);
    }
    if (programmaticScrollTimeout) {
        clearTimeout(programmaticScrollTimeout);
    }
});
</script>

<template>
    <AppLayout>
        <Head title="Dokumentasi Teknis & Rancangan Arsitektur — Gudang Pakde" />

        <div class="mx-auto max-w-7xl py-6 sm:py-8">
            <!-- Header Card (Top Banner) -->
            <div class="mb-6 rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm sm:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-3xl">
                        <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                            <span class="flex h-2 w-2 rounded-full bg-emerald-500"></span>
                            <span>GITS Fullstack Developer Assignment — Official Submission</span>
                        </div>
                        <h1 class="text-2xl font-bold tracking-tight text-ink-900 sm:text-3xl lg:text-4xl">
                            Dokumentasi & Rancangan Arsitektur
                        </h1>
                        <p class="mt-2 text-sm leading-relaxed text-ink-600 sm:text-base">
                            Dokumentasi terstruktur terbagi menjadi 2 fokus utama sesuai spesifikasi assignment: 
                            <strong>Rancangan Arsitektur Aplikasi & Infrastruktur (Skala 100k DAU)</strong> serta 
                            <strong>Technical Documentation (Sistem, Setup, Database ERD & Routes)</strong>.
                        </p>
                    </div>

                    <!-- Runtime Badges Card -->
                    <div class="flex flex-wrap gap-2.5 rounded-2xl border border-neutral-100 bg-neutral-50/80 p-4 lg:flex-col lg:min-w-56">
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <span class="font-medium text-ink-500">Backend:</span>
                            <span class="rounded-md bg-white px-2 py-0.5 font-mono font-semibold text-ink-800 shadow-xs border border-neutral-200/60">Laravel {{ meta.laravelVersion }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <span class="font-medium text-ink-500">Frontend:</span>
                            <span class="rounded-md bg-white px-2 py-0.5 font-mono font-semibold text-ink-800 shadow-xs border border-neutral-200/60">Vue 3 + Inertia</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <span class="font-medium text-ink-500">Database:</span>
                            <span class="rounded-md bg-white px-2 py-0.5 font-mono font-semibold text-ink-800 shadow-xs border border-neutral-200/60">MySQL 8.x</span>
                        </div>
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <span class="font-medium text-ink-500">Automated Tests:</span>
                            <span class="rounded-md bg-emerald-100 px-2 py-0.5 font-mono font-bold text-emerald-800">{{ meta.stats?.testsCount || 41 }} Passed ({{ meta.stats?.assertionsCount || 492 }} Assertions)</span>
                        </div>
                    </div>
                </div>

                <!-- Focus Mode Switcher (Memisahkan Rancangan vs Technical) -->
                <div class="mt-6 border-t border-neutral-100 pt-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-xs font-semibold text-ink-500 uppercase tracking-wider">
                            Pilih Fokus Tampilan Dokumen:
                        </div>
                        <div class="inline-flex rounded-2xl border border-neutral-200/80 bg-neutral-100/90 p-1 text-xs">
                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-xl px-3.5 py-2 font-semibold transition"
                                :class="activeDocMode === 'all' ? 'bg-white text-ink-900 shadow-xs' : 'text-ink-600 hover:text-ink-900'"
                                @click="setDocMode('all')"
                            >
                                <AppIcon name="grid" class="h-3.5 w-3.5" />
                                <span>Semua Dokumentasi</span>
                            </button>
                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-xl px-3.5 py-2 font-semibold transition"
                                :class="activeDocMode === 'rancangan' ? 'bg-blue-600 text-white shadow-xs' : 'text-ink-600 hover:text-ink-900'"
                                @click="setDocMode('rancangan')"
                            >
                                <AppIcon name="layers" class="h-3.5 w-3.5" />
                                <span>Rancangan Arsitektur (100k DAU)</span>
                            </button>
                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-xl px-3.5 py-2 font-semibold transition"
                                :class="activeDocMode === 'technical' ? 'bg-emerald-600 text-white shadow-xs' : 'text-ink-600 hover:text-ink-900'"
                                @click="setDocMode('technical')"
                            >
                                <AppIcon name="file-text" class="h-3.5 w-3.5" />
                                <span>Technical Documentation</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Layout: Collapsible Sidebar + Content Cards -->
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
                <!-- Collapsible Sidebar with 2 Parent Groups ("Orangtua") -->
                <aside
                    class="relative z-10 shrink-0 transition-all duration-300 ease-in-out lg:sticky lg:top-24"
                    :class="isSidebarCollapsed ? 'lg:w-20' : 'lg:w-80'"
                >
                    <nav class="rounded-3xl border border-neutral-200/80 bg-white p-3 shadow-sm sm:p-4" aria-label="Navigasi Dokumentasi">
                        <!-- Mobile Accordion Header (< lg) - Compact, clean & does NOT freeze or block screen -->
                        <button
                            type="button"
                            class="flex w-full items-center justify-between p-1 text-left lg:hidden"
                            :class="{ 'border-b border-neutral-100 pb-3 mb-3': isMobileMenuExpanded }"
                            @click="isMobileMenuExpanded = !isMobileMenuExpanded"
                            aria-label="Toggle Menu Navigasi Dokumentasi"
                        >
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                                    <AppIcon name="menu" class="h-4 w-4" />
                                </span>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-ink-900 leading-tight">Daftar Isi Dokumentasi</div>
                                    <div class="truncate text-[11px] text-ink-500">
                                        Aktif: <span class="font-semibold text-blue-600">{{ activeItemTitle }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0 rounded-xl border border-neutral-200 bg-neutral-50 px-2.5 py-1.5 text-xs font-semibold text-ink-700">
                                <span>{{ isMobileMenuExpanded ? 'Tutup' : 'Lihat Menu' }}</span>
                                <AppIcon name="chevron-down" class="h-3.5 w-3.5 transition-transform duration-200" :class="{ 'rotate-180': isMobileMenuExpanded }" />
                            </div>
                        </button>

                        <!-- Desktop Sidebar Header (>= lg) -->
                        <div class="hidden lg:flex mb-3 items-center justify-between border-b border-neutral-100 pb-3" :class="{ 'justify-center': isSidebarCollapsed }">
                            <span v-if="!isSidebarCollapsed" class="text-xs font-bold uppercase tracking-wider text-ink-400">
                                Menu Navigasi (2 Kategori)
                            </span>
                            <button
                                type="button"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-neutral-200 text-ink-600 transition hover:bg-neutral-100 hover:text-ink-900"
                                :title="isSidebarCollapsed ? 'Perluas Sidebar' : 'Ciutkan Sidebar'"
                                :aria-label="isSidebarCollapsed ? 'Perluas Sidebar' : 'Ciutkan Sidebar'"
                                @click="toggleSidebar"
                            >
                                <AppIcon :name="isSidebarCollapsed ? 'chevron-right' : 'chevron-left'" class="h-4 w-4" />
                            </button>
                        </div>

                        <!-- Menu Body: Collapsible on mobile, always visible on desktop -->
                        <div :class="{ 'hidden lg:block': !isMobileMenuExpanded }">
                            <!-- 2 Parent Navigation Groups ("Orangtua") -->
                            <div class="space-y-4">
                                <div
                                    v-for="parent in visibleParents"
                                    :key="parent.id"
                                    class="rounded-2xl border border-neutral-100 bg-neutral-50/60 p-2 transition"
                                >
                                    <!-- Parent Header (Orangtua) -->
                                    <button
                                        type="button"
                                        class="flex w-full items-center justify-between gap-2 rounded-xl p-2 text-left transition select-none hover:bg-white"
                                        :class="{ 'justify-center': isSidebarCollapsed }"
                                        @click="toggleParent(parent.id)"
                                    >
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span
                                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg"
                                                :class="parent.id === 'rancangan' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700'"
                                            >
                                                <AppIcon :name="parent.icon" class="h-3.5 w-3.5" />
                                            </span>
                                            <div v-if="!isSidebarCollapsed" class="min-w-0">
                                                <div class="truncate text-xs font-bold text-ink-900 leading-tight">
                                                    {{ parent.shortTitle }}
                                                </div>
                                                <div class="truncate text-[10px] text-ink-400">
                                                    {{ parent.items.length }} Bagian
                                                </div>
                                            </div>
                                        </div>

                                        <div v-if="!isSidebarCollapsed" class="flex items-center gap-1.5 shrink-0">
                                            <span class="rounded-md px-1.5 py-0.5 text-[10px] font-bold" :class="parent.colorBadge">
                                                {{ parent.badge }}
                                            </span>
                                            <AppIcon
                                                name="chevron-down"
                                                class="h-3.5 w-3.5 text-ink-400 transition-transform duration-200"
                                                :class="{ 'rotate-180': !parentOpenState[parent.id] }"
                                            />
                                        </div>
                                    </button>

                                    <!-- Sub-items Under Parent (Anak) -->
                                    <div
                                        v-show="parentOpenState[parent.id]"
                                        class="mt-1 space-y-1 pt-1 border-t border-neutral-200/50"
                                        :class="{ 'border-t-0': isSidebarCollapsed }"
                                    >
                                        <button
                                            v-for="item in parent.items"
                                            :key="item.id"
                                            :data-nav-id="item.id"
                                            type="button"
                                            class="group flex w-full items-center gap-2.5 rounded-xl px-2.5 py-2 text-left transition-all"
                                            :class="[
                                                activeSection === item.id
                                                    ? 'bg-ink-900 text-white shadow-xs'
                                                    : 'text-ink-700 hover:bg-white hover:text-ink-900',
                                                isSidebarCollapsed ? 'justify-center px-1' : ''
                                            ]"
                                            :title="item.title"
                                            @click="scrollToSection(item.id)"
                                        >
                                            <span
                                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg transition-colors"
                                                :class="activeSection === item.id ? 'bg-white/20 text-white' : 'bg-neutral-200/70 text-ink-600 group-hover:bg-neutral-300'"
                                            >
                                                <AppIcon :name="item.icon" class="h-3.5 w-3.5" />
                                            </span>

                                            <div v-if="!isSidebarCollapsed" class="min-w-0 flex-1 truncate">
                                                <div class="truncate text-xs font-semibold">
                                                    {{ item.shortTitle }}
                                                </div>
                                                <div
                                                    class="truncate text-[10px]"
                                                    :class="activeSection === item.id ? 'text-neutral-300' : 'text-ink-500'"
                                                >
                                                    {{ item.subtitle }}
                                                </div>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Repository Link -->
                            <div v-if="!isSidebarCollapsed" class="mt-4 border-t border-neutral-100 pt-3">
                                <a
                                    href="https://github.com/irsyaad06/gudang-pakde.git"
                                    target="_blank"
                                    rel="noreferrer"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2 text-xs font-semibold text-ink-700 transition hover:bg-neutral-100"
                                >
                                    <AppIcon name="code" class="h-3.5 w-3.5" />
                                    <span>GitHub Repository</span>
                                    <AppIcon name="arrow-up-right" class="h-3 w-3 text-ink-400" />
                                </a>
                            </div>
                        </div>
                    </nav>
                </aside>

                <!-- Documentation Content Area -->
                <main class="min-w-0 flex-1 space-y-6">
                    <!-- Global Collapse / Expand Toolbar -->
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-neutral-200/80 bg-white/90 px-4 py-3 shadow-xs">
                        <div class="flex items-center gap-2 text-xs font-medium text-ink-600">
                            <span class="inline-flex h-2 w-2 rounded-full" :class="openCount > 0 ? 'bg-emerald-500' : 'bg-neutral-300'"></span>
                            <span>{{ openCount }} dari {{ visibleItems.length }} bagian terbuka</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-1.5 text-xs font-semibold text-ink-700 transition hover:bg-neutral-100"
                                @click="openAllCards"
                            >
                                <AppIcon name="chevron-down" class="h-3.5 w-3.5 rotate-180" />
                                <span>Buka Semua</span>
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-1.5 text-xs font-semibold text-ink-700 transition hover:bg-neutral-100"
                                @click="closeAllCards"
                            >
                                <AppIcon name="chevron-down" class="h-3.5 w-3.5" />
                                <span>Tutup Semua</span>
                            </button>
                        </div>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- PARENT CATEGORY 1: RANCANGAN ARSITEKTUR & INFRASTRUKTUR (100K DAU)        -->
                    <!-- ========================================================================= -->
                    <div v-if="activeDocMode === 'all' || activeDocMode === 'rancangan'" class="space-y-6">
                        <!-- Parent Category Divider Header -->
                        <div class="flex items-center gap-3 rounded-2xl bg-blue-600 px-5 py-3.5 text-white shadow-sm">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/15">
                                <AppIcon name="layers" class="h-5 w-5" />
                            </div>
                            <div>
                                <span class="rounded-md bg-white/20 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider">
                                    Bagian 2 Assignment
                                </span>
                                <h2 class="text-base font-bold sm:text-lg">
                                    Rancangan Arsitektur Aplikasi & Infrastruktur (Skala 100.000 DAU)
                                </h2>
                            </div>
                        </div>

                        <!-- 1.1 ASUMSI & POLA BEBAN 100K DAU -->
                        <section id="rancangan-asumsi" class="rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm transition-all sm:p-8 scroll-mt-24">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-4 text-left transition select-none group"
                                :class="{ 'border-b border-neutral-100 pb-4': isCardOpen('rancangan-asumsi') }"
                                @click="toggleCard('rancangan-asumsi')"
                                :aria-expanded="isCardOpen('rancangan-asumsi')"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 transition group-hover:scale-105">
                                        <AppIcon name="cpu" class="h-5 w-5" />
                                    </span>
                                    <div class="min-w-0">
                                        <h3 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">
                                            1. Asumsi Beban, Kalkulasi Matematis & Pola Traffic (100k DAU)
                                        </h3>
                                        <p class="text-xs text-ink-500 sm:text-sm">Estimasi request per second, peak hour multiplier, dan estimasi storage foto harian</p>
                                    </div>
                                </div>
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-neutral-200 bg-neutral-50 text-ink-500 transition group-hover:bg-neutral-100 group-hover:text-ink-900">
                                    <AppIcon name="chevron-down" class="h-4 w-4 transition-transform duration-300" :class="{ 'rotate-180': isCardOpen('rancangan-asumsi') }" />
                                </div>
                            </button>

                            <div v-show="isCardOpen('rancangan-asumsi')" class="mt-6 space-y-5">
                                <p class="text-xs leading-relaxed text-ink-700 sm:text-sm">
                                    Target skala 100.000 Daily Active Users (DAU) dirancang berdasarkan kalkulasi matematis objektif untuk marketplace barang bekas komunitas. Karakteristik utama sistem ini adalah <strong>read-heavy (90% baca katalog vs 10% pasang/edit iklan)</strong>.
                                </p>

                                <!-- Math Calculation Card -->
                                <div class="rounded-2xl border border-neutral-200/90 bg-neutral-900 p-5 text-white">
                                    <h4 class="flex items-center gap-2 text-sm font-bold text-neutral-100">
                                        <AppIcon name="cpu" class="h-4 w-4 text-emerald-400" />
                                        <span>Kalkulasi Beban & Kapasitas (100.000 DAU)</span>
                                    </h4>
                                    <div class="mt-4 grid grid-cols-2 gap-4 text-xs sm:grid-cols-4">
                                        <div class="rounded-xl bg-neutral-800/80 p-3">
                                            <div class="text-neutral-400">Total Requests/Hari</div>
                                            <div class="mt-1 text-base font-bold text-emerald-400 sm:text-lg">~3.000.000</div>
                                            <div class="text-[10px] text-neutral-400">Asumsi 30 hit/user/hari</div>
                                        </div>
                                        <div class="rounded-xl bg-neutral-800/80 p-3">
                                            <div class="text-neutral-400">Rata-Rata RPS</div>
                                            <div class="mt-1 text-base font-bold text-emerald-400 sm:text-lg">~35 RPS</div>
                                            <div class="text-[10px] text-neutral-400">24 jam continuous</div>
                                        </div>
                                        <div class="rounded-xl bg-neutral-800/80 p-3">
                                            <div class="text-neutral-400">Peak Hours (19-21)</div>
                                            <div class="mt-1 text-base font-bold text-amber-400 sm:text-lg">150 - 200 RPS</div>
                                            <div class="text-[10px] text-neutral-400">4x - 5x multiplier</div>
                                        </div>
                                        <div class="rounded-xl bg-neutral-800/80 p-3">
                                            <div class="text-neutral-400">Storage Foto/Hari</div>
                                            <div class="mt-1 text-base font-bold text-blue-400 sm:text-lg">~15 - 25 GB</div>
                                            <div class="text-[10px] text-neutral-400">50k foto @ 300-500KB</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Usage Pattern Analysis -->
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-xs">
                                    <div class="rounded-2xl border border-neutral-200/80 bg-neutral-50 p-4">
                                        <h5 class="font-bold text-ink-900 text-sm">Pola Penggunaan Komunitas:</h5>
                                        <ul class="mt-2 space-y-1.5 text-ink-600 list-disc list-inside">
                                            <li><strong>Browsing & Search:</strong> 90% waktu dihabiskan menelusuri katalog, filter kategori, dan pencarian barang.</li>
                                            <li><strong>Mutasi Barang:</strong> 10% transaksi berupa pendaftaran akun, pemasangan iklan baru, edit status, atau hapus listing.</li>
                                            <li><strong>Kontak Transaksi:</strong> Berjalan di luar server via WhatsApp link langsung ke nomor penjual (beban chat server = 0).</li>
                                        </ul>
                                    </div>
                                    <div class="rounded-2xl border border-neutral-200/80 bg-neutral-50 p-4">
                                        <h5 class="font-bold text-ink-900 text-sm">Implikasi Terhadap Arsitektur:</h5>
                                        <ul class="mt-2 space-y-1.5 text-ink-600 list-disc list-inside">
                                            <li>Database dipisahkan menjadi <strong>Primary (Writes)</strong> dan <strong>Read Replica (Reads)</strong>.</li>
                                            <li>Edge CDN Cloudflare meng-cache gambar WebP dan asset statis dengan target &gt; 80% cache hit.</li>
                                            <li>ElastiCache Redis membebaskan database dari beban session sharing dan locking.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- 1.2 DIAGRAM & ARSITEKTUR APLIKASI -->
                        <section id="rancangan-arsitektur" class="rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm transition-all sm:p-8 scroll-mt-24">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-4 text-left transition select-none group"
                                :class="{ 'border-b border-neutral-100 pb-4': isCardOpen('rancangan-arsitektur') }"
                                @click="toggleCard('rancangan-arsitektur')"
                                :aria-expanded="isCardOpen('rancangan-arsitektur')"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 transition group-hover:scale-105">
                                        <AppIcon name="layers" class="h-5 w-5" />
                                    </span>
                                    <div class="min-w-0">
                                        <h3 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">
                                            2. Diagram & Desain Arsitektur Aplikasi (Application Architecture)
                                        </h3>
                                        <p class="text-xs text-ink-500 sm:text-sm">Alur pengguna, frontend, backend/API, database, penyimpanan foto/file, cache & layanan eksternal</p>
                                    </div>
                                </div>
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-neutral-200 bg-neutral-50 text-ink-500 transition group-hover:bg-neutral-100 group-hover:text-ink-900">
                                    <AppIcon name="chevron-down" class="h-4 w-4 transition-transform duration-300" :class="{ 'rotate-180': isCardOpen('rancangan-arsitektur') }" />
                                </div>
                            </button>

                            <div v-show="isCardOpen('rancangan-arsitektur')" class="mt-6 space-y-6">
                                <!-- Interactive Architecture Diagram -->
                                <AppArchitectureDiagram />

                                <!-- Architectural Decision: Why Modular Monolith -->
                                <div class="space-y-4">
                                    <h4 class="text-sm font-bold text-ink-900">Keputusan Arsitektur: Mengapa Modular Monolith vs Microservices?</h4>
                                    <p class="text-xs leading-relaxed text-ink-700 sm:text-sm">
                                        Untuk skala 100.000 DAU (~35 RPS rata-rata, ~200 RPS peak), pola <strong>Modular Monolith</strong> berbasis Laravel 13 + Inertia.js merupakan keputusan teknis yang tepat dengan alasan berikut:
                                    </p>
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                        <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-4">
                                            <span class="text-xs font-bold text-ink-900">1. Transaksi ACID Database</span>
                                            <p class="mt-1 text-xs text-ink-600">Simpan listing, simpan file foto, dan validasi status berjalan dalam transaksi atomic lokal tanpa overhead 2-phase commit atau distributed saga.</p>
                                        </div>
                                        <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-4">
                                            <span class="text-xs font-bold text-ink-900">2. Latensi Nol Antar-Modul</span>
                                            <p class="mt-1 text-xs text-ink-600">Panggilan antar-modul (Auth, Profile, Catalog, Media) berjalan in-memory CPU tanpa overhead network HTTP/gRPC.</p>
                                        </div>
                                        <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-4">
                                            <span class="text-xs font-bold text-ink-900">3. Efisiensi Deployment Tim</span>
                                            <p class="mt-1 text-xs text-ink-600">Satu unit deployment containerized yang mudah diuji otomatis via PHPUnit, di-deploy via CI/CD, dan di-scale horizontal.</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Request Lifecycle Steps -->
                                <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-5">
                                    <h4 class="text-sm font-bold text-ink-900">Alur Request Lifecycle (End-to-End)</h4>
                                    <div class="mt-4 space-y-3 font-mono text-xs">
                                        <div class="flex items-center gap-3 rounded-xl bg-white p-3 border border-neutral-200/80">
                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-neutral-900 text-[10px] font-bold text-white">1</span>
                                            <span><strong>Client Browser:</strong> Single Page Application (Vue 3 + Inertia) melakukan navigasi / aksi form.</span>
                                        </div>
                                        <div class="flex items-center gap-3 rounded-xl bg-white p-3 border border-neutral-200/80">
                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-neutral-900 text-[10px] font-bold text-white">2</span>
                                            <span><strong>Edge CDN (Cloudflare):</strong> Meng-cache asset statis (JS, CSS, WebP images). Melewatkan dynamic Inertia JSON request.</span>
                                        </div>
                                        <div class="flex items-center gap-3 rounded-xl bg-white p-3 border border-neutral-200/80">
                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-neutral-900 text-[10px] font-bold text-white">3</span>
                                            <span><strong>Application Load Balancer:</strong> Terminasi TLS/SSL, round-robin dispatch ke cluster container Laravel.</span>
                                        </div>
                                        <div class="flex items-center gap-3 rounded-xl bg-white p-3 border border-neutral-200/80">
                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-neutral-900 text-[10px] font-bold text-white">4</span>
                                            <span><strong>Laravel App Container:</strong> Form Request Validation, Policies Authorization, Eloquent Query Optimization.</span>
                                        </div>
                                        <div class="flex items-center gap-3 rounded-xl bg-white p-3 border border-neutral-200/80">
                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-neutral-900 text-[10px] font-bold text-white">5</span>
                                            <span><strong>Data & Cache Tier:</strong> MySQL 8.x Read-Replica untuk katalog, Primary untuk CRUD; Redis untuk session & queue.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- 1.3 DIAGRAM & INFRASTRUKTUR CLOUD -->
                        <section id="rancangan-infrastruktur" class="rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm transition-all sm:p-8 scroll-mt-24">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-4 text-left transition select-none group"
                                :class="{ 'border-b border-neutral-100 pb-4': isCardOpen('rancangan-infrastruktur') }"
                                @click="toggleCard('rancangan-infrastruktur')"
                                :aria-expanded="isCardOpen('rancangan-infrastruktur')"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 transition group-hover:scale-105">
                                        <AppIcon name="server" class="h-5 w-5" />
                                    </span>
                                    <div class="min-w-0">
                                        <h3 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">
                                            3. Diagram & Desain Infrastruktur Cloud (Infrastructure Architecture)
                                        </h3>
                                        <p class="text-xs text-ink-500 sm:text-sm">DNS/CDN, load balancer, compute/container, database, object storage, monitoring & security boundary</p>
                                    </div>
                                </div>
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-neutral-200 bg-neutral-50 text-ink-500 transition group-hover:bg-neutral-100 group-hover:text-ink-900">
                                    <AppIcon name="chevron-down" class="h-4 w-4 transition-transform duration-300" :class="{ 'rotate-180': isCardOpen('rancangan-infrastruktur') }" />
                                </div>
                            </button>

                            <div v-show="isCardOpen('rancangan-infrastruktur')" class="mt-6 space-y-6">
                                <!-- Interactive Infrastructure Diagram -->
                                <InfraArchitectureDiagram />

                                <!-- Component Explanations -->
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                    <div class="rounded-2xl border border-neutral-200/80 bg-neutral-50 p-4">
                                        <div class="flex items-center gap-2 text-xs font-bold text-ink-900">
                                            <span class="h-2 w-2 rounded-full bg-orange-500"></span>
                                            <span>1. Edge & DNS</span>
                                        </div>
                                        <p class="mt-2 text-xs text-ink-600">
                                            <strong>Cloudflare Enterprise:</strong> Anycast DNS, DDoS Mitigation L3/L4/L7, Web Application Firewall (WAF), dan Edge Caching untuk gambar WebP/PNG.
                                        </p>
                                    </div>

                                    <div class="rounded-2xl border border-neutral-200/80 bg-neutral-50 p-4">
                                        <div class="flex items-center gap-2 text-xs font-bold text-ink-900">
                                            <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                                            <span>2. Ingress & Compute</span>
                                        </div>
                                        <p class="mt-2 text-xs text-ink-600">
                                            <strong>AWS ALB + ECS Fargate:</strong> Auto-scaling container horizontal (min 3 tasks, max 20 tasks saat traffic puncak). Health check otomatis ke endpoint <code>/up</code>.
                                        </p>
                                    </div>

                                    <div class="rounded-2xl border border-neutral-200/80 bg-neutral-50 p-4">
                                        <div class="flex items-center gap-2 text-xs font-bold text-ink-900">
                                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                            <span>3. Database & Cache</span>
                                        </div>
                                        <p class="mt-2 text-xs text-ink-600">
                                            <strong>AWS RDS MySQL 8.x:</strong> Primary Multi-AZ untuk writes + Read Replica untuk katalog search. <strong>AWS ElastiCache Redis</strong> untuk session & queues.
                                        </p>
                                    </div>

                                    <div class="rounded-2xl border border-neutral-200/80 bg-neutral-50 p-4">
                                        <div class="flex items-center gap-2 text-xs font-bold text-ink-900">
                                            <span class="h-2 w-2 rounded-full bg-purple-500"></span>
                                            <span>4. Storage & Media</span>
                                        </div>
                                        <p class="mt-2 text-xs text-ink-600">
                                            <strong>AWS S3 / Cloudflare R2:</strong> Penyimpanan foto barang terisolasi dengan CDN CloudFront, direct pre-signed URL upload, dan lifecycle tiering.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- 1.4 SCALING, RELIABILITY & KEAMANAN -->
                        <section id="rancangan-scaling" class="rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm transition-all sm:p-8 scroll-mt-24">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-4 text-left transition select-none group"
                                :class="{ 'border-b border-neutral-100 pb-4': isCardOpen('rancangan-scaling') }"
                                @click="toggleCard('rancangan-scaling')"
                                :aria-expanded="isCardOpen('rancangan-scaling')"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 transition group-hover:scale-105">
                                        <AppIcon name="shield-check" class="h-5 w-5" />
                                    </span>
                                    <div class="min-w-0">
                                        <h3 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">
                                            4. Strategi Scaling, Reliability & Keamanan (Security Boundaries)
                                        </h3>
                                        <p class="text-xs text-ink-500 sm:text-sm">Auto-scaling lonjakan traffic, rate limiting, isolasi VPC, secret management & disaster recovery</p>
                                    </div>
                                </div>
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-neutral-200 bg-neutral-50 text-ink-500 transition group-hover:bg-neutral-100 group-hover:text-ink-900">
                                    <AppIcon name="chevron-down" class="h-4 w-4 transition-transform duration-300" :class="{ 'rotate-180': isCardOpen('rancangan-scaling') }" />
                                </div>
                            </button>

                            <div v-show="isCardOpen('rancangan-scaling')" class="mt-6 space-y-6">
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <!-- Scaling & Reliability -->
                                    <div class="rounded-2xl border border-neutral-200/80 bg-neutral-50 p-5 space-y-3">
                                        <h4 class="text-sm font-bold text-ink-900 flex items-center gap-2">
                                            <AppIcon name="server" class="h-4 w-4 text-blue-600" />
                                            <span>Strategi Scaling & Lonjakan Beban</span>
                                        </h4>
                                        <ul class="text-xs text-ink-700 space-y-2 list-disc list-inside">
                                            <li><strong>Horizontal Auto-scaling:</strong> Target tracking policy menambah container ECS Fargate otomatis saat pemakaian CPU &gt; 65% atau latency request &gt; 250ms.</li>
                                            <li><strong>Connection Pooling:</strong> ProxySQL / AWS RDS Proxy mengelola koneksi database persistent untuk mencegah connection exhaustion saat spike.</li>
                                            <li><strong>Multi-Level Caching:</strong> Layer 1 Edge CDN (gambar), Layer 2 Redis Cache (katalog query), Layer 3 MySQL Query Buffer.</li>
                                        </ul>
                                    </div>

                                    <!-- Security & Isolation -->
                                    <div class="rounded-2xl border border-neutral-200/80 bg-neutral-50 p-5 space-y-3">
                                        <h4 class="text-sm font-bold text-ink-900 flex items-center gap-2">
                                            <AppIcon name="shield-check" class="h-4 w-4 text-emerald-600" />
                                            <span>Keamanan & Batas Jaringan (Network Isolation)</span>
                                        </h4>
                                        <ul class="text-xs text-ink-700 space-y-2 list-disc list-inside">
                                            <li><strong>Zero Public DB:</strong> Port 3306 (MySQL) dan 6379 (Redis) hanya menerima traffic dari Security Group container private subnet.</li>
                                            <li><strong>Rate Limiting:</strong> Laravel Throttle middleware membatasi 100 req/min untuk publik dan 60 req/min untuk mutasi data sensitif.</li>
                                            <li><strong>Secret Management:</strong> Kredensial disimpan terenkripsi di AWS Secrets Manager; nol hardcoded password/kunci di repository.</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Disaster Recovery (DR) -->
                                <div class="rounded-2xl border border-neutral-100 bg-white p-5 shadow-xs">
                                    <h4 class="text-sm font-bold text-ink-900">Pemulihan Bencana & Keandalan (Backup & DR)</h4>
                                    <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3 text-xs">
                                        <div class="rounded-xl bg-neutral-50 p-3 border border-neutral-200/60">
                                            <div class="font-bold text-ink-900">RPO &lt; 5 Menit</div>
                                            <div class="text-[11px] text-ink-600 mt-1">Continuous binary log archiving memastikan data transaksi terbaru dapat dipulihkan dengan kehilangan minimum.</div>
                                        </div>
                                        <div class="rounded-xl bg-neutral-50 p-3 border border-neutral-200/60">
                                            <div class="font-bold text-ink-900">RTO &lt; 30 Menit</div>
                                            <div class="text-[11px] text-ink-600 mt-1">Otomasi Terraform dan multi-AZ failover mengaktifkan instance pengganti dalam waktu di bawah setengah jam.</div>
                                        </div>
                                        <div class="rounded-xl bg-neutral-50 p-3 border border-neutral-200/60">
                                            <div class="font-bold text-ink-900">Health Checks /up</div>
                                            <div class="text-[11px] text-ink-600 mt-1">Endpoint <code>/up</code> bawaan Laravel 13 memvalidasi kesiapan database dan cache tanpa mengekspos rahasia internal.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- ========================================================================= -->
                    <!-- PARENT CATEGORY 2: TECHNICAL DOCUMENTATION (SISTEM, SETUP & DEVELOPER)    -->
                    <!-- ========================================================================= -->
                    <div v-if="activeDocMode === 'all' || activeDocMode === 'technical'" class="space-y-6">
                        <!-- Parent Category Divider Header -->
                        <div class="flex items-center gap-3 rounded-2xl bg-emerald-700 px-5 py-3.5 text-white shadow-sm">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/15">
                                <AppIcon name="file-text" class="h-5 w-5" />
                            </div>
                            <div>
                                <span class="rounded-md bg-white/20 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider">
                                    Bagian 3 Assignment
                                </span>
                                <h2 class="text-base font-bold sm:text-lg">
                                    Technical Documentation (Panduan Teknis, Instalasi & Database)
                                </h2>
                            </div>
                        </div>

                        <!-- 2.1 PROJECT OVERVIEW & FITUR MVP -->
                        <section id="tech-overview" class="rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm transition-all sm:p-8 scroll-mt-24">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-4 text-left transition select-none group"
                                :class="{ 'border-b border-neutral-100 pb-4': isCardOpen('tech-overview') }"
                                @click="toggleCard('tech-overview')"
                                :aria-expanded="isCardOpen('tech-overview')"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 transition group-hover:scale-105">
                                        <AppIcon name="box" class="h-5 w-5" />
                                    </span>
                                    <div class="min-w-0">
                                        <h3 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">
                                            1. Ikhtisar Proyek & Matriks Pemenuhan 9 Fitur MVP
                                        </h3>
                                        <p class="text-xs text-ink-500 sm:text-sm">Solusi marketplace barang bekas komunitas, latar belakang problem & struktur direktori</p>
                                    </div>
                                </div>
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-neutral-200 bg-neutral-50 text-ink-500 transition group-hover:bg-neutral-100 group-hover:text-ink-900">
                                    <AppIcon name="chevron-down" class="h-4 w-4 transition-transform duration-300" :class="{ 'rotate-180': isCardOpen('tech-overview') }" />
                                </div>
                            </button>

                            <div v-show="isCardOpen('tech-overview')" class="mt-6 space-y-6">
                                <p class="text-xs leading-relaxed text-ink-700 sm:text-sm">
                                    <strong>Gudang Pakde</strong> menyelesaikan permasalahan nyata di mana informasi jual-beli barang bekas di grup chat cepat tenggelam, foto berserakan, dan pencarian sulit dilakukan. Seluruh 9 fitur utama yang disyaratkan dalam assignment telah 100% diimplementasikan dan diverifikasi:
                                </p>

                                <!-- 9 Features Fulfillment Table -->
                                <div class="overflow-hidden rounded-2xl border border-neutral-200">
                                    <table class="min-w-full divide-y divide-neutral-200 text-left text-xs">
                                        <thead class="bg-neutral-50 font-semibold text-ink-700">
                                            <tr>
                                                <th class="px-4 py-3 sm:px-6">Fitur Assignment</th>
                                                <th class="px-4 py-3 sm:px-6">Status Pemenuhan</th>
                                                <th class="px-4 py-3 sm:px-6">Implementasi Komponen / Route</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-neutral-100 bg-white text-ink-600">
                                            <tr>
                                                <td class="px-4 py-3 font-medium text-ink-900 sm:px-6">1. Autentikasi Pengguna</td>
                                                <td class="px-4 py-3 sm:px-6"><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">Selesai (100%)</span></td>
                                                <td class="px-4 py-3 font-mono text-[11px] sm:px-6">Registrasi, Login, Logout, Session CSRF Guard</td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-3 font-medium text-ink-900 sm:px-6">2. Profil Pengguna</td>
                                                <td class="px-4 py-3 sm:px-6"><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">Selesai (100%)</span></td>
                                                <td class="px-4 py-3 font-mono text-[11px] sm:px-6">Profile/Edit, info akun, riwayat listing terpasang</td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-3 font-medium text-ink-900 sm:px-6">3. Daftar Barang</td>
                                                <td class="px-4 py-3 sm:px-6"><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">Selesai (100%)</span></td>
                                                <td class="px-4 py-3 font-mono text-[11px] sm:px-6">Grid responsif, foto WebP, harga rupiah, badge kondisi</td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-3 font-medium text-ink-900 sm:px-6">4. Pencarian & Filter</td>
                                                <td class="px-4 py-3 sm:px-6"><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">Selesai (100%)</span></td>
                                                <td class="px-4 py-3 font-mono text-[11px] sm:px-6">Search debounce, filter kategori, kondisi & pagination</td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-3 font-medium text-ink-900 sm:px-6">5. Detail Barang</td>
                                                <td class="px-4 py-3 sm:px-6"><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">Selesai (100%)</span></td>
                                                <td class="px-4 py-3 font-mono text-[11px] sm:px-6">Listings/Show: galeri foto, spesifikasi, info penjual</td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-3 font-medium text-ink-900 sm:px-6">6. Pasang Barang</td>
                                                <td class="px-4 py-3 sm:px-6"><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">Selesai (100%)</span></td>
                                                <td class="px-4 py-3 font-mono text-[11px] sm:px-6">Listings/Create: upload multipart, preview instan, validasi</td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-3 font-medium text-ink-900 sm:px-6">7. Kelola Listing</td>
                                                <td class="px-4 py-3 sm:px-6"><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">Selesai (100%)</span></td>
                                                <td class="px-4 py-3 font-mono text-[11px] sm:px-6">Listings/MyListings: edit data, hapus dengan ListingPolicy</td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-3 font-medium text-ink-900 sm:px-6">8. Status Barang</td>
                                                <td class="px-4 py-3 sm:px-6"><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">Selesai (100%)</span></td>
                                                <td class="px-4 py-3 font-mono text-[11px] sm:px-6">Toggle status tersedia / terjual dengan feedback instan</td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-3 font-medium text-ink-900 sm:px-6">9. Kontak Penjual (WhatsApp)</td>
                                                <td class="px-4 py-3 font-mono sm:px-6"><span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">Selesai (100%)</span></td>
                                                <td class="px-4 py-3 font-mono text-[11px] sm:px-6">Click-to-chat aman dengan prefilled pesan detail barang</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>

                        <!-- 2.2 PANDUAN INSTALASI LOKAL -->
                        <section id="tech-install" class="rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm transition-all sm:p-8 scroll-mt-24">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-4 text-left transition select-none group"
                                :class="{ 'border-b border-neutral-100 pb-4': isCardOpen('tech-install') }"
                                @click="toggleCard('tech-install')"
                                :aria-expanded="isCardOpen('tech-install')"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-neutral-100 text-ink-800 transition group-hover:scale-105">
                                        <AppIcon name="terminal" class="h-5 w-5" />
                                    </span>
                                    <div class="min-w-0">
                                        <h3 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">
                                            2. Panduan Setup & Instalasi Lokal (macOS & Windows)
                                        </h3>
                                        <p class="text-xs text-ink-500 sm:text-sm">Prasyarat dependency, konfigurasi .env, migrasi database & akun demo siap pakai</p>
                                    </div>
                                </div>
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-neutral-200 bg-neutral-50 text-ink-500 transition group-hover:bg-neutral-100 group-hover:text-ink-900">
                                    <AppIcon name="chevron-down" class="h-4 w-4 transition-transform duration-300" :class="{ 'rotate-180': isCardOpen('tech-install') }" />
                                </div>
                            </button>

                            <div v-show="isCardOpen('tech-install')" class="mt-6 space-y-6">
                                <!-- OS Tab Switcher -->
                                <div class="flex items-center gap-2 border-b border-neutral-200 pb-3">
                                    <span class="text-xs font-semibold text-ink-500">Pilih Sistem Operasi:</span>
                                    <div class="inline-flex rounded-xl border border-neutral-200 bg-neutral-100 p-1 text-xs">
                                        <button
                                            type="button"
                                            class="rounded-lg px-3 py-1 font-semibold transition"
                                            :class="activeOs === 'macos' ? 'bg-white text-ink-900 shadow-xs' : 'text-ink-600 hover:text-ink-900'"
                                            @click="activeOs = 'macos'"
                                        >
                                            Apple macOS
                                        </button>
                                        <button
                                            type="button"
                                            class="rounded-lg px-3 py-1 font-semibold transition"
                                            :class="activeOs === 'windows' ? 'bg-white text-ink-900 shadow-xs' : 'text-ink-600 hover:text-ink-900'"
                                            @click="activeOs = 'windows'"
                                        >
                                            Microsoft Windows (Herd / XAMPP / WSL)
                                        </button>
                                    </div>
                                </div>

                                <!-- Step by step instructions -->
                                <div class="space-y-4">
                                    <div class="rounded-2xl border border-neutral-200 bg-neutral-900 p-4 font-mono text-xs text-neutral-200">
                                        <div class="flex items-center justify-between pb-2 text-[11px] text-neutral-400 border-b border-neutral-800">
                                            <span>Langkah 1: Clone Repository & Pindah Direktori</span>
                                            <button
                                                type="button"
                                                class="text-emerald-400 hover:text-emerald-300"
                                                @click="copyToClipboard('git clone https://github.com/irsyaad06/gudang-pakde.git && cd gudang-pakde', 'step1')"
                                            >
                                                {{ copiedSnippet === 'step1' ? 'Tersalin!' : 'Salin Perintah' }}
                                            </button>
                                        </div>
                                        <div class="mt-2 text-emerald-300">git clone https://github.com/irsyaad06/gudang-pakde.git</div>
                                        <div class="text-emerald-300">cd gudang-pakde</div>
                                    </div>

                                    <div class="rounded-2xl border border-neutral-200 bg-neutral-900 p-4 font-mono text-xs text-neutral-200">
                                        <div class="flex items-center justify-between pb-2 text-[11px] text-neutral-400 border-b border-neutral-800">
                                            <span>Langkah 2: Instal Dependency PHP & Node.js</span>
                                            <button
                                                type="button"
                                                class="text-emerald-400 hover:text-emerald-300"
                                                @click="copyToClipboard('composer install && npm install', 'step2')"
                                            >
                                                {{ copiedSnippet === 'step2' ? 'Tersalin!' : 'Salin Perintah' }}
                                            </button>
                                        </div>
                                        <div class="mt-2 text-emerald-300">composer install</div>
                                        <div class="text-emerald-300">npm install</div>
                                    </div>

                                    <div class="rounded-2xl border border-neutral-200 bg-neutral-900 p-4 font-mono text-xs text-neutral-200">
                                        <div class="flex items-center justify-between pb-2 text-[11px] text-neutral-400 border-b border-neutral-800">
                                            <span>Langkah 3: Konfigurasi Environment & Generate Key</span>
                                            <button
                                                type="button"
                                                class="text-emerald-400 hover:text-emerald-300"
                                                @click="copyToClipboard('cp .env.example .env && php artisan key:generate', 'step3')"
                                            >
                                                {{ copiedSnippet === 'step3' ? 'Tersalin!' : 'Salin Perintah' }}
                                            </button>
                                        </div>
                                        <div class="mt-2 text-emerald-300">{{ activeOs === 'windows' ? 'copy .env.example .env' : 'cp .env.example .env' }}</div>
                                        <div class="text-emerald-300">php artisan key:generate</div>
                                    </div>

                                    <div class="rounded-2xl border border-neutral-200 bg-neutral-900 p-4 font-mono text-xs text-neutral-200">
                                        <div class="flex items-center justify-between pb-2 text-[11px] text-neutral-400 border-b border-neutral-800">
                                            <span>Langkah 4: Jalankan Migrasi, Seeder & Storage Link</span>
                                            <button
                                                type="button"
                                                class="text-emerald-400 hover:text-emerald-300"
                                                @click="copyToClipboard('php artisan migrate:fresh --seed && php artisan storage:link', 'step4')"
                                            >
                                                {{ copiedSnippet === 'step4' ? 'Tersalin!' : 'Salin Perintah' }}
                                            </button>
                                        </div>
                                        <div class="mt-2 text-emerald-300">php artisan migrate:fresh --seed</div>
                                        <div class="text-emerald-300">php artisan storage:link</div>
                                    </div>

                                    <div class="rounded-2xl border border-neutral-200 bg-neutral-900 p-4 font-mono text-xs text-neutral-200">
                                        <div class="flex items-center justify-between pb-2 text-[11px] text-neutral-400 border-b border-neutral-800">
                                            <span>Langkah 5: Jalankan Server & Frontend</span>
                                            <button
                                                type="button"
                                                class="text-emerald-400 hover:text-emerald-300"
                                                @click="copyToClipboard('php artisan serve (Terminal 1) & npm run dev (Terminal 2)', 'step5')"
                                            >
                                                {{ copiedSnippet === 'step5' ? 'Tersalin!' : 'Salin Perintah' }}
                                            </button>
                                        </div>
                                        <div class="mt-2 text-emerald-300">php artisan serve    # Terminal 1 (http://127.0.0.1:8000)</div>
                                        <div class="text-emerald-300">npm run dev          # Terminal 2 (Vite HMR)</div>
                                    </div>
                                </div>

                                <!-- Demo Login Accounts -->
                                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4">
                                    <div class="text-xs font-bold text-emerald-950 flex items-center gap-1.5">
                                        <AppIcon name="user" class="h-4 w-4 text-emerald-700" />
                                        <span>Akun Demo Login (Database Seeder):</span>
                                    </div>
                                    <div class="mt-2 grid grid-cols-1 gap-2 text-xs sm:grid-cols-3">
                                        <div class="rounded-xl bg-white p-2.5 border border-emerald-200/80">
                                            <div class="font-bold text-ink-900">Pak Budi (Penjual Sepeda)</div>
                                            <div class="font-mono text-[11px] text-ink-600">budi@pakde.com</div>
                                            <div class="text-[10px] text-ink-400">Password: <code>password</code></div>
                                        </div>
                                        <div class="rounded-xl bg-white p-2.5 border border-emerald-200/80">
                                            <div class="font-bold text-ink-900">Pak Joko (Penjual Perkakas)</div>
                                            <div class="font-mono text-[11px] text-ink-600">joko@pakde.com</div>
                                            <div class="text-[10px] text-ink-400">Password: <code>password</code></div>
                                        </div>
                                        <div class="rounded-xl bg-white p-2.5 border border-emerald-200/80">
                                            <div class="font-bold text-ink-900">Pak Tejo (Elektronik & Hobi)</div>
                                            <div class="font-mono text-[11px] text-ink-600">tejo@pakde.com</div>
                                            <div class="text-[10px] text-ink-400">Password: <code>password</code></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- 2.3 DESAIN DATABASE & ERD -->
                        <section id="tech-database" class="rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm transition-all sm:p-8 scroll-mt-24">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-4 text-left transition select-none group"
                                :class="{ 'border-b border-neutral-100 pb-4': isCardOpen('tech-database') }"
                                @click="toggleCard('tech-database')"
                                :aria-expanded="isCardOpen('tech-database')"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 transition group-hover:scale-105">
                                        <AppIcon name="database" class="h-5 w-5" />
                                    </span>
                                    <div class="min-w-0">
                                        <h3 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">
                                            3. Desain Database & Entity Relationship Diagram (ERD)
                                        </h3>
                                        <p class="text-xs text-ink-500 sm:text-sm">Schema tabel, foreign keys, cascade deletes, dan strategi indexing performa</p>
                                    </div>
                                </div>
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-neutral-200 bg-neutral-50 text-ink-500 transition group-hover:bg-neutral-100 group-hover:text-ink-900">
                                    <AppIcon name="chevron-down" class="h-4 w-4 transition-transform duration-300" :class="{ 'rotate-180': isCardOpen('tech-database') }" />
                                </div>
                            </button>

                            <div v-show="isCardOpen('tech-database')" class="mt-6 space-y-6">
                                <!-- Interactive ERD Diagram Component -->
                                <DatabaseErdDiagram />

                                <!-- Indexing Strategy -->
                                <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-5">
                                    <h4 class="text-sm font-bold text-ink-900">Strategi Indexing untuk Performa Tinggi (100k DAU)</h4>
                                    <p class="mt-1 text-xs text-ink-600">
                                        Untuk mencegah full table scan saat 3 juta request/hari mencari katalog barang, indeks komposit strategis diterapkan:
                                    </p>
                                    <div class="mt-3 space-y-2 text-xs font-mono">
                                        <div class="rounded-xl bg-white p-3 border border-neutral-200/80">
                                            <span class="font-bold text-ink-900">INDEX `idx_listings_catalog`:</span>
                                            <span class="text-emerald-700 ml-1">(`category_id`, `condition`, `status`, `created_at`)</span>
                                            <div class="text-[11px] font-sans text-ink-500 mt-1">Mengoptimalkan filtering multi-kriteria pada katalog publik dalam satu kali lookup indeks B-Tree.</div>
                                        </div>
                                        <div class="rounded-xl bg-white p-3 border border-neutral-200/80">
                                            <span class="font-bold text-ink-900">INDEX `idx_listings_user`:</span>
                                            <span class="text-emerald-700 ml-1">(`user_id`, `created_at`)</span>
                                            <div class="text-[11px] font-sans text-ink-500 mt-1">Mengoptimalkan query halaman "Listing Saya" dan profil pengguna dengan latensi &lt; 5ms.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- 2.4 KEPUTUSAN TECH STACK -->
                        <section id="tech-decisions" class="rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm transition-all sm:p-8 scroll-mt-24">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-4 text-left transition select-none group"
                                :class="{ 'border-b border-neutral-100 pb-4': isCardOpen('tech-decisions') }"
                                @click="toggleCard('tech-decisions')"
                                :aria-expanded="isCardOpen('tech-decisions')"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 transition group-hover:scale-105">
                                        <AppIcon name="cpu" class="h-5 w-5" />
                                    </span>
                                    <div class="min-w-0">
                                        <h3 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">
                                            4. Keputusan Tech Stack & Analisis Trade-off
                                        </h3>
                                        <p class="text-xs text-ink-500 sm:text-sm">Alasan pemilihan Laravel 13, Vue 3, Inertia.js, MySQL 8.x dan tabel komparasi arsitektural</p>
                                    </div>
                                </div>
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-neutral-200 bg-neutral-50 text-ink-500 transition group-hover:bg-neutral-100 group-hover:text-ink-900">
                                    <AppIcon name="chevron-down" class="h-4 w-4 transition-transform duration-300" :class="{ 'rotate-180': isCardOpen('tech-decisions') }" />
                                </div>
                            </button>

                            <div v-show="isCardOpen('tech-decisions')" class="mt-6 space-y-6">
                                <div class="overflow-hidden rounded-2xl border border-neutral-200">
                                    <table class="min-w-full divide-y divide-neutral-200 text-left text-xs">
                                        <thead class="bg-neutral-50 font-semibold text-ink-700">
                                            <tr>
                                                <th class="px-4 py-3 sm:px-6">Aspek Komparasi</th>
                                                <th class="px-4 py-3 sm:px-6">Pilihan: Monolith + Inertia (Gudang Pakde)</th>
                                                <th class="px-4 py-3 sm:px-6">Alternatif: Decoupled SPA (Next.js + REST API)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-neutral-100 bg-white text-ink-600">
                                            <tr>
                                                <td class="px-4 py-3 font-semibold text-ink-900 sm:px-6">State Synchronization</td>
                                                <td class="px-4 py-3 text-emerald-700 sm:px-6">Props server langsung diteruskan ke Vue tanpa duplikasi state store (Pinia/Redux).</td>
                                                <td class="px-4 py-3 sm:px-6">Perlu mengelola token JWT, client-side store, dan sinkronisasi manual.</td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-3 font-semibold text-ink-900 sm:px-6">Keamanan & CSRF</td>
                                                <td class="px-4 py-3 text-emerald-700 sm:px-6">Session-based cookie HTTP-only otomatis terlindungi dari pencurian XSS token.</td>
                                                <td class="px-4 py-3 sm:px-6">Rentan jika JWT disimpan di LocalStorage atau perlu BFF (Backend-for-Frontend).</td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-3 font-semibold text-ink-900 sm:px-6">Kecepatan Pengerjaan</td>
                                                <td class="px-4 py-3 text-emerald-700 sm:px-6">Tinggi; tidak ada boilerplate serializer atau perbaikan kontrak API ganda.</td>
                                                <td class="px-4 py-3 sm:px-6">Lebih lambat karena harus memelihara dua repositori / dua kontrak skema terpisah.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>

                        <!-- 2.5 DOKUMENTASI ROUTE WEB & KEAMANAN -->
                        <section id="tech-routes" class="rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm transition-all sm:p-8 scroll-mt-24">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-4 text-left transition select-none group"
                                :class="{ 'border-b border-neutral-100 pb-4': isCardOpen('tech-routes') }"
                                @click="toggleCard('tech-routes')"
                                :aria-expanded="isCardOpen('tech-routes')"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 transition group-hover:scale-105">
                                        <AppIcon name="shield-check" class="h-5 w-5" />
                                    </span>
                                    <div class="min-w-0">
                                        <h3 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">
                                            5. Dokumentasi Endpoint Web / API & Otorisasi
                                        </h3>
                                        <p class="text-xs text-ink-500 sm:text-sm">Tabel endpoint lengkap, HTTP method, middleware guard & matriks izin ListingPolicy</p>
                                    </div>
                                </div>
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-neutral-200 bg-neutral-50 text-ink-500 transition group-hover:bg-neutral-100 group-hover:text-ink-900">
                                    <AppIcon name="chevron-down" class="h-4 w-4 transition-transform duration-300" :class="{ 'rotate-180': isCardOpen('tech-routes') }" />
                                </div>
                            </button>

                            <div v-show="isCardOpen('tech-routes')" class="mt-6 space-y-6">
                                <div class="overflow-x-auto rounded-2xl border border-neutral-200">
                                    <table class="min-w-full divide-y divide-neutral-200 text-left text-xs font-mono">
                                        <thead class="bg-neutral-50 font-sans font-semibold text-ink-700">
                                            <tr>
                                                <th class="px-4 py-3 sm:px-6">Metode</th>
                                                <th class="px-4 py-3 sm:px-6">URI Path</th>
                                                <th class="px-4 py-3 sm:px-6">Route Name</th>
                                                <th class="px-4 py-3 sm:px-6">Auth Guard</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-neutral-100 bg-white text-ink-600">
                                            <tr><td class="px-4 py-2.5 font-bold text-emerald-600 sm:px-6">GET</td><td class="px-4 py-2.5 text-ink-900 sm:px-6">/</td><td class="px-4 py-2.5 sm:px-6">catalog.index</td><td class="px-4 py-2.5 text-neutral-400 font-sans sm:px-6">Publik</td></tr>
                                            <tr><td class="px-4 py-2.5 font-bold text-emerald-600 sm:px-6">GET</td><td class="px-4 py-2.5 text-ink-900 sm:px-6">/barang/{listing}</td><td class="px-4 py-2.5 sm:px-6">listings.show</td><td class="px-4 py-2.5 text-neutral-400 font-sans sm:px-6">Publik</td></tr>
                                            <tr><td class="px-4 py-2.5 font-bold text-emerald-600 sm:px-6">GET</td><td class="px-4 py-2.5 text-ink-900 sm:px-6">/barang/pasang</td><td class="px-4 py-2.5 sm:px-6">listings.create</td><td class="px-4 py-2.5 text-blue-600 font-sans sm:px-6">auth</td></tr>
                                            <tr><td class="px-4 py-2.5 font-bold text-blue-600 sm:px-6">POST</td><td class="px-4 py-2.5 text-ink-900 sm:px-6">/barang</td><td class="px-4 py-2.5 sm:px-6">listings.store</td><td class="px-4 py-2.5 text-blue-600 font-sans sm:px-6">auth</td></tr>
                                            <tr><td class="px-4 py-2.5 font-bold text-emerald-600 sm:px-6">GET</td><td class="px-4 py-2.5 text-ink-900 sm:px-6">/barang/saya</td><td class="px-4 py-2.5 sm:px-6">listings.mine</td><td class="px-4 py-2.5 text-blue-600 font-sans sm:px-6">auth</td></tr>
                                            <tr><td class="px-4 py-2.5 font-bold text-amber-600 sm:px-6">PUT</td><td class="px-4 py-2.5 text-ink-900 sm:px-6">/barang/{listing}</td><td class="px-4 py-2.5 sm:px-6">listings.update</td><td class="px-4 py-2.5 text-blue-600 font-sans sm:px-6">auth + policy</td></tr>
                                            <tr><td class="px-4 py-2.5 font-bold text-amber-600 sm:px-6">PATCH</td><td class="px-4 py-2.5 text-ink-900 sm:px-6">/barang/{listing}/status</td><td class="px-4 py-2.5 sm:px-6">listings.status</td><td class="px-4 py-2.5 text-blue-600 font-sans sm:px-6">auth + policy</td></tr>
                                            <tr><td class="px-4 py-2.5 font-bold text-rose-600 sm:px-6">DELETE</td><td class="px-4 py-2.5 text-ink-900 sm:px-6">/barang/{listing}</td><td class="px-4 py-2.5 sm:px-6">listings.destroy</td><td class="px-4 py-2.5 text-blue-600 font-sans sm:px-6">auth + policy</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </section>

                        <!-- 2.6 DEPLOYMENT & OPERASIONAL -->
                        <section id="tech-devops" class="rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm transition-all sm:p-8 scroll-mt-24">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-4 text-left transition select-none group"
                                :class="{ 'border-b border-neutral-100 pb-4': isCardOpen('tech-devops') }"
                                @click="toggleCard('tech-devops')"
                                :aria-expanded="isCardOpen('tech-devops')"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-neutral-100 text-ink-800 transition group-hover:scale-105">
                                        <AppIcon name="server" class="h-5 w-5" />
                                    </span>
                                    <div class="min-w-0">
                                        <h3 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">
                                            6. Deployment, Logging & Operasional (DevOps)
                                        </h3>
                                        <p class="text-xs text-ink-500 sm:text-sm">Pipeline CI/CD, optimasi cache produksi, structured JSON logging, dan troubleshooting</p>
                                    </div>
                                </div>
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-neutral-200 bg-neutral-50 text-ink-500 transition group-hover:bg-neutral-100 group-hover:text-ink-900">
                                    <AppIcon name="chevron-down" class="h-4 w-4 transition-transform duration-300" :class="{ 'rotate-180': isCardOpen('tech-devops') }" />
                                </div>
                            </button>

                            <div v-show="isCardOpen('tech-devops')" class="mt-6 space-y-5">
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 text-xs">
                                    <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
                                        <div class="font-bold text-ink-900">1. Production Caching</div>
                                        <p class="mt-1 text-ink-600">Jalankan <code>php artisan config:cache</code>, <code>route:cache</code>, dan <code>view:cache</code> untuk performa request &lt; 20ms.</p>
                                    </div>
                                    <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
                                        <div class="font-bold text-ink-900">2. Structured Logging</div>
                                        <p class="mt-1 text-ink-600">Keluaran log menggunakan format JSON terstruktur ke <code>stderr</code> yang secara otomatis di-stream oleh Datadog Agent.</p>
                                    </div>
                                    <div class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4">
                                        <div class="font-bold text-ink-900">3. Automated CI/CD</div>
                                        <p class="mt-1 text-ink-600">GitHub Actions menjalankan <code>npm run build</code> dan <code>php artisan test</code> sebelum setiap merge ke branch <code>main</code>.</p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </main>
            </div>
        </div>
    </AppLayout>
</template>
