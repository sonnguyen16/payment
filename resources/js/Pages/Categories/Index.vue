<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({
  categories: Object
})
</script>

<template>
  <Head title="Quản lý danh mục" />

  <AdminLayout>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Quản lý danh mục</h1>
          </div>
          <div class="col-sm-6">
            <div class="float-sm-right">
              <Link :href="route('categories.create')" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm danh mục
              </Link>
            </div>
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
                  <th>Tên danh mục</th>
                  <th>Mô tả</th>
                  <th>Màu sắc</th>
                  <th>Trạng thái</th>
                  <th>Số phiếu</th>
                  <th>Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="category in categories.data" :key="category.id">
                  <td>
                    <div class="d-flex align-items-center">
                      <div 
                        class="color-indicator mr-2" 
                        :style="{ backgroundColor: category.color }"
                      ></div>
                      {{ category.name }}
                    </div>
                  </td>
                  <td>{{ category.description || '-' }}</td>
                  <td>
                    <span class="badge" :style="{ backgroundColor: category.color, color: '#fff' }">
                      {{ category.color }}
                    </span>
                  </td>
                  <td>
                    <span class="badge" :class="category.is_active ? 'badge-success' : 'badge-secondary'">
                      {{ category.is_active ? 'Hoạt động' : 'Không hoạt động' }}
                    </span>
                  </td>
                  <td>{{ category.payment_requests_count || 0 }}</td>
                  <td>
                    <div class="btn-group">
                      <Link :href="route('categories.show', category.id)" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i>
                      </Link>
                      <Link :href="route('categories.edit', category.id)" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                      </Link>
                      <button 
                        v-if="category.payment_requests_count === 0"
                        @click="deleteCategory(category)"
                        class="btn btn-sm btn-danger"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <tr v-if="categories.data.length === 0">
                  <td colspan="6" class="text-center text-muted">
                    <i class="fas fa-inbox"></i> Chưa có danh mục nào
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <Pagination :links="categories.links" :meta="categories.meta" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import Swal from 'sweetalert2'
import { router } from '@inertiajs/vue3'

export default {
  methods: {
    deleteCategory(category) {
      Swal.fire({
        title: 'Xác nhận xóa',
        text: `Bạn có chắc chắn muốn xóa danh mục "${category.name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
      }).then((result) => {
        if (result.isConfirmed) {
          router.delete(route('categories.destroy', category.id))
        }
      })
    }
  }
}
</script>

<style scoped>
.color-indicator {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 2px solid #fff;
  box-shadow: 0 0 0 1px rgba(0,0,0,0.1);
}
</style>
