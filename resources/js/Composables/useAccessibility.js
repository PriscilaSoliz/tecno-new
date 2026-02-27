import { computed, watch } from 'vue';
import { useStorage } from '@vueuse/core';

export function useAccessibility() {
    const theme = useStorage('accessibility-theme', 'default');
    const fontSize = useStorage('accessibility-font-size', 'medium');
    const fontFamily = useStorage('accessibility-font-family', 'system');
    const isDark = useStorage('theme-dark', false);

    const fontSizeMap = {
        small: '14px',
        medium: '16px',
        large: '18px',
        xlarge: '20px'
    };

    const fontFamilyMap = {
        system: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
        sans: '"Inter", "Helvetica Neue", Arial, sans-serif',
        serif: '"Georgia", "Times New Roman", serif',
        mono: '"Courier New", Courier, monospace'
    };

    const applySettings = () => {
        const root = document.documentElement;
        
        // Aplicar tema visual
        root.setAttribute('data-theme', theme.value);
        
        // Aplicar modo oscuro
        if (isDark.value) {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }
        
        // Aplicar tamaño de fuente
        root.style.setProperty('--font-size-base', fontSizeMap[fontSize.value]);
        
        // Aplicar familia de fuente
        root.style.setProperty('--font-family-base', fontFamilyMap[fontFamily.value]);
    };

    const setTheme = (newTheme) => {
        theme.value = newTheme;
        applySettings();
    };

    const setFontSize = (newSize) => {
        fontSize.value = newSize;
        applySettings();
    };

    const setFontFamily = (newFamily) => {
        fontFamily.value = newFamily;
        applySettings();
    };

    const toggleDark = () => {
        isDark.value = !isDark.value;
        applySettings();
    };

    const resetToDefault = () => {
        theme.value = 'default';
        fontSize.value = 'medium';
        fontFamily.value = 'system';
        isDark.value = false;
        applySettings();
    };

    // Observar cambios
    watch([theme, fontSize, fontFamily, isDark], applySettings, { immediate: true });

    return {
        theme: computed(() => theme.value),
        fontSize: computed(() => fontSize.value),
        fontFamily: computed(() => fontFamily.value),
        isDark: computed(() => isDark.value),
        setTheme,
        setFontSize,
        setFontFamily,
        toggleDark,
        resetToDefault,
        fontSizeMap,
        fontFamilyMap
    };
}
