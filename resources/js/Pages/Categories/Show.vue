<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({
  category: Object
})

const formatMoney = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('vi-VN')
}
</script>

<template>
  <Head :title="category.name" />

  <AdminLayout>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <div class="">
              <Link :href="route('admin.categories.edit', category.id)" class="btn btn-warning mr-2">
                <i class="fas fa-edit"></i> Chỉnh sửa
              </Link>
              <Link :href="route('admin.categories.index')" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Thông tin danh mục</h3>
              </div>
              <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                  <div class="color-indicator mr-3" :style="{ backgroundColor: category.color }"></div>
                  <div>
                    <h4 class="mb-1">{{ category.name }}</h4>
                    <p class="mb-0 text-muted">{{ category.description || 'Không có mô tả' }}</p>
                  </div>
                </div>

                <div class="mb-3">
                  <span class="badge" :style="{ backgroundColor: category.color, color: '#fff' }">
                    {{ category.color }}
                  </span>
                  <span class="badge ml-2" :class="category.is_active ? 'badge-success' : 'badge-secondary'">
                    {{ category.is_active ? 'Hoạt động' : 'Không hoạt động' }}
                  </span>
                </div>

                <hr />

                <div class="row">
                  <div class="col-6">
                    <strong>Số phiếu:</strong><br />
                    <span class="text-primary">{{
                      category.payment_requests ? category.payment_requests.length : 0
                    }}</span>
                  </div>
                  <div class="col-6">
                    <strong>Ngày tạo:</strong><br />
                    {{ formatDate(category.created_at) }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Phiếu đề xuất gần đây</h3>
              </div>
              <div class="card-body p-0">
                <table class="table table-sm" v-if="category.payment_requests && category.payment_requests.length > 0">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Người tạo</th>
                      <th>Số tiền</th>
                      <th>Trạng thái</th>
                      <th>Ngày tạo</th>
                      <th>Thao tác</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="request in category.payment_requests" :key="request.id">
                      <td>#{{ request.id }}</td>
                      <td>{{ request.user.name }}</td>
                      <td class="text-right">{{ formatMoney(request.amount) }}</td>
                      <td class="text-center"><StatusBadge :status="request.status" /></td>
                      <td class="text-center">{{ formatDate(request.created_at) }}</td>
                      <td>
                        <Link :href="route('payment-requests.show', request.id)" class="btn btn-sm btn-info">
                          <i class="fas fa-eye"></i>
                        </Link>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div v-else class="text-center p-4 text-muted">
                  <i class="fas fa-inbox fa-3x mb-3"></i>
                  <p>Chưa có phiếu đề xuất nào sử dụng danh mục này</p>
                </div>
              </div>
              <div class="card-footer" v-if="category.payment_requests && category.payment_requests.length >= 10">
                <Link :href="route('payment-requests.index', { category: category.id })" class="btn btn-sm btn-primary">
                  Xem tất cả phiếu của danh mục này
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.color-indicator {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  border: 3px solid #fff;
  box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1);
}
</style>
