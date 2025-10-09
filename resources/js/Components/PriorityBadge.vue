<script setup>
import { computed } from 'vue';

const props = defineProps({
    priority: String,
});

const badgeClass = computed(() => {
    return props.priority === 'urgent' ? 'badge-danger' : 'badge-secondary';
});

const label = computed(() => {
    return props.priority === 'urgent' ? 'Gấp' : 'Bình thường';
});
</script>

<template>
    <span class="badge" :class="badgeClass">
        <i class="fas" :class="priority === 'urgent' ? 'fa-exclamation-triangle' : 'fa-clock'"></i>
        {{ label }}
    </span>
</template>
