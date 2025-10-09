import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';

export function useFlashMessage() {
    const page = usePage();

    const showFlashMessage = () => {
        watch(
            () => page.props.flash,
            (flash) => {
                if (flash?.success) {
                    // Using native alert for now, can be replaced with toast library
                    alert(flash.success);
                }
                if (flash?.error) {
                    alert(flash.error);
                }
            },
            { immediate: true, deep: true }
        );
    };

    const showSuccess = (message) => {
        alert(message);
    };

    const showError = (message) => {
        alert(message);
    };

    const showWarning = (message) => {
        alert(message);
    };

    const showInfo = (message) => {
        alert(message);
    };

    return {
        showFlashMessage,
        showSuccess,
        showError,
        showWarning,
        showInfo,
    };
}
