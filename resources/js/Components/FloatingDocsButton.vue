<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppIcon from '@/Components/AppIcon.vue';

const page = usePage();
const isOpen = ref(false);
const containerRef = ref(null);
const buttonRef = ref(null);

const posY = ref(null);
const isDragging = ref(false);

let isPointerDown = false;
let startY = 0;
let startPosY = 0;
let hasMoved = false;
let justDragged = false;
let dragTimeout = null;

const isDocsPage = computed(() => page.url.startsWith('/dokumentasi'));

const openAbove = computed(() => {
    if (typeof window === 'undefined' || posY.value === null) return true;
    return posY.value > window.innerHeight / 2;
});

const containerStyle = computed(() => {
    if (posY.value !== null) {
        return {
            top: `${posY.value}px`,
            viewTransitionName: 'app-floating-docs',
        };
    }
    return {
        bottom: '1.5rem',
        viewTransitionName: 'app-floating-docs',
    };
});

const onPointerDown = (e) => {
    // Only respond to primary mouse button or touch
    if (e.button !== undefined && e.button !== 0) return;

    isPointerDown = true;
    hasMoved = false;
    startY = e.clientY;
    startPosY = posY.value ?? (window.innerHeight - 100);

    if (buttonRef.value && typeof buttonRef.value.setPointerCapture === 'function') {
        try {
            buttonRef.value.setPointerCapture(e.pointerId);
        } catch (err) {}
    }
};

const onPointerMove = (e) => {
    if (!isPointerDown) return;

    const deltaY = e.clientY - startY;
    if (Math.abs(deltaY) > 5) {
        hasMoved = true;
        isDragging.value = true;
    }

    if (hasMoved) {
        const minY = 80;
        const maxY = Math.max(minY, window.innerHeight - 84);
        posY.value = Math.max(minY, Math.min(maxY, startPosY + deltaY));
    }
};

const onPointerUp = (e) => {
    if (!isPointerDown) return;
    isPointerDown = false;

    if (buttonRef.value && typeof buttonRef.value.releasePointerCapture === 'function') {
        try {
            if (buttonRef.value.hasPointerCapture(e.pointerId)) {
                buttonRef.value.releasePointerCapture(e.pointerId);
            }
        } catch (err) {}
    }

    if (hasMoved) {
        justDragged = true;
        if (dragTimeout) clearTimeout(dragTimeout);
        dragTimeout = setTimeout(() => {
            justDragged = false;
        }, 120);

        try {
            if (posY.value !== null) {
                localStorage.setItem('gudang_pakde_docs_btn_y', posY.value.toString());
            }
        } catch (err) {}
    }

    isDragging.value = false;
};

const onClick = (e) => {
    if (justDragged || hasMoved) {
        e.preventDefault();
        e.stopPropagation();
        return;
    }
    isOpen.value = !isOpen.value;
};

const handleClickOutside = (event) => {
    if (containerRef.value && !containerRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

const handleWindowResize = () => {
    if (typeof window === 'undefined' || posY.value === null) return;
    const maxY = Math.max(80, window.innerHeight - 84);
    if (posY.value > maxY) {
        posY.value = maxY;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    window.addEventListener('resize', handleWindowResize);

    const defaultY = Math.max(80, window.innerHeight - 100);
    try {
        const saved = localStorage.getItem('gudang_pakde_docs_btn_y');
        if (saved !== null) {
            const parsed = parseFloat(saved);
            const maxY = Math.max(80, window.innerHeight - 84);
            if (!isNaN(parsed) && parsed >= 80 && parsed <= maxY) {
                posY.value = parsed;
                return;
            }
        }
    } catch (err) {}
    posY.value = defaultY;
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
    window.removeEventListener('resize', handleWindowResize);
    if (dragTimeout) clearTimeout(dragTimeout);
});
</script>

<template>
    <aside
        ref="containerRef"
        class="fixed right-4 z-40 sm:right-6"
        :style="containerStyle"
        aria-label="Akses Cepat Dokumentasi"
    >
        <div class="relative flex flex-col items-end">
            <!-- Popover Card (appears above or below depending on vertical position) -->
            <Transition
                enter-active-class="transition duration-200 ease-out"
                :enter-from-class="openAbove ? 'transform translate-y-2 scale-95 opacity-0' : 'transform -translate-y-2 scale-95 opacity-0'"
                enter-to-class="transform translate-y-0 scale-100 opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="transform translate-y-0 scale-100 opacity-100"
                :leave-to-class="openAbove ? 'transform translate-y-2 scale-95 opacity-0' : 'transform -translate-y-2 scale-95 opacity-0'"
            >
                <div
                    v-if="isOpen"
                    class="absolute right-0 w-72 rounded-2xl border border-neutral-200/90 bg-white p-4 text-ink-900 shadow-2xl ring-1 ring-black/5"
                    :class="openAbove ? 'bottom-full mb-3' : 'top-full mt-3'"
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
                            class="rounded-full p-1 text-ink-400 hover:bg-neutral-100 hover:text-ink-700 focus:outline-none"
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

            <!-- Floating Document Button: Large, White Background, Draggable Up/Down, Click Only -->
            <button
                ref="buttonRef"
                type="button"
                class="group relative flex h-16 w-16 items-center justify-center rounded-full border border-neutral-200/90 bg-white text-ink-900 shadow-xl shadow-black/10 ring-1 ring-black/5 transition-all duration-150 hover:bg-neutral-50 hover:shadow-2xl focus:outline-none focus:ring-2 focus:ring-ink-900 focus:ring-offset-2 active:scale-95"
                :class="{
                    'cursor-grabbing select-none scale-105 shadow-2xl ring-2 ring-ink-900': isDragging,
                    'cursor-grab': !isDragging,
                    'ring-2 ring-emerald-500 ring-offset-2': isDocsPage && !isDragging,
                }"
                style="touch-action: none;"
                aria-label="Buka Menu Dokumentasi (bisa digeser atas-bawah)"
                title="Dokumentasi Aplikasi (klik untuk buka, drag untuk geser atas-bawah)"
                @pointerdown="onPointerDown"
                @pointermove="onPointerMove"
                @pointerup="onPointerUp"
                @pointercancel="onPointerUp"
                @click="onClick"
            >
                <!-- Subtle vertical drag handle indicator -->
                <span class="absolute left-2 flex flex-col gap-1 opacity-25 transition-opacity group-hover:opacity-60" aria-hidden="true">
                    <span class="h-1 w-1 rounded-full bg-ink-900"></span>
                    <span class="h-1 w-1 rounded-full bg-ink-900"></span>
                    <span class="h-1 w-1 rounded-full bg-ink-900"></span>
                </span>

                <!-- Document Icon (larger and clear) -->
                <AppIcon name="file-text" class="h-7 w-7 text-ink-900 transition-transform group-hover:scale-110" />

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
        </div>
    </aside>
</template>
