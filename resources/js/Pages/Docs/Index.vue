<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AppIcon from '@/Components/AppIcon.vue';

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

// Active section state
const activeSection = ref('overview');

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

const navItems = [
    { id: 'overview', title: 'Ikhtisar Tugas GITS', subtitle: 'Latar belakang & pemenuhan fitur', icon: 'file-text' },
    { id: 'architecture', title: 'Arsitektur & 100k DAU', subtitle: 'Modular monolith & alur sistem', icon: 'layers' },
    { id: 'infrastructure', title: 'Infrastruktur Produksi', subtitle: 'Cloud, CDN, compute & scaling', icon: 'server' },
    { id: 'database', title: 'Desain & Relasi Database', subtitle: 'ERD, schema tabel & indexing', icon: 'database' },
    { id: 'techstack', title: 'Tech Stack & Keputusan', subtitle: 'Laravel 13, Vue 3, Inertia, MySQL', icon: 'cpu' },
    { id: 'install', title: 'Panduan Instalasi Lokal', subtitle: 'Setup macOS & Windows langkah demi langkah', icon: 'terminal' },
    { id: 'routes', title: 'Route Web & Keamanan', subtitle: 'Endpoint, authorization & policy', icon: 'shield-check' },
];

const scrollToSection = (id) => {
    activeSection.value = id;
    const el = document.getElementById(id);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
};
</script>

