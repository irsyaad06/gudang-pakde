<script setup>
import { Link } from '@inertiajs/vue3';
import AppIcon from '@/Components/AppIcon.vue';
import BrandMark from '@/Components/BrandMark.vue';
import ToastContainer from '@/Components/ToastContainer.vue';
</script>

<template>
    <div class="flex min-h-screen flex-col bg-canvas font-sans text-ink-900">
        <ToastContainer />
        <main class="flex flex-1 items-center justify-center px-4 py-8 sm:px-8 sm:py-12" style="view-transition-name: app-main;">
            <div class="grid w-full max-w-5xl overflow-hidden rounded-[2rem] border border-border bg-surface shadow-sm lg:grid-cols-[0.9fr_1.1fr]">
                <aside class="relative hidden flex-col justify-between border-r border-border bg-photo p-10 lg:flex" aria-label="Gudang Pakde">
                    <div><BrandMark class="h-14 w-14 shrink-0" /><p class="mt-6 text-xs font-semibold uppercase tracking-[0.18em] text-ink-500">Gudang Pakde</p></div>
                    <div class="py-12"><h2 class="text-4xl font-semibold leading-tight tracking-tight">Barang bekas,<br />kesempatan baru.</h2><p class="mt-5 max-w-xs text-sm leading-relaxed text-ink-600">Temukan barang yang kamu butuhkan. Beri ruang untuk cerita berikutnya.</p></div>
                    <div class="flex items-center gap-3 border-t border-border pt-6 text-sm text-ink-500"><AppIcon name="box" class="h-5 w-5" /><span>Marketplace barang bekas komunitas.</span></div>
                </aside>
                <div class="min-w-0 px-6 py-8 sm:px-10 sm:py-10 lg:px-12 lg:py-12">
                    <div class="mb-7 flex items-center justify-between gap-3">
                        <Link
                            :href="route('catalog.index')"
                            class="inline-flex min-h-10 items-center gap-2 rounded-full border border-border bg-canvas px-4 text-xs font-semibold text-ink-600 transition hover:bg-white hover:text-ink-900 hover:shadow-sm sm:min-h-11 sm:text-sm"
                        >
                            <AppIcon name="arrow-left" class="h-4 w-4" />
                            <span>Kembali ke katalog</span>
                        </Link>
                        <div class="flex items-center gap-2 lg:hidden">
                            <BrandMark class="h-8 w-8 shrink-0" />
                            <span class="text-sm font-bold tracking-tight text-ink-900">Gudang Pakde</span>
                        </div>
                    </div>
                    <div class="page-content-wrapper min-w-0 w-full">
                        <slot />
                    </div>
                </div>
            </div>
        </main>
        <footer class="px-5 py-6 text-center text-xs text-ink-500">Gudang Pakde · Barang bekas, kesempatan baru.</footer>
    </div>
</template>
