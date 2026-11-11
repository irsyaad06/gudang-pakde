<script setup>
import { ref } from 'vue';
import AppIcon from '@/Components/AppIcon.vue';

const copiedMermaid = ref(false);

const mermaidCode = `erDiagram
    users ||--o{ listings : "owns (1:N)"
    categories ||--o{ listings : "classifies (1:N)"
    listings ||--o{ listing_images : "contains (1:N)"

    users {
        bigint id PK
        string name
        string email UK
        string phone
        string location
        string password
        string remember_token
        timestamp created_at
        timestamp updated_at
    }

    categories {
        bigint id PK
        string name
        string slug UK
        timestamp created_at
        timestamp updated_at
    }

    listings {
        bigint id PK
        bigint user_id FK
        bigint category_id FK
        string title
        string slug UK
        decimal price
        string condition
        text description
        string location
        string status
        timestamp created_at
        timestamp updated_at
    }

    listing_images {
        bigint id PK
        bigint listing_id FK
        string image_path
        integer sort_order
        timestamp created_at
        timestamp updated_at
    }`;

const copyMermaid = () => {
    navigator.clipboard.writeText(mermaidCode).then(() => {
        copiedMermaid.value = true;
        setTimeout(() => {
            copiedMermaid.value = false;
        }, 2200);
    });
};

const activeTab = ref('visual'); // 'visual' | 'mermaid'
</script>

