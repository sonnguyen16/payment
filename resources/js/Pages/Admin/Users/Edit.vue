<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  user: Object,
  roles: Array,
  offices: Array,
  departments: Array
})

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',
  password_confirmation: '',
  role: props.user.roles[0]?.name || '',
  office_id: props.user.office_id || '',
  department_id: props.user.department_id || ''
})

const filteredDepartments = computed(() => {
  if (!form.office_id) return []
  return props.departments.filter((dept) => dept.office_id == form.office_id)
})

const submit = () => {
  form.put(route('admin.users.update', props.user.id))
}
</script>

<template>
  <Head title="Sửa người dùng" />

  <AdminLayout>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Sửa người dùng: {{ user.name }}</h1>
          </div>
          <div class="col-sm-6">
            <Link :href="route('admin.users.index')" class="btn btn-secondary float-right">
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
                <label>Tên <span class="text-danger">*</span></label>
                <input
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.name }"
                />
                <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
              </div>

              <div class="form-group">
                <label>Email <span class="text-danger">*</span></label>
                <input
                  v-model="form.email"
                  type="email"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.email }"
                />
                <div v-if="form.errors.email" class="invalid-feedback">{{ form.errors.email }}</div>
              </div>

              <div class="form-group">
                <label>Mật khẩu mới (để trống nếu không đổi)</label>
                <input
                  v-model="form.password"
                  type="password"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.password }"
                />
                <div v-if="form.errors.password" class="invalid-feedback">{{ form.errors.password }}</div>
              </div>

              <div class="form-group" v-if="form.password">
                <label>Xác nhận mật khẩu</label>
                <input v-model="form.password_confirmation" type="password" class="form-control" />
              </div>

              <div class="form-group">
                <label>Vai trò <span class="text-danger">*</span></label>
                <select v-model="form.role" class="form-control" :class="{ 'is-invalid': form.errors.role }">
                  <option value="">-- Chọn vai trò --</option>
                  <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                </select>
                <div v-if="form.errors.role" class="invalid-feedback">{{ form.errors.role }}</div>
              </div>

              <div class="form-group" v-if="!['admin', 'ceo'].includes(form.role)">
                <label>Văn phòng <span class="text-danger" v-if="form.role === 'accountant'">*</span></label>
                <select v-model="form.office_id" class="form-control" @change="form.department_id = ''">
                  <option value="">-- Chọn văn phòng --</option>
                  <option v-for="office in offices" :key="office.id" :value="office.id">{{ office.name }}</option>
                </select>
                <div v-if="form.errors.office_id" class="invalid-feedback">{{ form.errors.office_id }}</div>
              </div>

              <div
                class="form-group"
                v-if="form.role && !['admin', 'ceo', 'accountant'].includes(form.role) && form.office_id"
              >
                <label>Bộ phận <span class="text-danger">*</span></label>
                <select v-model="form.department_id" class="form-control" :class="{ 'is-invalid': form.errors.department_id }">
                  <option value="">-- Chọn bộ phận --</option>
                  <option v-for="dept in filteredDepartments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
                <div v-if="form.errors.department_id" class="invalid-feedback">{{ form.errors.department_id }}</div>
              </div>
            </div>

            <div class="card-footer">
              <button type="submit" class="btn btn-primary" :disabled="form.processing">
                <i class="fas fa-save"></i> Cập nhật
              </button>
              <Link :href="route('admin.users.index')" class="btn btn-secondary ml-2">Hủy</Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
