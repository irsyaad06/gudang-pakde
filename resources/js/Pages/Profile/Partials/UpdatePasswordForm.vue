<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);
const confirmationInput = ref(null);
const form = useForm({ current_password: '', password: '', password_confirmation: '' });
const updatePassword = () => {
    if (form.processing) return;
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) form.reset('password', 'password_confirmation');
            if (form.errors.current_password) form.reset('current_password');
            nextTick(() => {
                if (form.errors.current_password) currentPasswordInput.value?.focus();
                else if (form.errors.password) passwordInput.value?.focus();
                else if (form.errors.password_confirmation) confirmationInput.value?.focus();
            });
        },
    });
};
</script>

<template>
    <section aria-labelledby="password-heading">
        <header class="border-b border-border pb-6"><h2 id="password-heading" class="text-xl font-semibold tracking-tight text-ink-900">Ubah kata sandi</h2><p class="mt-2 text-sm leading-relaxed text-ink-600">Jaga keamanan akun dengan kata sandi yang kuat dan unik.</p></header>
        <form class="mt-6 space-y-5" @submit.prevent="updatePassword">
            <div><InputLabel for="current_password" value="Kata sandi saat ini" /><TextInput id="current_password" ref="currentPasswordInput" v-model="form.current_password" type="password" class="mt-2 block w-full" autocomplete="current-password" :aria-invalid="!!form.errors.current_password" :aria-describedby="form.errors.current_password ? 'current-password-error' : undefined" /><InputError id="current-password-error" :message="form.errors.current_password" class="mt-2" /></div>
            <div><InputLabel for="new_password" value="Kata sandi baru" /><TextInput id="new_password" ref="passwordInput" v-model="form.password" type="password" class="mt-2 block w-full" autocomplete="new-password" :aria-invalid="!!form.errors.password" :aria-describedby="form.errors.password ? 'new-password-error' : undefined" /><InputError id="new-password-error" :message="form.errors.password" class="mt-2" /></div>
            <div><InputLabel for="new_password_confirmation" value="Konfirmasi kata sandi baru" /><TextInput id="new_password_confirmation" ref="confirmationInput" v-model="form.password_confirmation" type="password" class="mt-2 block w-full" autocomplete="new-password" :aria-invalid="!!form.errors.password_confirmation" :aria-describedby="form.errors.password_confirmation ? 'new-password-confirmation-error' : undefined" /><InputError id="new-password-confirmation-error" :message="form.errors.password_confirmation" class="mt-2" /></div>
            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-border pt-5"><p v-if="form.recentlySuccessful" role="status" class="text-sm text-success">Kata sandi tersimpan.</p><PrimaryButton type="submit" class="w-full sm:ml-auto sm:w-auto" :disabled="form.processing">{{ form.processing ? 'Menyimpan...' : 'Simpan Kata Sandi' }}</PrimaryButton></div>
        </form>
    </section>
</template>
