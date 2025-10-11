<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  department: Object,
  offices: Array
})

const form = useForm({
  name: props.department.name,
  office_id: props.department.office_id
})

const submit = () => {
  form.put(route('admin.departments.update', props.department.id))
}
</script>

<template>
  <Head title="Sửa bộ phận" />

  <AdminLayout>
    <div class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Sửa bộ phận: {{ department.name }}</h3>
              </div>
              <form @submit.prevent="submit">
                <div class="card-body">
                  <div class="form-group">
                    <label>Tên bộ phận <span class="text-danger">*</span></label>
                    <input
                      v-model="form.name"
                      type="text"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.name }"
                    />
                    <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                  </div>

                  <div class="form-group">
                    <label>Văn phòng <span class="text-danger">*</span></label>
                    <select
                      v-model="form.office_id"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.office_id }"
                    >
                      <option value="">-- Chọn văn phòng --</option>
                      <option v-for="office in offices" :key="office.id" :value="office.id">{{ office.name }}</option>
                    </select>
                    <div v-if="form.errors.office_id" class="invalid-feedback">{{ form.errors.office_id }}</div>
                  </div>
                </div>

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    <i class="fas fa-save"></i> Cập nhật
                  </button>
                  <Link :href="route('admin.departments.index')" class="btn btn-secondary ml-2">Hủy</Link>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
