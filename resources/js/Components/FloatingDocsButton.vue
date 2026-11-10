<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppIcon from '@/Components/AppIcon.vue';

const page = usePage();
const isOpen = ref(false);
const containerRef = ref(null);
let hoverTimeout = null;

const isDocsPage = computed(() => page.url.startsWith('/dokumentasi'));

const openPopover = () => {
    if (hoverTimeout) clearTimeout(hoverTimeout);
    isOpen.value = true;
};

const closePopoverWithDelay = () => {
    if (hoverTimeout) clearTimeout(hoverTimeout);
    hoverTimeout = setTimeout(() => {
        isOpen.value = false;
    }, 280);
};

const togglePopover = () => {
    isOpen.value = !isOpen.value;
};

const handleClickOutside = (event) => {
    if (containerRef.value && !containerRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
    if (hoverTimeout) clearTimeout(hoverTimeout);
});
</script>

<template>
    <aside
        ref="containerRef"
        class="fixed bottom-6 right-4 z-40 flex flex-col items-end sm:bottom-8 sm:right-6"
        aria-label="Akses Cepat Dokumentasi"
        @mouseenter="openPopover"
        @mouseleave="closePopoverWithDelay"
    >
        <!-- Popover Card -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform translate-y-2 scale-95 opacity-0"
            enter-to-class="transform translate-y-0 scale-100 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="transform translate-y-0 scale-100 opacity-100"
            leave-to-class="transform translate-y-2 scale-95 opacity-0"
        >
            <div
                v-if="isOpen"
                class="mb-3 w-72 rounded-2xl border border-neutral-200/90 bg-white p-4 text-ink-900 shadow-2xl ring-1 ring-black/5"
                role="dialog"
                aria-label="Dokumentasi Aplikasi"
            >
                <div class="flex items-start justify-between gap-2 pb-2">
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                        <AppIcon name="check" class="h-3.5 w-3.5" />
                        <span>Alhamdulillah diklik</span>
                    </div>
                    <button
                        type="button"
                        class="rounded-full p-1 text-ink-400 hover:bg-neutral-100 hover:text-ink-700"
                        aria-label="Tutup"
                        @click.stop="isOpen = false"
                    >
                        <AppIcon name="close" class="h-3.5 w-3.5" />
                    </button>
                </div>

                <p class="mt-1 text-xs leading-relaxed text-ink-600">
                    Pelajari arsitektur sistem, infrastruktur 100k DAU, relasi database, dan panduan instalasi lokal.
                </p>

                <div class="mt-3.5">
                    <Link
                        :href="route('docs.index')"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-ink-900 px-3.5 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-neutral-800 active:scale-[0.98]"
                        @click="isOpen = false"
                    >
                        <AppIcon name="file-text" class="h-4 w-4" />
                        <span>Lihat Dokumentasi</span>
                    </Link>
                </div>
            </div>
        </Transition>

        <!-- Floating Document Button -->
        <button
            type="button"
            class="group relative flex h-13 w-13 items-center justify-center rounded-full border border-neutral-700/40 bg-ink-900 text-white shadow-lg shadow-ink-900/25 transition-all duration-200 hover:scale-105 hover:bg-neutral-800 focus:outline-none focus:ring-2 focus:ring-ink-900 focus:ring-offset-2 active:scale-95"
            :class="{ 'ring-2 ring-emerald-500 ring-offset-2': isDocsPage }"
            aria-label="Buka Menu Dokumentasi"
            @click="togglePopover"
        >
            <!-- Document Icon -->
            <AppIcon name="file-text" class="h-6 w-6 text-white transition-transform group-hover:scale-110" />

            <!-- Ping animation on button for discoverability -->
            <span
                v-if="!isOpen"
                class="absolute -right-0.5 -top-0.5 flex h-3.5 w-3.5"
                aria-hidden="true"
            >
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex h-3.5 w-3.5 rounded-full border-2 border-white bg-emerald-500"></span>
            </span>
        </button>
    </aside>
</template>
