<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({
  stats: Object,
  recent_requests: Array,
  pending_my_approval: Array
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

const getStatusLabel = (status) => {
  const labels = {
    draft: 'Nháp',
    pending_department_head: 'Chờ Trưởng BP',
    pending_accountant: 'Chờ Kế toán',
    pending_ceo: 'Chờ TGĐ',
    pending_payment: 'Chờ thanh toán',
    paid: 'Đã thanh toán',
    rejected: 'Bị từ chối',
    cancelled: 'Đã hủy',
    deleted: 'Đã xóa'
  }
  return labels[status] || status
}

const getStatusBadgeClass = (status) => {
  const classes = {
    draft: 'badge-secondary',
    pending_department_head: 'badge-warning',
    pending_accountant: 'badge-info',
    pending_ceo: 'badge-primary',
    pending_payment: 'badge-success',
    paid: 'badge-success',
    rejected: 'badge-danger',
    cancelled: 'badge-dark',
    deleted: 'badge-dark'
  }
  return classes[status] || 'badge-secondary'
}
</script>

<template>
  <Head title="Dashboard" />

  <AdminLayout>
    <!-- Main content -->
    <div class="content pt-3">
      <div class="container-fluid">
        <!-- Stats Cards -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ stats.total_requests }}</h3>
                <p>Tổng phiếu của tôi</p>
              </div>
              <div class="icon">
                <i class="fas fa-file-invoice"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ stats.pending_approval }}</h3>
                <p>Đang chờ duyệt</p>
              </div>
              <div class="icon">
                <i class="fas fa-clock"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ stats.approved_this_month }}</h3>
                <p>Đã duyệt tháng này</p>
              </div>
              <div class="icon">
                <i class="fas fa-check-circle"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
              <div class="inner">
                <h3>{{ formatMoney(stats.total_amount_this_month) }}</h3>
                <p>Tổng tiền tháng này</p>
              </div>
              <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Requests -->
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Phiếu gần đây</h3>
                <div class="card-tools">
                  <Link :href="route('payment-requests.index')" class="btn btn-tool">
                    <i class="fas fa-list"></i> Xem tất cả
                  </Link>
                </div>
              </div>
              <div class="card-body p-0">
                <table class="table table-sm">
                  <thead>
                    <tr>
                      <th style="width: 50px">STT</th>
                      <th>Số tiền</th>
                      <th>Trạng thái</th>
                      <th>Ngày tạo</th>
                      <th>Thao tác</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(request, index) in recent_requests" :key="request.id">
                      <td class="text-center">{{ index + 1 }}</td>
                      <td class="text-right">{{ formatMoney(request.amount) }}</td>
                      <td class="text-center">
                        <span class="badge" :class="getStatusBadgeClass(request.status)">
                          {{ getStatusLabel(request.status) }}
                        </span>
                      </td>
                      <td class="text-center">{{ formatDate(request.created_at) }}</td>
                      <td class="text-center">
                        <Link :href="route('payment-requests.show', request.id)" class="btn btn-sm btn-info">
                          <i class="fas fa-eye"></i> Xem
                        </Link>
                      </td>
                    </tr>
                    <tr v-if="recent_requests.length === 0">
                      <td colspan="5" class="text-center text-muted"><i class="fas fa-inbox"></i> Chưa có phiếu nào</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Pending My Approval -->
          <div class="col-md-6" v-if="pending_my_approval && pending_my_approval.length > 0">
            <div class="card">
              <div class="card-header bg-warning">
                <h3 class="card-title">Phiếu cần tôi duyệt</h3>
                <div class="card-tools">
                  <span class="badge badge-light">{{ pending_my_approval ? pending_my_approval.length : 0 }}</span>
                </div>
              </div>
              <div class="card-body p-0">
                <table class="table table-sm">
                  <thead>
                    <tr>
                      <th style="width: 50px">STT</th>
                      <th>Người tạo</th>
                      <th>Số tiền</th>
                      <th>Ưu tiên</th>
                      <th>Thao tác</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(request, index) in pending_my_approval" :key="request.id">
                      <td class="text-center">{{ index + 1 }}</td>
                      <td>{{ request.user.name }}</td>
                      <td class="text-right">{{ formatMoney(request.amount) }}</td>
                      <td class="text-center">
                        <span class="badge" :class="request.priority === 'urgent' ? 'badge-danger' : 'badge-secondary'">
                          {{ request.priority === 'urgent' ? 'Gấp' : 'Bình thường' }}
                        </span>
                      </td>
                      <td class="text-center">
                        <Link :href="route('payment-requests.show', request.id)" class="btn btn-sm btn-warning">
                          <i class="fas fa-eye"></i> Duyệt
                        </Link>
                      </td>
                    </tr>
                    <tr v-if="!pending_my_approval || pending_my_approval.length === 0">
                      <td colspan="5" class="text-center text-muted">
                        <i class="fas fa-check-circle"></i> Không có phiếu cần duyệt
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
