<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'
import { ref } from 'vue'

const props = defineProps({
  categories: Object,
  filters: Object
})

const search = ref(props.filters?.search || '')
const is_active = ref(props.filters?.is_active || '')

const applyFilters = () => {
  router.get(
    route('admin.categories.index'),
    {
      search: search.value,
      is_active: is_active.value
    },
    {
      preserveState: true,
      preserveScroll: true
    }
  )
}

const clearFilters = () => {
  search.value = ''
  is_active.value = ''
  router.get(route('admin.categories.index'))
}

const deleteCategory = (category) => {
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
      router.delete(route('admin.categories.destroy', category.id))
    }
  })
}
</script>

<template>
  <Head title="Quản lý danh mục" />

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
                  placeholder="Tìm kiếm danh mục..."
                />
              </div>
              <div class="col-md-2">
                <select v-model="is_active" @change="applyFilters" class="form-control">
                  <option value="">Tất cả trạng thái</option>
                  <option value="active">Hoạt động</option>
                  <option value="inactive">Không hoạt động</option>
                </select>
              </div>
              <div class="col-md-5 d-flex gap-3">
                <button @click="clearFilters" class="btn btn-secondary"><i class="fas fa-times"></i> Xóa bộ lọc</button>
                <Link :href="route('admin.categories.create')" class="btn btn-primary">
                  <i class="fas fa-plus"></i> Thêm danh mục
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Danh sách danh mục</h3>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th style="width: 50px">STT</th>
                    <th>Tên danh mục</th>
                    <th>Mô tả</th>
                    <th>Màu sắc</th>
                    <th>Trạng thái</th>
                    <th>Số phiếu</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(category, index) in categories.data" :key="category.id">
                    <td class="text-center">{{ categories.from + index }}</td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="color-indicator mr-2" :style="{ backgroundColor: category.color }"></div>
                        {{ category.name }}
                      </div>
                    </td>
                    <td>{{ category.description || '-' }}</td>
                    <td class="text-center">
                      <span class="badge" :style="{ backgroundColor: category.color, color: '#fff' }">
                        {{ category.color }}
                      </span>
                    </td>
                    <td class="text-center">
                      <span class="badge" :class="category.is_active ? 'badge-success' : 'badge-secondary'">
                        {{ category.is_active ? 'Hoạt động' : 'Không hoạt động' }}
                      </span>
                    </td>
                    <td>{{ category.payment_requests_count || 0 }}</td>
                    <td class="text-center">
                      <div class="btn-group">
                        <Link :href="route('admin.categories.show', category.id)" class="btn btn-sm btn-info">
                          <i class="fas fa-eye"></i>
                        </Link>
                        <Link :href="route('admin.categories.edit', category.id)" class="btn btn-sm btn-warning">
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
          </div>
          <Pagination :links="categories.links" :meta="categories.meta" />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.color-indicator {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 2px solid #fff;
  box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1);
}
</style>
