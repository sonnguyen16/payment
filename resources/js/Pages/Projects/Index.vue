<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
  projects: Object,
  can: Object,
  filters: Object
})

const search = ref(props.filters?.search || '')
const status = ref(props.filters?.status || '')

const formatMoney = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const getStatusBadge = (status) => {
  const badges = {
    planning: 'badge-info',
    active: 'badge-success',
    completed: 'badge-secondary',
    cancelled: 'badge-danger'
  }
  return badges[status] || 'badge-secondary'
}

const getStatusLabel = (status) => {
  const labels = {
    planning: 'Đang lên kế hoạch',
    active: 'Đang thực hiện',
    completed: 'Hoàn thành',
    cancelled: 'Đã hủy'
  }
  return labels[status] || status
}

const applyFilters = () => {
  router.get(
    route('projects.index'),
    {
      search: search.value,
      status: status.value
    },
    {
      preserveState: true,
      preserveScroll: true
    }
  )
}

const clearFilters = () => {
  search.value = ''
  status.value = ''
  router.get(route('projects.index'))
}
</script>

<template>
  <Head title="Danh sách dự án" />

  <AdminLayout>
    <div class="content pt-3">
      <div class="container-fluid">
        <!-- Filter Toolbar -->
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <input
                  v-model="search"
                  @keyup.enter="applyFilters"
                  type="text"
                  class="form-control"
                  placeholder="Tìm kiếm tên, mã dự án..."
                />
              </div>
              <div class="col-md-2">
                <select v-model="status" @change="applyFilters" class="form-control">
                  <option value="">Tất cả trạng thái</option>
                  <option value="planning">Đang lên kế hoạch</option>
                  <option value="active">Đang thực hiện</option>
                  <option value="completed">Hoàn thành</option>
                  <option value="cancelled">Đã hủy</option>
                </select>
              </div>
              <div class="col-md-5 d-flex gap-3">
                <button @click="clearFilters" class="btn btn-secondary"><i class="fas fa-times"></i> Xóa bộ lọc</button>
                <Link v-if="can.create" :href="route('projects.create')" class="btn btn-primary">
                  <i class="fas fa-plus"></i> Tạo dự án mới
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Danh sách dự án</h3>
          </div>
          <div class="card-body p-0">
            <table class="table">
              <thead>
                <tr>
                  <th style="width: 50px">STT</th>
                  <th>Tên dự án</th>
                  <th>Mã dự án</th>
                  <th>Ngân sách</th>
                  <th>Đã chi</th>
                  <th>Còn lại</th>
                  <th>Trạng thái</th>
                  <th>Số phiếu</th>
                  <th style="width: 100px">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(project, index) in projects.data" :key="project.id">
                  <td class="text-center">{{ projects.from + index }}</td>
                  <td>
                    <Link :href="route('projects.show', project.id)" class="text-primary">
                      {{ project.name }}
                    </Link>
                  </td>
                  <td>{{ project.code }}</td>
                  <td>{{ formatMoney(project.budget) }}</td>
                  <td>{{ formatMoney(project.spent) }}</td>
                  <td>
                    <span :class="project.is_over_budget ? 'text-danger' : 'text-success'">
                      {{ formatMoney(project.budget - project.spent) }}
                    </span>
                  </td>
                  <td>
                    <span class="badge" :class="getStatusBadge(project.status)">
                      {{ getStatusLabel(project.status) }}
                    </span>
                  </td>
                  <td class="text-center">{{ project.payment_requests_count || 0 }}</td>
                  <td>
                    <div class="btn-group">
                      <Link :href="route('projects.show', project.id)" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i>
                      </Link>
                      <Link v-if="can.update" :href="route('projects.edit', project.id)" class="btn btn-sm btn-info">
                        <i class="fas fa-edit"></i>
                      </Link>
                    </div>
                  </td>
                </tr>
                <tr v-if="projects.data.length === 0">
                  <td colspan="10" class="text-center text-muted"><i class="fas fa-inbox"></i> Chưa có dự án nào</td>
                </tr>
              </tbody>
            </table>
          </div>
          <Pagination :links="projects.links" :meta="projects.meta" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
