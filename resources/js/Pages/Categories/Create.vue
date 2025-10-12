<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  description: '',
  color: '#007bff',
  is_active: true
})

const submit = () => {
  form.post(route('admin.categories.store'))
}
</script>

<template>
  <Head title="Thêm danh mục" />

  <AdminLayout>
    <div class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Thông tin danh mục</h3>
              </div>
              <form @submit.prevent="submit">
                <div class="card-body">
                  <div class="form-group">
                    <label for="name">Tên danh mục <span class="text-danger">*</span></label>
                    <input
                      id="name"
                      v-model="form.name"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.name }"
                      placeholder="Nhập tên danh mục"
                      required
                    />
                    <div v-if="form.errors.name" class="invalid-feedback">
                      {{ form.errors.name }}
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="description">Mô tả</label>
                    <textarea
                      id="description"
                      v-model="form.description"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.description }"
                      rows="3"
                      placeholder="Nhập mô tả danh mục"
                    ></textarea>
                    <div v-if="form.errors.description" class="invalid-feedback">
                      {{ form.errors.description }}
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="color">Màu sắc <span class="text-danger">*</span></label>
                    <div class="d-flex align-items-center">
                      <input
                        id="color"
                        v-model="form.color"
                        type="color"
                        class="form-control mr-3"
                        :class="{ 'is-invalid': form.errors.color }"
                        style="width: 60px; height: 38px"
                        required
                      />
                      <input
                        v-model="form.color"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.color }"
                        placeholder="#007bff"
                        pattern="^#[0-9A-Fa-f]{6}$"
                        maxlength="7"
                      />
                    </div>
                    <div v-if="form.errors.color" class="invalid-feedback">
                      {{ form.errors.color }}
                    </div>
                    <small class="form-text text-muted"> Chọn màu sắc để phân biệt danh mục </small>
                  </div>

                  <div class="form-group">
                    <div class="custom-control custom-switch">
                      <input id="is_active" v-model="form.is_active" type="checkbox" class="custom-control-input" />
                      <label class="custom-control-label" for="is_active"> Kích hoạt danh mục </label>
                    </div>
                    <small class="form-text text-muted">
                      Chỉ những danh mục được kích hoạt mới hiển thị khi tạo phiếu
                    </small>
                  </div>
                </div>

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    <i class="fas fa-save"></i>
                    {{ form.processing ? 'Đang lưu...' : 'Lưu danh mục' }}
                  </button>
                  <Link :href="route('admin.categories.index')" class="btn btn-secondary ml-2"> Hủy </Link>
                </div>
              </form>
            </div>
          </div>

          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Xem trước</h3>
              </div>
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <div class="color-indicator mr-3" :style="{ backgroundColor: form.color }"></div>
                  <div>
                    <h5 class="mb-1">{{ form.name || 'Tên danh mục' }}</h5>
                    <p class="mb-0 text-muted">{{ form.description || 'Mô tả danh mục' }}</p>
                  </div>
                </div>
                <span class="badge" :style="{ backgroundColor: form.color, color: '#fff' }">
                  {{ form.color }}
                </span>
                <span class="badge ml-2" :class="form.is_active ? 'badge-success' : 'badge-secondary'">
                  {{ form.is_active ? 'Hoạt động' : 'Không hoạt động' }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.color-indicator {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid #fff;
  box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1);
}
</style>
