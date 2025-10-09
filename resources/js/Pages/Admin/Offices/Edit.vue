<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  office: Object
})

const form = useForm({
  name: props.office.name,
  location: props.office.location || ''
})

const submit = () => {
  form.put(route('admin.offices.update', props.office.id))
}
</script>

<template>
  <Head title="Sửa văn phòng" />

  <AdminLayout>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Sửa văn phòng: {{ office.name }}</h1>
          </div>
          <div class="col-sm-6">
            <Link :href="route('admin.offices.index')" class="btn btn-secondary float-right">
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
                <label>Tên văn phòng <span class="text-danger">*</span></label>
                <input
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.name }"
                />
                <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
              </div>

              <div class="form-group">
                <label>Địa chỉ</label>
                <textarea v-model="form.location" class="form-control" rows="3"></textarea>
              </div>
            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-primary" :disabled="form.processing">
                <i class="fas fa-save"></i> Cập nhật
              </button>
              <Link :href="route('admin.offices.index')" class="btn btn-secondary ml-2">Hủy</Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
