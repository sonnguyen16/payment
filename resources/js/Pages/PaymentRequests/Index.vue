<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import PriorityBadge from '@/Components/PriorityBadge.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    requests: Object,
    filters: Object,
    can: Object,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const priority = ref(props.filters.priority || '');

const formatMoney = (amount) => {
    return new Intl.NumberFormat('vi-VN', { 
        style: 'currency', 
        currency: 'VND' 
    }).format(amount);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('vi-VN');
};


const applyFilters = () => {
    router.get(route('payment-requests.index'), {
        search: search.value,
        status: status.value,
        priority: priority.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    search.value = '';
    status.value = '';
    priority.value = '';
    applyFilters();
};
</script>

<template>
    <Head title="Danh sách phiếu đề xuất" />

    <AdminLayout>
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Danh sách phiếu đề xuất</h1>
                    </div>
                    <div class="col-sm-6">
                        <Link v-if="can.create" :href="route('payment-requests.create')" class="btn btn-primary float-right">
                            <i class="fas fa-plus"></i> Tạo phiếu mới
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Bộ lọc</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <input v-model="search" @keyup.enter="applyFilters" type="text" class="form-control" placeholder="Tìm kiếm...">
                            </div>
                            <div class="col-md-2">
                                <select v-model="status" @change="applyFilters" class="form-control">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="draft">Nháp</option>
                                    <option value="pending_department_head">Chờ Trưởng BP</option>
                                    <option value="pending_accountant">Chờ Kế toán</option>
                                    <option value="pending_ceo">Chờ TGĐ</option>
                                    <option value="paid">Đã thanh toán</option>
                                    <option value="rejected">Bị từ chối</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select v-model="priority" @change="applyFilters" class="form-control">
                                    <option value="">Tất cả ưu tiên</option>
                                    <option value="urgent">Gấp</option>
                                    <option value="normal">Bình thường</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button @click="clearFilters" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Xóa bộ lọc
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Loại</th>
                                    <th>Số tiền</th>
                                    <th>Mô tả</th>
                                    <th>Trạng thái</th>
                                    <th>Ưu tiên</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="request in requests.data" :key="request.id">
                                    <td>#{{ request.id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center" v-if="request.category">
                                            <div 
                                                class="category-indicator mr-2" 
                                                :style="{ backgroundColor: request.category.color }"
                                            ></div>
                                            {{ request.category.name }}
                                        </div>
                                        <span v-else class="text-muted">Chưa phân loại</span>
                                    </td>
                                    <td>{{ formatMoney(request.amount) }}</td>
                                    <td>{{ request.description.substring(0, 50) }}...</td>
                                    <td><StatusBadge :status="request.status" /></td>
                                    <td><PriorityBadge :priority="request.priority" /></td>
                                    <td>{{ formatDate(request.created_at) }}</td>
                                    <td>
                                        <Link :href="route('payment-requests.show', request.id)" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Xem
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="requests.data.length === 0">
                                    <td colspan="8" class="text-center">Không có dữ liệu</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <Pagination :links="requests.links" :meta="requests.meta" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.category-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.3);
    box-shadow: 0 0 0 1px rgba(0,0,0,0.1);
}
</style>
