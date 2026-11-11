<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import AppIcon from '@/Components/AppIcon.vue';

const props = defineProps({
    src: {
        type: String,
        required: true,
    },
    alt: {
        type: String,
        default: 'Diagram',
    },
    title: {
        type: String,
        default: '',
    },
});

const containerRef = ref(null);
const imageRef = ref(null);

const scale = ref(1);
const translateX = ref(0);
const translateY = ref(0);
const isDragging = ref(false);
const isFullscreen = ref(false);

const dragStart = { x: 0, y: 0 };
let lastTouchDistance = null;

// Zoom in / out increments
const minScale = 0.25;
const maxScale = 5;

const zoomPercentage = computed(() => {
    return Math.round(scale.value * 100);
});

// Fit diagram to view
const fitToView = () => {
    if (!containerRef.value || !imageRef.value) return;
    const container = containerRef.value;
    const img = imageRef.value;

    const containerWidth = container.clientWidth;
    const containerHeight = container.clientHeight;
    const imgWidth = img.naturalWidth || 1200;
    const imgHeight = img.naturalHeight || 1600;

    const scaleX = (containerWidth - 40) / imgWidth;
    const scaleY = (containerHeight - 40) / imgHeight;
    const initialScale = Math.min(Math.max(Math.min(scaleX, scaleY), 0.3), 1.2);

    scale.value = initialScale;
    translateX.value = (containerWidth - imgWidth * initialScale) / 2;
    translateY.value = Math.max((containerHeight - imgHeight * initialScale) / 2, 20);
};

const resetZoom = () => {
    fitToView();
};

const zoomIn = () => {
    const newScale = Math.min(scale.value * 1.25, maxScale);
    adjustZoomCenter(newScale);
};

const zoomOut = () => {
    const newScale = Math.max(scale.value / 1.25, minScale);
    adjustZoomCenter(newScale);
};

const adjustZoomCenter = (newScale) => {
    if (!containerRef.value) return;
    const container = containerRef.value;
    const centerX = container.clientWidth / 2;
    const centerY = container.clientHeight / 2;

    translateX.value = centerX - (centerX - translateX.value) * (newScale / scale.value);
    translateY.value = centerY - (centerY - translateY.value) * (newScale / scale.value);
    scale.value = newScale;
};

// Mouse Wheel Zoom centered on cursor
const handleWheel = (e) => {
    e.preventDefault();
    const zoomFactor = e.deltaY < 0 ? 1.15 : 0.87;
    const newScale = Math.min(Math.max(scale.value * zoomFactor, minScale), maxScale);

    if (!containerRef.value) return;
    const container = containerRef.value;
    const rect = container.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;

    translateX.value = mouseX - (mouseX - translateX.value) * (newScale / scale.value);
    translateY.value = mouseY - (mouseY - translateY.value) * (newScale / scale.value);
    scale.value = newScale;
};

// Mouse Drag Events
const handleMouseDown = (e) => {
    // Only left click drags
    if (e.button !== 0) return;
    isDragging.value = true;
    dragStart.x = e.clientX - translateX.value;
    dragStart.y = e.clientY - translateY.value;
    window.addEventListener('mousemove', handleMouseMove);
    window.addEventListener('mouseup', handleMouseUp);
};

const handleMouseMove = (e) => {
    if (!isDragging.value) return;
    translateX.value = e.clientX - dragStart.x;
    translateY.value = e.clientY - dragStart.y;
};

const handleMouseUp = () => {
    isDragging.value = false;
    window.removeEventListener('mousemove', handleMouseMove);
    window.removeEventListener('mouseup', handleMouseUp);
};

// Touch Events for Mobile / Tablet
const handleTouchStart = (e) => {
    if (e.touches.length === 1) {
        isDragging.value = true;
        dragStart.x = e.touches[0].clientX - translateX.value;
        dragStart.y = e.touches[0].clientY - translateY.value;
    } else if (e.touches.length === 2) {
        // Pinch zoom start
        isDragging.value = false;
        const dx = e.touches[0].clientX - e.touches[1].clientX;
        const dy = e.touches[0].clientY - e.touches[1].clientY;
        lastTouchDistance = Math.hypot(dx, dy);
    }
};

