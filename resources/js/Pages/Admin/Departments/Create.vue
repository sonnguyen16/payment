<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  offices: Array
})

const form = useForm({
  name: '',
  office_id: ''
})

const submit = () => {
  form.post(route('admin.departments.store'))
}
</script>

<template>
  <Head title="Thêm bộ phận" />

  <AdminLayout>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Thêm bộ phận</h1>
          </div>
          <div class="col-sm-6">
            <Link :href="route('admin.departments.index')" class="btn btn-secondary float-right">
              <i class="fas fa-arrow-left"></i> Quay lại
            </Link>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="card">
          <form @submit.prevent="submit">
            <div class="card-body">
              <div class="form-group">
                <label>Tên bộ phận <span class="text-danger">*</span></label>
                <input
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.name }"
                  placeholder="VD: Công nghệ thông tin"
                />
                <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
              </div>

              <div class="form-group">
                <label>Văn phòng <span class="text-danger">*</span></label>
                <select v-model="form.office_id" class="form-control" :class="{ 'is-invalid': form.errors.office_id }">
                  <option value="">-- Chọn văn phòng --</option>
                  <option v-for="office in offices" :key="office.id" :value="office.id">{{ office.name }}</option>
                </select>
                <div v-if="form.errors.office_id" class="invalid-feedback">{{ form.errors.office_id }}</div>
              </div>
            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-primary" :disabled="form.processing">
                <i class="fas fa-save"></i> Lưu
              </button>
              <Link :href="route('admin.departments.index')" class="btn btn-secondary ml-2">Hủy</Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
