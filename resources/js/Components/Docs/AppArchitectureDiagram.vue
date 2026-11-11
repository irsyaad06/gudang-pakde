<script setup>
import { ref } from 'vue';
import AppIcon from '@/Components/AppIcon.vue';
import SvgPanZoomViewer from '@/Components/Docs/SvgPanZoomViewer.vue';
import archSvgRaw from './diagrams/Diagram Arsitektur.svg?raw';

const copiedMermaid = ref(false);

const mermaidCode = `flowchart TB
  subgraph CLIENT["Client Layer (Browser)"]
    B["Pengguna / Vue 3 + Inertia SPA"]
    WA_EXT["WhatsApp Web / App (Eksternal)"]
  end

  subgraph EDGE["Edge & Security Layer"]
    CDN["Cloudflare Anycast CDN & WAF"]
  end

  subgraph INGRESS["Ingress & Load Balancing"]
    ALB["AWS Application Load Balancer (ALB)"]
  end

  subgraph APP["Application Core (Modular Monolith)"]
    R["Laravel 13 HTTP Routing"]
    P["Form Requests & Policies Authorization"]
    subgraph MODULES["Modul Fungsional"]
      M1["Identity & Auth"]
      M2["Profile & User"]
      M3["Catalog & Filter"]
      M4["Listing & Mutation"]
      M5["Media Storage"]
      M6["WhatsApp Contact"]
    end
    E["Eloquent ORM Engine"]
  end

  subgraph DATA["Data & Persistence Layer"]
    DB_WRITE[("AWS RDS MySQL Primary (Writes)")]
    DB_READ[("AWS RDS MySQL Read Replica (Reads)")]
    REDIS[("AWS ElastiCache Redis (Cache & Session)")]
    S3[("AWS S3 / R2 Object Storage (Photos)")]
  end

  B -->|"HTTPS / Inertia JSON"| CDN
  CDN -->|"TLS Forwarding"| ALB
  ALB -->|"HTTP / FastCGI"| R
  R --> P --> MODULES --> E
  E -->|"Transactional Writes"| DB_WRITE
  E -->|"Catalog Query Reads"| DB_READ
  MODULES -->|"Session, Cache & Queue"| REDIS
  MODULES -->|"Direct S3 Upload"| S3
  B -->|"Klik Chat (External URL direct)"| WA_EXT
  DB_WRITE -.->|"Asynchronous Replication"| DB_READ`;

const copyMermaid = () => {
    navigator.clipboard.writeText(mermaidCode).then(() => {
        copiedMermaid.value = true;
        setTimeout(() => {
            copiedMermaid.value = false;
        }, 2200);
    });
};

const activeTab = ref('svg'); // 'svg' | 'flow' | 'mermaid'
</script>

