<script setup>
import AppIcon from '@/Components/AppIcon.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { nextTick } from 'vue';

defineProps({ canResetPassword: { type: Boolean }, status: { type: String } });
const form = useForm({ email: '', password: '', remember: false });
const submit = () => form.post(route('login'), {
    onError: () => nextTick(() => document.querySelector('#login-form [aria-invalid="true"]')?.focus()),
    onFinish: () => form.reset('password'),
});
</script>

<template>
    <GuestLayout>
        <Head title="Masuk" />
        <h1 class="text-2xl font-semibold tracking-tight text-ink-900 sm:text-3xl">Selamat datang kembali</h1>
        <p class="mt-1 text-xs leading-relaxed text-ink-600 sm:text-sm">Masuk untuk memasang dan mengelola barangmu.</p>
        <div v-if="status" role="status" class="mt-3.5 rounded-xl border border-border bg-success-soft p-3 text-xs font-medium text-success sm:text-sm">{{ status }}</div>
        <form id="login-form" class="mt-5 space-y-3.5 sm:mt-6" @submit.prevent="submit">
            <div>
                <InputLabel for="login-email" value="Email" />
                <TextInput id="login-email" v-model="form.email" type="email" class="mt-1.5 block w-full" placeholder="nama@email.com" required autofocus autocomplete="username" :aria-invalid="!!form.errors.email" :aria-describedby="form.errors.email ? 'login-email-error' : undefined" />
                <InputError id="login-email-error" :message="form.errors.email" class="mt-1" />
            </div>
            <div>
                <div class="flex flex-wrap items-center justify-between gap-1.5">
                    <InputLabel for="login-password" value="Kata sandi" />
                    <Link v-if="canResetPassword" :href="route('password.request')" class="inline-flex items-center rounded-lg text-xs font-medium text-ink-600 hover:text-ink-900 hover:underline">Lupa kata sandi?</Link>
                </div>
                <TextInput id="login-password" v-model="form.password" type="password" class="mt-1.5 block w-full" placeholder="Masukkan kata sandi akun Anda" required autocomplete="current-password" :aria-invalid="!!form.errors.password" :aria-describedby="form.errors.password ? 'login-password-error' : undefined" />
                <InputError id="login-password-error" :message="form.errors.password" class="mt-1" />
            </div>
            <label class="inline-flex min-h-7 items-center gap-2 text-xs text-ink-600 sm:text-sm">
                <input v-model="form.remember" type="checkbox" name="remember" class="h-4 w-4 rounded-md border-border text-ink-900 focus:ring-brand-600" />
                <span>Ingat saya</span>
            </label>
            <PrimaryButton type="submit" class="w-full" :disabled="form.processing">
                {{ form.processing ? 'Memproses...' : 'Masuk' }}
                <AppIcon name="arrow-right" class="h-4 w-4" />
            </PrimaryButton>
        </form>
        <p class="mt-5 border-t border-border pt-3.5 text-center text-xs text-ink-600 sm:text-sm">Belum punya akun? <Link :href="route('register')" class="inline-flex items-center rounded-lg font-semibold text-ink-900 underline underline-offset-4">Daftar sekarang</Link></p>
    </GuestLayout>
</template>
