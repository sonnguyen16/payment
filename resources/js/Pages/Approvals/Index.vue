<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import PriorityBadge from '@/Components/PriorityBadge.vue'
import Pagination from '@/Components/Pagination.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({
  requests: Object,
  stats: Object,
  filters: Object
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
  <Head title="Phiếu cần duyệt" />

  <AdminLayout>
    <div class="content pt-3">
      <div class="container-fluid">
        <!-- Stats -->
        <div class="row">
          <div class="col-lg-6 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ stats.pending_count }}</h3>
                <p>Phiếu cần duyệt</p>
              </div>
              <div class="icon">
                <i class="fas fa-clock"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ stats.approved_today }}</h3>
                <p>Đã duyệt hôm nay</p>
              </div>
              <div class="icon">
                <i class="fas fa-check-circle"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Requests Table -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Danh sách phiếu</h3>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>STT</th>
                    <th>Người tạo</th>
                    <th>Loại</th>
                    <th>Số tiền</th>
                    <th>Mô tả</th>
                    <th>Ưu tiên</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(request, index) in requests.data"
                    :key="request.id"
                    :class="{ 'table-danger': request.priority === 'urgent' }"
                  >
                    <td class="text-center">{{ requests.from + index }}</td>
                    <td>{{ request.user.name }}</td>
                    <td>
                      <span v-if="request.type === 'advance'">Tạm ứng</span>
                      <span v-else-if="request.type === 'payment_proposal'">Đề xuất thanh toán</span>
                      <span v-else>Chi phí khác</span>
                    </td>
                    <td class="text-right">{{ formatMoney(request.amount) }}</td>
                    <td>{{ request.description.substring(0, 50) }}...</td>
                    <td class="text-center">
                      <span class="badge" :class="request.priority === 'urgent' ? 'badge-danger' : 'badge-secondary'">
                        {{ request.priority === 'urgent' ? 'Gấp' : 'Bình thường' }}
                      </span>
                    </td>
                    <td class="text-center">{{ formatDate(request.created_at) }}</td>
                    <td class="text-center">
                      <Link :href="route('payment-requests.show', request.id)" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i> Xem & Duyệt
                      </Link>
                    </td>
                  </tr>
                  <tr v-if="requests.data.length === 0">
                    <td colspan="8" class="text-center">
                      <div class="alert alert-info m-3">
                        <i class="fas fa-info-circle"></i> Không có phiếu nào cần duyệt
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <Pagination :links="requests.links" :meta="requests.meta" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
