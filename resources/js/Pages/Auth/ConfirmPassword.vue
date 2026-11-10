<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { nextTick } from 'vue';

const form = useForm({ password: '' });
const submit = () => form.post(route('password.confirm'), {
    onError: () => nextTick(() => document.querySelector('#confirm-password')?.focus()),
    onFinish: () => form.reset(),
});
</script>

<template>
    <GuestLayout>
        <Head title="Konfirmasi Kata Sandi" />
        <h1 class="text-3xl font-semibold tracking-tight text-ink-900">Konfirmasi kata sandi</h1><p class="mt-3 text-sm leading-relaxed text-ink-600">Untuk melanjutkan ke halaman aman ini, masukkan kembali kata sandimu.</p>
        <form class="mt-8 space-y-5" @submit.prevent="submit">
            <div><InputLabel for="confirm-password" value="Kata sandi" /><TextInput id="confirm-password" v-model="form.password" type="password" class="mt-2 block w-full" required autofocus autocomplete="current-password" :aria-invalid="!!form.errors.password" :aria-describedby="form.errors.password ? 'confirm-password-error' : undefined" /><InputError id="confirm-password-error" :message="form.errors.password" class="mt-2" /></div>
            <PrimaryButton type="submit" class="w-full" :disabled="form.processing">{{ form.processing ? 'Memproses...' : 'Konfirmasi' }}</PrimaryButton>
        </form>
    </GuestLayout>
</template>
