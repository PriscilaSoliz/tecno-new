import { computed, onMounted, watch } from 'vue';
import { useStorage } from '@vueuse/core';

export function useDarkMode() {
    const isDark = useStorage('theme-dark', false);

    const toggleDark = () => {
        isDark.value = !isDark.value;
    };

    const updateTheme = () => {
        if (isDark.value) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    };

    // Observar cambios y aplicar
    watch(isDark, updateTheme, { immediate: true });

    return {
        isDark: computed(() => isDark.value),
        toggleDark,
    };
}
