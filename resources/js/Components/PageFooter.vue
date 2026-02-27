<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const visits = computed(() => page.props.pageVisits || 0);
</script>

<template>
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-center items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Visitas: <strong class="text-gray-900 dark:text-gray-100">{{ visits }}</strong>
                </span>
            </div>
        </div>
    </footer>
</template>
