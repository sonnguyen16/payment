<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({
  departments: Object
})
</script>

<template>
  <Head title="Quản lý bộ phận" />

  <AdminLayout>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Quản lý bộ phận</h1>
          </div>
          <div class="col-sm-6">
            <Link :href="route('admin.departments.create')" class="btn btn-primary float-right">
              <i class="fas fa-plus"></i> Thêm bộ phận
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
                  <th>Văn phòng</th>
                  <th>Trưởng bộ phận</th>
                  <th>Số nhân viên</th>
                  <th>Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="dept in departments.data" :key="dept.id">
                  <td>{{ dept.id }}</td>
                  <td>{{ dept.name }}</td>
                  <td>{{ dept.office?.name || '-' }}</td>
                  <td>{{ dept.head?.name || '-' }}</td>
                  <td>{{ dept.users_count || 0 }}</td>
                  <td>
                    <Link :href="route('admin.departments.edit', dept.id)" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <Pagination :links="departments.links" :meta="departments.meta" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
