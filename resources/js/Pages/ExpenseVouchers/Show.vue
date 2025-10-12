<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const props = defineProps({
  voucher: Object,
  can: Object
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

const formatDateTime = (datetime) => {
  return new Date(datetime).toLocaleString('vi-VN')
}

const deleteVoucher = () => {
  Swal.fire({
    title: 'Xác nhận xóa?',
    text: 'Bạn có chắc muốn xóa phiếu chi này?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Xóa',
    cancelButtonText: 'Hủy'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('expense-vouchers.destroy', props.voucher.id), {
        onSuccess: () => {
          Swal.fire('Đã xóa!', 'Phiếu chi đã được xóa.', 'success')
        }
      })
    }
  })
}
</script>

<template>
  <Head title="Chi tiết phiếu chi" />

  <AdminLayout>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Chi tiết phiếu chi</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><Link :href="route('dashboard')">Dashboard</Link></li>
              <li class="breadcrumb-item"><Link :href="route('expense-vouchers.index')">Phiếu chi</Link></li>
              <li class="breadcrumb-item active">Chi tiết</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <!-- Actions -->
        <div class="mb-3">
          <Link :href="route('expense-vouchers.index')" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
          </Link>
          <Link v-if="can.update" :href="route('expense-vouchers.edit', voucher.id)" class="btn btn-warning ml-2">
            <i class="fas fa-edit"></i> Chỉnh sửa
          </Link>
          <button v-if="can.delete" @click="deleteVoucher" class="btn btn-danger ml-2">
            <i class="fas fa-trash"></i> Xóa
          </button>
        </div>

        <!-- Voucher Info -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Thông tin phiếu chi</h3>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <table class="table table-borderless">
                  <tr>
                    <th style="width: 150px">Ngày chi:</th>
                    <td>{{ formatDate(voucher.expense_date) }}</td>
                  </tr>
                  <tr>
                    <th>Danh mục:</th>
                    <td>
                      <div class="d-flex align-items-center" v-if="voucher.category">
                        <div
                          class="category-indicator mr-2"
                          :style="{ backgroundColor: voucher.category.color }"
                        ></div>
                        {{ voucher.category.name }}
                      </div>
                      <span v-else class="text-muted">-</span>
                    </td>
                  </tr>
                  <tr>
                    <th>Dự án:</th>
                    <td>{{ voucher.project?.name || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Số tiền:</th>
                    <td class="text-primary font-weight-bold">{{ formatMoney(voucher.amount) }}</td>
                  </tr>
                </table>
              </div>
              <div class="col-md-6">
                <table class="table table-borderless">
                  <tr>
                    <th style="width: 150px">Người nhận:</th>
                    <td>{{ voucher.recipient }}</td>
                  </tr>
                  <tr>
                    <th>Người tạo:</th>
                    <td>{{ voucher.user.name }}</td>
                  </tr>
                  <tr>
                    <th>Ngày tạo:</th>
                    <td>{{ formatDateTime(voucher.created_at) }}</td>
                  </tr>
                  <tr>
                    <th>Cập nhật:</th>
                    <td>{{ formatDateTime(voucher.updated_at) }}</td>
                  </tr>
                </table>
              </div>
            </div>

            <div class="row mt-3">
              <div class="col-12">
                <h5>Nội dung:</h5>
                <p class="text-muted">{{ voucher.description }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Update History -->
        <div v-if="voucher.update_histories && voucher.update_histories.length > 0" class="card">
          <div class="card-header">
            <h3 class="card-title">Lịch sử chỉnh sửa</h3>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Thời gian</th>
                    <th>Người sửa</th>
                    <th>Lý do</th>
                    <th>Thay đổi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="history in voucher.update_histories" :key="history.id">
                    <td>{{ formatDateTime(history.created_at) }}</td>
                    <td>{{ history.user.name }}</td>
                    <td>{{ history.reason }}</td>
                    <td>
                      <div v-if="history.changes">
                        <div v-for="(change, field) in JSON.parse(history.changes)" :key="field" class="mb-2">
                          <strong>{{ getFieldLabel(field) }}:</strong>
                          <br />
                          <span class="text-danger">{{ change.old }}</span>
                          <i class="fas fa-arrow-right mx-2"></i>
                          <span class="text-success">{{ change.new }}</span>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
export default {
  methods: {
    getFieldLabel(field) {
      const labels = {
        expense_date: 'Ngày chi',
        description: 'Nội dung',
        amount: 'Số tiền',
        category_id: 'Danh mục',
        project_id: 'Dự án',
        recipient: 'Người nhận'
      }
      return labels[field] || field
    }
  }
}
</script>

<style scoped>
.category-indicator {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  display: inline-block;
}
</style>
