<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  offices: Object,
  filters: Object
})

const search = ref(props.filters?.search || '')

const applyFilters = () => {
  router.get(
    route('admin.offices.index'),
    {
      search: search.value
    },
    {
      preserveState: true,
      preserveScroll: true
    }
  )
}

const clearFilters = () => {
  search.value = ''
  router.get(route('admin.offices.index'))
}
</script>

<template>
  <Head title="Quản lý văn phòng" />

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
                  placeholder="Tìm kiếm văn phòng..."
                />
              </div>
              <div class="col-md-7 d-flex gap-3">
                <button @click="clearFilters" class="btn btn-secondary"><i class="fas fa-times"></i> Xóa bộ lọc</button>
                <Link :href="route('admin.offices.create')" class="btn btn-primary">
                  <i class="fas fa-plus"></i> Thêm văn phòng
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Danh sách văn phòng</h3>
          </div>
          <div class="card-body p-0">
            <table class="table">
              <thead>
                <tr>
                  <th style="width: 50px">STT</th>
                  <th>Tên văn phòng</th>
                  <th>Địa chỉ</th>
                  <th style="width: 100px">Số bộ phận</th>
                  <th style="width: 100px">Số nhân viên</th>
                  <th style="width: 100px">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(office, index) in offices.data" :key="office.id">
                  <td>{{ offices.from + index }}</td>
                  <td>{{ office.name }}</td>
                  <td>{{ office.location || '-' }}</td>
                  <td class="text-center">{{ office.departments_count || 0 }}</td>
                  <td class="text-center">{{ office.users_count || 0 }}</td>
                  <td>
                    <Link :href="route('admin.offices.edit', office.id)" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </Link>
                  </td>
                </tr>
                <tr v-if="offices.data.length === 0">
                  <td colspan="6" class="text-center text-muted"><i class="fas fa-inbox"></i> Chưa có văn phòng nào</td>
                </tr>
              </tbody>
            </table>
          </div>
          <Pagination :links="offices.links" :meta="offices.meta" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
