<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';

const page = usePage();
const menuItems = computed(() => page.props.menuItems || []);

// Obtener roles del usuario
const userRoles = computed(() => {
    const roles = page.props.auth?.user?.roles || [];
    if (!Array.isArray(roles)) return [];
    return roles.map(r => (typeof r === 'object' && r !== null ? (r.name ?? r) : String(r)));
});

const isActive = (routeName) => {
    if (!routeName) return false;
    try {
        return route().current(routeName);
    } catch {
        return false;
    }
};

const hasAccess = (itemRoles) => {
    // Si el usuario no está autenticado, no mostrar nada
    if (!page.props.auth?.user) {
        return false;
    }
    
    // Propietario siempre puede ver todo
    if (userRoles.value.includes('propietario')) return true;

    // Si no hay roles asignados al item o es array vacío -> todos pueden ver
    if (!itemRoles || (Array.isArray(itemRoles) && itemRoles.length === 0)) {
        return true;
    }

    // Asegurar que itemRoles sea un array
    const roles = Array.isArray(itemRoles) ? itemRoles : [];

    // Verificar si el usuario tiene alguno de los roles requeridos
    return roles.some(r => userRoles.value.includes(r));
};

const routeExists = (routeName) => {
    if (!routeName) return false;
    try {
        route(routeName);
        return true;
    } catch {
        return false;
    }
};
</script>

<template>
    <div class="hidden sm:flex sm:ms-10 items-center space-x-4">
        <template v-for="item in menuItems" :key="item.id">
            <div v-if="item.is_active && hasAccess(item.roles)">
                <!-- Item sin hijos -->
                <div v-if="!item.children?.length && routeExists(item.route)" class="relative">
                    <NavLink :href="route(item.route)" :active="isActive(item.route)">
                        <i v-if="item.icon" :class="item.icon" class="mr-1"></i>
                        {{ item.title }}
                    </NavLink>
                </div>
                
                <!-- Item con hijos (dropdown) -->
                <div v-else-if="item.children?.length" class="relative group">
                    <button class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-sm font-semibold transition-all duration-200 text-gray-600 hover:text-amber-600 hover:bg-amber-50">
                        <i v-if="item.icon" :class="item.icon"></i>
                        {{ item.title }}
                        <svg class="ms-2 -me-0.5 size-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="absolute left-0 top-full mt-3 w-56 rounded-2xl border border-amber-100 bg-white/95 backdrop-blur shadow-xl shadow-amber-100/60 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-20">
                        <div class="py-3 px-2 space-y-1">
                            <template v-for="child in item.children" :key="child.id">
                                <NavLink 
                                    v-if="child.is_active && hasAccess(child.roles) && routeExists(child.route)" 
                                    :href="route(child.route)" 
                                    :active="isActive(child.route)" 
                                    class="flex w-full items-center gap-2 px-4 py-2 text-sm text-gray-600 rounded-lg transition-colors duration-150 hover:bg-amber-50 hover:text-amber-600"
                                >
                                    <i v-if="child.icon" :class="child.icon"></i>
                                    {{ child.title }}
                                </NavLink>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
