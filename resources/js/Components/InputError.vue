<script setup>
defineProps({
    message: String,
});
</script>

<template>
    <div v-show="message">
        <p class="text-sm text-red-600 font-semibold" style="color: #dc2626 !important;">
            {{ message }}
        </p>
    </div>
</template>
