<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({
  users: Object
})

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
</script>

<template>
  <Head title="Quản lý người dùng" />

  <AdminLayout>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Quản lý người dùng</h1>
          </div>
          <div class="col-sm-6">
            <Link :href="route('admin.users.create')" class="btn btn-primary float-right">
              <i class="fas fa-plus"></i> Thêm người dùng
            </Link>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-body p-0">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Tên</th>
                  <th>Email</th>
                  <th>Vai trò</th>
                  <th>Văn phòng</th>
                  <th>Bộ phận</th>
                  <th>Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users.data" :key="user.id">
                  <td>{{ user.id }}</td>
                  <td>{{ user.name }}</td>
                  <td>{{ user.email }}</td>
                  <td>
                    <span v-for="role in user.roles" :key="role.id" class="badge mr-1" :class="getRoleBadgeClass(role.name)">
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
