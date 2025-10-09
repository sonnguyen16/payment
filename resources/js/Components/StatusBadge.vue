<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: String,
});

const badgeClass = computed(() => {
    const classes = {
        'draft': 'badge-secondary',
        'pending_department_head': 'badge-warning',
        'pending_accountant': 'badge-info',
        'pending_ceo': 'badge-primary',
        'pending_payment': 'badge-success',
        'paid': 'badge-success',
        'rejected': 'badge-danger',
        'cancelled': 'badge-dark',
        'deleted': 'badge-dark',
    };
    return classes[props.status] || 'badge-secondary';
});

const statusLabel = computed(() => {
    const labels = {
        'draft': 'Nháp',
        'pending_department_head': 'Chờ Trưởng BP',
        'pending_accountant': 'Chờ Kế toán',
        'pending_ceo': 'Chờ TGĐ',
        'pending_payment': 'Chờ thanh toán',
        'paid': 'Đã thanh toán',
        'rejected': 'Bị từ chối',
        'cancelled': 'Đã hủy',
        'deleted': 'Đã xóa',
    };
    return labels[props.status] || props.status;
});
</script>

<template>
    <span class="badge" :class="badgeClass">
        {{ statusLabel }}
    </span>
</template>
