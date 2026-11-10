<script setup>
import AppIcon from '@/Components/AppIcon.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { nextTick } from 'vue';

defineProps({ mustVerifyEmail: { type: Boolean }, status: { type: String } });
const user = usePage().props.auth.user;
const form = useForm({ name: user.name, email: user.email, phone: user.phone ?? '' });
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
                <div><InputLabel for="profile-name" value="Nama" /><TextInput id="profile-name" v-model="form.name" type="text" required autocomplete="name" class="mt-2 block w-full" :aria-invalid="!!form.errors.name" :aria-describedby="form.errors.name ? 'profile-name-error' : undefined" /><InputError id="profile-name-error" :message="form.errors.name" class="mt-2" /></div>
                <div><InputLabel for="profile-email" value="Email" /><TextInput id="profile-email" v-model="form.email" type="email" required autocomplete="username" class="mt-2 block w-full" :aria-invalid="!!form.errors.email" :aria-describedby="form.errors.email ? 'profile-email-error' : undefined" /><InputError id="profile-email-error" :message="form.errors.email" class="mt-2" /></div>
            </div>
            <div><InputLabel for="profile-phone" value="Nomor WhatsApp" /><TextInput id="profile-phone" v-model="form.phone" type="tel" inputmode="tel" autocomplete="tel" placeholder="628123456789" class="mt-2 block w-full" :aria-invalid="!!form.errors.phone" :aria-describedby="form.errors.phone ? 'profile-phone-help profile-phone-error' : 'profile-phone-help'" /><p id="profile-phone-help" class="mt-2 text-sm leading-relaxed text-ink-500">Gunakan format 62 diikuti nomor ponsel, tanpa angka 0 di depan. Nomor ini diperlukan untuk memasang barang agar pembeli dapat menghubungimu.</p><InputError id="profile-phone-error" :message="form.errors.phone" class="mt-2" /></div>
            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="rounded-2xl border border-border bg-photo p-4 text-sm text-ink-600"><p>Alamat emailmu belum diverifikasi.</p><Link :href="route('verification.send')" method="post" as="button" class="mt-2 min-h-11 rounded-lg font-semibold text-ink-900 underline underline-offset-4">Kirim ulang email verifikasi</Link><p v-if="status === 'verification-link-sent'" role="status" class="mt-2 font-medium text-success">Tautan verifikasi baru telah dikirim ke emailmu.</p></div>
            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-border pt-5"><p v-if="form.recentlySuccessful" role="status" class="inline-flex items-center gap-2 text-sm text-success"><AppIcon name="check" class="h-4 w-4" />Profil tersimpan.</p><PrimaryButton type="submit" class="w-full sm:ml-auto sm:w-auto" :disabled="form.processing">{{ form.processing ? 'Menyimpan...' : 'Simpan Profil' }}</PrimaryButton></div>
        </form>
    </section>
</template>
