<script setup>
import { computed, nextTick, onBeforeUnmount, ref, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppIcon from '@/Components/AppIcon.vue';
import CustomSelect from '@/Components/CustomSelect.vue';
import LocationSelect from '@/Components/LocationSelect.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({ categories: { type: Array, required: true }, listing: { type: Object, default: null } });

const conditionOptions = [
    { value: 'new', label: 'Baru' },
    { value: 'like_new', label: 'Seperti Baru' },
    { value: 'good', label: 'Bagus' },
    { value: 'fair', label: 'Cukup' },
];

const form = useForm({
    ...(props.listing ? { _method: 'patch' } : {}),
    title: props.listing?.title ?? '',
    category_id: props.listing?.category?.id ?? props.listing?.category_id ?? '',
    price: props.listing?.price ?? '',
    item_condition: props.listing?.item_condition ?? '',
    location: props.listing?.location ?? '',
    description: props.listing?.description ?? '',
    images: [],
    delete_image_ids: [],
});

const formatNumberWithDots = (val) => {
    if (val === null || val === undefined || val === '') return '';
    let digits = String(val).replace(/\D/g, '');
    if (digits.length > 1) {
        digits = digits.replace(/^0+/, '') || '0';
    }
    if (!digits) return '';
    return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

const displayPrice = ref(formatNumberWithDots(props.listing?.price));

watch(
    () => form.price,
    (newVal) => {
        const formatted = formatNumberWithDots(newVal);
        if (displayPrice.value !== formatted) {
            displayPrice.value = formatted;
        }
    }
);

const onPriceInput = (event) => {
    const rawVal = event.target.value;
    let digits = rawVal.replace(/\D/g, '');
    if (digits.length > 1) {
        digits = digits.replace(/^0+/, '') || '0';
    }
    const formatted = digits ? digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';

    displayPrice.value = formatted;
    event.target.value = formatted;

    form.price = digits !== '' ? parseInt(digits, 10) : '';
};

const existingImages = ref(
    (props.listing?.images && props.listing.images.length > 0)
        ? [...props.listing.images]
        : (props.listing?.image_url ? [{ id: 'primary', url: props.listing.image_url }] : [])
);

const newImages = ref([]);
const deletedImageIds = ref([]);
const imageError = ref('');
const fileInput = ref(null);

const activeExistingImages = computed(() => {
    return existingImages.value.filter(img => !deletedImageIds.value.includes(img.id));
});

const totalPhotosCount = computed(() => {
    return activeExistingImages.value.length + newImages.value.length;
});

const allDisplayPhotos = computed(() => {
    const list = [];
    activeExistingImages.value.forEach(img => {
        list.push({ type: 'existing', id: img.id, url: img.url });
    });
    newImages.value.forEach((item, idx) => {
        list.push({ type: 'new', id: `new-${idx}`, url: item.preview, fileIndex: idx });
    });
    return list;
});

const activePreview = ref(allDisplayPhotos.value[0]?.url || null);

watch(allDisplayPhotos, (photos) => {
    if (!activePreview.value || !photos.some(p => p.url === activePreview.value)) {
        activePreview.value = photos[0]?.url || null;
    }
}, { immediate: true });

const releaseNewPreviews = () => {
    newImages.value.forEach(img => {
        if (img.preview) URL.revokeObjectURL(img.preview);
    });
};

onBeforeUnmount(releaseNewPreviews);

const chooseImages = (event) => {
    imageError.value = '';
    form.clearErrors('images');
    const files = Array.from(event.target.files || []);
    if (!files.length) return;

    if (totalPhotosCount.value + files.length > 5) {
        imageError.value = 'Maksimal 5 foto barang. Hapus foto lain jika ingin menambah foto baru.';
        event.target.value = '';
        return;
    }

    for (const file of files) {
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            imageError.value = 'Format foto harus berupa JPG, PNG, atau WebP.';
            event.target.value = '';
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            imageError.value = `Ukuran foto "${file.name}" melebihi batas 2 MB.`;
            event.target.value = '';
            return;
        }
    }

    files.forEach(file => {
        const previewUrl = URL.createObjectURL(file);
        newImages.value.push({ file, preview: previewUrl });
        activePreview.value = previewUrl;
    });

    event.target.value = '';
};

const removePhoto = (photo) => {
    imageError.value = '';
    form.clearErrors('images');

    if (photo.type === 'existing') {
        if (typeof photo.id === 'number') {
            deletedImageIds.value.push(photo.id);
        } else {
            existingImages.value = existingImages.value.filter(img => img.id !== photo.id);
        }
    } else {
        const removed = newImages.value.splice(photo.fileIndex, 1)[0];
        if (removed?.preview) {
            URL.revokeObjectURL(removed.preview);
        }
    }
};

const triggerFileInput = () => {
    fileInput.value?.click();
};

const submit = () => {
    if (form.processing) return;

    if (totalPhotosCount.value === 0) {
        imageError.value = 'Wajib mengunggah minimal satu foto barang.';
        nextTick(() => fileInput.value?.focus());
        return;
    }

    form.images = newImages.value.map(i => i.file);
    form.delete_image_ids = deletedImageIds.value.filter(id => typeof id === 'number');

    form.transform((data) => {
        const payload = { ...data };
        if (payload.images && payload.images.length === 0) {
            delete payload.images;
        }
        if (payload.delete_image_ids && payload.delete_image_ids.length === 0) {
            delete payload.delete_image_ids;
        }
        return payload;
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
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-ink-500">Foto barang</p>
                        <h2 id="listing-photo-heading" class="mt-2 text-xl font-semibold tracking-tight">Tampilkan sisi terbaiknya.</h2>
                    </div>
                    <span class="shrink-0 rounded-full border border-border px-3 py-1.5 text-xs font-medium text-ink-600">
                        {{ totalPhotosCount }} / 5 foto
                    </span>
                </div>

                <!-- Pratinjau Utama Foto -->
                <div
                    class="relative flex aspect-[4/3] max-h-60 w-full items-center justify-center overflow-hidden rounded-3xl border border-border bg-photo sm:max-h-72 lg:aspect-square lg:max-h-none"
                    :class="{ 'cursor-pointer transition-colors hover:bg-canvas': !activePreview && totalPhotosCount < 5 }"
                    @click="!activePreview && totalPhotosCount < 5 ? triggerFileInput() : null"
                >
                    <img
                        v-if="activePreview"
                        :src="activePreview"
                        alt="Pratinjau foto terpilih"
                        class="h-full w-full object-contain p-4 sm:p-6"
                    />
                    <div v-else class="px-6 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl border border-border bg-white shadow-sm">
                            <AppIcon name="image" class="h-7 w-7 text-ink-500" />
                        </div>
                        <p class="mt-5 text-base font-semibold">Foto yang jelas, kesan yang baik</p>
                        <p class="mx-auto mt-2 max-w-xs text-sm leading-relaxed text-ink-600">
                            Klik di sini atau tombol <span class="font-semibold text-brand-700">+ Tambah</span> di bawah untuk mengunggah hingga 5 foto.
                        </p>
                    </div>
                </div>

                <!-- Kotak-kotak Thumbnail & Tambah Foto -->
                <div class="mt-4">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase tracking-wider text-ink-500">
                            Daftar Foto (maks. 5)
                        </p>
                        <span class="text-xs text-ink-500">
                            Maks. 2 MB/foto
                        </span>
                    </div>

                    <div class="mt-2.5 flex flex-wrap items-center gap-2.5 sm:gap-3">
                        <div
                            v-for="(photo, idx) in allDisplayPhotos"
                            :key="photo.id || idx"
                            class="group relative h-16 w-16 sm:h-20 sm:w-20 overflow-hidden rounded-xl border-2 bg-photo p-1 transition-all"
                            :class="activePreview === photo.url ? 'border-brand-600 ring-2 ring-brand-600/30' : 'border-border hover:border-ink-600/40'"
                        >
                            <button
                                type="button"
                                :aria-label="`Tampilkan foto ${idx + 1}`"
                                class="h-full w-full focus:outline-none"
                                @click="activePreview = photo.url"
                            >
                                <img :src="photo.url" :alt="`Foto ${idx + 1}`" class="h-full w-full object-contain" />
                            </button>

                            <button
                                type="button"
                                :aria-label="`Hapus foto ${idx + 1}`"
                                :disabled="form.processing"
                                class="absolute right-1 top-1 flex h-5 w-5 items-center justify-center rounded-full bg-ink-900/80 text-white shadow hover:bg-danger focus:outline-none focus-visible:ring-1 focus-visible:ring-white disabled:opacity-50"
                                @click.stop="removePhoto(photo)"
                            >
                                <AppIcon name="close" class="h-3 w-3" />
                            </button>

                            <span v-if="idx === 0" class="absolute bottom-0.5 left-0.5 right-0.5 rounded bg-ink-900/75 px-1 py-0.5 text-center text-[9px] font-semibold text-white">
                                Utama
                            </span>
                        </div>

                        <!-- Tombol Tambah Foto -->
                        <button
                            v-if="totalPhotosCount < 5"
                            type="button"
                            :disabled="form.processing"
                            class="flex h-16 w-16 sm:h-20 sm:w-20 flex-col items-center justify-center rounded-xl border-2 border-dashed border-border bg-white text-ink-600 transition-all hover:border-brand-600 hover:bg-brand-50 hover:text-brand-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 disabled:opacity-50"
                            aria-label="Tambah foto baru"
                            @click="triggerFileInput"
                        >
                            <AppIcon name="upload" class="h-5 w-5" />
                            <span class="mt-1 text-[10px] font-semibold">+ Tambah</span>
                        </button>
                    </div>

                    <!-- Keterangan & Error Validasi Foto -->
                    <p class="mt-2 text-xs text-ink-500">
                        Format JPG, PNG, atau WebP. Anda dapat memilih beberapa foto sekaligus.
                    </p>
                    <InputError id="listing-images-error" :message="imageError || form.errors.images" class="mt-2" />

                    <!-- Input File Tersembunyi tapi Aksesibel untuk triggerFileInput -->
                    <input
                        id="listing-images"
                        ref="fileInput"
                        type="file"
                        multiple
                        accept="image/jpeg,image/png,image/webp"
                        :disabled="form.processing || totalPhotosCount >= 5"
                        class="sr-only"
                        :aria-invalid="!!(imageError || form.errors.images)"
                        aria-describedby="listing-images-error"
                        @change="chooseImages"
                    />
                </div>
            </section>

            <section class="min-w-0 p-5 sm:p-8 lg:p-10" aria-labelledby="listing-details-heading">
                <div class="mb-7"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-ink-500">Detail barang</p><h2 id="listing-details-heading" class="mt-2 text-xl font-semibold tracking-tight">Ceritakan tentang barangmu.</h2><p class="mt-2 text-sm leading-relaxed text-ink-600">Informasi yang lengkap membantu pembeli. Kolom bertanda * wajib diisi.</p></div>
                <p v-if="form.errors.phone" role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-danger">{{ form.errors.phone }} <Link id="listing-phone-link" :href="route('profile.edit')" class="inline-flex min-h-11 items-center font-semibold underline">Perbarui profil</Link></p>
                <div class="space-y-5">
                    <div><label for="listing-title" class="block text-sm font-semibold">Nama barang *</label><input id="listing-title" v-model="form.title" type="text" required maxlength="200" autocomplete="off" placeholder="Contoh: Sepeda lipat 20 inci" :class="field" :aria-invalid="!!form.errors.title" :aria-describedby="form.errors.title ? 'listing-title-error' : undefined" /><InputError id="listing-title-error" :message="form.errors.title" class="mt-2" /></div>
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
                        <div>
                            <label for="listing-category" class="block text-sm font-semibold">Kategori *</label>
                            <CustomSelect
                                id="listing-category"
                                v-model="form.category_id"
                                name="category_id"
                                :options="categories"
                                value-key="id"
                                label-key="name"
                                placeholder="Pilih kategori"
                                rounded="2xl"
                                class="mt-2"
                                :invalid="!!form.errors.category_id"
                                :described-by="form.errors.category_id ? 'listing-category-error' : undefined"
                                :disabled="form.processing"
                            />
                            <InputError id="listing-category-error" :message="form.errors.category_id" class="mt-2" />
                        </div>
                        <div>
                            <label for="listing-condition" class="block text-sm font-semibold">Kondisi *</label>
                            <CustomSelect
                                id="listing-condition"
                                v-model="form.item_condition"
                                name="item_condition"
                                :options="conditionOptions"
                                placeholder="Pilih kondisi"
                                rounded="2xl"
                                class="mt-2"
                                :invalid="!!form.errors.item_condition"
                                :described-by="form.errors.item_condition ? 'listing-condition-error' : undefined"
                                :disabled="form.processing"
                            />
                            <InputError id="listing-condition-error" :message="form.errors.item_condition" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <label for="listing-price" class="block text-sm font-semibold">Harga *</label>
                        <div
                            class="mt-2 flex min-h-12 w-full rounded-2xl border border-border bg-surface transition-all focus-within:border-brand-600 focus-within:ring-1 focus-within:ring-brand-600"
                            :class="{ '!border-danger': !!form.errors.price }"
                        >
                            <span class="inline-flex shrink-0 select-none items-center rounded-l-2xl border-r border-border bg-photo px-4 text-sm font-semibold text-ink-900">
                                Rp
                            </span>
                            <input
                                id="listing-price"
                                :value="displayPrice"
                                type="text"
                                inputmode="numeric"
                                required
                                autocomplete="off"
                                placeholder="1.250.000"
                                class="min-h-12 min-w-0 flex-1 rounded-r-2xl border-0 bg-transparent px-4 text-sm text-ink-900 placeholder:text-ink-500 focus:ring-0 disabled:cursor-not-allowed disabled:opacity-60"
                                :aria-invalid="!!form.errors.price"
                                :aria-describedby="form.errors.price ? 'listing-price-error' : undefined"
                                @input="onPriceInput"
                            />
                        </div>
                        <InputError id="listing-price-error" :message="form.errors.price" class="mt-2" />
                    </div>
                    <div>
                        <label for="listing-location" class="block text-sm font-semibold">Wilayah / Lokasi *</label>
                        <LocationSelect
                            id="listing-location"
                            v-model="form.location"
                            name="location"
                            placeholder="Pilih kota & provinsi..."
                            rounded="2xl"
                            class="mt-2"
                            :allow-province-only="false"
                            :invalid="!!form.errors.location"
                            :described-by="form.errors.location ? 'listing-location-error' : undefined"
                            :disabled="form.processing"
                        />
                        <InputError id="listing-location-error" :message="form.errors.location" class="mt-2" />
                    </div>
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
