<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  users: Object,
  offices: Array,
  filters: Object
})

const search = ref(props.filters?.search || '')
const role = ref(props.filters?.role || '')
const office_id = ref(props.filters?.office_id || '')

const getRoleBadgeClass = (roleName) => {
  const badges = {
    admin: 'badge-danger',
    ceo: 'badge-purple',
    department_head: 'badge-warning',
    accountant: 'badge-info',
    employee: 'badge-secondary'
  }
  return badges[roleName] || 'badge-secondary'
}

const getRoleLabel = (roleName) => {
  const labels = {
    admin: 'Admin',
    ceo: 'Tổng Giám Đốc',
    department_head: 'Trưởng Bộ Phận',
    accountant: 'Kế Toán',
    employee: 'Nhân Viên'
  }
  return labels[roleName] || roleName
}

const applyFilters = () => {
  router.get(
    route('admin.users.index'),
    {
      search: search.value,
      role: role.value,
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
  role.value = ''
  office_id.value = ''
  router.get(route('admin.users.index'))
}
</script>

<template>
  <Head title="Quản lý người dùng" />

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
                  placeholder="Tìm kiếm tên, email..."
                />
              </div>
              <div class="col-md-2">
                <select v-model="role" @change="applyFilters" class="form-control">
                  <option value="">Tất cả vai trò</option>
                  <option value="admin">Admin</option>
                  <option value="ceo">Tổng Giám Đốc</option>
                  <option value="accountant">Kế Toán</option>
                  <option value="department_head">Trưởng Bộ Phận</option>
                  <option value="employee">Nhân Viên</option>
                </select>
              </div>
              <div class="col-md-2">
                <select v-model="office_id" @change="applyFilters" class="form-control">
                  <option value="">Tất cả văn phòng</option>
                  <option v-for="office in offices" :key="office.id" :value="office.id">
                    {{ office.name }}
                  </option>
                </select>
              </div>
              <div class="col-md-3 d-flex gap-3">
                <button @click="clearFilters" class="btn btn-secondary"><i class="fas fa-times"></i> Xóa bộ lọc</button>
                <Link :href="route('admin.users.create')" class="btn btn-primary">
                  <i class="fas fa-plus"></i> Thêm người dùng
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Danh sách người dùng</h3>
          </div>
          <div class="card-body p-0">
            <table class="table">
              <thead>
                <tr>
                  <th style="width: 50px">STT</th>
                  <th>Tên</th>
                  <th>Email</th>
                  <th>Vai trò</th>
                  <th>Văn phòng</th>
                  <th>Bộ phận</th>
                  <th>Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(user, index) in users.data" :key="user.id">
                  <td class="text-center">{{ users.from + index }}</td>
                  <td>{{ user.name }}</td>
                  <td>{{ user.email }}</td>
                  <td>
                    <span
                      v-for="role in user.roles"
                      :key="role.id"
                      class="badge mr-1"
                      :class="getRoleBadgeClass(role.name)"
                    >
                      {{ getRoleLabel(role.name) }}
                    </span>
                  </td>
                  <td>{{ user.office?.name || '-' }}</td>
                  <td>{{ user.department?.name || '-' }}</td>
                  <td>
                    <Link :href="route('admin.users.edit', user.id)" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <Pagination :links="users.links" :meta="users.meta" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
