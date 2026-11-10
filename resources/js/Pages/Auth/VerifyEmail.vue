<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ status: { type: String } });
const form = useForm({});
const submit = () => form.post(route('verification.send'));
const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Verifikasi Email" />
        <h1 class="text-3xl font-semibold tracking-tight text-ink-900">Verifikasi email</h1><p class="mt-3 text-sm leading-relaxed text-ink-600">Periksa emailmu dan buka tautan verifikasi yang kami kirim. Belum menerimanya? Minta tautan baru di bawah ini.</p>
        <p v-if="verificationLinkSent" role="status" class="mt-5 rounded-2xl border border-border bg-success-soft p-4 text-sm font-medium text-success">Tautan verifikasi baru telah dikirim ke email yang kamu daftarkan.</p>
        <form class="mt-8" @submit.prevent="submit"><PrimaryButton type="submit" class="w-full" :disabled="form.processing">{{ form.processing ? 'Mengirim...' : 'Kirim ulang email verifikasi' }}</PrimaryButton></form>
        <Link :href="route('logout')" method="post" as="button" class="mt-4 inline-flex min-h-12 w-full items-center justify-center rounded-full border border-border text-sm font-semibold text-ink-900 hover:bg-canvas">Keluar</Link>
    </GuestLayout>
</template>
