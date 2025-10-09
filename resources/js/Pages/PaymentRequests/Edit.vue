<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({
  request: Object,
  projects: Array,
  categories: Array
})

const form = useForm({
  category_id: props.request.category_id || '',
  amount: props.request.amount,
  description: props.request.description,
  reason: props.request.reason,
  expected_date: props.request.expected_date.split('T')[0],
  priority: props.request.priority,
  project_id: props.request.project_id || '',
  update_reason: ''
})

const submit = () => {
  form.put(route('payment-requests.update', props.request.id))
}
</script>

<template>
  <Head title="Chỉnh sửa phiếu" />

  <AdminLayout>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Chỉnh sửa phiếu #{{ request.id }}</h1>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="card">
          <form @submit.prevent="submit">
            <div class="card-body">
              <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Bạn đang chỉnh sửa phiếu. Vui lòng nhập lý do chỉnh sửa ở cuối form.
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Danh mục <span class="text-danger">*</span></label>
                    <select v-model="form.category_id" class="form-control" :class="{ 'is-invalid': form.errors.category_id }">
                      <option value="">-- Chọn danh mục --</option>
                      <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                      </option>
                    </select>
                    <div v-if="form.errors.category_id" class="invalid-feedback">{{ form.errors.category_id }}</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Số tiền (VNĐ) <span class="text-danger">*</span></label>
                    <input
                      v-model="form.amount"
                      type="number"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.amount }"
                    />
                    <div v-if="form.errors.amount" class="invalid-feedback">{{ form.errors.amount }}</div>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>Mô tả <span class="text-danger">*</span></label>
                <textarea
                  v-model="form.description"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.description }"
                  rows="3"
                ></textarea>
                <div v-if="form.errors.description" class="invalid-feedback">{{ form.errors.description }}</div>
              </div>

              <div class="form-group">
                <label>Lý do <span class="text-danger">*</span></label>
                <textarea
                  v-model="form.reason"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.reason }"
                  rows="2"
                ></textarea>
                <div v-if="form.errors.reason" class="invalid-feedback">{{ form.errors.reason }}</div>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Ngày dự kiến <span class="text-danger">*</span></label>
                    <input
                      v-model="form.expected_date"
                      type="date"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.expected_date }"
                    />
                    <div v-if="form.errors.expected_date" class="invalid-feedback">{{ form.errors.expected_date }}</div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Ưu tiên <span class="text-danger">*</span></label>
                    <select
                      v-model="form.priority"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.priority }"
                    >
                      <option value="normal">Bình thường</option>
                      <option value="urgent">Gấp</option>
                    </select>
                    <div v-if="form.errors.priority" class="invalid-feedback">{{ form.errors.priority }}</div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Dự án (nếu có)</label>
                    <select
                      v-model="form.project_id"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.project_id }"
                    >
                      <option value="">-- Chọn dự án --</option>
                      <option v-for="project in projects" :key="project.id" :value="project.id">
                        {{ project.name }} ({{ project.code }})
                      </option>
                    </select>
                    <div v-if="form.errors.project_id" class="invalid-feedback">{{ form.errors.project_id }}</div>
                  </div>
                </div>
              </div>

              <hr />

              <div class="form-group">
                <label>Lý do chỉnh sửa <span class="text-danger">*</span></label>
                <textarea
                  v-model="form.update_reason"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.update_reason }"
                  rows="2"
                  placeholder="Nhập lý do chỉnh sửa phiếu này..."
                ></textarea>
                <div v-if="form.errors.update_reason" class="invalid-feedback">{{ form.errors.update_reason }}</div>
              </div>
            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-primary" :disabled="form.processing">
                <i class="fas fa-save"></i> Cập nhật phiếu
              </button>
              <a :href="route('payment-requests.show', request.id)" class="btn btn-secondary ml-2">
                <i class="fas fa-times"></i> Hủy
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
