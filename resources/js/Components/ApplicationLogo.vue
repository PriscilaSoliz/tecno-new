<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const logoUrl = computed(() => {
    const baseUrl = page.props.app_url || '';
    const cleanBase = baseUrl.endsWith('/') ? baseUrl : baseUrl + '/';
    return `${cleanBase}images/logo.png`;
});
</script>

<template>
    <img :src="logoUrl" alt="Application Logo" class="rounded-full" />
</template>
