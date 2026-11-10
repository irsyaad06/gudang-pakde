<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { nextTick } from 'vue';

const form = useForm({ name: '', email: '', password: '', password_confirmation: '' });
const submit = () => form.post(route('register'), {
    onError: () => nextTick(() => document.querySelector('#register-form [aria-invalid="true"]')?.focus()),
    onFinish: () => form.reset('password', 'password_confirmation'),
});
</script>

<template>
    <GuestLayout>
        <Head title="Daftar" />
        <h1 class="text-3xl font-semibold tracking-tight text-ink-900">Buat akun</h1><p class="mt-3 text-sm leading-relaxed text-ink-600">Bergabung dan mulai temukan atau pasang barang di komunitas.</p>
        <form id="register-form" class="mt-8 space-y-5" @submit.prevent="submit">
            <div><InputLabel for="register-name" value="Nama" /><TextInput id="register-name" v-model="form.name" type="text" class="mt-2 block w-full" required autofocus autocomplete="name" :aria-invalid="!!form.errors.name" :aria-describedby="form.errors.name ? 'register-name-error' : undefined" /><InputError id="register-name-error" :message="form.errors.name" class="mt-2" /></div>
            <div><InputLabel for="register-email" value="Email" /><TextInput id="register-email" v-model="form.email" type="email" class="mt-2 block w-full" required autocomplete="username" :aria-invalid="!!form.errors.email" :aria-describedby="form.errors.email ? 'register-email-error' : undefined" /><InputError id="register-email-error" :message="form.errors.email" class="mt-2" /></div>
            <div><InputLabel for="register-password" value="Kata sandi" /><TextInput id="register-password" v-model="form.password" type="password" class="mt-2 block w-full" required autocomplete="new-password" :aria-invalid="!!form.errors.password" :aria-describedby="form.errors.password ? 'register-password-error' : undefined" /><InputError id="register-password-error" :message="form.errors.password" class="mt-2" /></div>
            <div><InputLabel for="register-confirmation" value="Konfirmasi kata sandi" /><TextInput id="register-confirmation" v-model="form.password_confirmation" type="password" class="mt-2 block w-full" required autocomplete="new-password" :aria-invalid="!!form.errors.password_confirmation" :aria-describedby="form.errors.password_confirmation ? 'register-confirmation-error' : undefined" /><InputError id="register-confirmation-error" :message="form.errors.password_confirmation" class="mt-2" /></div>
            <PrimaryButton type="submit" class="w-full" :disabled="form.processing">{{ form.processing ? 'Memproses...' : 'Buat Akun' }}</PrimaryButton>
        </form>
        <p class="mt-8 border-t border-border pt-5 text-center text-sm text-ink-600">Sudah punya akun? <Link :href="route('login')" class="inline-flex min-h-11 items-center rounded-lg font-semibold text-ink-900 underline underline-offset-4">Masuk</Link></p>
    </GuestLayout>
</template>