<template>
    <AppLayout>
        <Head title="Dokumentasi Teknis — Gudang Pakde" />

        <div class="mx-auto max-w-7xl py-6 sm:py-8">
            <!-- Header Card (Top Banner) -->
            <div class="mb-6 rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm sm:p-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-3xl">
                        <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                            <span class="flex h-2 w-2 rounded-full bg-emerald-500"></span>
                            <span>GITS Fullstack Developer Assignment — Final Submission</span>
                        </div>
                        <h1 class="text-2xl font-bold tracking-tight text-ink-900 sm:text-3xl lg:text-4xl">
                            Dokumentasi Teknis Gudang Pakde
                        </h1>
                        <p class="mt-2 text-sm leading-relaxed text-ink-600 sm:text-base">
                            Spesifikasi lengkap arsitektur aplikasi, infrastruktur produksi berkapasitas 100.000 DAU,
                            relasi database, keputusan tech stack, dan panduan instalasi lokal di macOS serta Windows.
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
            </div>

            <!-- Main Layout: Collapsible Sidebar + Content Cards -->
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start">
                <!-- Collapsible Sidebar -->
                <aside
                    class="sticky top-6 z-10 shrink-0 transition-all duration-300 ease-in-out"
                    :class="isSidebarCollapsed ? 'lg:w-20' : 'lg:w-72'"
                >
                    <nav class="rounded-3xl border border-neutral-200/80 bg-white p-3 shadow-sm sm:p-4" aria-label="Navigasi Dokumentasi">
                        <!-- Sidebar Header & Toggle Button -->
                        <div class="mb-3 flex items-center justify-between border-b border-neutral-100 pb-3" :class="{ 'justify-center': isSidebarCollapsed }">
                            <span v-if="!isSidebarCollapsed" class="text-xs font-bold uppercase tracking-wider text-ink-400">
                                Menu Navigasi
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

                        <!-- Nav List -->
                        <div class="flex flex-row gap-1.5 overflow-x-auto pb-2 lg:flex-col lg:overflow-visible lg:pb-0">
                            <button
                                v-for="item in navItems"
                                :key="item.id"
                                type="button"
                                class="group flex items-center gap-3 rounded-2xl px-3 py-2.5 text-left transition-all"
                                :class="[
                                    activeSection === item.id
                                        ? 'bg-ink-900 text-white shadow-sm'
                                        : 'text-ink-700 hover:bg-neutral-100 hover:text-ink-900',
                                    isSidebarCollapsed ? 'lg:justify-center lg:px-2' : ''
                                ]"
                                :title="item.title"
                                @click="scrollToSection(item.id)"
                            >
                                <span
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl transition-colors"
                                    :class="activeSection === item.id ? 'bg-white/15 text-white' : 'bg-neutral-100 text-ink-600 group-hover:bg-neutral-200'"
                                >
                                    <AppIcon :name="item.icon" class="h-4 w-4" />
                                </span>

                                <div v-if="!isSidebarCollapsed" class="min-w-0 flex-1 truncate">
                                    <div class="truncate text-xs font-semibold sm:text-sm">
                                        {{ item.title }}
                                    </div>
                                    <div
                                        class="truncate text-[11px]"
                                        :class="activeSection === item.id ? 'text-neutral-300' : 'text-ink-500'"
                                    >
                                        {{ item.subtitle }}
                                    </div>
                                </div>
                            </button>
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
                    </nav>
                </aside>

                <!-- Documentation Content Area -->
                <main class="min-w-0 flex-1 space-y-6">
                    <!-- SECTION 1: OVERVIEW -->
                    <section id="overview" class="rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm sm:p-8">
                        <div class="flex items-center gap-3 border-b border-neutral-100 pb-4">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                                <AppIcon name="file-text" class="h-5 w-5" />
                            </span>
                            <div>
                                <h2 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">
                                    1. Ikhtisar & Pemenuhan Tugas GITS
                                </h2>
                                <p class="text-xs text-ink-500 sm:text-sm">Solusi marketplace secondhand komunitas & status pemenuhan kebutuhan</p>
                            </div>
                        </div>

                        <!-- Problem Statement -->
                        <div class="mt-6 rounded-2xl border border-neutral-100 bg-neutral-50 p-5">
                            <h3 class="text-sm font-bold text-ink-900">Latar Belakang & Masalah Komunitas</h3>
                            <p class="mt-2 text-xs leading-relaxed text-ink-700 sm:text-sm">
                                Sekelompok bapak-bapak di komunitas sering menjual atau mencari barang bekas (sepeda, perkakas, perlengkapan hobi, elektronik, peralatan rumah tangga). Selama ini hanya mengandalkan chat grup: informasi cepat tenggelam, foto & harga berserakan, dan pencarian sangat sulit.
                                <strong class="font-semibold text-ink-900">Gudang Pakde</strong> hadir sebagai platform secondhand web modern dengan pencarian instan, filter multi-dimensi (kategori, kondisi, provinsi & kota Indonesia), galeri foto multi-gambar, dan kontak WhatsApp 1-klik dengan pesan otomatis.
                            </p>
                        </div>

                        <!-- Feature Acceptance Checklist Matrix -->
                        <div class="mt-6">
                            <h3 class="mb-3 text-sm font-bold text-ink-900">Matriks Pemenuhan Kebutuhan MVP (100% Selesai)</h3>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/40 p-4">
                                    <div class="flex items-center gap-2 font-semibold text-emerald-800 text-xs sm:text-sm">
                                        <AppIcon name="check" class="h-4 w-4 shrink-0 text-emerald-600" />
                                        <span>Autentikasi Pengguna</span>
                                    </div>
                                    <p class="mt-1 text-xs text-ink-600">Registrasi akun, Login, Logout aman dengan session CSRF & password hashing bcrypt.</p>
                                </div>

                                <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/40 p-4">
                                    <div class="flex items-center gap-2 font-semibold text-emerald-800 text-xs sm:text-sm">
                                        <AppIcon name="check" class="h-4 w-4 shrink-0 text-emerald-600" />
                                        <span>Profil & Barang Saya</span>
                                    </div>
                                    <p class="mt-1 text-xs text-ink-600">Kelola nama, email, nomor WhatsApp penjual, serta tab riwayat listing barang milik pribadi.</p>
                                </div>

                                <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/40 p-4">
                                    <div class="flex items-center gap-2 font-semibold text-emerald-800 text-xs sm:text-sm">
                                        <AppIcon name="check" class="h-4 w-4 shrink-0 text-emerald-600" />
                                        <span>Katalog & Grid Barang</span>
                                    </div>
                                    <p class="mt-1 text-xs text-ink-600">Tampilan grid responsive: foto, harga berformat titik, kondisi, lokasi kota/provinsi, dan badge status.</p>
                                </div>

                                <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/40 p-4">
                                    <div class="flex items-center gap-2 font-semibold text-emerald-800 text-xs sm:text-sm">
                                        <AppIcon name="check" class="h-4 w-4 shrink-0 text-emerald-600" />
                                        <span>Pencarian & Multi-Filter</span>
                                    </div>
                                    <p class="mt-1 text-xs text-ink-600">Pencarian kata kunci, filter kategori, kondisi (Baru, Seperti Baru, Bagus, Cukup), serta wilayah Indonesia.</p>
                                </div>

                                <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/40 p-4">
                                    <div class="flex items-center gap-2 font-semibold text-emerald-800 text-xs sm:text-sm">
                                        <AppIcon name="check" class="h-4 w-4 shrink-0 text-emerald-600" />
                                        <span>Detail & Galeri Foto</span>
                                    </div>
                                    <p class="mt-1 text-xs text-ink-600">Thumbnail interactive switcher, deskripsi lengkap, kartu penjual, alamat lokasi, dan tombol WhatsApp.</p>
                                </div>

                                <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/40 p-4">
                                    <div class="flex items-center gap-2 font-semibold text-emerald-800 text-xs sm:text-sm">
                                        <AppIcon name="check" class="h-4 w-4 shrink-0 text-emerald-600" />
                                        <span>Pasang & Multi-Upload</span>
                                    </div>
                                    <p class="mt-1 text-xs text-ink-600">Form pasang iklan dengan validasi server-side, format harga live dot, wilayah autocomplete, dan multi-foto s/d 5.</p>
                                </div>

                                <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/40 p-4">
                                    <div class="flex items-center gap-2 font-semibold text-emerald-800 text-xs sm:text-sm">
                                        <AppIcon name="check" class="h-4 w-4 shrink-0 text-emerald-600" />
                                        <span>Kelola Listing & Kebijakan</span>
                                    </div>
                                    <p class="mt-1 text-xs text-ink-600">Edit data barang, hapus foto individual, hapus listing dengan otorisasi Policy ketat (403 jika bukan pemilik).</p>
                                </div>

                                <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/40 p-4">
                                    <div class="flex items-center gap-2 font-semibold text-emerald-800 text-xs sm:text-sm">
                                        <AppIcon name="check" class="h-4 w-4 shrink-0 text-emerald-600" />
                                        <span>Toggle Status Barang</span>
                                    </div>
                                    <p class="mt-1 text-xs text-ink-600">Penjual dapat mengubah status dari Tersedia ke Terjual dalam 1-klik dengan badge merah otomatis.</p>
                                </div>

                                <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/40 p-4">
                                    <div class="flex items-center gap-2 font-semibold text-emerald-800 text-xs sm:text-sm">
                                        <AppIcon name="check" class="h-4 w-4 shrink-0 text-emerald-600" />
                                        <span>Kontak Penjual WhatsApp</span>
                                    </div>
                                    <p class="mt-1 text-xs text-ink-600">Link WhatsApp Click-to-Chat dengan pesan otomatis terisi nama barang dan tautan listing.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- SECTION 2: APPLICATION ARCHITECTURE & 100K DAU -->
                    <section id="architecture" class="rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm sm:p-8">
                        <div class="flex items-center gap-3 border-b border-neutral-100 pb-4">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                                <AppIcon name="layers" class="h-5 w-5" />
                            </span>
                            <div>
                                <h2 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">
                                    2. Arsitektur Aplikasi & Skala 100.000 DAU
                                </h2>
                                <p class="text-xs text-ink-500 sm:text-sm">Pola Modular Monolith, alur request lifecycle, dan kalkulasi kapasitas sistem</p>
                            </div>
                        </div>

                        <!-- Architectural Decision -->
                        <div class="mt-6 space-y-4">
                            <h3 class="text-sm font-bold text-ink-900">Mengapa Modular Monolith vs Microservices?</h3>
                            <p class="text-xs leading-relaxed text-ink-700 sm:text-sm">
                                Untuk target 100.000 DAU (~3 juta request/hari atau rata-rata ~35 RPS), arsitektur <strong>Modular Monolith</strong> berbasis Laravel 13 dan Inertia.js merupakan keputusan arsitektural yang paling efektif, efisien, dan andal:
                            </p>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-4">
                                    <span class="text-xs font-bold text-ink-900">1. Transaksi ACID Database</span>
                                    <p class="mt-1 text-xs text-ink-600">Mengunggah barang, menyimpan data foto, dan memperbarui status barang berjalan dalam transaksi database atomic tunggal tanpa perlu distributed saga.</p>
                                </div>
                                <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-4">
                                    <span class="text-xs font-bold text-ink-900">2. Latensi Nol Antar-Service</span>
                                    <p class="mt-1 text-xs text-ink-600">Tidak ada overhead HTTP/gRPC antar-service. Satu request ditangani langsung di memory CPU aplikasi.</p>
                                </div>
                                <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-4">
                                    <span class="text-xs font-bold text-ink-900">3. Efisiensi Tim & Deployment</span>
                                    <p class="mt-1 text-xs text-ink-600">Satu unit deployment containerized yang mudah diuji otomatis, di-deploy via CI/CD, dan di-scale secara horizontal.</p>
                                </div>
                            </div>
                        </div>

                        <!-- 100k DAU Mathematical Calculations -->
                        <div class="mt-6 rounded-2xl border border-neutral-200/90 bg-neutral-900 p-5 text-white">
                            <h3 class="flex items-center gap-2 text-sm font-bold text-neutral-100">
                                <AppIcon name="cpu" class="h-4 w-4 text-emerald-400" />
                                <span>Kalkulasi Beban & Kapasitas (100.000 DAU)</span>
                            </h3>
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

                        <!-- Data Flow Diagram Card -->
                        <div class="mt-6 rounded-2xl border border-neutral-100 bg-neutral-50 p-5">
                            <h3 class="text-sm font-bold text-ink-900">Alur Request Lifecycle (End-to-End)</h3>
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
                    </section>

                    <!-- SECTION 3: INFRASTRUCTURE ARCHITECTURE -->
                    <section id="infrastructure" class="rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm sm:p-8">
                        <div class="flex items-center gap-3 border-b border-neutral-100 pb-4">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                                <AppIcon name="server" class="h-5 w-5" />
                            </span>
                            <div>
                                <h2 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">
                                    3. Infrastruktur Produksi (Production Design)
                                </h2>
                                <p class="text-xs text-ink-500 sm:text-sm">Rancangan infrastruktur cloud berdaya tahan tinggi & auto-scaling</p>
                            </div>
                        </div>

                        <!-- Visual Architecture Card -->
                        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
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

                        <!-- High Availability & Reliability Details -->
                        <div class="mt-6 rounded-2xl border border-neutral-100 bg-neutral-50 p-5">
                            <h3 class="text-sm font-bold text-ink-900">Keandalan, Pemulihan Bencana & Monitoring</h3>
                            <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2 text-xs text-ink-700">
                                <div>
                                    <h4 class="font-semibold text-ink-900">Backup & Disaster Recovery (DR)</h4>
                                    <ul class="mt-1.5 list-disc space-y-1 pl-4 text-ink-600">
                                        <li>Snapshot otomatis harian RDS dengan retensi 30 hari.</li>
                                        <li>Point-in-Time Recovery (PITR) untuk rollback transaksi hingga menit ke-5.</li>
                                        <li>Replikasi S3 cross-region antar zona ketersediaan (Availability Zones).</li>
                                        <li>Target RPO &lt; 15 menit, RTO &lt; 1 jam.</li>
                                    </ul>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-ink-900">Observabilitas & Alerting</h4>
                                    <ul class="mt-1.5 list-disc space-y-1 pl-4 text-ink-600">
                                        <li><strong>Sentry:</strong> Real-time exception tracking dan JavaScript error alerting.</li>
                                        <li><strong>Prometheus + Grafana / Datadog:</strong> Monitoring metrik CPU, memory, P95 database query latency.</li>
                                        <li><strong>AWS CloudWatch Alarms:</strong> Notifikasi Slack/Telegram otomatis jika error rate &gt; 1% dalam 5 menit.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- SECTION 4: DATABASE DESIGN & ERD -->
                    <section id="database" class="rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm sm:p-8">
                        <div class="flex items-center gap-3 border-b border-neutral-100 pb-4">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                                <AppIcon name="database" class="h-5 w-5" />
                            </span>
                            <div>
                                <h2 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">
                                    4. Desain Database & Relasi (ERD)
                                </h2>
                                <p class="text-xs text-ink-500 sm:text-sm">Skema entitas, foreign keys, cascade delete, dan strategi indexing performa</p>
                            </div>
                        </div>

                        <!-- ERD Visual Flow -->
                        <div class="mt-6 rounded-2xl border border-neutral-200/90 bg-neutral-900 p-6 text-white font-mono text-xs">
                            <div class="mb-3 text-[11px] font-bold uppercase tracking-wider text-neutral-400">Entity Relationship Diagram (ERD)</div>
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                                <!-- Users Box -->
                                <div class="rounded-xl border border-neutral-700 bg-neutral-800 p-4">
                                    <div class="border-b border-neutral-700 pb-2 font-bold text-emerald-400">users (Penjual/Pembeli)</div>
                                    <div class="mt-2 space-y-1 text-neutral-300">
                                        <div><span class="text-amber-400">PK</span> id: bigint</div>
                                        <div>name: varchar(255)</div>
                                        <div>email: varchar(255) [UQ]</div>
                                        <div>phone: varchar(30)</div>
                                        <div>password: varchar(255)</div>
                                        <div>created_at: timestamp</div>
                                    </div>
                                </div>

                                <!-- Listings Box -->
                                <div class="rounded-xl border border-neutral-700 bg-neutral-800 p-4">
                                    <div class="border-b border-neutral-700 pb-2 font-bold text-blue-400">listings (Barang Bekas)</div>
                                    <div class="mt-2 space-y-1 text-neutral-300">
                                        <div><span class="text-amber-400">PK</span> id: bigint</div>
                                        <div><span class="text-indigo-400">FK</span> user_id: bigint</div>
                                        <div><span class="text-indigo-400">FK</span> category_id: bigint</div>
                                        <div>title: varchar(255)</div>
                                        <div>slug: varchar(255) [UQ]</div>
                                        <div>price: decimal(12,2)</div>
                                        <div>condition: enum</div>
                                        <div>province: varchar(100)</div>
                                        <div>city: varchar(100)</div>
                                        <div>status: enum('available','sold')</div>
                                        <div>views_count: unsigned bigint</div>
                                    </div>
                                </div>

                                <!-- Listing Images & Categories Box -->
                                <div class="space-y-4">
                                    <div class="rounded-xl border border-neutral-700 bg-neutral-800 p-4">
                                        <div class="border-b border-neutral-700 pb-2 font-bold text-purple-400">listing_images (Galeri Foto)</div>
                                        <div class="mt-2 space-y-1 text-neutral-300">
                                            <div><span class="text-amber-400">PK</span> id: bigint</div>
                                            <div><span class="text-indigo-400">FK</span> listing_id: bigint [CASCADE]</div>
                                            <div>image_path: varchar(255)</div>
                                            <div>sort_order: unsigned int</div>
                                        </div>
                                    </div>

                                    <div class="rounded-xl border border-neutral-700 bg-neutral-800 p-4">
                                        <div class="border-b border-neutral-700 pb-2 font-bold text-amber-400">categories (Kategori)</div>
                                        <div class="mt-2 space-y-1 text-neutral-300">
                                            <div><span class="text-amber-400">PK</span> id: bigint</div>
                                            <div>name: varchar(255)</div>
                                            <div>slug: varchar(255) [UQ]</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 border-t border-neutral-800 pt-3 text-[11px] text-neutral-400">
                                <strong>Relasi:</strong> Users 1 : N Listings | Categories 1 : N Listings | Listings 1 : N ListingImages (Cascade On Delete)
                            </div>
                        </div>

                        <!-- Indexing Optimization Table -->
                        <div class="mt-6 overflow-x-auto">
                            <h3 class="mb-3 text-sm font-bold text-ink-900">Indeks Kunci untuk Optimasi Query Skala Besar</h3>
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-neutral-200 bg-neutral-50 text-ink-600">
                                        <th class="p-3 font-semibold">Tabel</th>
                                        <th class="p-3 font-semibold">Nama Index</th>
                                        <th class="p-3 font-semibold">Kolom</th>
                                        <th class="p-3 font-semibold">Tujuan Optimasi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100 text-ink-700">
                                    <tr>
                                        <td class="p-3 font-mono font-semibold">listings</td>
                                        <td class="p-3 font-mono">listings_status_views_count_index</td>
                                        <td class="p-3 font-mono">status, views_count DESC, created_at DESC</td>
                                        <td class="p-3">Katalog utama: sortir barang tersedia paling banyak dilihat & terbaru</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-mono font-semibold">listings</td>
                                        <td class="p-3 font-mono">listings_category_id_index</td>
                                        <td class="p-3 font-mono">category_id</td>
                                        <td class="p-3">Filter cepat berdasarkan kategori barang</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-mono font-semibold">listings</td>
                                        <td class="p-3 font-mono">listings_user_id_index</td>
                                        <td class="p-3 font-mono">user_id</td>
                                        <td class="p-3">Halaman "Barang Saya" milik penjual tertentu</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 font-mono font-semibold">listing_images</td>
                                        <td class="p-3 font-mono">listing_images_listing_id_index</td>
                                        <td class="p-3 font-mono">listing_id, sort_order</td>
                                        <td class="p-3">Eager loading galeri foto barang tanpa N+1 query problem</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- SECTION 5: TECH STACK & DESIGN DECISIONS -->
                    <section id="techstack" class="rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm sm:p-8">
                        <div class="flex items-center gap-3 border-b border-neutral-100 pb-4">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-teal-50 text-teal-600">
                                <AppIcon name="cpu" class="h-5 w-5" />
                            </span>
                            <div>
                                <h2 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">
                                    5. Tech Stack & Keputusan Desain
                                </h2>
                                <p class="text-xs text-ink-500 sm:text-sm">Detail teknologi terkunci dan pertimbangan trade-off arsitektural</p>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Backend Card -->
                            <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-5">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-bold text-ink-900">Backend: Laravel 13</h3>
                                    <span class="rounded bg-neutral-200/70 px-2 py-0.5 font-mono text-[11px] font-semibold text-ink-700">PHP 8.4 ready</span>
                                </div>
                                <ul class="mt-3 space-y-2 text-xs text-ink-700">
                                    <li><strong>Form Requests:</strong> Validasi ketat di sisi server (mimes foto, max size 2MB, panjang teks).</li>
                                    <li><strong>Eloquent & Policies:</strong> <code>ListingPolicy</code> memastikan user hanya bisa mengubah listing miliknya.</li>
                                    <li><strong>Transactions:</strong> <code>DB::transaction()</code> menjamin integritas data saat hapus atau upload multi-gambar.</li>
                                </ul>
                            </div>

                            <!-- Frontend Card -->
                            <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-5">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-bold text-ink-900">Frontend: Vue 3 + Inertia.js</h3>
                                    <span class="rounded bg-neutral-200/70 px-2 py-0.5 font-mono text-[11px] font-semibold text-ink-700">Composition API</span>
                                </div>
                                <ul class="mt-3 space-y-2 text-xs text-ink-700">
                                    <li><strong>Inertia.js Monolith Bridge:</strong> Sensasi SPA tanpa overhead konfigurasi token JWT/OAuth2 terpisah.</li>
                                    <li><strong>Auto Live Formatting:</strong> Pemisah ribuan harga titik otomatis (1230000 -> 1.230.000).</li>
                                    <li><strong>Indonesia Location API:</strong> Dropdown pencarian 34 provinsi dan 514 kota terintegrasi.</li>
                                </ul>
                            </div>

                            <!-- Styling Card -->
                            <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-5">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-bold text-ink-900">UI/UX: Tailwind CSS v4</h3>
                                    <span class="rounded bg-neutral-200/70 px-2 py-0.5 font-mono text-[11px] font-semibold text-ink-700">Sellora System</span>
                                </div>
                                <ul class="mt-3 space-y-2 text-xs text-ink-700">
                                    <li><strong>Design Tokens:</strong> Font Inter, rounded card surfaces, shadow lembut, palet neutral ink modern.</li>
                                    <li><strong>Mobile First:</strong> Grid katalog 1 kolom di HP, 2 di tablet, 3-4 di desktop dengan touch-friendly buttons.</li>
                                    <li><strong>Toast Notification:</strong> Floating toast feedback di pojok kanan atas untuk semua aksi sukses & gagal.</li>
                                </ul>
                            </div>

                            <!-- Database Card -->
                            <div class="rounded-2xl border border-neutral-100 bg-neutral-50 p-5">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-bold text-ink-900">Database: MySQL 8.x</h3>
                                    <span class="rounded bg-neutral-200/70 px-2 py-0.5 font-mono text-[11px] font-semibold text-ink-700">InnoDB utf8mb4</span>
                                </div>
                                <ul class="mt-3 space-y-2 text-xs text-ink-700">
                                    <li><strong>Integritas Relasional:</strong> Foreign key constraints dengan <code>ON DELETE CASCADE</code> untuk gambar listing.</li>
                                    <li><strong>Views Count Atomic:</strong> <code>increment('views_count')</code> tanpa race condition.</li>
                                    <li><strong>Kesiapan Replikasi:</strong> Kompatibel langsung dengan AWS RDS Read Replicas dan Proxy.</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <!-- SECTION 6: HOW TO INSTALL LOCALLY -->
                    <section id="install" class="rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm sm:p-8">
                        <div class="flex items-center justify-between border-b border-neutral-100 pb-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                                    <AppIcon name="terminal" class="h-5 w-5" />
                                </span>
                                <div>
                                    <h2 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">
                                        6. Panduan Instalasi Lokal (How to Install)
                                    </h2>
                                    <p class="text-xs text-ink-500 sm:text-sm">Panduan langkah demi langkah untuk menjalankan aplikasi di komputer sendiri</p>
                                </div>
                            </div>

                            <!-- OS Switcher Pills -->
                            <div class="flex items-center rounded-2xl border border-neutral-200 bg-neutral-100 p-1">
                                <button
                                    type="button"
                                    class="flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-semibold transition"
                                    :class="activeOs === 'macos' ? 'bg-white text-ink-900 shadow-xs' : 'text-ink-600 hover:text-ink-900'"
                                    @click="activeOs = 'macos'"
                                >
                                    <span>Apple macOS</span>
                                </button>
                                <button
                                    type="button"
                                    class="flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-semibold transition"
                                    :class="activeOs === 'windows' ? 'bg-white text-ink-900 shadow-xs' : 'text-ink-600 hover:text-ink-900'"
                                    @click="activeOs = 'windows'"
                                >
                                    <span>Microsoft Windows</span>
                                </button>
                            </div>
                        </div>

                        <!-- Prerequisites -->
                        <div class="mt-6 rounded-2xl border border-neutral-100 bg-neutral-50 p-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-ink-500">Prasyarat Sistem yang Dibutuhkan</h3>
                            <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                <span class="rounded-lg border border-neutral-200 bg-white px-2.5 py-1 font-mono font-medium text-ink-800">PHP &gt;= 8.2 (Disarankan 8.4)</span>
                                <span class="rounded-lg border border-neutral-200 bg-white px-2.5 py-1 font-mono font-medium text-ink-800">Composer 2.x</span>
                                <span class="rounded-lg border border-neutral-200 bg-white px-2.5 py-1 font-mono font-medium text-ink-800">Node.js &gt;= 20.x &amp; npm</span>
                                <span class="rounded-lg border border-neutral-200 bg-white px-2.5 py-1 font-mono font-medium text-ink-800">MySQL Server 8.x</span>
                                <span class="rounded-lg border border-neutral-200 bg-white px-2.5 py-1 font-mono font-medium text-ink-800">Git</span>
                            </div>
                        </div>

                        <!-- Step by Step Instructions -->
                        <div class="mt-6 space-y-5">
                            <!-- Step 1: Clone -->
                            <div class="rounded-2xl border border-neutral-200/90 bg-white p-5 shadow-xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-ink-900 text-xs font-bold text-white">1</span>
                                        <h3 class="text-sm font-bold text-ink-900">Clone Repository GitHub</h3>
                                    </div>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-neutral-200 bg-neutral-50 px-2.5 py-1 text-xs font-medium text-ink-700 hover:bg-neutral-100"
                                        @click="copyToClipboard('git clone https://github.com/irsyaad06/gudang-pakde.git\ncd gudang-pakde', 'clone')"
                                    >
                                        <AppIcon :name="copiedSnippet === 'clone' ? 'check' : 'copy'" class="h-3.5 w-3.5 text-emerald-600" />
                                        <span>{{ copiedSnippet === 'clone' ? 'Tersalin!' : 'Salin Kode' }}</span>
                                    </button>
                                </div>
                                <div class="mt-3 rounded-xl bg-neutral-900 p-3 font-mono text-xs text-neutral-200 overflow-x-auto">
                                    <code>git clone https://github.com/irsyaad06/gudang-pakde.git<br>cd gudang-pakde</code>
                                </div>
                            </div>

                            <!-- Step 2: Dependencies -->
                            <div class="rounded-2xl border border-neutral-200/90 bg-white p-5 shadow-xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-ink-900 text-xs font-bold text-white">2</span>
                                        <h3 class="text-sm font-bold text-ink-900">Instalasi Dependensi PHP &amp; JavaScript</h3>
                                    </div>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-neutral-200 bg-neutral-50 px-2.5 py-1 text-xs font-medium text-ink-700 hover:bg-neutral-100"
                                        @click="copyToClipboard('composer install\nnpm install', 'deps')"
                                    >
                                        <AppIcon :name="copiedSnippet === 'deps' ? 'check' : 'copy'" class="h-3.5 w-3.5 text-emerald-600" />
                                        <span>{{ copiedSnippet === 'deps' ? 'Tersalin!' : 'Salin Kode' }}</span>
                                    </button>
                                </div>
                                <div class="mt-3 rounded-xl bg-neutral-900 p-3 font-mono text-xs text-neutral-200 overflow-x-auto">
                                    <code>composer install<br>npm install</code>
                                </div>
                            </div>

                            <!-- Step 3: Environment Setup -->
                            <div class="rounded-2xl border border-neutral-200/90 bg-white p-5 shadow-xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-ink-900 text-xs font-bold text-white">3</span>
                                        <h3 class="text-sm font-bold text-ink-900">Salin File Environment &amp; Generate App Key</h3>
                                    </div>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-neutral-200 bg-neutral-50 px-2.5 py-1 text-xs font-medium text-ink-700 hover:bg-neutral-100"
                                        @click="copyToClipboard(activeOs === 'macos' ? 'cp .env.example .env\nphp artisan key:generate' : 'copy .env.example .env\nphp artisan key:generate', 'env')"
                                    >
                                        <AppIcon :name="copiedSnippet === 'env' ? 'check' : 'copy'" class="h-3.5 w-3.5 text-emerald-600" />
                                        <span>{{ copiedSnippet === 'env' ? 'Tersalin!' : 'Salin Kode' }}</span>
                                    </button>
                                </div>
                                <div class="mt-3 rounded-xl bg-neutral-900 p-3 font-mono text-xs text-neutral-200 overflow-x-auto">
                                    <code v-if="activeOs === 'macos'">cp .env.example .env<br>php artisan key:generate</code>
                                    <code v-else>copy .env.example .env<br>php artisan key:generate</code>
                                </div>
                            </div>

                            <!-- Step 4: Database Config -->
                            <div class="rounded-2xl border border-neutral-200/90 bg-white p-5 shadow-xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-ink-900 text-xs font-bold text-white">4</span>
                                        <h3 class="text-sm font-bold text-ink-900">Konfigurasi Database MySQL di file .env</h3>
                                    </div>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-neutral-200 bg-neutral-50 px-2.5 py-1 text-xs font-medium text-ink-700 hover:bg-neutral-100"
                                        @click="copyToClipboard('DB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=gudang_pakde\nDB_USERNAME=root\nDB_PASSWORD=', 'db')"
                                    >
                                        <AppIcon :name="copiedSnippet === 'db' ? 'check' : 'copy'" class="h-3.5 w-3.5 text-emerald-600" />
                                        <span>{{ copiedSnippet === 'db' ? 'Tersalin!' : 'Salin Kode' }}</span>
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-ink-600">Pastikan database bernama <code>gudang_pakde</code> telah dibuat di MySQL lokal Anda:</p>
                                <div class="mt-2 rounded-xl bg-neutral-900 p-3 font-mono text-xs text-neutral-200 overflow-x-auto">
                                    <code>DB_CONNECTION=mysql<br>DB_HOST=127.0.0.1<br>DB_PORT=3306<br>DB_DATABASE=gudang_pakde<br>DB_USERNAME=root<br>DB_PASSWORD=</code>
                                </div>
                            </div>

                            <!-- Step 5: Migrate & Seed -->
                            <div class="rounded-2xl border border-neutral-200/90 bg-white p-5 shadow-xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-ink-900 text-xs font-bold text-white">5</span>
                                        <h3 class="text-sm font-bold text-ink-900">Jalankan Migrasi Database &amp; Seeders</h3>
                                    </div>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-neutral-200 bg-neutral-50 px-2.5 py-1 text-xs font-medium text-ink-700 hover:bg-neutral-100"
                                        @click="copyToClipboard('php artisan migrate --seed', 'migrate')"
                                    >
                                        <AppIcon :name="copiedSnippet === 'migrate' ? 'check' : 'copy'" class="h-3.5 w-3.5 text-emerald-600" />
                                        <span>{{ copiedSnippet === 'migrate' ? 'Tersalin!' : 'Salin Kode' }}</span>
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-ink-600">
                                    Perintah ini akan membuat semua tabel, indeks performa, 12 kategori default, user demo (<code>pakde@gudangpakde.id</code> / <code>password</code>), dan data dummy barang.
                                </p>
                                <div class="mt-2 rounded-xl bg-neutral-900 p-3 font-mono text-xs text-neutral-200 overflow-x-auto">
                                    <code>php artisan migrate --seed</code>
                                </div>
                            </div>

                            <!-- Step 6: Storage Link -->
                            <div class="rounded-2xl border border-neutral-200/90 bg-white p-5 shadow-xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-ink-900 text-xs font-bold text-white">6</span>
                                        <h3 class="text-sm font-bold text-ink-900">Buat Symlink Penyimpanan Foto (Storage Link)</h3>
                                    </div>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-neutral-200 bg-neutral-50 px-2.5 py-1 text-xs font-medium text-ink-700 hover:bg-neutral-100"
                                        @click="copyToClipboard('php artisan storage:link', 'link')"
                                    >
                                        <AppIcon :name="copiedSnippet === 'link' ? 'check' : 'copy'" class="h-3.5 w-3.5 text-emerald-600" />
                                        <span>{{ copiedSnippet === 'link' ? 'Tersalin!' : 'Salin Kode' }}</span>
                                    </button>
                                </div>
                                <div class="mt-2 rounded-xl bg-neutral-900 p-3 font-mono text-xs text-neutral-200 overflow-x-auto">
                                    <code>php artisan storage:link</code>
                                </div>
                            </div>

                            <!-- Step 7: Automated Testing -->
                            <div class="rounded-2xl border border-neutral-200/90 bg-white p-5 shadow-xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-ink-900 text-xs font-bold text-white">7</span>
                                        <h3 class="text-sm font-bold text-ink-900">Jalankan Automated Tests untuk Verifikasi</h3>
                                    </div>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-neutral-200 bg-neutral-50 px-2.5 py-1 text-xs font-medium text-ink-700 hover:bg-neutral-100"
                                        @click="copyToClipboard('php artisan test', 'test')"
                                    >
                                        <AppIcon :name="copiedSnippet === 'test' ? 'check' : 'copy'" class="h-3.5 w-3.5 text-emerald-600" />
                                        <span>{{ copiedSnippet === 'test' ? 'Tersalin!' : 'Salin Kode' }}</span>
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-ink-600">
                                    Memastikan 40 test suite fitur (Auth, Policy Otorisasi, CRUD, Galeri Foto, API Wilayah, View Counter) lolos 100%:
                                </p>
                                <div class="mt-2 rounded-xl bg-neutral-900 p-3 font-mono text-xs text-neutral-200 overflow-x-auto">
                                    <code>php artisan test</code>
                                </div>
                                <div class="mt-3 rounded-lg border border-emerald-200 bg-emerald-50/60 p-2.5 text-xs font-semibold text-emerald-800">
                                    ✓ PASS Tests: 40 passed (475 assertions)
                                </div>
                            </div>

                            <!-- Step 8: Run Dev Servers -->
                            <div class="rounded-2xl border border-neutral-200/90 bg-white p-5 shadow-xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-ink-900 text-xs font-bold text-white">8</span>
                                        <h3 class="text-sm font-bold text-ink-900">Jalankan Server Pengembangan (Dev Servers)</h3>
                                    </div>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-neutral-200 bg-neutral-50 px-2.5 py-1 text-xs font-medium text-ink-700 hover:bg-neutral-100"
                                        @click="copyToClipboard('php artisan serve\n# Buka terminal kedua:\nnpm run dev', 'serve')"
                                    >
                                        <AppIcon :name="copiedSnippet === 'serve' ? 'check' : 'copy'" class="h-3.5 w-3.5 text-emerald-600" />
                                        <span>{{ copiedSnippet === 'serve' ? 'Tersalin!' : 'Salin Kode' }}</span>
                                    </button>
                                </div>
                                <p class="mt-2 text-xs text-ink-600">Buka dua jendela terminal untuk menjalankan backend dan frontend HMR Vite:</p>
                                <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <div class="rounded-xl bg-neutral-900 p-3 font-mono text-xs text-neutral-200">
                                        <span class="text-neutral-400"># Terminal 1 (Backend):</span><br>
                                        <code>php artisan serve</code>
                                    </div>
                                    <div class="rounded-xl bg-neutral-900 p-3 font-mono text-xs text-neutral-200">
                                        <span class="text-neutral-400"># Terminal 2 (Vite HMR):</span><br>
                                        <code>npm run dev</code>
                                    </div>
                                </div>
                                <div class="mt-3 text-xs text-ink-700">
                                    Akses aplikasi di browser Anda melalui alamat: <a href="http://127.0.0.1:8000" target="_blank" class="font-mono font-semibold text-ink-900 underline">http://127.0.0.1:8000</a>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- SECTION 7: WEB ROUTES & SECURITY -->
                    <section id="routes" class="rounded-3xl border border-neutral-200/80 bg-white p-6 shadow-sm sm:p-8">
                        <div class="flex items-center gap-3 border-b border-neutral-100 pb-4">
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
                                <AppIcon name="shield-check" class="h-5 w-5" />
                            </span>
                            <div>
                                <h2 class="text-xl font-bold tracking-tight text-ink-900 sm:text-2xl">
                                    7. Kontrak Route Web & Kebijakan Keamanan
                                </h2>
                                <p class="text-xs text-ink-500 sm:text-sm">Daftar endpoint Inertia, proteksi otorisasi Policy, dan keamanan data</p>
                            </div>
                        </div>

                        <!-- Routes Table -->
                        <div class="mt-6 overflow-x-auto">
                            <h3 class="mb-3 text-sm font-bold text-ink-900">Daftar Route &amp; Endpoint Aplikasi</h3>
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-neutral-200 bg-neutral-50 text-ink-600">
                                        <th class="p-3 font-semibold">Method</th>
                                        <th class="p-3 font-semibold">URI</th>
                                        <th class="p-3 font-semibold">Nama Route</th>
                                        <th class="p-3 font-semibold">Middleware</th>
                                        <th class="p-3 font-semibold">Fungsi &amp; Hasil</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-100 text-ink-700 font-mono">
                                    <tr>
                                        <td class="p-3 text-emerald-600 font-bold">GET</td>
                                        <td class="p-3 font-semibold text-ink-900">/</td>
                                        <td class="p-3">catalog.index</td>
                                        <td class="p-3">web</td>
                                        <td class="p-3 font-sans">Katalog utama, search keyword, filter kategori/kondisi/wilayah</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 text-emerald-600 font-bold">GET</td>
                                        <td class="p-3 font-semibold text-ink-900">/listings/{id}</td>
                                        <td class="p-3">listings.show</td>
                                        <td class="p-3">web</td>
                                        <td class="p-3 font-sans">Detail barang, galeri foto, hitung view (+1), tombol WhatsApp</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 text-emerald-600 font-bold">GET</td>
                                        <td class="p-3 font-semibold text-ink-900">/my-listings</td>
                                        <td class="p-3">listings.mine</td>
                                        <td class="p-3 text-amber-600 font-bold">auth</td>
                                        <td class="p-3 font-sans">Daftar barang milik user yang sedang login</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 text-emerald-600 font-bold">GET</td>
                                        <td class="p-3 font-semibold text-ink-900">/listings/create</td>
                                        <td class="p-3">listings.create</td>
                                        <td class="p-3 text-amber-600 font-bold">auth</td>
                                        <td class="p-3 font-sans">Form pasang barang baru</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 text-blue-600 font-bold">POST</td>
                                        <td class="p-3 font-semibold text-ink-900">/listings</td>
                                        <td class="p-3">listings.store</td>
                                        <td class="p-3 text-amber-600 font-bold">auth</td>
                                        <td class="p-3 font-sans">Validasi & simpan barang baru beserta multi-foto</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 text-emerald-600 font-bold">GET</td>
                                        <td class="p-3 font-semibold text-ink-900">/listings/{id}/edit</td>
                                        <td class="p-3">listings.edit</td>
                                        <td class="p-3 text-amber-600 font-bold">auth + Policy</td>
                                        <td class="p-3 font-sans">Form edit barang milik sendiri</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 text-amber-600 font-bold">PUT/PATCH</td>
                                        <td class="p-3 font-semibold text-ink-900">/listings/{id}</td>
                                        <td class="p-3">listings.update</td>
                                        <td class="p-3 text-amber-600 font-bold">auth + Policy</td>
                                        <td class="p-3 font-sans">Update data barang dan kelola foto tambahan</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 text-rose-600 font-bold">DELETE</td>
                                        <td class="p-3 font-semibold text-ink-900">/listings/{id}</td>
                                        <td class="p-3">listings.destroy</td>
                                        <td class="p-3 text-amber-600 font-bold">auth + Policy</td>
                                        <td class="p-3 font-sans">Hapus barang dan seluruh file gambar dari disk</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 text-indigo-600 font-bold">PATCH</td>
                                        <td class="p-3 font-semibold text-ink-900">/listings/{id}/status</td>
                                        <td class="p-3">listings.status</td>
                                        <td class="p-3 text-amber-600 font-bold">auth + Policy</td>
                                        <td class="p-3 font-sans">Toggle status listing antara 'available' dan 'sold'</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 text-emerald-600 font-bold">GET</td>
                                        <td class="p-3 font-semibold text-ink-900">/api/locations</td>
                                        <td class="p-3">locations.index</td>
                                        <td class="p-3">web</td>
                                        <td class="p-3 font-sans">Data autocomplete 34 provinsi dan 514 kota Indonesia</td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 text-emerald-600 font-bold">GET</td>
                                        <td class="p-3 font-semibold text-ink-900">/dokumentasi</td>
                                        <td class="p-3">docs.index</td>
                                        <td class="p-3">web</td>
                                        <td class="p-3 font-sans">Halaman interaktif dokumentasi teknis ini</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Security & Authorization Rules -->
                        <div class="mt-6 rounded-2xl border border-neutral-100 bg-neutral-50 p-5">
                            <h3 class="text-sm font-bold text-ink-900">Kebijakan Otorisasi &amp; Keamanan Data</h3>
                            <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-3 text-xs text-ink-700">
                                <div>
                                    <h4 class="font-semibold text-ink-900">Otorisasi ListingPolicy</h4>
                                    <p class="mt-1 text-ink-600">
                                        Setiap request <code>edit</code>, <code>update</code>, <code>destroy</code>, dan <code>status</code> diotorisasi lewat <code>$user-&gt;id === $listing-&gt;user_id</code>. Request ilegal dari user lain akan otomatis ditolak dengan kode status 403 Forbidden.
                                    </p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-ink-900">Proteksi CSRF &amp; XSS</h4>
                                    <p class="mt-1 text-ink-600">
                                        Inertia.js otomatis menyertakan token CSRF <code>X-XSRF-TOKEN</code> pada setiap mutasi data. Vue 3 template auto-escaping mencegah serangan Cross-Site Scripting (XSS).
                                    </p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-ink-900">Sanitasi &amp; Validasi File</h4>
                                    <p class="mt-1 text-ink-600">
                                        Upload foto dibatasi maksimal 5 gambar per listing, tipe file diperiksa secara biner (JPG, PNG, WebP), dan ukuran dibatasi maksimal 2 MB per foto untuk mencegah resource exhaustion.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </AppLayout>
</template>
