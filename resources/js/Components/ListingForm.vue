<script setup>
import { nextTick, onBeforeUnmount, ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppIcon from '@/Components/AppIcon.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({ categories: { type: Array, required: true }, listing: { type: Object, default: null } });
const form = useForm({
    ...(props.listing ? { _method: 'patch' } : {}),
    title: props.listing?.title ?? '',
    category_id: props.listing?.category?.id ?? props.listing?.category_id ?? '',
    price: props.listing?.price ?? '',
    item_condition: props.listing?.item_condition ?? '',
    location: props.listing?.location ?? '',
    description: props.listing?.description ?? '',
    image: null,
});
const preview = ref(null);
const imageError = ref('');
const input = ref(null);
const releasePreview = () => {
    if (preview.value) URL.revokeObjectURL(preview.value);
    preview.value = null;
};
onBeforeUnmount(releasePreview);
const chooseImage = (event) => {
    releasePreview();
    imageError.value = '';
    form.clearErrors('image');
    const file = event.target.files?.[0];
    form.image = null;
    if (!file) return;
    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 2 * 1024 * 1024) {
        imageError.value = 'Gunakan foto JPG, PNG, atau WebP maksimal 2 MB.';
        event.target.value = '';
        nextTick(() => input.value?.focus());
        return;
    }
    form.image = file;
    preview.value = URL.createObjectURL(file);
};
const removeImage = () => {
    releasePreview();
    form.image = null;
    imageError.value = '';
    form.clearErrors('image');
    if (input.value) input.value.value = '';
    input.value?.focus();
};
const submit = () => {
    if (form.processing || imageError.value) return;
    form.transform((data) => {
        if (props.listing && !data.image) {
            const { image, ...fields } = data;
            return fields;
        }
        return data;
    }).post(props.listing ? route('listings.update', props.listing.id) : route('listings.store'), {
        forceFormData: true,
        onError: () => nextTick(() => (document.querySelector('#listing-form [aria-invalid="true"]') || document.querySelector('#listing-phone-link'))?.focus()),
    });
};
const field = 'mt-2 block min-h-12 w-full rounded-2xl border-border bg-white px-4 text-sm text-ink-900 placeholder:text-ink-500 focus:border-brand-600 focus:ring-brand-600 aria-[invalid=true]:border-danger disabled:opacity-60';
</script>

