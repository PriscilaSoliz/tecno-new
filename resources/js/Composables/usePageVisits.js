import { ref, computed, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

export function usePageVisits() {
    const visits = ref(0);
    const pageName = ref('');
    const pageTitle = ref('');
    const isLoading = ref(false);

    const page = usePage();

    /**
     * Cargar datos de visitas de la página actual
     */
    const loadPageVisits = () => {
        try {
            // Obtener información de la ruta actual
            const currentRoute = route().current();
            const routeName = currentRoute ? currentRoute : 'unknown';
            
            pageName.value = routeName;
            pageTitle.value = document.title || ucfirst(routeName.replace(/\./g, ' - '));

            // Las visitas se cargan desde props del servidor (si existen)
            if (page.props.pageVisits) {
                visits.value = page.props.pageVisits;
            }
        } catch (error) {
            console.error('Error loading page visits:', error);
        }
    };

    /**
     * Reiniciar contador de todas las páginas
     */
    const resetAllVisits = async () => {
        if (!confirm('¿Estás seguro de que deseas reiniciar el contador de visitas de TODAS las páginas?')) {
            return;
        }

        isLoading.value = true;
        try {
            await router.post(route('page-visits.reset-all'), {}, {
                onSuccess: () => {
                    visits.value = 0;
                    window.location.reload();
                },
                onError: (errors) => {
                    console.error('Error resetting visits:', errors);
                    alert('Error al reiniciar contadores');
                }
            });
        } catch (error) {
            console.error('Error:', error);
        } finally {
            isLoading.value = false;
        }
    };

    /**
     * Reiniciar contador de la página actual
     */
    const resetCurrentPageVisits = async () => {
        if (!confirm(`¿Reiniciar el contador de esta página?`)) {
            return;
        }

        isLoading.value = true;
        try {
            await router.post(route('page-visits.reset', { pageName: pageName.value }), {}, {
                onSuccess: () => {
                    visits.value = 0;
                    window.location.reload();
                },
                onError: (errors) => {
                    console.error('Error resetting current page visits:', errors);
                }
            });
        } catch (error) {
            console.error('Error:', error);
        } finally {
            isLoading.value = false;
        }
    };

    onMounted(() => {
        loadPageVisits();
    });

    return {
        visits: computed(() => visits.value),
        pageName: computed(() => pageName.value),
        pageTitle: computed(() => pageTitle.value),
        isLoading: computed(() => isLoading.value),
        loadPageVisits,
        resetAllVisits,
        resetCurrentPageVisits
    };
}

function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}
