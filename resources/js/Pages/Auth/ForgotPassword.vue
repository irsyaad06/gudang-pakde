<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AppIcon from '@/Components/AppIcon.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { nextTick } from 'vue';

defineProps({ status: { type: String } });
const form = useForm({ email: '' });
const submit = () => form.post(route('password.email'), {
    onError: () => nextTick(() => document.querySelector('#forgot-email')?.focus()),
});
</script>

<template>
    <GuestLayout>
        <Head title="Lupa Kata Sandi" />

        <!-- Coming Soon Feature Badge -->
        <div class="mb-3.5 inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800 shadow-sm">
            <span class="flex h-2 w-2 rounded-full bg-amber-500 animate-pulse" />
            <span>Coming Soon Feature</span>
        </div>

        <h1 class="text-2xl font-semibold tracking-tight text-ink-900 sm:text-3xl">Lupa kata sandi?</h1>
        <p class="mt-1.5 text-xs leading-relaxed text-ink-600 sm:text-sm">
            Layanan pengiriman email reset kata sandi saat ini belum diaktifkan (fitur Coming Soon). Untuk demo aplikasi, silakan gunakan akun yang sudah ada atau buat akun baru.
        </p>

        <p v-if="status" role="status" class="mt-4 rounded-xl border border-border bg-success-soft p-3 text-xs font-medium text-success sm:text-sm">
            {{ status }}
        </p>

        <form class="mt-5 space-y-3.5 sm:mt-6" @submit.prevent>
            <div>
                <InputLabel for="forgot-email" value="Email akun" class="opacity-70" />
                <TextInput
                    id="forgot-email"
                    v-model="form.email"
                    type="email"
                    class="mt-1.5 block w-full"
                    placeholder="Fitur belum aktif (Coming Soon)"
                    disabled
                    autocomplete="username"
                />
            </div>

            <PrimaryButton type="button" class="w-full" disabled>
                <span>Kirim tautan pemulihan (Coming Soon)</span>
                <AppIcon name="arrow-right" class="h-4 w-4" />
            </PrimaryButton>
        </form>

        <p class="mt-5 border-t border-border pt-3.5 text-center text-xs text-ink-600 sm:text-sm">
            Ingat kata sandi? <Link :href="route('login')" class="inline-flex items-center rounded-lg font-semibold text-ink-900 underline underline-offset-4">Kembali ke masuk</Link>
        </p>
    </GuestLayout>
</template>
