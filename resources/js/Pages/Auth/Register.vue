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
        <h1 class="text-2xl font-semibold tracking-tight text-ink-900 sm:text-3xl">Buat akun</h1>
        <p class="mt-1 text-xs leading-relaxed text-ink-600 sm:text-sm">Bergabung dan mulai temukan atau pasang barang di komunitas.</p>
        <form id="register-form" class="mt-4 space-y-2.5 sm:mt-5 sm:space-y-3" @submit.prevent="submit">
            <div>
                <InputLabel for="register-name" value="Nama" />
                <TextInput id="register-name" v-model="form.name" type="text" class="mt-1 block w-full" placeholder="Masukkan Nama Lengkap Anda" required autofocus autocomplete="name" :aria-invalid="!!form.errors.name" :aria-describedby="form.errors.name ? 'register-name-error' : undefined" />
                <InputError id="register-name-error" :message="form.errors.name" class="mt-1" />
            </div>
            <div>
                <InputLabel for="register-email" value="Email" />
                <TextInput id="register-email" v-model="form.email" type="email" class="mt-1 block w-full" placeholder="nama@email.com" required autocomplete="username" :aria-invalid="!!form.errors.email" :aria-describedby="form.errors.email ? 'register-email-error' : undefined" />
                <InputError id="register-email-error" :message="form.errors.email" class="mt-1" />
            </div>
            <div>
                <InputLabel for="register-password" value="Kata sandi" />
                <TextInput id="register-password" v-model="form.password" type="password" class="mt-1 block w-full" placeholder="Minimal 8 karakter kombinasi" required autocomplete="new-password" :aria-invalid="!!form.errors.password" :aria-describedby="form.errors.password ? 'register-password-error' : undefined" />
                <InputError id="register-password-error" :message="form.errors.password" class="mt-1" />
            </div>
            <div>
                <InputLabel for="register-confirmation" value="Konfirmasi kata sandi" />
                <TextInput id="register-confirmation" v-model="form.password_confirmation" type="password" class="mt-1 block w-full" placeholder="Ulangi kata sandi Anda" required autocomplete="new-password" :aria-invalid="!!form.errors.password_confirmation" :aria-describedby="form.errors.password_confirmation ? 'register-confirmation-error' : undefined" />
                <InputError id="register-confirmation-error" :message="form.errors.password_confirmation" class="mt-1" />
            </div>
            <PrimaryButton type="submit" class="w-full mt-1" :disabled="form.processing">{{ form.processing ? 'Memproses...' : 'Buat Akun' }}</PrimaryButton>
        </form>
        <p class="mt-4 border-t border-border pt-3 text-center text-xs text-ink-600 sm:text-sm">Sudah punya akun? <Link :href="route('login')" class="inline-flex items-center rounded-lg font-semibold text-ink-900 underline underline-offset-4">Masuk</Link></p>
    </GuestLayout>
</template>
