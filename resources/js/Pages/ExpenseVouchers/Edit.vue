<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, router, useForm } from '@inertiajs/vue3'

const props = defineProps({
  voucher: Object,
  categories: Array,
  projects: Array
})

const form = useForm({
  expense_date: props.voucher.expense_date.toString().split('T')[0],
  description: props.voucher.description,
  amount: props.voucher.amount,
  expense_category_id: props.voucher.expense_category_id,
  project_id: props.voucher.project_id,
  recipient: props.voucher.recipient
})

const formatNumber = (value) => {
  if (!value) return ''
  const num = value.toString().replace(/[^0-9]/g, '')
  return num.replace(/\B(?=(\d{3})+(?!\d))/g, ',')
}

const handleAmountInput = (event) => {
  const rawValue = event.target.value.replace(/,/g, '')
  form.amount = rawValue
}

const submit = () => {
  form.put(route('expense-vouchers.update', props.voucher.id))
}
</script>

<template>
  <Head title="Chỉnh sửa phiếu chi" />

  <AdminLayout>
    <div class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Thông tin phiếu chi</h3>
              </div>
              <form @submit.prevent="submit">
                <div class="card-body">
                  <!-- Ngày chi (chiếm 1/2 chiều rộng) -->
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Ngày chi <span class="text-danger">*</span></label>
                        <input
                          type="date"
                          v-model="form.expense_date"
                          class="form-control"
                          :class="{ 'is-invalid': form.errors.expense_date }"
                        />
                        <div v-if="form.errors.expense_date" class="invalid-feedback">
                          {{ form.errors.expense_date }}
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Nội dung -->
                  <div class="form-group">
                    <label>Nội dung <span class="text-danger">*</span></label>
                    <textarea
                      v-model="form.description"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.description }"
                      rows="4"
                      placeholder="Nhập nội dung chi tiết"
                    ></textarea>
                    <div v-if="form.errors.description" class="invalid-feedback">
                      {{ form.errors.description }}
                    </div>
                  </div>

                  <!-- Dự án -->
                  <div class="form-group">
                    <label>Dự án</label>
                    <select
                      v-model="form.project_id"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.project_id }"
                    >
                      <option value="">-- Chọn dự án (nếu có) --</option>
                      <option v-for="project in projects" :key="project.id" :value="project.id">
                        {{ project.name }}
                      </option>
                    </select>
                    <div v-if="form.errors.project_id" class="invalid-feedback">
                      {{ form.errors.project_id }}
                    </div>
                  </div>

                  <!-- Số tiền -->
                  <div class="form-group">
                    <label>Số tiền <span class="text-danger">*</span></label>
                    <input
                      type="text"
                      :value="formatNumber(form.amount)"
                      @input="handleAmountInput"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.amount }"
                      placeholder="0"
                    />
                    <div v-if="form.errors.amount" class="invalid-feedback">
                      {{ form.errors.amount }}
                    </div>
                  </div>

                  <!-- Người nhận -->
                  <div class="form-group">
                    <label>Người nhận <span class="text-danger">*</span></label>
                    <input
                      type="text"
                      v-model="form.recipient"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.recipient }"
                      placeholder="Nhập tên người nhận"
                    />
                    <div v-if="form.errors.recipient" class="invalid-feedback">
                      {{ form.errors.recipient }}
                    </div>
                  </div>

                  <!-- Danh mục -->
                  <div class="form-group">
                    <label>Danh mục <span class="text-danger">*</span></label>
                    <select
                      v-model="form.expense_category_id"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.expense_category_id }"
                    >
                      <option value="">-- Chọn danh mục --</option>
                      <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                      </option>
                    </select>
                    <div v-if="form.errors.expense_category_id" class="invalid-feedback">
                      {{ form.errors.expense_category_id }}
                    </div>
                  </div>
                </div>

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    <i class="fas fa-save"></i> Cập nhật phiếu chi
                  </button>
                  <a @click.prevent="router.visit(route('expense-vouchers.index'))" class="btn btn-secondary ml-2">
                    <i class="fas fa-times"></i> Hủy
                  </a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
