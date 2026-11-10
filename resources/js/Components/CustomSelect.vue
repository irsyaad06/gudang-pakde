<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, useId } from 'vue';
import AppIcon from '@/Components/AppIcon.vue';

const props = defineProps({
    modelValue: {
        type: [String, Number, null],
        default: '',
    },
    options: {
        type: Array,
        required: true,
    },
    valueKey: {
        type: String,
        default: 'value',
    },
    labelKey: {
        type: String,
        default: 'label',
    },
    placeholder: {
        type: String,
        default: 'Pilih opsi',
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
    required: {
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
        default: 'xl', // 'xl' or '2xl'
    },
    bgClass: {
        type: String,
        default: 'bg-surface',
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const generatedId = useId();
const elementId = computed(() => props.id || `custom-select-${generatedId}`);
const listboxId = computed(() => `${elementId.value}-listbox`);

const open = ref(false);
const rootRef = ref(null);
const triggerButton = ref(null);

const normalizedOptions = computed(() => {
    return props.options.map(opt => {
        if (typeof opt === 'object' && opt !== null) {
            const value = opt[props.valueKey] !== undefined ? opt[props.valueKey] : opt.value;
            const label = opt[props.labelKey] !== undefined ? opt[props.labelKey] : (opt.label || opt.name || String(value));
            return { value, label, raw: opt };
        }
        return { value: opt, label: String(opt), raw: opt };
    });
});

const selectedOption = computed(() => {
    if (props.modelValue === '' || props.modelValue === null || props.modelValue === undefined) {
        return null;
    }
    return normalizedOptions.value.find(opt => String(opt.value) === String(props.modelValue)) || null;
});

const toggleDropdown = () => {
    if (props.disabled) return;
    open.value = !open.value;
};

const selectOption = (opt) => {
    if (props.disabled) return;
    emit('update:modelValue', opt.value);
    emit('change', opt.value);
    open.value = false;
    nextTick(() => {
        triggerButton.value?.focus();
    });
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
    } else if (event.key === 'ArrowDown') {
        event.preventDefault();
        if (!open.value) {
            open.value = true;
        } else {
            const currentIndex = normalizedOptions.value.findIndex(o => String(o.value) === String(props.modelValue));
            if (currentIndex < normalizedOptions.value.length - 1) {
                selectOption(normalizedOptions.value[currentIndex + 1]);
            }
        }
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        if (!open.value) {
            open.value = true;
        } else {
            const currentIndex = normalizedOptions.value.findIndex(o => String(o.value) === String(props.modelValue));
            if (currentIndex > 0) {
                selectOption(normalizedOptions.value[currentIndex - 1]);
            }
        }
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const roundedClasses = computed(() => {
    return props.rounded === '2xl' ? 'rounded-2xl' : 'rounded-xl';
});
</script>

<template>
    <div ref="rootRef" class="relative w-full" @keydown="handleKeyDown">
        <!-- Native Hidden input for form data -->
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
            class="flex min-h-11 w-full items-center justify-between gap-3 border border-border px-4 py-2.5 text-left text-sm transition-all focus:outline-none disabled:cursor-not-allowed disabled:opacity-60"
            :class="[
                roundedClasses,
                bgClass,
                open ? 'border-brand-600 ring-2 ring-brand-600/20' : 'hover:border-ink-900/30',
                invalid ? '!border-danger !ring-danger/20' : '',
            ]"
            @click="toggleDropdown"
        >
            <span
                class="truncate font-normal"
                :class="selectedOption ? 'text-ink-900 font-medium' : 'text-ink-500'"
            >
                {{ selectedOption ? selectedOption.label : placeholder }}
            </span>

            <AppIcon
                name="chevron-down"
                class="h-4 w-4 shrink-0 text-ink-500 transition-transform duration-200"
                :class="{ 'rotate-180 text-ink-900': open }"
            />
        </button>

        <!-- Dropdown Result List Panel (Hasil Down) -->
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
                :aria-labelledby="elementId"
                class="absolute left-0 right-0 top-full z-40 mt-1.5 max-h-60 overflow-y-auto rounded-2xl border border-border bg-surface p-1.5 shadow-lg focus:outline-none"
            >
                <button
                    v-for="opt in normalizedOptions"
                    :key="String(opt.value)"
                    type="button"
                    role="option"
                    :aria-selected="String(modelValue) === String(opt.value)"
                    class="group flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-left text-sm transition-colors"
                    :class="[
                        String(modelValue) === String(opt.value)
                            ? 'bg-brand-50 text-brand-700 font-semibold'
                            : 'text-ink-900 hover:bg-canvas font-medium'
                    ]"
                    @click="selectOption(opt)"
                >
                    <span class="truncate">{{ opt.label }}</span>
                    <AppIcon
                        v-if="String(modelValue) === String(opt.value)"
                        name="check"
                        class="h-4 w-4 shrink-0 text-brand-600"
                    />
                </button>
            </div>
        </Transition>
    </div>
</template>
