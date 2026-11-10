<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, useId, watch } from 'vue';
import AppIcon from '@/Components/AppIcon.vue';
import locationsData from '../../data/indonesia-locations.json';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Pilih kota atau provinsi...',
    },
    id: {
        type: String,
        default: null,
    },
    name: {
        type: String,
        default: null,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    invalid: {
        type: Boolean,
        default: false,
    },
    describedBy: {
        type: String,
        default: undefined,
    },
    rounded: {
        type: String,
        default: '2xl', // 'xl' or '2xl'
    },
    allowProvinceOnly: {
        type: Boolean,
        default: true,
    },
    bgClass: {
        type: String,
        default: 'bg-surface',
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const generatedId = useId();
const elementId = computed(() => props.id || `location-select-${generatedId}`);
const listboxId = computed(() => `${elementId.value}-listbox`);

const open = ref(false);
const query = ref('');
const rootRef = ref(null);
const searchInput = ref(null);
const triggerButton = ref(null);

const provinces = locationsData.provinces || [];
const cities = locationsData.cities || [];

// Filtered options based on user search query
const filteredProvinces = computed(() => {
    if (!props.allowProvinceOnly) return [];
    const q = query.value.trim().toLowerCase();
    if (!q) return [];
    return provinces.filter(p => p.toLowerCase().includes(q)).slice(0, 5);
});

const filteredCities = computed(() => {
    const q = query.value.trim().toLowerCase();
    if (!q) {
        // Show sample popular cities by default when opened without query
        return cities.slice(0, 15);
    }
    return cities.filter(c => {
        return c.city.toLowerCase().includes(q)
            || c.province.toLowerCase().includes(q)
            || c.label.toLowerCase().includes(q);
    }).slice(0, 35);
});

const toggleDropdown = () => {
    if (props.disabled) return;
    open.value = !open.value;
    if (open.value) {
        query.value = '';
        nextTick(() => {
            searchInput.value?.focus();
        });
    }
};

const selectLocation = (val) => {
    if (props.disabled) return;
    emit('update:modelValue', val);
    emit('change', val);
    open.value = false;
    nextTick(() => {
        triggerButton.value?.focus();
    });
};

const clearLocation = (event) => {
    event.stopPropagation();
    emit('update:modelValue', '');
    emit('change', '');
    query.value = '';
};

const handleClickOutside = (event) => {
    if (rootRef.value && !rootRef.value.contains(event.target)) {
        open.value = false;
    }
};

const handleKeyDown = (event) => {
    if (props.disabled) return;
    if (event.key === 'Escape' && open.value) {
        event.preventDefault();
        open.value = false;
        triggerButton.value?.focus();
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const roundedClasses = computed(() => {
    return props.rounded === 'xl' ? 'rounded-xl' : 'rounded-2xl';
});
</script>

<template>
    <div ref="rootRef" class="relative w-full" @keydown="handleKeyDown">
        <!-- Hidden input for standard forms -->
        <input
            v-if="name"
            type="hidden"
            :name="name"
            :value="modelValue"
        />

        <!-- Trigger Button -->
        <button
            :id="elementId"
            ref="triggerButton"
            type="button"
            role="combobox"
            aria-haspopup="listbox"
            :aria-expanded="open"
            :aria-controls="listboxId"
            :aria-invalid="invalid"
            :aria-describedby="describedBy"
            :disabled="disabled"
            class="flex min-h-11 w-full items-center justify-between gap-2.5 border border-border px-3.5 py-2.5 text-left text-sm transition-all focus:outline-none disabled:cursor-not-allowed disabled:opacity-60"
            :class="[
                roundedClasses,
                bgClass,
                open ? 'border-brand-600 ring-2 ring-brand-600/20' : 'hover:border-ink-900/30',
                invalid ? '!border-danger !ring-danger/20' : '',
            ]"
            @click="toggleDropdown"
        >
            <div class="flex min-w-0 items-center gap-2 truncate">
                <AppIcon name="location" class="h-4 w-4 shrink-0 text-ink-500" />
                <span
                    class="truncate"
                    :class="modelValue ? 'font-medium text-ink-900' : 'font-normal text-ink-500'"
                >
                    {{ modelValue || placeholder }}
                </span>
            </div>

            <div class="flex shrink-0 items-center gap-1.5">
                <button
                    v-if="modelValue && !disabled"
                    type="button"
                    class="flex h-5 w-5 items-center justify-center rounded-full text-ink-400 hover:bg-canvas hover:text-ink-900 focus:outline-none"
                    aria-label="Hapus pilihan wilayah"
                    @click="clearLocation"
                >
                    <AppIcon name="close" class="h-3.5 w-3.5" />
                </button>
                <AppIcon
                    name="chevron-down"
                    class="h-4 w-4 text-ink-500 transition-transform duration-200"
                    :class="{ 'rotate-180 text-ink-900': open }"
                />
            </div>
        </button>

        <!-- Search Dropdown Panel -->
        <Transition
            enter-active-class="transition ease-out duration-150"
            enter-from-class="opacity-0 scale-95 -translate-y-1"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 -translate-y-1"
        >
            <div
                v-show="open"
                :id="listboxId"
                role="listbox"
                class="absolute left-0 right-0 top-full z-50 mt-1.5 overflow-hidden rounded-2xl border border-border bg-surface shadow-xl focus:outline-none"
            >
                <!-- Search Input Header -->
                <div class="border-b border-border p-2">
                    <div class="flex items-center rounded-xl bg-canvas px-3 focus-within:ring-2 focus-within:ring-brand-600">
                        <AppIcon name="search" class="h-4 w-4 shrink-0 text-ink-500" />
                        <input
                            ref="searchInput"
                            v-model="query"
                            type="text"
                            placeholder="Ketik kota atau provinsi..."
                            class="h-10 w-full border-0 bg-transparent px-2.5 text-sm text-ink-900 placeholder:text-ink-500 focus:ring-0"
                            autocomplete="off"
                        />
                        <button
                            v-if="query"
                            type="button"
                            class="text-ink-500 hover:text-ink-900"
                            @click="query = ''"
                        >
                            <AppIcon name="close" class="h-3.5 w-3.5" />
                        </button>
                    </div>
                </div>

                <!-- Results List -->
                <div class="max-h-64 overflow-y-auto p-1.5">
                    <!-- Province Category Match -->
                    <template v-if="filteredProvinces.length > 0">
                        <p class="px-3 pb-1 pt-2 text-[11px] font-semibold uppercase tracking-wider text-ink-500">
                            Provinsi
                        </p>
                        <button
                            v-for="prov in filteredProvinces"
                            :key="`prov-${prov}`"
                            type="button"
                            role="option"
                            :aria-selected="modelValue === prov"
                            class="group flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm transition-colors"
                            :class="modelValue === prov ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-ink-900 hover:bg-canvas font-medium'"
                            @click="selectLocation(prov)"
                        >
                            <div class="flex items-center gap-2 truncate">
                                <span class="rounded bg-brand-100 px-1.5 py-0.5 text-[10px] font-bold text-brand-700">PROV</span>
                                <span class="truncate">{{ prov }}</span>
                            </div>
                            <AppIcon v-if="modelValue === prov" name="check" class="h-4 w-4 shrink-0 text-brand-600" />
                        </button>
                    </template>

                    <!-- City / Regency Matches -->
                    <template v-if="filteredCities.length > 0">
                        <p class="px-3 pb-1 pt-2 text-[11px] font-semibold uppercase tracking-wider text-ink-500">
                            Kota & Kabupaten
                        </p>
                        <button
                            v-for="item in filteredCities"
                            :key="item.label"
                            type="button"
                            role="option"
                            :aria-selected="modelValue === item.label"
                            class="group flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm transition-colors"
                            :class="modelValue === item.label ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-ink-900 hover:bg-canvas font-medium'"
                            @click="selectLocation(item.label)"
                        >
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-ink-900 group-hover:text-ink-900" :class="{ '!text-brand-700 font-semibold': modelValue === item.label }">
                                    {{ item.city }}
                                </p>
                                <p class="truncate text-xs text-ink-500">
                                    {{ item.province }}
                                </p>
                            </div>
                            <AppIcon v-if="modelValue === item.label" name="check" class="h-4 w-4 shrink-0 text-brand-600" />
                        </button>
                    </template>

                    <!-- Empty State -->
                    <div
                        v-if="filteredProvinces.length === 0 && filteredCities.length === 0"
                        class="p-6 text-center text-sm text-ink-500"
                    >
                        <AppIcon name="location" class="mx-auto mb-2 h-6 w-6 text-ink-400" />
                        <p class="font-medium">Wilayah tidak ditemukan</p>
                        <p class="mt-1 text-xs text-ink-400">Coba gunakan kata kunci kota atau provinsi lain.</p>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>
