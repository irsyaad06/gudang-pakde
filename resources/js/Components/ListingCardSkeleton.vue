<template>
    <article
        class="relative flex min-w-0 flex-col overflow-hidden rounded-3xl border border-border bg-surface p-3 shadow-sm select-none"
        aria-hidden="true"
    >
        <!-- Shimmer wave sweep across entire card -->
        <div class="pointer-events-none absolute inset-0 -translate-x-full animate-shimmer bg-gradient-to-r from-transparent via-white/50 to-transparent z-20" />

        <!-- Image placeholder box (aspect 4/3 matching ListingCard) -->
        <div class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-neutral-100">
            <!-- Simulated status badge in top left -->
            <div class="absolute left-2.5 top-2.5 h-6 w-16 rounded-full bg-neutral-200/90 sm:left-3 sm:top-3" />
        </div>

        <!-- Content Info placeholders -->
        <div class="flex flex-1 flex-col px-1 pb-1 pt-4 sm:px-2">
            <!-- Category & Condition badges -->
            <div class="flex items-center gap-2">
                <div class="h-3.5 w-16 rounded bg-neutral-200/90" />
                <div class="h-5 w-20 rounded-full bg-neutral-200/90" />
            </div>

            <!-- Title (2 lines) -->
            <div class="mt-2.5 space-y-1.5">
                <div class="h-4.5 w-11/12 rounded-md bg-neutral-200/90" />
                <div class="h-4.5 w-7/12 rounded-md bg-neutral-200/90" />
            </div>

            <!-- Price -->
            <div class="mt-3 h-6 w-2/5 rounded-md bg-neutral-200/90" />

            <!-- Location -->
            <div class="mb-4 mt-2.5 flex items-center gap-1.5">
                <div class="h-3.5 w-3.5 shrink-0 rounded-full bg-neutral-200/90" />
                <div class="h-3.5 w-32 rounded bg-neutral-200/90" />
            </div>

            <!-- Action buttons: Pill button + WhatsApp circle button -->
            <div class="mt-auto border-t border-border pt-3">
                <div class="flex items-center gap-2">
                    <div class="h-10 flex-1 rounded-full bg-neutral-200/90" />
                    <div class="h-10 w-10 shrink-0 rounded-full bg-neutral-200/90" />
                </div>
            </div>
        </div>
    </article>
</template>
