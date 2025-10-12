<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Swal from 'sweetalert2'

const props = defineProps({
  vouchers: Object,
  filters: Object,
  categories: Array,
  projects: Array,
  can: Object
})

const search = ref(props.filters.search || '')
const categoryId = ref(props.filters.category_id || '')
const projectId = ref(props.filters.project_id || '')
const startDate = ref(props.filters.start_date || '')
const endDate = ref(props.filters.end_date || '')

const formatMoney = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('vi-VN')
}

const applyFilters = () => {
  router.get(
    route('expense-vouchers.index'),
    {
      search: search.value,
      category_id: categoryId.value,
      project_id: projectId.value,
      start_date: startDate.value,
      end_date: endDate.value
    },
    {
      preserveState: true,
      preserveScroll: true
    }
  )
}

const clearFilters = () => {
  search.value = ''
  categoryId.value = ''
  projectId.value = ''
  startDate.value = ''
  endDate.value = ''
  applyFilters()
}

const deleteVoucher = (voucher) => {
  Swal.fire({
    title: 'Xác nhận xóa?',
    text: `Bạn có chắc muốn xóa phiếu chi này?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Xóa',
    cancelButtonText: 'Hủy'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('expense-vouchers.destroy', voucher.id), {
        onSuccess: () => {
          Swal.fire('Đã xóa!', 'Phiếu chi đã được xóa.', 'success')
        }
      })
    }
  })
}
</script>

<template>
  <Head title="Phiếu chi" />

  <AdminLayout>
    <div class="content pt-3">
      <div class="container-fluid">
        <!-- Filter Card -->
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <input
                  v-model="search"
                  @keyup.enter="applyFilters"
                  type="text"
                  class="form-control"
                  placeholder="Tìm kiếm nội dung, người nhận..."
                />
              </div>
              <div class="col-md-2">
                <select v-model="categoryId" @change="applyFilters" class="form-control">
                  <option value="">Tất cả danh mục</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>
              <div class="col-md-2">
                <select v-model="projectId" @change="applyFilters" class="form-control">
                  <option value="">Tất cả dự án</option>
                  <option v-for="project in projects" :key="project.id" :value="project.id">
                    {{ project.name }}
                  </option>
                </select>
              </div>
              <div class="col-md-3 flex gap-2">
                <button @click="clearFilters" class="btn btn-secondary" title="Xóa bộ lọc">
                  <i class="fas fa-times"></i> Xóa bộ lọc
                </button>
                <Link v-if="can.create" :href="route('expense-vouchers.create')" class="btn btn-primary">
                  <i class="fas fa-plus"></i> Tạo phiếu chi
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Data Card -->
        <div class="card">
          <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
              <h3 class="card-title">Danh sách phiếu chi</h3>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>STT</th>
                    <th>Ngày chi</th>
                    <th>Nội dung</th>
                    <th>Danh mục</th>
                    <th>Dự án</th>
                    <th>Số tiền</th>
                    <th>Người nhận</th>
                    <th>Người tạo</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(voucher, index) in vouchers.data" :key="voucher.id">
                    <td class="text-center">{{ vouchers.from + index }}</td>
                    <td class="text-center">{{ formatDate(voucher.expense_date) }}</td>
                    <td>{{ voucher.description.substring(0, 50) }}...</td>
                    <td>
                      <div class="d-flex align-items-center" v-if="voucher.category">
                        <div class="category-indicator mr-2" :style="{ backgroundColor: voucher.category.color }"></div>
                        {{ voucher.category.name }}
                      </div>
                      <span v-else class="text-muted">-</span>
                    </td>
                    <td>{{ voucher.project?.name || '-' }}</td>
                    <td class="text-right">{{ formatMoney(voucher.amount) }}</td>
                    <td>{{ voucher.recipient }}</td>
                    <td>{{ voucher.user.name }}</td>
                    <td class="text-center">
                      <div class="btn-group">
                        <Link :href="route('expense-vouchers.show', voucher.id)" class="btn btn-sm btn-info">
                          <i class="fas fa-eye"></i>
                        </Link>
                        <Link
                          v-if="voucher.user_id === $page.props.auth.user.id"
                          :href="route('expense-vouchers.edit', voucher.id)"
                          class="btn btn-sm btn-warning"
                        >
                          <i class="fas fa-edit"></i>
                        </Link>
                        <button
                          v-if="voucher.user_id === $page.props.auth.user.id"
                          @click="deleteVoucher(voucher)"
                          class="btn btn-sm btn-danger"
                        >
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="vouchers.data.length === 0">
                    <td colspan="9" class="text-center">Không có dữ liệu</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <Pagination :links="vouchers.links" :meta="vouchers.meta" />
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
</style>
