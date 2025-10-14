<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  paymentRequests: Array,
  totalAmount: Number,
  filters: Object,
  userRole: String
})

const startDate = ref(props.filters.start_date)
const endDate = ref(props.filters.end_date)

const formatMoney = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('vi-VN')
}

const applyFilter = () => {
  router.get(
    route('reports.payment-requests'),
    {
      start_date: startDate.value,
      end_date: endDate.value
    },
    {
      preserveState: true,
      preserveScroll: true
    }
  )
}

const exportToExcel = () => {
  // TODO: Implement export to Excel functionality
  alert('Chức năng xuất Excel đang được phát triển')
}

const printReport = () => {
  window.print()
}
</script>

<template>
  <Head title="Báo cáo phiếu đề xuất" />

  <AdminLayout>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Báo cáo phiếu đề xuất đã thanh toán</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><router-link :to="{ name: 'dashboard' }">Dashboard</router-link></li>
              <li class="breadcrumb-item active">Báo cáo</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <!-- Filter Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Bộ lọc</h3>
          </div>
          <div class="card-body">
            <div class="row sm:gap-0 gap-3">
              <div class="col-md-3">
                <div class="form-group">
                  <label>Từ ngày</label>
                  <input type="date" v-model="startDate" class="form-control" />
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label>Đến ngày</label>
                  <input type="date" v-model="endDate" class="form-control" />
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button @click="applyFilter" class="btn btn-primary btn-block">
                    <i class="fas fa-filter"></i> Lọc
                  </button>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <div class="btn-group btn-block">
                    <button @click="printReport" class="btn btn-secondary"><i class="fas fa-print"></i> In</button>
                    <!-- <button @click="exportToExcel" class="btn btn-success">
                      <i class="fas fa-file-excel"></i> Excel
                    </button> -->
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="alert alert-info mb-0">
                  <i class="fas fa-info-circle"></i>
                  <span v-if="userRole === 'accountant'"> Bạn đang xem báo cáo của văn phòng mình phụ trách </span>
                  <span v-else-if="userRole === 'ceo'"> Bạn đang xem báo cáo của tất cả văn phòng </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Summary Card -->
        <div class="row" v-if="paymentRequests.length > 0">
          <div class="col-md-4">
            <div class="info-box">
              <span class="info-box-icon bg-info"><i class="fas fa-file-invoice"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Tổng số phiếu</span>
                <span class="info-box-number">{{ paymentRequests.length }}</span>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-box">
              <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Tổng tiền</span>
                <span class="info-box-number">{{ formatMoney(totalAmount) }}</span>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-box">
              <span class="info-box-icon bg-warning"><i class="fas fa-calculator"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Trung bình/phiếu</span>
                <span class="info-box-number">{{ formatMoney(totalAmount / paymentRequests.length) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Report Card -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              Danh sách phiếu đã thanh toán ({{ formatDate(filters.start_date) }} - {{ formatDate(filters.end_date) }})
            </h3>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr class="bg-light">
                    <th style="width: 50px">STT</th>
                    <th>Mã phiếu</th>
                    <th>Ngày tạo</th>
                    <th>Người tạo</th>
                    <th>Danh mục</th>
                    <th>Dự án</th>
                    <th>Nội dung</th>
                    <th class="text-right">Số tiền</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(request, index) in paymentRequests" :key="request.id">
                    <td class="text-center">{{ index + 1 }}</td>
                    <td>
                      <router-link
                        :to="{ name: 'payment-requests.show', params: { id: request.id } }"
                        class="text-primary"
                      >
                        #{{ request.id }}
                      </router-link>
                    </td>
                    <td class="text-center">{{ formatDate(request.created_at) }}</td>
                    <td>{{ request.user.name }}</td>
                    <td>
                      <div class="d-flex align-items-center" v-if="request.category">
                        <div class="category-indicator mr-2" :style="{ backgroundColor: request.category.color }"></div>
                        {{ request.category.name }}
                      </div>
                      <span v-else class="text-muted">-</span>
                    </td>
                    <td>{{ request.project?.name || '-' }}</td>
                    <td>{{ request.description.substring(0, 50) }}...</td>
                    <td class="text-right">{{ formatMoney(request.amount) }}</td>
                  </tr>
                  <tr v-if="paymentRequests.length === 0">
                    <td colspan="8" class="text-center text-muted">
                      <i class="fas fa-inbox"></i> Không có dữ liệu trong khoảng thời gian này
                    </td>
                  </tr>
                </tbody>
                <tfoot v-if="paymentRequests.length > 0">
                  <tr class="bg-light font-weight-bold">
                    <td colspan="7" class="text-right">TỔNG CỘNG:</td>
                    <td class="text-right text-primary">{{ formatMoney(totalAmount) }}</td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
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
  display: inline-block;
}

@media print {
  .content-header,
  .card-header,
  .btn,
  .breadcrumb,
  .alert,
  .info-box {
    display: none !important;
  }

  .card {
    border: none !important;
    box-shadow: none !important;
  }

  table {
    font-size: 12px;
  }
}
</style>
