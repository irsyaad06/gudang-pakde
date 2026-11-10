<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { nextTick } from 'vue';

defineProps({ status: { type: String } });
const form = useForm({ email: '' });
const submit = () => form.post(route('password.email'), { onError: () => nextTick(() => document.querySelector('#forgot-email')?.focus()) });
</script>

<template>
    <GuestLayout>
        <Head title="Lupa Kata Sandi" />
        <h1 class="text-3xl font-semibold tracking-tight text-ink-900">Lupa kata sandi?</h1><p class="mt-3 text-sm leading-relaxed text-ink-600">Masukkan email akunmu. Kami akan mengirim tautan untuk mengatur ulang kata sandi.</p>
        <p v-if="status" role="status" class="mt-5 rounded-2xl border border-border bg-success-soft p-4 text-sm font-medium text-success">{{ status }}</p>
        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <div><InputLabel for="forgot-email" value="Email" /><TextInput id="forgot-email" v-model="form.email" type="email" class="mt-2 block w-full" required autofocus autocomplete="username" :aria-invalid="!!form.errors.email" :aria-describedby="form.errors.email ? 'forgot-email-error' : undefined" /><InputError id="forgot-email-error" :message="form.errors.email" class="mt-2" /></div>
            <PrimaryButton type="submit" class="w-full" :disabled="form.processing">{{ form.processing ? 'Mengirim...' : 'Kirim tautan pemulihan' }}</PrimaryButton>
        </form>
        <p class="mt-6 text-center text-sm"><Link :href="route('login')" class="inline-flex min-h-11 items-center rounded-full border border-border px-5 font-semibold text-ink-900 hover:bg-canvas">Kembali ke masuk</Link></p>
    </GuestLayout>
</template>
