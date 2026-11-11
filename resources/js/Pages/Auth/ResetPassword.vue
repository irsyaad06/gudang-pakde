<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { nextTick } from 'vue';

const props = defineProps({ email: { type: String, required: true }, token: { type: String, required: true } });
const form = useForm({ token: props.token, email: props.email, password: '', password_confirmation: '' });
const submit = () => form.post(route('password.store'), {
    onError: () => nextTick(() => document.querySelector('#reset-form [aria-invalid="true"]')?.focus()),
    onFinish: () => form.reset('password', 'password_confirmation'),
});
</script>

<template>
    <GuestLayout>
        <Head title="Atur Ulang Kata Sandi" />
        <h1 class="text-3xl font-semibold tracking-tight text-ink-900">Atur ulang kata sandi</h1><p class="mt-3 text-sm leading-relaxed text-ink-600">Buat kata sandi baru untuk kembali ke akunmu.</p>
        <form id="reset-form" class="mt-8 space-y-5" @submit.prevent="submit">
            <div><InputLabel for="reset-email" value="Email" /><TextInput id="reset-email" v-model="form.email" type="email" class="mt-2 block w-full" placeholder="nama@email.com" required autofocus autocomplete="username" :aria-invalid="!!form.errors.email" :aria-describedby="form.errors.email ? 'reset-email-error' : undefined" /><InputError id="reset-email-error" :message="form.errors.email" class="mt-2" /></div>
            <div><InputLabel for="reset-password" value="Kata sandi baru" /><TextInput id="reset-password" v-model="form.password" type="password" class="mt-2 block w-full" placeholder="Minimal 8 karakter baru" required autocomplete="new-password" :aria-invalid="!!form.errors.password" :aria-describedby="form.errors.password ? 'reset-password-error' : undefined" /><InputError id="reset-password-error" :message="form.errors.password" class="mt-2" /></div>
            <div><InputLabel for="reset-confirmation" value="Konfirmasi kata sandi baru" /><TextInput id="reset-confirmation" v-model="form.password_confirmation" type="password" class="mt-2 block w-full" placeholder="Ulangi kata sandi baru Anda" required autocomplete="new-password" :aria-invalid="!!form.errors.password_confirmation" :aria-describedby="form.errors.password_confirmation ? 'reset-confirmation-error' : undefined" /><InputError id="reset-confirmation-error" :message="form.errors.password_confirmation" class="mt-2" /></div>
            <PrimaryButton type="submit" class="w-full" :disabled="form.processing">{{ form.processing ? 'Menyimpan...' : 'Simpan kata sandi baru' }}</PrimaryButton>
        </form>
    </GuestLayout>
</template>