const handleTouchMove = (e) => {
    if (e.touches.length === 1 && isDragging.value) {
        translateX.value = e.touches[0].clientX - dragStart.x;
        translateY.value = e.touches[0].clientY - dragStart.y;
    } else if (e.touches.length === 2 && lastTouchDistance) {
        e.preventDefault();
        const dx = e.touches[0].clientX - e.touches[1].clientX;
        const dy = e.touches[0].clientY - e.touches[1].clientY;
        const newDistance = Math.hypot(dx, dy);
        const factor = newDistance / lastTouchDistance;
        lastTouchDistance = newDistance;

        const newScale = Math.min(Math.max(scale.value * factor, minScale), maxScale);
        if (containerRef.value) {
            const container = containerRef.value;
            const midX = (e.touches[0].clientX + e.touches[1].clientX) / 2 - container.getBoundingClientRect().left;
            const midY = (e.touches[0].clientY + e.touches[1].clientY) / 2 - container.getBoundingClientRect().top;
            translateX.value = midX - (midX - translateX.value) * (newScale / scale.value);
            translateY.value = midY - (midY - translateY.value) * (newScale / scale.value);
            scale.value = newScale;
        }
    }
};

const handleTouchEnd = () => {
    isDragging.value = false;
    lastTouchDistance = null;
};

// Fullscreen toggle
const toggleFullscreen = () => {
    isFullscreen.value = !isFullscreen.value;
    setTimeout(() => {
        fitToView();
    }, 150);
};

const handleKeyDown = (e) => {
    if (e.key === 'Escape' && isFullscreen.value) {
        isFullscreen.value = false;
        setTimeout(() => {
            fitToView();
        }, 150);
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeyDown);
    window.removeEventListener('mousemove', handleMouseMove);
    window.removeEventListener('mouseup', handleMouseUp);
});
</script>

