<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AppIcon from '@/Components/AppIcon.vue';
import OwnedListings from '@/Components/OwnedListings.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({ mustVerifyEmail: { type: Boolean }, status: { type: String }, ownedListings: { type: Object, required: true } });
const page = usePage();
const sections = [
    { id: 'account-information', label: 'Informasi profil', icon: 'user' },
    { id: 'account-security', label: 'Kata sandi', icon: 'settings' },
    { id: 'account-listings', label: 'Barang saya', icon: 'box' },
    { id: 'account-deletion', label: 'Hapus akun', icon: 'trash' },
];
</script>

<template>
    <Head title="Pengaturan Akun" />
    <AppLayout>
        <header class="mb-8"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-ink-500">Akun saya</p><h1 class="mt-3 text-3xl font-semibold tracking-tight text-ink-900 sm:text-4xl">Pengaturan akun</h1><p class="mt-3 text-sm leading-relaxed text-ink-600">Detail pribadi, keamanan, dan barang yang kamu pasang.</p></header>
        <div class="grid items-start gap-6 lg:grid-cols-[15rem_minmax(0,1fr)] lg:gap-8">
            <aside class="min-w-0 rounded-3xl border border-border bg-surface p-5 shadow-sm lg:sticky lg:top-6">
                <div class="border-b border-border pb-5"><div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-photo text-ink-600"><AppIcon name="user" class="h-6 w-6" /></div><p class="break-words font-semibold tracking-tight">{{ page.props.auth.user.name }}</p><p class="mt-1 break-words text-sm text-ink-500">{{ page.props.auth.user.email }}</p></div>
                <nav class="mt-4 flex flex-wrap gap-1 lg:flex-col" aria-label="Bagian pengaturan akun"><a v-for="section in sections" :key="section.id" :href="`#${section.id}`" class="inline-flex min-h-11 items-center gap-2.5 rounded-full px-3 text-sm font-medium text-ink-600 hover:bg-canvas hover:text-ink-900"><AppIcon :name="section.icon" class="h-4 w-4 shrink-0" />{{ section.label }}</a></nav>
            </aside>
            <div class="min-w-0 space-y-6">
                <div id="account-information" class="scroll-mt-6 rounded-[2rem] border border-border bg-surface p-5 shadow-sm sm:p-8"><UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" /></div>
                <div id="account-security" class="scroll-mt-6 rounded-[2rem] border border-border bg-surface p-5 shadow-sm sm:p-8"><UpdatePasswordForm /></div>
                <section id="account-listings" class="min-w-0 scroll-mt-6" aria-labelledby="owned-heading">
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-3"><div><h2 id="owned-heading" class="text-xl font-semibold tracking-tight text-ink-900">Barang yang saya pasang</h2><p class="mt-2 text-sm text-ink-600">Kelola barang tanpa meninggalkan profilmu.</p></div><Link :href="route('listings.mine')" class="inline-flex min-h-11 items-center gap-2 rounded-full border border-border bg-white px-4 text-sm font-semibold text-ink-900 hover:bg-photo">Barang Saya<AppIcon name="arrow-up-right" class="h-4 w-4" /></Link></div>
                    <OwnedListings :listings="ownedListings" />
                </section>
                <div id="account-deletion" class="scroll-mt-6 rounded-[2rem] border border-border bg-surface p-5 shadow-sm sm:p-8"><DeleteUserForm /></div>
            </div>
        </div>
    </AppLayout>
</template>
