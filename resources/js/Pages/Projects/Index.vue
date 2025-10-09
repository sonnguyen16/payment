<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    projects: Object,
    can: Object,
});

const formatMoney = (amount) => {
    return new Intl.NumberFormat('vi-VN', { 
        style: 'currency', 
        currency: 'VND' 
    }).format(amount);
};

const getStatusBadge = (status) => {
    const badges = {
        'planning': 'badge-info',
        'active': 'badge-success',
        'completed': 'badge-secondary',
        'cancelled': 'badge-danger',
    };
    return badges[status] || 'badge-secondary';
};

const getStatusLabel = (status) => {
    const labels = {
        'planning': 'Đang lên kế hoạch',
        'active': 'Đang thực hiện',
        'completed': 'Hoàn thành',
        'cancelled': 'Đã hủy',
    };
    return labels[status] || status;
};
</script>

<template>
    <Head title="Danh sách dự án" />

    <AdminLayout>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Danh sách dự án</h1>
                    </div>
                    <div class="col-sm-6">
                        <Link v-if="can.create" :href="route('projects.create')" class="btn btn-primary float-right">
                            <i class="fas fa-plus"></i> Tạo dự án mới
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4" v-for="project in projects.data" :key="project.id">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <Link :href="route('projects.show', project.id)" class="text-dark">
                                        {{ project.name }}
                                    </Link>
                                </h3>
                                <div class="card-tools">
                                    <span class="badge" :class="getStatusBadge(project.status)">
                                        {{ getStatusLabel(project.status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <p><strong>Mã dự án:</strong> {{ project.code }}</p>
                                <p><strong>Ngân sách:</strong> {{ formatMoney(project.budget) }}</p>
                                <p><strong>Đã chi:</strong> {{ formatMoney(project.spent) }}</p>
                                <p><strong>Còn lại:</strong> 
                                    <span :class="project.is_over_budget ? 'text-danger' : 'text-success'">
                                        {{ formatMoney(project.remaining_budget) }}
                                    </span>
                                </p>
                                
                                <div class="progress mb-2">
                                    <div class="progress-bar" 
                                         :class="project.budget_utilization_percentage > 100 ? 'bg-danger' : 'bg-success'"
                                         :style="`width: ${Math.min(project.budget_utilization_percentage, 100)}%`">
                                        {{ project.budget_utilization_percentage.toFixed(1) }}%
                                    </div>
                                </div>
                                
                                <p class="mb-0">
                                    <small class="text-muted">
                                        <i class="fas fa-file-invoice"></i> 
                                        {{ project.payment_requests_count || 0 }} phiếu đề xuất
                                    </small>
                                </p>
                            </div>
                            <div class="card-footer">
                                <Link :href="route('projects.show', project.id)" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Xem chi tiết
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" v-if="projects.data.length === 0">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Chưa có dự án nào
                        </div>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <Pagination :links="projects.links" :meta="projects.meta" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
