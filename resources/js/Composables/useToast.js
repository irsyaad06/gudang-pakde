import { ref } from 'vue';

const toasts = ref([]);
let toastSeq = 0;

export function useToast() {
    const add = ({ message, type = 'success', title = '', duration = 4500 }) => {
        if (!message) return;
        const id = ++toastSeq;
        const toast = { id, message, type, title };
        toasts.value.push(toast);

        if (duration > 0) {
            setTimeout(() => {
                remove(id);
            }, duration);
        }

        return id;
    };

    const remove = (id) => {
        const index = toasts.value.findIndex((t) => t.id === id);
        if (index !== -1) {
            toasts.value.splice(index, 1);
        }
    };

    const success = (message, title = '', duration = 4500) =>
        add({ message, type: 'success', title, duration });

    const error = (message, title = '', duration = 5000) =>
        add({ message, type: 'error', title, duration });

    const info = (message, title = '', duration = 4500) =>
        add({ message, type: 'info', title, duration });

    const warning = (message, title = '', duration = 4500) =>
        add({ message, type: 'warning', title, duration });

    return {
        toasts,
        add,
        remove,
        success,
        error,
        info,
        warning,
    };
}
