<script setup>
import { Link } from '@inertiajs/vue3';
import AppIcon from '@/Components/AppIcon.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { conditionLabel, formatPrice } from '@/Utils/listing.js';

defineProps({ listing: { type: Object, required: true } });
</script>

<template>
    <Link :href="route('listings.show', listing.id)" class="group flex min-w-0 flex-col rounded-xl border border-border bg-surface p-2 motion-safe:transition-shadow hover:shadow-sm sm:p-3">
        <div class="aspect-[4/3] overflow-hidden rounded-lg bg-photo">
            <img v-if="listing.image_url" :src="listing.image_url" :alt="`Foto ${listing.title}`" class="h-full w-full object-contain p-2 sm:p-3" loading="lazy" decoding="async" />
            <div v-else class="flex h-full flex-col items-center justify-center gap-2 px-2 text-center text-xs text-ink-500">
                <AppIcon name="image" class="h-8 w-8" />
                Foto belum tersedia
            </div>
        </div>
        <div class="flex flex-1 flex-col px-0.5 pb-3 pt-3 sm:px-1 sm:pb-4 sm:pt-4">
            <h3 class="line-clamp-2 min-h-10 break-words text-sm font-semibold leading-5 tracking-tight text-ink-900 sm:min-h-12 sm:text-base sm:leading-6">{{ listing.title }}</h3>
            <p v-if="listing.category?.name" class="mt-1 truncate text-xs text-ink-600 sm:text-sm">{{ listing.category.name }}</p>
            <p class="mt-3 break-words text-[15px] font-bold leading-snug tracking-tight text-ink-900 sm:text-xl">{{ formatPrice(listing.price) }}</p>
            <div class="mt-auto space-y-1.5 pt-3 text-xs text-ink-600">
                <p class="break-words">{{ conditionLabel(listing.item_condition) }}</p>
                <p class="flex min-w-0 items-start gap-1"><AppIcon name="location" class="mt-px h-3.5 w-3.5 shrink-0 text-ink-500" /><span class="line-clamp-2 break-words">{{ listing.location }}</span></p>
            </div>
        </div>
        <StatusBadge :status="listing.status" class="w-full shrink-0" />
    </Link>
</template>
