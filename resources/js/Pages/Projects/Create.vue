<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  code: '',
  name: '',
  description: '',
  budget: '',
  status: 'active',
  start_date: '',
  end_date: ''
})

const submit = () => {
  form.post(route('admin.projects.store'))
}
</script>

<template>
  <Head title="Tạo dự án mới" />

  <AdminLayout>
    <div class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Thông tin dự án</h3>
              </div>
              <form @submit.prevent="submit">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="code">Mã dự án <span class="text-danger">*</span></label>
                        <input
                          id="code"
                          v-model="form.code"
                          type="text"
                          class="form-control"
                          :class="{ 'is-invalid': form.errors.code }"
                          placeholder="VD: PRJ001"
                        />
                        <div v-if="form.errors.code" class="invalid-feedback">
                          {{ form.errors.code }}
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="status">Trạng thái <span class="text-danger">*</span></label>
                        <select
                          id="status"
                          v-model="form.status"
                          class="form-control"
                          :class="{ 'is-invalid': form.errors.status }"
                        >
                          <option value="active">Đang thực hiện</option>
                          <option value="completed">Hoàn thành</option>
                          <option value="cancelled">Đã hủy</option>
                        </select>
                        <div v-if="form.errors.status" class="invalid-feedback">
                          {{ form.errors.status }}
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="name">Tên dự án <span class="text-danger">*</span></label>
                    <input
                      id="name"
                      v-model="form.name"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.name }"
                      placeholder="Nhập tên dự án"
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
                      placeholder="Mô tả chi tiết về dự án"
                    ></textarea>
                    <div v-if="form.errors.description" class="invalid-feedback">
                      {{ form.errors.description }}
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="budget">Ngân sách <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <input
                        id="budget"
                        v-model="form.budget"
                        type="number"
                        step="0.01"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.budget }"
                        placeholder="0.00"
                      />
                      <div v-if="form.errors.budget" class="invalid-feedback">
                        {{ form.errors.budget }}
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="start_date">Ngày bắt đầu <span class="text-danger">*</span></label>
                        <input
                          id="start_date"
                          v-model="form.start_date"
                          type="date"
                          class="form-control"
                          :class="{ 'is-invalid': form.errors.start_date }"
                        />
                        <div v-if="form.errors.start_date" class="invalid-feedback">
                          {{ form.errors.start_date }}
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="end_date">Ngày kết thúc <span class="text-danger">*</span></label>
                        <input
                          id="end_date"
                          v-model="form.end_date"
                          type="date"
                          class="form-control"
                          :class="{ 'is-invalid': form.errors.end_date }"
                        />
                        <div v-if="form.errors.end_date" class="invalid-feedback">
                          {{ form.errors.end_date }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    <i class="fas fa-save"></i>
                    {{ form.processing ? 'Đang lưu...' : 'Tạo dự án' }}
                  </button>
                  <Link :href="route('admin.projects.index')" class="btn btn-secondary ml-2"> Hủy </Link>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