<template>
    <form id="listing-form" class="overflow-hidden rounded-[2rem] border border-border bg-surface shadow-sm" :aria-busy="form.processing" @submit.prevent="submit">
        <div class="grid lg:grid-cols-2">
            <section class="min-w-0 border-b border-border p-5 sm:p-8 lg:border-b-0 lg:border-r lg:p-10" aria-labelledby="listing-photo-heading">
                <div class="mb-6 flex items-start justify-between gap-3">
                    <div><p class="text-xs font-semibold uppercase tracking-[0.16em] text-ink-500">Foto barang</p><h2 id="listing-photo-heading" class="mt-2 text-xl font-semibold tracking-tight">Tampilkan sisi terbaiknya.</h2></div>
                    <span class="shrink-0 rounded-full border border-border px-3 py-1.5 text-xs font-medium text-ink-600">1 foto</span>
                </div>
                <div class="relative flex aspect-[4/3] max-h-56 w-full items-center justify-center overflow-hidden rounded-3xl border border-border bg-photo sm:max-h-72 lg:aspect-square lg:max-h-none">
                    <img v-if="preview || listing?.image_url" :src="preview || listing.image_url" :alt="preview ? 'Pratinjau foto baru' : `Foto saat ini untuk ${listing.title}`" class="h-full w-full object-contain p-4 sm:p-6" />
                    <div v-else class="px-6 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border border-border bg-white"><AppIcon name="image" class="h-7 w-7 text-ink-500" /></div>
                        <p class="mt-5 text-base font-semibold">Foto yang jelas, kesan yang baik</p>
                        <p class="mx-auto mt-2 max-w-xs text-sm leading-relaxed text-ink-600">Pilih satu foto yang memperlihatkan kondisi barang secara keseluruhan.</p>
                    </div>
                </div>
                <div class="mt-5 rounded-2xl border border-dashed border-border p-4 sm:p-5">
                    <label for="listing-image" class="flex items-center gap-2 text-sm font-semibold"><AppIcon name="upload" class="h-5 w-5" />{{ listing ? 'Ganti foto barang' : 'Unggah foto barang *' }}</label>
                    <p id="listing-image-help" class="mt-2 text-sm leading-relaxed text-ink-600">JPG, PNG, atau WebP · Maksimal 2 MB. {{ listing ? 'Foto lama tetap tersimpan jika tidak diganti.' : 'Satu foto untuk setiap barang.' }}</p>
                    <input id="listing-image" ref="input" type="file" accept="image/jpeg,image/png,image/webp" :required="!listing" :disabled="form.processing" class="mt-4 block min-h-11 w-full min-w-0 rounded-xl text-sm text-ink-600 file:mr-3 file:min-h-11 file:rounded-full file:border file:border-border file:bg-white file:px-4 file:text-sm file:font-semibold file:text-ink-900 hover:file:bg-canvas focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-600 disabled:opacity-60" :aria-invalid="!!(imageError || form.errors.image)" :aria-describedby="imageError || form.errors.image ? 'listing-image-help listing-image-error' : 'listing-image-help'" @change="chooseImage" />
                    <InputError id="listing-image-error" :message="imageError || form.errors.image" class="mt-2" />
                    <SecondaryButton v-if="preview" class="mt-3" :disabled="form.processing" @click="removeImage"><AppIcon name="close" class="h-4 w-4" />Batalkan foto baru</SecondaryButton>
                </div>
            </section>

            <section class="min-w-0 p-5 sm:p-8 lg:p-10" aria-labelledby="listing-details-heading">
                <div class="mb-7"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-ink-500">Detail barang</p><h2 id="listing-details-heading" class="mt-2 text-xl font-semibold tracking-tight">Ceritakan tentang barangmu.</h2><p class="mt-2 text-sm leading-relaxed text-ink-600">Informasi yang lengkap membantu pembeli. Kolom bertanda * wajib diisi.</p></div>
                <p v-if="form.errors.phone" role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-danger">{{ form.errors.phone }} <Link id="listing-phone-link" :href="route('profile.edit')" class="inline-flex min-h-11 items-center font-semibold underline">Perbarui profil</Link></p>
                <div class="space-y-5">
                    <div><label for="listing-title" class="block text-sm font-semibold">Nama barang *</label><input id="listing-title" v-model="form.title" type="text" required maxlength="200" autocomplete="off" placeholder="Contoh: Sepeda lipat 20 inci" :class="field" :aria-invalid="!!form.errors.title" :aria-describedby="form.errors.title ? 'listing-title-error' : undefined" /><InputError id="listing-title-error" :message="form.errors.title" class="mt-2" /></div>
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                        <div><label for="listing-category" class="block text-sm font-semibold">Kategori *</label><select id="listing-category" v-model="form.category_id" required :class="field" :aria-invalid="!!form.errors.category_id" :aria-describedby="form.errors.category_id ? 'listing-category-error' : undefined"><option value="" disabled>Pilih kategori</option><option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option></select><InputError id="listing-category-error" :message="form.errors.category_id" class="mt-2" /></div>
                        <div><label for="listing-condition" class="block text-sm font-semibold">Kondisi *</label><select id="listing-condition" v-model="form.item_condition" required :class="field" :aria-invalid="!!form.errors.item_condition" :aria-describedby="form.errors.item_condition ? 'listing-condition-error' : undefined"><option value="" disabled>Pilih kondisi</option><option value="new">Baru</option><option value="like_new">Seperti Baru</option><option value="good">Bagus</option><option value="fair">Cukup</option></select><InputError id="listing-condition-error" :message="form.errors.item_condition" class="mt-2" /></div>
                    </div>
                    <div><label for="listing-price" class="block text-sm font-semibold">Harga (Rp) *</label><input id="listing-price" v-model="form.price" type="number" min="0" step="1" inputmode="numeric" required placeholder="Contoh: 1250000" :class="field" :aria-invalid="!!form.errors.price" :aria-describedby="form.errors.price ? 'listing-price-error' : undefined" /><InputError id="listing-price-error" :message="form.errors.price" class="mt-2" /></div>
                    <div><label for="listing-location" class="block text-sm font-semibold">Lokasi *</label><input id="listing-location" v-model="form.location" type="text" maxlength="255" required placeholder="Kota atau area pengambilan" :class="field" :aria-invalid="!!form.errors.location" :aria-describedby="form.errors.location ? 'listing-location-error' : undefined" /><InputError id="listing-location-error" :message="form.errors.location" class="mt-2" /></div>
                    <div><label for="listing-description" class="block text-sm font-semibold">Deskripsi *</label><textarea id="listing-description" v-model="form.description" rows="5" maxlength="10000" required placeholder="Jelaskan kondisi, ukuran, kelengkapan, dan hal lain yang perlu diketahui pembeli…" :class="field" :aria-invalid="!!form.errors.description" :aria-describedby="form.errors.description ? 'listing-description-help listing-description-error' : 'listing-description-help'"></textarea><p id="listing-description-help" class="mt-2 text-xs leading-relaxed text-ink-500">Ceritakan kondisi barang dengan jujur, termasuk kekurangannya.</p><InputError id="listing-description-error" :message="form.errors.description" class="mt-2" /></div>
                </div>
            </section>
        </div>
        <footer class="flex flex-col-reverse gap-4 border-t border-border px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-8 lg:px-10">
            <Link :href="listing ? route('listings.mine') : route('catalog.index')" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-full border border-border px-5 text-sm font-semibold text-ink-600 hover:bg-canvas"><AppIcon name="arrow-left" class="h-4 w-4" />Batal</Link>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center"><span v-if="form.progress" role="status" class="text-center text-sm text-ink-600">Mengunggah: {{ form.progress.percentage }}%</span><PrimaryButton type="submit" :disabled="form.processing || !!imageError">{{ form.processing ? 'Menyimpan...' : listing ? 'Simpan Perubahan' : 'Pasang Barang' }}<AppIcon name="arrow-right" class="h-4 w-4" /></PrimaryButton></div>
        </footer>
    </form>
</template>
