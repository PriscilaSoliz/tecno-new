import { ref } from 'vue';

const notifications = ref([]);
const confirmation = ref({
    show: false,
    title: '',
    message: '',
    resolve: null,
    reject: null
});

export function useNotifications() {
    const notify = ({ title, message, type = 'info', duration = 5000 }) => {
        const id = Date.now() + Math.random();

        notifications.value.push({
            id,
            title,
            message,
            type, // 'success', 'error', 'warning', 'info'
            duration
        });

        if (duration > 0) {
            setTimeout(() => {
                remove(id);
            }, duration);
        }
    };

    const remove = (id) => {
        const index = notifications.value.findIndex(n => n.id === id);
        if (index !== -1) {
            notifications.value.splice(index, 1);
        }
    };

    const confirm = (message, title = 'Confirmar Acción') => {
        confirmation.value = {
            show: true,
            title,
            message,
            resolve: null,
            reject: null
        };

        return new Promise((resolve, reject) => {
            confirmation.value.resolve = resolve;
            confirmation.value.reject = reject;
        });
    };

    const handleConfirm = (value) => {
        if (value && confirmation.value.resolve) {
            confirmation.value.resolve(true);
        } else if (confirmation.value.resolve) {
            confirmation.value.resolve(false);
        }
        confirmation.value.show = false;
    };

    const success = (message, title = 'Éxito') => notify({ title, message, type: 'success' });
    const error = (message, title = 'Error') => notify({ title, message, type: 'error' });
    const warning = (message, title = 'Advertencia') => notify({ title, message, type: 'warning' });
    const info = (message, title = 'Información') => notify({ title, message, type: 'info' });

    return {
        notifications,
        confirmation,
        notify,
        remove,
        confirm,
        handleConfirm,
        success,
        error,
        warning,
        info
    };
}
