<script setup>
defineProps({
    size: {
        type: String,
        default: 'md', // sm, md, lg
    },
    text: {
        type: String,
        default: 'Đang tải...',
    },
});
</script>

<template>
    <div class="text-center">
        <div class="spinner-border" :class="{
            'spinner-border-sm': size === 'sm',
            'spinner-border-lg': size === 'lg'
        }" role="status">
            <span class="sr-only">{{ text }}</span>
        </div>
        <p class="mt-2" v-if="text">{{ text }}</p>
    </div>
</template>

<style scoped>
.spinner-border-lg {
    width: 3rem;
    height: 3rem;
    border-width: 0.3em;
}
</style>
