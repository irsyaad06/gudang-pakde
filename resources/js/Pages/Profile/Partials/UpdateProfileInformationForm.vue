<script setup>
import { nextTick, ref, watch } from 'vue';
import AppIcon from '@/Components/AppIcon.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({ mustVerifyEmail: { type: Boolean }, status: { type: String } });
const user = usePage().props.auth.user;

const extractLocalPhone = (phone) => {
    if (!phone) return '';
    let str = String(phone).trim();
    if (str.startsWith('+62')) str = str.slice(3);
    else if (str.startsWith('62')) str = str.slice(2);
    return str.replace(/^0+/, '');
};

const localPhone = ref(extractLocalPhone(user.phone));

const form = useForm({
    name: user.name,
    email: user.email,
    phone: user.phone ? (user.phone.startsWith('+62') ? user.phone.slice(1) : (user.phone.startsWith('62') ? user.phone : `62${extractLocalPhone(user.phone)}`)) : '',
});

const onPhoneInput = (event) => {
    let val = event.target.value.trim();

    // Remove leading +62 or 62 if pasted
    if (val.startsWith('+62')) {
        val = val.slice(3);
    } else if (val.startsWith('62')) {
        val = val.slice(2);
    }

    // Keep only numeric characters
    val = val.replace(/\D/g, '');

    // If user types 0 at the beginning, automatically strip it
    val = val.replace(/^0+/, '');

    localPhone.value = val;
    event.target.value = val;

    form.phone = val ? `62${val}` : '';
};

watch(localPhone, (newVal) => {
    let val = newVal.replace(/\D/g, '').replace(/^0+/, '');
    if (val !== newVal) {
        localPhone.value = val;
    }
    form.phone = val ? `62${val}` : '';
});

const submit = () => {
    if (form.processing) return;
    form.patch(route('profile.update'), {
        preserveScroll: true,
        onError: () => nextTick(() => document.querySelector('#profile-information [aria-invalid="true"]')?.focus()),
    });
};
</script>

<template>
    <section aria-labelledby="profile-information-heading">
        <header class="border-b border-border pb-6"><h2 id="profile-information-heading" class="text-xl font-semibold tracking-tight text-ink-900">Informasi profil</h2><p class="mt-2 text-sm leading-relaxed text-ink-600">Identitas dan kontak yang digunakan untuk akun dan barangmu.</p></header>
        <form id="profile-information" class="mt-6 space-y-5" @submit.prevent="submit">
            <div class="grid gap-5 sm:grid-cols-2">
                <div><InputLabel for="profile-name" value="Nama" /><TextInput id="profile-name" v-model="form.name" type="text" placeholder="Masukkan Nama Lengkap Anda" required autocomplete="name" class="mt-2 block w-full" :aria-invalid="!!form.errors.name" :aria-describedby="form.errors.name ? 'profile-name-error' : undefined" /><InputError id="profile-name-error" :message="form.errors.name" class="mt-2" /></div>
                <div><InputLabel for="profile-email" value="Email" /><TextInput id="profile-email" v-model="form.email" type="email" placeholder="Gunakan email berakhiran @" required autocomplete="username" class="mt-2 block w-full" :aria-invalid="!!form.errors.email" :aria-describedby="form.errors.email ? 'profile-email-error' : undefined" /><InputError id="profile-email-error" :message="form.errors.email" class="mt-2" /></div>
            </div>
            <div>
                <InputLabel for="profile-phone" value="Nomor WhatsApp" />
                <div
                    class="mt-2 flex min-h-12 w-full rounded-2xl border border-border bg-surface transition-all focus-within:border-brand-600 focus-within:ring-1 focus-within:ring-brand-600"
                    :class="{ '!border-danger': !!form.errors.phone }"
                >
                    <div class="inline-flex shrink-0 select-none items-center rounded-l-2xl border-r border-border bg-photo px-4 text-sm font-semibold text-ink-900">
                        +62
                    </div>
                    <input
                        id="profile-phone"
                        :value="localPhone"
                        type="tel"
                        inputmode="numeric"
                        autocomplete="tel"
                        placeholder="Contoh: 81234567890 (tanpa angka 0)"
                        class="min-h-12 min-w-0 flex-1 rounded-r-2xl border-0 bg-transparent px-4 text-sm text-ink-900 placeholder:text-ink-500 focus:ring-0 disabled:cursor-not-allowed disabled:opacity-60"
                        :aria-invalid="!!form.errors.phone"
                        :aria-describedby="form.errors.phone ? 'profile-phone-help profile-phone-error' : 'profile-phone-help'"
                        @input="onPhoneInput"
                    />
                </div>
                <p id="profile-phone-help" class="mt-2 text-sm leading-relaxed text-ink-500">
                    Nomor menggunakan prefix <strong>+62</strong>. Cukup masukkan nomor mulai dari angka 8 (angka 0 di awal akan dihilangkan otomatis). Nomor ini diperlukan untuk memasang barang agar pembeli dapat menghubungimu via WhatsApp.
                </p>
                <InputError id="profile-phone-error" :message="form.errors.phone" class="mt-2" />
            </div>
            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="rounded-2xl border border-border bg-photo p-4 text-sm text-ink-600"><p>Alamat emailmu belum diverifikasi.</p><Link :href="route('verification.send')" method="post" as="button" class="mt-2 min-h-11 rounded-lg font-semibold text-ink-900 underline underline-offset-4">Kirim ulang email verifikasi</Link><p v-if="status === 'verification-link-sent'" role="status" class="mt-2 font-medium text-success">Tautan verifikasi baru telah dikirim ke emailmu.</p></div>
            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-border pt-5"><p v-if="form.recentlySuccessful" role="status" class="inline-flex items-center gap-2 text-sm text-success"><AppIcon name="check" class="h-4 w-4" />Profil tersimpan.</p><PrimaryButton type="submit" class="w-full sm:ml-auto sm:w-auto" :disabled="form.processing">{{ form.processing ? 'Menyimpan...' : 'Simpan Profil' }}</PrimaryButton></div>
        </form>
    </section>
</template>
