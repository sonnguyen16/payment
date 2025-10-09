<script setup>
import { Link } from '@inertiajs/vue3';
import StatusBadge from './StatusBadge.vue';
import PriorityBadge from './PriorityBadge.vue';

defineProps({
    request: Object,
});

const formatMoney = (amount) => {
    return new Intl.NumberFormat('vi-VN', { 
        style: 'currency', 
        currency: 'VND' 
    }).format(amount);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('vi-VN');
};
</script>

<template>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <Link :href="route('payment-requests.show', request.id)" class="text-dark">
                    Phiếu #{{ request.id }}
                </Link>
            </h3>
            <div class="card-tools">
                <PriorityBadge :priority="request.priority" />
            </div>
        </div>
        <div class="card-body">
            <p class="mb-2">
                <strong>Số tiền:</strong> 
                <span class="text-danger">{{ formatMoney(request.amount) }}</span>
            </p>
            <p class="mb-2">
                <strong>Mô tả:</strong> {{ request.description }}
            </p>
            <p class="mb-2" v-if="request.project">
                <strong>Dự án:</strong> {{ request.project.name }}
            </p>
            <p class="mb-0">
                <strong>Trạng thái:</strong> <StatusBadge :status="request.status" />
            </p>
        </div>
        <div class="card-footer">
            <small class="text-muted">
                <i class="fas fa-calendar"></i> {{ formatDate(request.created_at) }}
            </small>
            <Link :href="route('payment-requests.show', request.id)" class="btn btn-sm btn-primary float-right">
                <i class="fas fa-eye"></i> Xem chi tiết
            </Link>
        </div>
    </div>
</template>