<template>
    <div class="rounded-2xl border border-neutral-200/90 bg-white overflow-hidden shadow-xs">
        <!-- Diagram Top Header & Switcher -->
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-neutral-100 bg-neutral-50/80 px-4 py-3 sm:px-5">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                    <AppIcon name="layers" class="h-4 w-4" />
                </span>
                <div>
                    <h4 class="text-xs font-bold text-ink-900 sm:text-sm">Diagram Arsitektur Aplikasi (Application Architecture)</h4>
                    <p class="text-[11px] text-ink-500">Pola Modular Monolith: Alur Pengguna, Inertia, Laravel, Cache, Database & External</p>
                </div>
            </div>

            <!-- Tabs: SVG Pan/Zoom vs Component Flow vs Mermaid -->
            <div class="flex items-center gap-1.5">
                <div class="inline-flex rounded-xl border border-neutral-200 bg-white p-1 text-xs">
                    <button
                        type="button"
                        class="rounded-lg px-2.5 py-1 font-semibold transition flex items-center gap-1.5"
                        :class="activeTab === 'svg' ? 'bg-blue-600 text-white shadow-xs' : 'text-ink-600 hover:text-ink-900'"
                        @click="activeTab = 'svg'"
                    >
                        <span>Diagram SVG (Zoom & Geser)</span>
                    </button>
                    <button
                        type="button"
                        class="rounded-lg px-2.5 py-1 font-semibold transition"
                        :class="activeTab === 'flow' ? 'bg-ink-900 text-white shadow-xs' : 'text-ink-600 hover:text-ink-900'"
                        @click="activeTab = 'flow'"
                    >
                        Alur Komponen
                    </button>
                    <button
                        type="button"
                        class="rounded-lg px-2.5 py-1 font-semibold transition"
                        :class="activeTab === 'mermaid' ? 'bg-ink-900 text-white shadow-xs' : 'text-ink-600 hover:text-ink-900'"
                        @click="activeTab = 'mermaid'"
                    >
                        Kode Mermaid
                    </button>
                </div>

                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-neutral-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-ink-700 transition hover:bg-neutral-50"
                    @click="copyMermaid"
                    :title="copiedMermaid ? 'Tersalin ke clipboard!' : 'Salin kode Mermaid'"
                >
                    <AppIcon :name="copiedMermaid ? 'check' : 'copy'" class="h-3.5 w-3.5" :class="{ 'text-emerald-600': copiedMermaid }" />
                    <span class="hidden sm:inline">{{ copiedMermaid ? 'Tersalin!' : 'Salin Mermaid' }}</span>
                </button>
            </div>
        </div>

        <!-- Tab 1: Interactive SVG Pan and Zoom Viewer -->
        <div v-show="activeTab === 'svg'" class="p-3 sm:p-5">
            <SvgPanZoomViewer
                :raw-svg="archSvgRaw"
                src="/images/Diagram%20Arsitektur.svg"
                alt="Diagram Arsitektur Aplikasi Gudang Pakde"
                title="Diagram Arsitektur Aplikasi — Interaktif (Dapat Di-Zoom & Digeser)"
            />
        </div>

        <!-- Tab 2: Structured Flow Cards -->
        <div v-show="activeTab === 'flow'" class="p-4 sm:p-6 bg-gradient-to-b from-neutral-50/50 to-white">
            <div class="space-y-4">
                <!-- 1. Client Tier -->
                <div class="rounded-2xl border-2 border-indigo-200/80 bg-indigo-50/40 p-4 transition hover:border-indigo-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-indigo-600 text-[11px] font-bold text-white">1</span>
                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-900">Client Tier (Browser)</span>
                        </div>
                        <span class="rounded-md bg-indigo-100 px-2 py-0.5 font-mono text-[10px] font-bold text-indigo-700">Vue 3 + Inertia.js + Tailwind</span>
                    </div>
                    <div class="mt-3 grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                        <div class="rounded-xl border border-indigo-100 bg-white p-3 shadow-xs">
                            <div class="text-xs font-bold text-ink-900">Single Page Application (SPA)</div>
                            <div class="text-[11px] text-ink-600 mt-0.5">Navigasi instan tanpa full reload, skeleton loading wave, form validation reaktif, dynamic scrollspy.</div>
                        </div>
                        <div class="rounded-xl border border-emerald-100 bg-emerald-50/70 p-3 shadow-xs">
                            <div class="text-xs font-bold text-emerald-950 flex items-center gap-1.5">
                                <AppIcon name="whatsapp" class="h-3.5 w-3.5 text-emerald-600" />
                                <span>WhatsApp Click-to-Chat (Eksternal)</span>
                            </div>
                            <div class="text-[11px] text-emerald-800 mt-0.5">Membuka tautan <code>wa.me</code> langsung di browser pengguna. <strong>Nol API backend & nol dependensi server</strong>.</div>
                        </div>
                    </div>
                </div>

                <!-- Arrow down -->
                <div class="flex justify-center">
                    <div class="flex items-center gap-2 rounded-full border border-neutral-200 bg-white px-3 py-1 font-mono text-[10px] text-ink-500 shadow-xs">
                        <span>HTTPS / Inertia JSON Payload (X-Inertia Header)</span>
                        <AppIcon name="chevron-down" class="h-3 w-3 text-ink-400" />
                    </div>
                </div>

                <!-- 2. Edge & Security Tier -->
                <div class="rounded-2xl border-2 border-orange-200/80 bg-orange-50/40 p-4 transition hover:border-orange-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-orange-600 text-[11px] font-bold text-white">2</span>
                            <span class="text-xs font-bold uppercase tracking-wider text-orange-900">Edge & Security Perimeter</span>
                        </div>
                        <span class="rounded-md bg-orange-100 px-2 py-0.5 font-mono text-[10px] font-bold text-orange-700">Cloudflare Enterprise CDN & WAF</span>
                    </div>
                    <div class="mt-3 grid grid-cols-1 gap-2.5 sm:grid-cols-3 text-xs">
                        <div class="rounded-xl border border-orange-100 bg-white p-2.5">
                            <div class="font-bold text-ink-900">Anycast DNS</div>
                            <div class="text-[11px] text-ink-600">Resolusi domain instan & routing latensi terendah.</div>
                        </div>
                        <div class="rounded-xl border border-orange-100 bg-white p-2.5">
                            <div class="font-bold text-ink-900">DDoS Mitigation & WAF</div>
                            <div class="text-[11px] text-ink-600">Perlindungan layer 3, 4, dan 7 dari serangan botnet.</div>
                        </div>
                        <div class="rounded-xl border border-orange-100 bg-white p-2.5">
                            <div class="font-bold text-ink-900">Static Edge Caching</div>
                            <div class="text-[11px] text-ink-600">Cache asset JS/CSS Vite & gambar WebP di 300+ PoP global.</div>
                        </div>
                    </div>
                </div>

                <!-- Arrow down -->
                <div class="flex justify-center">
                    <div class="flex items-center gap-2 rounded-full border border-neutral-200 bg-white px-3 py-1 font-mono text-[10px] text-ink-500 shadow-xs">
                        <span>TLS Offloading & Health Checks (/up)</span>
                        <AppIcon name="chevron-down" class="h-3 w-3 text-ink-400" />
                    </div>
                </div>

                <!-- 3. Ingress & Compute Core -->
                <div class="rounded-2xl border-2 border-blue-200/80 bg-blue-50/40 p-4 transition hover:border-blue-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-blue-600 text-[11px] font-bold text-white">3</span>
                            <span class="text-xs font-bold uppercase tracking-wider text-blue-900">Application Core: Modular Monolith</span>
                        </div>
                        <span class="rounded-md bg-blue-100 px-2 py-0.5 font-mono text-[10px] font-bold text-blue-700">Laravel 13 (PHP 8.4) + Fargate Containers</span>
                    </div>

                    <!-- Monolith Modules Box -->
                    <div class="mt-3 rounded-xl border border-blue-200 bg-white p-3 shadow-xs">
                        <div class="flex items-center justify-between border-b border-neutral-100 pb-2 text-[11px] font-bold text-ink-700">
                            <span>Modular Logical Boundaries (Single Deployment Unit)</span>
                            <span class="text-emerald-600">Zero Network Overhead Antar-Modul</span>
                        </div>
                        <div class="mt-2.5 grid grid-cols-2 gap-2 text-[11px] sm:grid-cols-3 lg:grid-cols-6">
                            <div class="rounded-lg bg-neutral-50 p-2 text-center border border-neutral-200/60">
                                <div class="font-bold text-ink-900">Identity</div>
                                <div class="text-[10px] text-ink-500">Auth, Session</div>
                            </div>
                            <div class="rounded-lg bg-neutral-50 p-2 text-center border border-neutral-200/60">
                                <div class="font-bold text-ink-900">Profile</div>
                                <div class="text-[10px] text-ink-500">User & Listings</div>
                            </div>
                            <div class="rounded-lg bg-neutral-50 p-2 text-center border border-neutral-200/60">
                                <div class="font-bold text-ink-900">Catalog</div>
                                <div class="text-[10px] text-ink-500">Search & Filter</div>
                            </div>
                            <div class="rounded-lg bg-neutral-50 p-2 text-center border border-neutral-200/60">
                                <div class="font-bold text-ink-900">Listings</div>
                                <div class="text-[10px] text-ink-500">CRUD & Status</div>
                            </div>
                            <div class="rounded-lg bg-neutral-50 p-2 text-center border border-neutral-200/60">
                                <div class="font-bold text-ink-900">Media</div>
                                <div class="text-[10px] text-ink-500">Upload & Resize</div>
                            </div>
                            <div class="rounded-lg bg-neutral-50 p-2 text-center border border-neutral-200/60">
                                <div class="font-bold text-ink-900">Contact</div>
                                <div class="text-[10px] text-ink-500">WhatsApp Link</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Arrow down -->
                <div class="flex justify-center">
                    <div class="flex items-center gap-2 rounded-full border border-neutral-200 bg-white px-3 py-1 font-mono text-[10px] text-ink-500 shadow-xs">
                        <span>Eloquent ORM, Session Driver & Object Store</span>
                        <AppIcon name="chevron-down" class="h-3 w-3 text-ink-400" />
                    </div>
                </div>

                <!-- 4. Persistence & Storage Tier -->
                <div class="rounded-2xl border-2 border-emerald-200/80 bg-emerald-50/40 p-4 transition hover:border-emerald-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-600 text-[11px] font-bold text-white">4</span>
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-900">Data, In-Memory & Storage Tier</span>
                        </div>
                        <span class="rounded-md bg-emerald-100 px-2 py-0.5 font-mono text-[10px] font-bold text-emerald-700">MySQL 8.x + Redis 7.x + S3 Storage</span>
                    </div>
                    <div class="mt-3 grid grid-cols-1 gap-2.5 sm:grid-cols-3">
                        <div class="rounded-xl border border-emerald-200 bg-white p-3 shadow-xs">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-ink-900">
                                <AppIcon name="database" class="h-3.5 w-3.5 text-emerald-600" />
                                <span>MySQL Primary + Replica</span>
                            </div>
                            <div class="text-[11px] text-ink-600 mt-1">
                                Primary untuk transaksi ACID mutasi barang; Read Replica untuk pencarian katalog 100k DAU tanpa mengganggu writes.
                            </div>
                        </div>
                        <div class="rounded-xl border border-red-200 bg-white p-3 shadow-xs">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-ink-900">
                                <AppIcon name="server" class="h-3.5 w-3.5 text-red-600" />
                                <span>AWS ElastiCache Redis</span>
                            </div>
                            <div class="text-[11px] text-ink-600 mt-1">
                                Shared session store terpusat antar-container, cache tags listing katalog, dan asynchronous queue driver.
                            </div>
                        </div>
                        <div class="rounded-xl border border-purple-200 bg-white p-3 shadow-xs">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-ink-900">
                                <AppIcon name="box" class="h-3.5 w-3.5 text-purple-600" />
                                <span>AWS S3 / Cloudflare R2</span>
                            </div>
                            <div class="text-[11px] text-ink-600 mt-1">
                                Penyimpanan foto barang terisolasi dengan direct CDN URL, tanpa membebani disk container aplikasi.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Raw Mermaid Code -->
        <div v-show="activeTab === 'mermaid'" class="p-4 sm:p-5 bg-neutral-900 text-neutral-200">
            <div class="flex items-center justify-between pb-3 border-b border-neutral-800">
                <span class="font-mono text-xs text-neutral-400">mermaid: application-architecture.mmd</span>
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 text-xs font-mono text-emerald-400 hover:text-emerald-300"
                    @click="copyMermaid"
                >
                    <AppIcon :name="copiedMermaid ? 'check' : 'copy'" class="h-3.5 w-3.5" />
                    <span>{{ copiedMermaid ? 'Tersalin!' : 'Salin Kode' }}</span>
                </button>
            </div>
            <pre class="mt-3 overflow-x-auto font-mono text-xs leading-relaxed text-emerald-300"><code>{{ mermaidCode }}</code></pre>
        </div>
    </div>
</template>
