<script setup>
import AppIcon from '@/Components/AppIcon.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref, useId } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);
const dialogId = useId();
const form = useForm({ password: '' });
const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};
const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
const deleteUser = () => {
    if (form.processing) return;
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: closeModal,
        onError: () => { if (form.errors.password) passwordInput.value?.focus(); },
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <section aria-labelledby="delete-account-heading">
        <div class="flex flex-col items-start justify-between gap-5 sm:flex-row sm:items-center"><header><h2 id="delete-account-heading" class="text-xl font-semibold tracking-tight text-ink-900">Hapus akun</h2><p class="mt-2 max-w-lg text-sm leading-relaxed text-ink-600">Akun hanya dapat dihapus jika kamu belum pernah memasang barang. Penghapusan bersifat permanen.</p></header><button type="button" class="inline-flex min-h-12 shrink-0 items-center gap-2 rounded-full border border-red-200 px-5 text-sm font-semibold text-danger hover:bg-red-50" @click="confirmUserDeletion"><AppIcon name="trash" class="h-4 w-4" />Hapus Akun</button></div>
        <Modal :show="confirmingUserDeletion" :closeable="!form.processing" max-width="md" :title-id="`${dialogId}-title`" :description-id="`${dialogId}-description`" @close="closeModal">
            <div class="p-6 sm:p-8">
                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-danger"><AppIcon name="trash" class="h-5 w-5" /></div>
                <h2 :id="`${dialogId}-title`" class="text-xl font-semibold tracking-tight text-ink-900">Yakin ingin menghapus akun?</h2>
                <p :id="`${dialogId}-description`" class="mt-3 text-sm leading-relaxed text-ink-600">Akun yang memiliki riwayat barang tidak dapat dihapus. Masukkan kata sandi untuk mengonfirmasi.</p>
                <p v-if="form.errors.account" role="alert" class="mt-4 rounded-2xl bg-red-50 p-4 text-sm text-danger">{{ form.errors.account }}</p>
                <div class="mt-5"><InputLabel :for="`${dialogId}-password`" value="Kata sandi" /><TextInput :id="`${dialogId}-password`" ref="passwordInput" v-model="form.password" type="password" placeholder="Masukkan kata sandi Anda untuk konfirmasi" autocomplete="current-password" class="mt-2 block w-full" :aria-invalid="!!form.errors.password" :aria-describedby="form.errors.password ? `${dialogId}-password-error` : undefined" @keyup.enter="deleteUser" /><InputError :id="`${dialogId}-password-error`" :message="form.errors.password" class="mt-2" /></div>
                <div class="mt-8 flex flex-wrap justify-end gap-3"><SecondaryButton :disabled="form.processing" @click="closeModal">Batal</SecondaryButton><DangerButton :disabled="form.processing" @click="deleteUser">{{ form.processing ? 'Menghapus...' : 'Hapus Akun' }}</DangerButton></div>
            </div>
        </Modal>
    </section>
</template>
