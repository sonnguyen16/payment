<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { ref } from 'vue'

const props = defineProps({
  departments: Object,
  offices: Array,
  filters: Object
})

const search = ref(props.filters?.search || '')
const office_id = ref(props.filters?.office_id || '')

const applyFilters = () => {
  router.get(
    route('admin.departments.index'),
    {
      search: search.value,
      office_id: office_id.value
    },
    {
      preserveState: true,
      preserveScroll: true
    }
  )
}

const clearFilters = () => {
  search.value = ''
  office_id.value = ''
  router.get(route('admin.departments.index'))
}

const deleteDepartment = (department) => {
  Swal.fire({
    title: 'Xác nhận xóa',
    text: `Bạn có chắc chắn muốn xóa bộ phận "${department.name}"?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Xóa',
    cancelButtonText: 'Hủy'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('admin.departments.destroy', department.id))
    }
  })
}
</script>

<template>
  <Head title="Quản lý bộ phận" />

  <AdminLayout>
    <div class="content pt-3">
      <div class="container-fluid">
        <!-- Filter Toolbar -->
        <div class="card">
          <div class="card-body">
            <div class="row sm:gap-0 gap-3">
              <div class="col-md-3">
                <input
                  v-model="search"
                  @keyup.enter="applyFilters"
                  type="text"
                  class="form-control"
                  placeholder="Tìm kiếm bộ phận..."
                />
              </div>
              <div class="col-md-2">
                <select v-model="office_id" @change="applyFilters" class="form-control">
                  <option value="">Tất cả văn phòng</option>
                  <option v-for="office in offices" :key="office.id" :value="office.id">
                    {{ office.name }}
                  </option>
                </select>
              </div>
              <div class="col-md-5 d-flex gap-3">
                <button @click="clearFilters" class="btn btn-secondary"><i class="fas fa-times"></i> Xóa bộ lọc</button>
                <Link :href="route('admin.departments.create')" class="btn btn-primary">
                  <i class="fas fa-plus"></i> Thêm bộ phận
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Danh sách bộ phận</h3>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th style="width: 50px">STT</th>
                    <th>Tên</th>
                    <th>Văn phòng</th>
                    <th>Trưởng bộ phận</th>
                    <th>Số nhân viên</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(dept, index) in departments.data" :key="dept.id">
                    <td class="text-center">{{ departments.from + index }}</td>
                    <td>{{ dept.name }}</td>
                    <td>{{ dept.office?.name || '-' }}</td>
                    <td>{{ dept.head?.name || '-' }}</td>
                    <td>{{ dept.users_count || 0 }}</td>
                    <td class="text-center">
                      <div class="btn-group">
                        <Link :href="route('admin.departments.edit', dept.id)" class="btn btn-sm btn-warning">
                          <i class="fas fa-edit"></i>
                        </Link>
                        <button
                          v-if="dept.users_count === 0"
                          @click="deleteDepartment(dept)"
                          class="btn btn-sm btn-danger"
                        >
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <Pagination :links="departments.links" :meta="departments.meta" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