<template>
    <!-- Normal Mode or Fullscreen Modal -->
    <div
        :class="[
            isFullscreen
                ? 'fixed inset-0 z-50 flex flex-col bg-neutral-950/95 p-3 sm:p-6 backdrop-blur-md'
                : 'relative w-full rounded-2xl border border-neutral-200/90 bg-neutral-50/50 shadow-xs overflow-hidden'
        ]"
    >
        <!-- Viewer Header Toolbar -->
        <div
            class="flex flex-wrap items-center justify-between gap-2.5 border-b border-neutral-200/80 bg-white/90 px-3.5 py-2.5 backdrop-blur-xs text-xs"
            :class="{ 'rounded-t-2xl': !isFullscreen, 'rounded-2xl shadow-lg border-neutral-700 bg-neutral-900/90 text-white': isFullscreen }"
        >
            <div class="flex items-center gap-2 min-w-0">
                <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="truncate font-semibold" :class="isFullscreen ? 'text-white' : 'text-ink-900'">
                    {{ title || 'Interactive SVG Diagram (Pan & Zoom)' }}
                </span>
                <span
                    class="hidden sm:inline-flex items-center rounded-md px-2 py-0.5 font-mono text-[10px] font-bold"
                    :class="isFullscreen ? 'bg-neutral-800 text-neutral-300' : 'bg-neutral-100 text-ink-600'"
                >
                    Scroll: Zoom | Drag: Geser
                </span>
            </div>

            <!-- Controls (Zoom in, Zoom out, Reset, Fullscreen) -->
            <div class="flex items-center gap-1.5 shrink-0">
                <!-- Zoom Out Button -->
                <button
                    type="button"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border transition"
                    :class="isFullscreen ? 'border-neutral-700 bg-neutral-800 text-neutral-200 hover:bg-neutral-700' : 'border-neutral-200 bg-white text-ink-700 hover:bg-neutral-50'"
                    @click="zoomOut"
                    title="Zoom Out (-)"
                    aria-label="Zoom Out"
                >
                    <span class="text-base font-bold leading-none">−</span>
                </button>

                <!-- Zoom Level Display -->
                <button
                    type="button"
                    class="inline-flex h-8 min-w-14 items-center justify-center rounded-lg border px-2 font-mono text-xs font-bold transition"
                    :class="isFullscreen ? 'border-neutral-700 bg-neutral-800 text-neutral-200 hover:bg-neutral-700' : 'border-neutral-200 bg-white text-ink-700 hover:bg-neutral-50'"
                    @click="resetZoom"
                    title="Klik untuk reset zoom (Fit to Screen)"
                >
                    {{ zoomPercentage }}%
                </button>

                <!-- Zoom In Button -->
                <button
                    type="button"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border transition"
                    :class="isFullscreen ? 'border-neutral-700 bg-neutral-800 text-neutral-200 hover:bg-neutral-700' : 'border-neutral-200 bg-white text-ink-700 hover:bg-neutral-50'"
                    @click="zoomIn"
                    title="Zoom In (+)"
                    aria-label="Zoom In"
                >
                    <span class="text-base font-bold leading-none">+</span>
                </button>

                <!-- Reset / Fit Button -->
                <button
                    type="button"
                    class="inline-flex items-center gap-1 rounded-lg border px-2.5 h-8 font-semibold transition"
                    :class="isFullscreen ? 'border-neutral-700 bg-neutral-800 text-neutral-200 hover:bg-neutral-700' : 'border-neutral-200 bg-white text-ink-700 hover:bg-neutral-50'"
                    @click="resetZoom"
                    title="Reset Posisi & Zoom"
                >
                    <span>Reset</span>
                </button>

                <!-- Open in New Tab -->
                <a
                    :href="src"
                    target="_blank"
                    rel="noreferrer"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border transition"
                    :class="isFullscreen ? 'border-neutral-700 bg-neutral-800 text-neutral-200 hover:bg-neutral-700' : 'border-neutral-200 bg-white text-ink-700 hover:bg-neutral-50'"
                    title="Buka File SVG Asli di Tab Baru"
                >
                    <AppIcon name="arrow-up-right" class="h-3.5 w-3.5" />
                </a>

                <!-- Fullscreen Toggle -->
                <button
                    type="button"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border transition"
                    :class="isFullscreen ? 'border-amber-500/50 bg-amber-500/20 text-amber-300 hover:bg-amber-500/30' : 'border-neutral-200 bg-white text-ink-700 hover:bg-neutral-50'"
                    @click="toggleFullscreen"
                    :title="isFullscreen ? 'Keluar Fullscreen (Esc)' : 'Perluas Fullscreen'"
                >
                    <AppIcon :name="isFullscreen ? 'close' : 'grid'" class="h-3.5 w-3.5" />
                </button>
            </div>
        </div>

        <!-- Interactive Canvas Area -->
        <div
            ref="containerRef"
            class="relative w-full select-none overflow-hidden touch-none"
            :class="[
                isFullscreen ? 'flex-1 mt-3 rounded-2xl bg-neutral-900 border border-neutral-800' : 'h-[520px] sm:h-[620px] lg:h-[720px] bg-neutral-100/60',
                isDragging ? 'cursor-grabbing' : 'cursor-grab'
            ]"
            style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 20px 20px;"
            @mousedown="handleMouseDown"
            @wheel="handleWheel"
            @touchstart.passive="handleTouchStart"
            @touchmove="handleTouchMove"
            @touchend="handleTouchEnd"
        >
            <!-- Transformable Image Layer -->
            <div
                class="absolute left-0 top-0 transition-transform origin-top-left will-change-transform"
                :style="{
                    transform: `translate(${translateX}px, ${translateY}px) scale(${scale})`,
                    transition: isDragging ? 'none' : 'transform 80ms ease-out',
                }"
            >
                <img
                    ref="imageRef"
                    :src="src"
                    :alt="alt"
                    class="pointer-events-none select-none max-w-none shadow-md rounded-xl bg-white p-2"
                    draggable="false"
                    @load="fitToView"
                />
            </div>

            <!-- Floating Helper Badge at Bottom -->
            <div class="pointer-events-none absolute bottom-3 left-3 z-10 flex items-center gap-1.5 rounded-full border border-neutral-300/80 bg-white/90 px-3 py-1 font-mono text-[10px] text-ink-600 shadow-sm backdrop-blur-xs">
                <AppIcon name="search" class="h-3 w-3 text-ink-400" />
                <span>Gunakan mouse wheel atau pinch untuk zoom, tahan klik kiri untuk menggeser</span>
            </div>
        </div>
    </div>
</template>
