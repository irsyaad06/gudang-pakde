<script setup>
import { Link } from '@inertiajs/vue3';
import AppIcon from '@/Components/AppIcon.vue';
import BrandMark from '@/Components/BrandMark.vue';
import ToastContainer from '@/Components/ToastContainer.vue';
</script>

<template>
    <div class="flex h-screen h-dvh flex-col justify-between overflow-hidden bg-canvas font-sans text-ink-900">
        <ToastContainer />
        <main class="flex flex-1 items-center justify-center p-3 sm:p-5 lg:p-6" style="view-transition-name: app-main;">
            <div class="grid w-full max-w-4xl overflow-hidden rounded-[2rem] border border-border bg-surface shadow-sm lg:grid-cols-[0.9fr_1.1fr]">
                <aside class="relative hidden flex-col justify-between border-r border-border bg-photo p-8 lg:flex" aria-label="Gudang Pakde">
                    <div>
                        <BrandMark class="h-12 w-12 shrink-0" />
                        <p class="mt-4 text-xs font-semibold uppercase tracking-[0.18em] text-ink-500">Gudang Pakde</p>
                    </div>
                    <div class="py-6">
                        <h2 class="text-3xl font-semibold leading-tight tracking-tight">Barang bekas,<br />kesempatan baru.</h2>
                        <p class="mt-3 max-w-xs text-xs leading-relaxed text-ink-600 sm:text-sm">Temukan barang yang kamu butuhkan. Beri ruang untuk cerita berikutnya.</p>
                    </div>
                    <div class="flex items-center gap-2.5 border-t border-border pt-4 text-xs text-ink-500">
                        <AppIcon name="box" class="h-4 w-4" />
                        <span>Marketplace barang bekas komunitas.</span>
                    </div>
                </aside>
                <div class="min-w-0 px-6 py-6 sm:px-8 sm:py-7 lg:px-9 lg:py-8">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <Link
                            :href="route('catalog.index')"
                            class="inline-flex min-h-9 items-center gap-2 rounded-full border border-border bg-canvas px-3.5 text-xs font-semibold text-ink-600 transition hover:bg-white hover:text-ink-900 hover:shadow-sm"
                        >
                            <AppIcon name="arrow-left" class="h-3.5 w-3.5" />
                            <span>Kembali ke katalog</span>
                        </Link>
                        <div class="flex items-center gap-2 lg:hidden">
                            <BrandMark class="h-7 w-7 shrink-0" />
                            <span class="text-sm font-bold tracking-tight text-ink-900">Gudang Pakde</span>
                        </div>
                    </div>
                    <div class="page-content-wrapper min-w-0 w-full">
                        <slot />
                    </div>
                </div>
            </div>
        </main>
        <footer class="py-3 text-center text-xs text-ink-500">Gudang Pakde · Barang bekas, kesempatan baru.</footer>
    </div>
</template>
