<script setup>
import { ref, useId } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppIcon from '@/Components/AppIcon.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import ConditionBadge from '@/Components/ConditionBadge.vue';
import ListingPagination from '@/Components/ListingPagination.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { formatPrice, conditionLabel } from '@/Utils/listing';
import { useToast } from '@/Composables/useToast.js';

defineProps({ listings: { type: Object, required: true } });
const toast = useToast();
const pendingId = ref(null);
const deleting = ref(null);
const dialogId = useId();
const setStatus = (listing) => {
    if (pendingId.value !== null) return;
    pendingId.value = listing.id;
    router.patch(route('listings.status', listing.id), { status: listing.status === 'sold' ? 'available' : 'sold' }, {
        preserveScroll: true,
        onError: () => { toast.error('Status gagal diubah. Coba lagi.'); },
        onFinish: () => { pendingId.value = null; },
    });
};
const remove = () => {
    if (!deleting.value || pendingId.value !== null) return;
    const id = deleting.value.id;
    pendingId.value = id;
    router.delete(route('listings.destroy', id), {
        preserveScroll: true,
        onSuccess: () => { deleting.value = null; },
        onError: () => { toast.error('Barang gagal dihapus. Coba lagi.'); deleting.value = null; },
        onFinish: () => { pendingId.value = null; },
    });
};
</script>

<template>
    <div v-if="listings.data?.length" class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        <article v-for="listing in listings.data" :key="listing.id" class="flex min-w-0 flex-col overflow-hidden rounded-3xl border border-border bg-surface p-3 shadow-sm" :aria-busy="pendingId === listing.id">
            <div class="relative overflow-hidden rounded-2xl bg-photo">
                <Link :href="route('listings.show', listing.id)" :aria-label="`Lihat ${listing.title}`" class="block aspect-[4/3] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-[-4px] focus-visible:outline-brand-600">
                    <img v-if="listing.image_url" :src="listing.image_url" :alt="`Foto ${listing.title}`" loading="lazy" class="h-full w-full object-cover" />
                    <span v-else class="flex h-full flex-col items-center justify-center gap-3 p-4 text-center text-sm text-ink-500"><AppIcon name="image" class="h-8 w-8" />Foto belum tersedia</span>
                </Link>
                <div class="pointer-events-none absolute left-3 top-3"><StatusBadge :status="listing.status" /></div>
            </div>
            <div class="flex flex-1 flex-col px-2 pb-2 pt-5 sm:px-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span v-if="listing.category?.name" class="text-xs font-medium text-ink-500">{{ listing.category.name }}</span>
                        <ConditionBadge :condition="listing.item_condition" />
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-border bg-photo px-2.5 py-0.5 text-xs font-medium text-ink-600" :title="`${listing.views_count || 0} kali dilihat atau diklik`">
                        <AppIcon name="eye" class="h-3.5 w-3.5 text-ink-500" />
                        <span>{{ Number(listing.views_count || 0).toLocaleString('id-ID') }}x dilihat</span>
                    </span>
                </div>
                <h3 class="mt-2 break-words text-lg font-semibold tracking-tight text-ink-900"><Link :href="route('listings.show', listing.id)" class="rounded-lg hover:underline">{{ listing.title }}</Link></h3>
                <p class="mt-2 break-words text-xl font-semibold tracking-tight text-ink-900">{{ formatPrice(listing.price) }}</p>
                <p class="mb-5 mt-2 flex items-start gap-1.5 break-words text-sm text-ink-600"><AppIcon name="location" class="mt-0.5 h-4 w-4 shrink-0" /><span class="min-w-0 break-words">{{ listing.location }}</span></p>
                <div class="mt-auto space-y-2 border-t border-border pt-4">
                    <button type="button" :disabled="pendingId !== null" :aria-label="`${listing.status === 'sold' ? 'Tandai Tersedia' : 'Tandai Terjual'}: ${listing.title}`" class="flex min-h-11 w-full items-center justify-center gap-2 rounded-full bg-ink-900 px-3 text-sm font-semibold text-white hover:bg-ink-600 disabled:cursor-not-allowed disabled:opacity-50" @click="setStatus(listing)"><AppIcon name="check" class="h-4 w-4 shrink-0" />{{ pendingId === listing.id ? 'Memproses...' : listing.status === 'sold' ? 'Tandai Tersedia' : 'Tandai Terjual' }}</button>
                    <div class="grid grid-cols-2 gap-2">
                        <Link :href="route('listings.edit', listing.id)" :aria-label="`Edit ${listing.title}`" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-full border border-border px-3 text-sm font-semibold text-ink-900 hover:bg-canvas"><AppIcon name="edit" class="h-4 w-4" />Edit</Link>
                        <button type="button" :disabled="pendingId !== null" :aria-label="`Hapus ${listing.title}`" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-full border border-border px-3 text-sm font-semibold text-ink-600 hover:border-red-200 hover:bg-red-50 hover:text-danger disabled:cursor-not-allowed disabled:opacity-50" @click="deleting = listing"><AppIcon name="trash" class="h-4 w-4" />Hapus</button>
                    </div>
                </div>
            </div>
        </article>
    </div>
    <div v-else class="rounded-[2rem] border border-border bg-surface px-5 py-16 text-center shadow-sm">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border border-border bg-photo"><AppIcon name="box" class="h-7 w-7 text-ink-500" /></div>
        <h2 class="mt-6 text-xl font-semibold tracking-tight text-ink-900">Belum ada barang yang dipasang</h2>
        <p class="mx-auto mt-2 max-w-sm text-sm leading-relaxed text-ink-600">Mulai dari satu barang yang ingin kamu berikan kesempatan baru.</p>
        <Link :href="route('listings.create')" class="mt-6 inline-flex min-h-12 items-center gap-2 rounded-full bg-ink-900 px-6 text-sm font-semibold text-white hover:bg-ink-600"><AppIcon name="plus" class="h-4 w-4" />Pasang Barang</Link>
    </div>
    <ListingPagination :paginator="listings" />
    <Modal :show="!!deleting" max-width="md" :closeable="pendingId === null" :title-id="`${dialogId}-title`" :description-id="`${dialogId}-description`" @close="deleting = null">
        <div v-if="deleting" class="p-6 sm:p-8">
            <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-danger"><AppIcon name="trash" class="h-5 w-5" /></div>
            <h2 :id="`${dialogId}-title`" class="text-xl font-semibold tracking-tight text-ink-900">Hapus barang ini?</h2>
            <p :id="`${dialogId}-description`" class="mt-3 break-words text-sm leading-relaxed text-ink-600">{{ deleting.title }} akan dihapus dari katalog. Tindakan ini tidak dapat dibatalkan.</p>
            <div class="mt-8 flex flex-wrap justify-end gap-3"><SecondaryButton :disabled="pendingId !== null" @click="deleting = null">Batal</SecondaryButton><DangerButton :disabled="pendingId !== null" @click="remove">{{ pendingId === deleting.id ? 'Menghapus...' : 'Ya, hapus' }}</DangerButton></div>
        </div>
    </Modal>
</template>
