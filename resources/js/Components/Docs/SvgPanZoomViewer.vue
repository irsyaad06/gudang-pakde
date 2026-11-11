<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import AppIcon from '@/Components/AppIcon.vue';

const props = defineProps({
    rawSvg: {
        type: String,
        default: '',
    },
    src: {
        type: String,
        default: '',
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
const svgHostRef = ref(null);

const scale = ref(1);
const translateX = ref(0);
const translateY = ref(0);
const isDragging = ref(false);
const isFullscreen = ref(false);
const isLoading = ref(true);
const loadError = ref(null);
const hasInitialFit = ref(false);

const naturalWidth = ref(1274);
const naturalHeight = ref(2128);
const processedSvg = ref('');

const dragStart = { x: 0, y: 0 };
let lastTouchDistance = null;
let resizeObserver = null;

const minScale = 0.15;
const maxScale = 5;

const zoomPercentage = computed(() => {
    return Math.round(scale.value * 100);
});

// Process and namespace SVG string
const processSvgString = (rawText) => {
    if (!rawText || typeof rawText !== 'string') return '';

    // Generate unique ID prefix to prevent CSS & ID collisions
    const uniqueId = `diag_${Math.random().toString(36).substring(2, 9)}`;
    let text = rawText.replaceAll('export-svg', uniqueId);

    // Parse viewBox to determine natural dimensions
    const vbMatch = text.match(/viewBox\s*=\s*["']([^"']+)["']/i);
    if (vbMatch) {
        const parts = vbMatch[1].trim().split(/[\s,]+/);
        if (parts.length === 4) {
            naturalWidth.value = parseFloat(parts[2]) || naturalWidth.value;
            naturalHeight.value = parseFloat(parts[3]) || naturalHeight.value;
        }
    }

    // Clean restrictive attributes so the SVG expands to 100% of the transform wrapper
    text = text.replace(/<svg\b([^>]*)>/i, (match, attrs) => {
        let cleaned = attrs
            .replace(/\bwidth\s*=\s*["'][^"']*["']/gi, '')
            .replace(/\bheight\s*=\s*["'][^"']*["']/gi, '')
            .replace(/\bstyle\s*=\s*["'][^"']*["']/gi, '');
        return `<svg ${cleaned.trim()} width="100%" height="100%" style="display: block; width: 100%; height: 100%;">`;
    });

    return text;
};

// Fit diagram to view
const fitToView = () => {
    if (!containerRef.value) return;
    const container = containerRef.value;

    const containerWidth = container.clientWidth;
    const containerHeight = container.clientHeight;

    if (!containerWidth || !containerHeight) {
        requestAnimationFrame(() => {
            fitToView();
        });
        return;
    }

    const w = naturalWidth.value || 1274;
    const h = naturalHeight.value || 2128;

    // Scale so width fits with 48px padding
    const scaleX = (containerWidth - 48) / w;
    const scaleY = (containerHeight - 48) / h;

    // Use scaleX or balanced scale
    const targetScale = Math.min(Math.max(scaleX, minScale), 1.0);
    scale.value = Number(targetScale.toFixed(2));

    translateX.value = Math.max((containerWidth - w * targetScale) / 2, 20);
    translateY.value = 24;
    hasInitialFit.value = true;
};

// Initialize or load SVG
const initSvg = async () => {
    isLoading.value = true;
    loadError.value = null;

    if (props.rawSvg) {
        processedSvg.value = processSvgString(props.rawSvg);
        isLoading.value = false;
        nextTick(() => {
            fitToView();
        });
        return;
    }

    if (props.src) {
        try {
            const url = encodeURI(props.src);
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Gagal memuat file SVG (${response.status} ${response.statusText})`);
            }
            const text = await response.text();
            processedSvg.value = processSvgString(text);
            isLoading.value = false;
            nextTick(() => {
                fitToView();
            });
        } catch (err) {
            console.error('Error fetching SVG:', err);
            loadError.value = err.message || 'Gagal mengambil data SVG';
            isLoading.value = false;
        }
    } else {
        isLoading.value = false;
    }
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
    scale.value = Number(newScale.toFixed(3));
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
    scale.value = Number(newScale.toFixed(3));
};

// Mouse Drag Events
const handleMouseDown = (e) => {
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
            scale.value = Number(newScale.toFixed(3));
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

watch(() => props.rawSvg, () => {
    initSvg();
});

watch(() => props.src, () => {
    initSvg();
});

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
    initSvg();

    if (typeof ResizeObserver !== 'undefined' && containerRef.value) {
        resizeObserver = new ResizeObserver((entries) => {
            for (const entry of entries) {
                if (entry.contentRect.width > 0 && !hasInitialFit.value) {
                    fitToView();
                }
            }
        });
        resizeObserver.observe(containerRef.value);
    }
});

onBeforeUnmount(() => {
    if (resizeObserver) resizeObserver.disconnect();
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
                : 'relative w-full rounded-2xl border border-neutral-200/90 bg-white shadow-xs overflow-hidden'
        ]"
    >
        <!-- Viewer Header Toolbar -->
        <div
            class="flex flex-wrap items-center justify-between gap-2.5 border-b border-neutral-200/80 bg-white/95 px-3.5 py-2.5 backdrop-blur-xs text-xs"
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
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border transition font-bold"
                    :class="isFullscreen ? 'border-neutral-700 bg-neutral-800 text-neutral-200 hover:bg-neutral-700' : 'border-neutral-200 bg-white text-ink-700 hover:bg-neutral-50'"
                    @click="zoomOut"
                    title="Zoom Out (-)"
                    aria-label="Zoom Out"
                >
                    <span class="text-base leading-none">−</span>
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
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border transition font-bold"
                    :class="isFullscreen ? 'border-neutral-700 bg-neutral-800 text-neutral-200 hover:bg-neutral-700' : 'border-neutral-200 bg-white text-ink-700 hover:bg-neutral-50'"
                    @click="zoomIn"
                    title="Zoom In (+)"
                    aria-label="Zoom In"
                >
                    <span class="text-base leading-none">+</span>
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

                <!-- Open original file in new tab if src available -->
                <a
                    v-if="src"
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
                isFullscreen ? 'flex-1 mt-3 rounded-2xl bg-neutral-900 border border-neutral-800' : 'h-[620px] sm:h-[720px] lg:h-[820px] bg-neutral-50',
                isDragging ? 'cursor-grabbing' : 'cursor-grab'
            ]"
            style="background-image: radial-gradient(#cbd5e1 1.2px, transparent 1.2px); background-size: 20px 20px;"
            @mousedown="handleMouseDown"
            @wheel="handleWheel"
            @touchstart.passive="handleTouchStart"
            @touchmove="handleTouchMove"
            @touchend="handleTouchEnd"
        >
            <!-- Loading State -->
            <div v-if="isLoading" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-white/80 backdrop-blur-xs z-20">
                <div class="h-8 w-8 animate-spin rounded-full border-3 border-neutral-300 border-t-blue-600"></div>
                <div class="text-xs font-semibold text-ink-600">Memuat diagram vektor...</div>
            </div>

            <!-- Error State -->
            <div v-if="loadError" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-white/95 p-6 z-20">
                <div class="rounded-full bg-rose-100 p-3 text-rose-600">
                    <AppIcon name="close" class="h-6 w-6" />
                </div>
                <div class="text-sm font-bold text-ink-900">Gagal Memuat Diagram SVG</div>
                <div class="text-xs text-ink-500 max-w-md text-center">{{ loadError }}</div>
                <button
                    type="button"
                    class="rounded-xl bg-ink-900 px-4 py-2 text-xs font-semibold text-white shadow-xs transition hover:bg-ink-800"
                    @click="initSvg"
                >
                    Coba Lagi
                </button>
            </div>

            <!-- Transformable SVG Layer -->
            <div
                v-show="!isLoading && !loadError && processedSvg"
                ref="svgHostRef"
                class="absolute left-0 top-0 origin-top-left will-change-transform"
                :style="{
                    width: `${naturalWidth}px`,
                    height: `${naturalHeight}px`,
                    transform: `translate(${translateX}px, ${translateY}px) scale(${scale})`,
                    transition: isDragging ? 'none' : 'transform 80ms ease-out',
                }"
            >
                <div
                    v-html="processedSvg"
                    class="w-full h-full pointer-events-none select-none rounded-2xl shadow-xl border border-neutral-200/90 bg-white overflow-hidden"
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
