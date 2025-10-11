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
    <div class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Sửa văn phòng: {{ office.name }}</h3>
              </div>
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
      </div>
    </div>
  </AdminLayout>
</template>