<template>
    <div class="rounded-2xl border border-neutral-200/90 bg-white overflow-hidden shadow-xs">
        <!-- Header & Switcher -->
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-neutral-100 bg-neutral-50/80 px-4 py-3 sm:px-5">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                    <AppIcon name="database" class="h-4 w-4" />
                </span>
                <div>
                    <h4 class="text-xs font-bold text-ink-900 sm:text-sm">Entity Relationship Diagram (ERD Relasional)</h4>
                    <p class="text-[11px] text-ink-500">Skema 4 Entitas Utama: Relasi Foreign Key, Indexing & Cascade Behavior</p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex items-center gap-1.5">
                <div class="inline-flex rounded-xl border border-neutral-200 bg-white p-1 text-xs">
                    <button
                        type="button"
                        class="rounded-lg px-2.5 py-1 font-semibold transition"
                        :class="activeTab === 'visual' ? 'bg-ink-900 text-white shadow-xs' : 'text-ink-600 hover:text-ink-900'"
                        @click="activeTab = 'visual'"
                    >
                        Tampilan Skema
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
                >
                    <AppIcon :name="copiedMermaid ? 'check' : 'copy'" class="h-3.5 w-3.5" :class="{ 'text-emerald-600': copiedMermaid }" />
                    <span class="hidden sm:inline">{{ copiedMermaid ? 'Tersalin!' : 'Salin Mermaid' }}</span>
                </button>
            </div>
        </div>

        <!-- Visual Table Cards -->
        <div v-show="activeTab === 'visual'" class="p-4 sm:p-6 bg-gradient-to-b from-neutral-50/50 to-white">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Users Table -->
                <div class="rounded-2xl border border-neutral-200 bg-white shadow-xs overflow-hidden">
                    <div class="bg-neutral-900 px-3 py-2 text-white flex items-center justify-between">
                        <span class="font-mono text-xs font-bold">users</span>
                        <span class="text-[10px] text-neutral-400">1:N ke listings</span>
                    </div>
                    <div class="p-3 text-[11px] font-mono space-y-1 divide-y divide-neutral-100">
                        <div class="flex justify-between py-1"><span class="text-amber-700 font-bold">id</span><span class="text-neutral-400">BIGINT PK</span></div>
                        <div class="flex justify-between py-1"><span>name</span><span class="text-neutral-400">VARCHAR(255)</span></div>
                        <div class="flex justify-between py-1"><span class="text-blue-700 font-semibold">email</span><span class="text-neutral-400">UNIQUE</span></div>
                        <div class="flex justify-between py-1"><span>phone</span><span class="text-neutral-400">VARCHAR(30)</span></div>
                        <div class="flex justify-between py-1"><span>location</span><span class="text-neutral-400">VARCHAR(255)</span></div>
                        <div class="flex justify-between py-1"><span>password</span><span class="text-neutral-400">HASH</span></div>
                    </div>
                </div>

                <!-- Categories Table -->
                <div class="rounded-2xl border border-neutral-200 bg-white shadow-xs overflow-hidden">
                    <div class="bg-neutral-900 px-3 py-2 text-white flex items-center justify-between">
                        <span class="font-mono text-xs font-bold">categories</span>
                        <span class="text-[10px] text-neutral-400">1:N ke listings</span>
                    </div>
                    <div class="p-3 text-[11px] font-mono space-y-1 divide-y divide-neutral-100">
                        <div class="flex justify-between py-1"><span class="text-amber-700 font-bold">id</span><span class="text-neutral-400">BIGINT PK</span></div>
                        <div class="flex justify-between py-1"><span>name</span><span class="text-neutral-400">VARCHAR(100)</span></div>
                        <div class="flex justify-between py-1"><span class="text-blue-700 font-semibold">slug</span><span class="text-neutral-400">UNIQUE</span></div>
                        <div class="flex justify-between py-1"><span>created_at</span><span class="text-neutral-400">TIMESTAMP</span></div>
                    </div>
                </div>

                <!-- Listings Table -->
                <div class="rounded-2xl border-2 border-emerald-500 bg-white shadow-xs overflow-hidden">
                    <div class="bg-emerald-700 px-3 py-2 text-white flex items-center justify-between">
                        <span class="font-mono text-xs font-bold">listings (Center)</span>
                        <span class="text-[10px] text-emerald-200">Indeks Komposit</span>
                    </div>
                    <div class="p-3 text-[11px] font-mono space-y-1 divide-y divide-neutral-100">
                        <div class="flex justify-between py-1"><span class="text-amber-700 font-bold">id</span><span class="text-neutral-400">BIGINT PK</span></div>
                        <div class="flex justify-between py-1"><span class="text-indigo-600 font-bold">user_id</span><span class="text-neutral-400">FK -> users</span></div>
                        <div class="flex justify-between py-1"><span class="text-indigo-600 font-bold">category_id</span><span class="text-neutral-400">FK -> categories</span></div>
                        <div class="flex justify-between py-1"><span>title</span><span class="text-neutral-400">VARCHAR(255)</span></div>
                        <div class="flex justify-between py-1"><span class="text-blue-700 font-semibold">slug</span><span class="text-neutral-400">UNIQUE</span></div>
                        <div class="flex justify-between py-1"><span>price</span><span class="text-neutral-400">DECIMAL(12,2)</span></div>
                        <div class="flex justify-between py-1"><span>condition</span><span class="text-neutral-400">ENUM(3)</span></div>
                        <div class="flex justify-between py-1"><span>status</span><span class="text-neutral-400">ENUM(2)</span></div>
                        <div class="flex justify-between py-1"><span>location</span><span class="text-neutral-400">VARCHAR(255)</span></div>
                    </div>
                </div>

                <!-- Listing Images Table -->
                <div class="rounded-2xl border border-neutral-200 bg-white shadow-xs overflow-hidden">
                    <div class="bg-neutral-900 px-3 py-2 text-white flex items-center justify-between">
                        <span class="font-mono text-xs font-bold">listing_images</span>
                        <span class="text-[10px] text-neutral-400">Cascade On Delete</span>
                    </div>
                    <div class="p-3 text-[11px] font-mono space-y-1 divide-y divide-neutral-100">
                        <div class="flex justify-between py-1"><span class="text-amber-700 font-bold">id</span><span class="text-neutral-400">BIGINT PK</span></div>
                        <div class="flex justify-between py-1"><span class="text-indigo-600 font-bold">listing_id</span><span class="text-neutral-400">FK -> listings</span></div>
                        <div class="flex justify-between py-1"><span>image_path</span><span class="text-neutral-400">VARCHAR(255)</span></div>
                        <div class="flex justify-between py-1"><span>sort_order</span><span class="text-neutral-400">INT DEFAULT 0</span></div>
                        <div class="flex justify-between py-1"><span>created_at</span><span class="text-neutral-400">TIMESTAMP</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mermaid Code -->
        <div v-show="activeTab === 'mermaid'" class="p-4 sm:p-5 bg-neutral-900 text-neutral-200">
            <div class="flex items-center justify-between pb-3 border-b border-neutral-800">
                <span class="font-mono text-xs text-neutral-400">mermaid: database-erd.mmd</span>
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

