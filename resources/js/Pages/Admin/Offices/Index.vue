<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({
  offices: Object
})
</script>

<template>
  <Head title="Quản lý văn phòng" />

  <AdminLayout>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Quản lý văn phòng</h1>
          </div>
          <div class="col-sm-6">
            <Link :href="route('admin.offices.create')" class="btn btn-primary float-right">
              <i class="fas fa-plus"></i> Thêm văn phòng
            </Link>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4" v-for="office in offices.data" :key="office.id">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">{{ office.name }}</h3>
              </div>
              <div class="card-body">
                <p><strong>Địa chỉ:</strong><br>{{ office.location || '-' }}</p>
                <p><strong>Số bộ phận:</strong> {{ office.departments_count || 0 }}</p>
                <p><strong>Số nhân viên:</strong> {{ office.users_count || 0 }}</p>
              </div>
              <div class="card-footer">
                <Link :href="route('admin.offices.edit', office.id)" class="btn btn-sm btn-warning">
                  <i class="fas fa-edit"></i> Sửa
                </Link>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Pagination -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <Pagination :links="offices.links" :meta="offices.meta" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
