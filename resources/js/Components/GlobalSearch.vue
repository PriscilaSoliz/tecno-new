<script setup>
import { ref, watch, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { useDebounceFn, onClickOutside } from '@vueuse/core';

const query = ref('');
const results = ref([]);
const isOpen = ref(false);
const isLoading = ref(false);
const isExpanded = ref(false);
const searchContainer = ref(null);
const searchInput = ref(null);

const search = useDebounceFn(async (val) => {
    if (val.length < 2) {
        results.value = [];
        return;
    }

    isLoading.value = true;
    try {
        const response = await axios.get(route('global.search'), {
            params: { query: val }
        });
        results.value = response.data;
        isOpen.value = true;
    } catch (error) {
        console.error('Error searching:', error);
    } finally {
        isLoading.value = false;
    }
}, 300);

watch(query, (newVal) => {
    if (newVal === '') {
        isOpen.value = false;
        results.value = [];
    } else {
        search(newVal);
    }
});

const navigate = (url) => {
    isOpen.value = false;
    query.value = '';
    results.value = [];
    isExpanded.value = false;
    router.visit(url);
};

// Expand search bar
const expandSearch = () => {
    isExpanded.value = true;
    nextTick(() => {
        if (searchInput.value) {
            searchInput.value.focus();
        }
    });
};

// Close search if clicked outside
onClickOutside(searchContainer, () => {
    if (query.value === '') {
        isExpanded.value = false;
    }
    isOpen.value = false;
});

</script>

<template>
    <div ref="searchContainer" class="relative flex items-center">
        <!-- Icon Button (Always keeps its spot in navbar) -->
        <button @click="isExpanded ? (isExpanded = false, isOpen = false) : expandSearch()"
            :class="isExpanded ? 'bg-gray-100 dark:bg-gray-700 text-indigo-600' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
            class="p-2 rounded-lg focus:outline-none transition-colors duration-200 flex items-center justify-center"
            title="Buscar (Ctrl+K)">
            <i class="fas fa-search text-lg"></i>
        </button>

        <!-- Expanded Search Input (Dropdown) -->
        <div v-show="isExpanded" class="absolute right-0 top-full mt-4 w-72 sm:w-96 z-50 origin-top-right transition-all duration-200"
            :class="{ 'opacity-100 scale-100': isExpanded, 'opacity-0 scale-95 pointer-events-none': !isExpanded }">
            <div class="relative shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-white dark:ring-opacity-10 rounded-lg bg-white dark:bg-gray-800">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input ref="searchInput" type="text" v-model="query"
                    class="block w-full pl-11 pr-11 py-3 border-0 border-b border-gray-100 dark:border-gray-700 rounded-t-lg bg-transparent text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:ring-0 focus:outline-none sm:text-sm"
                    placeholder="Buscar todo..." />
                
                <!-- Close/Clear Button inside input -->
                <button v-if="query.length > 0" @click="query = ''; searchInput.focus();"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 cursor-pointer transition-colors">
                    <i class="fas fa-times"></i>
                </button>

                <div v-if="isLoading" class="absolute inset-y-0 right-10 flex items-center">
                    <i class="fas fa-spinner fa-spin text-gray-400"></i>
                </div>
            </div>

            <!-- Results Dropdown (Attached directly below) -->
            <div v-if="isOpen && results.length > 0"
                class="absolute left-0 w-full bg-white dark:bg-gray-800 shadow-xl rounded-b-lg py-1 text-base ring-1 ring-black ring-opacity-5 dark:ring-white dark:ring-opacity-10 overflow-auto max-h-96 focus:outline-none z-50 text-left border-t-0">
                <div v-for="(group, category) in results.reduce((acc, item) => {
                    (acc[item.category] = acc[item.category] || []).push(item);
                    return acc;
                }, {})" :key="category">

                    <div
                        class="px-4 py-2 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-700/50 sticky top-0 backdrop-blur-sm">
                        {{ category }}
                    </div>

                    <button v-for="item in group" :key="item.id + item.type" @click="navigate(item.url)"
                        class="w-full cursor-pointer select-none relative py-3 pl-4 pr-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <div class="flex items-center">
                            <span
                                class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                                <i :class="item.icon"></i>
                            </span>
                            <div class="ml-3 text-left min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ item.title }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ item.subtitle }}
                                </p>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <div v-if="isOpen && query.length >= 2 && results.length === 0 && !isLoading"
                class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 shadow-xl rounded-lg py-4 text-center text-sm text-gray-500 dark:text-gray-400 z-50">
                No se encontraron resultados para "{{ query }}"
            </div>
        </div>
    </div>
</template>
