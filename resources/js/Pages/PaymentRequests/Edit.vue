<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  request: Object,
  projects: Array,
  categories: Array
})

const form = useForm({
  category_id: props.request.category_id || '',
  reason: props.request.reason,
  expected_date: props.request.expected_date.split('T')[0],
  priority: props.request.priority,
  project_id: props.request.project_id || '',
  details:
    props.request.details && props.request.details.length > 0
      ? props.request.details.map((detail) => ({
          description: detail.description,
          amount_before_tax: parseInt(detail.amount_before_tax),
          tax_amount: parseInt(detail.tax_amount),
          total_amount: parseInt(detail.total_amount),
          invoice_number: detail.invoice_number || ''
        }))
      : [
          {
            description: props.request.description || '',
            amount_before_tax: props.request.amount || '',
            tax_amount: 0,
            total_amount: props.request.amount || '',
            invoice_number: ''
          }
        ],
  update_reason: ''
})

const addDetail = () => {
  form.details.push({
    description: '',
    amount_before_tax: '',
    tax_amount: 0,
    total_amount: '',
    invoice_number: ''
  })
}

const removeDetail = (index) => {
  if (form.details.length > 1) {
    form.details.splice(index, 1)
  }
}

const formatNumber = (value) => {
  if (!value) return ''
  const num = value.toString().replace(/[^0-9]/g, '')
  return num.replace(/\B(?=(\d{3})+(?!\d))/g, ',')
}

const parseNumber = (value) => {
  if (!value) return 0
  return parseFloat(value.toString().replace(/,/g, '')) || 0
}

const handleAmountInput = (detail, field, event) => {
  const rawValue = event.target.value.replace(/,/g, '')
  detail[field] = rawValue
  calculateTotal(detail)
}

const calculateTotal = (detail) => {
  const beforeTax = parseNumber(detail.amount_before_tax)
  const tax = parseNumber(detail.tax_amount)
  detail.total_amount = beforeTax + tax
}

const getTotalAmount = () => {
  return form.details.reduce((sum, detail) => {
    return sum + parseNumber(detail.total_amount)
  }, 0)
}

const getTotalBeforeTax = () => {
  return form.details.reduce((sum, detail) => {
    return sum + parseNumber(detail.amount_before_tax)
  }, 0)
}

const getTotalTax = () => {
  return form.details.reduce((sum, detail) => {
    return sum + parseNumber(detail.tax_amount)
  }, 0)
}

const submit = () => {
  form.put(route('payment-requests.update', props.request.id))
}
</script>

<template>
  <Head title="Chỉnh sửa phiếu" />

  <AdminLayout>
    <div class="content pt-3">
      <div class="container-fluid">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Chỉnh sửa phiếu #{{ request.id }}</h3>
          </div>
          <form @submit.prevent="submit">
            <div class="card-body">
              <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Bạn đang chỉnh sửa phiếu. Vui lòng nhập lý do chỉnh sửa ở cuối form.
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Danh mục <span class="text-danger">*</span></label>
                    <select
                      v-model="form.category_id"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.category_id }"
                    >
                      <option value="">-- Chọn danh mục --</option>
                      <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                      </option>
                    </select>
                    <div v-if="form.errors.category_id" class="invalid-feedback">{{ form.errors.category_id }}</div>
                  </div>
                </div>
              </div>

              <!-- Chi tiết thanh toán -->
              <div class="row">
                <div class="col-12">
                  <h5 class="mb-2">Chi tiết thanh toán</h5>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th style="width: 50px">STT</th>
                          <th>Nội dung <span class="text-danger">*</span></th>
                          <th style="width: 150px">Số tiền chưa thuế <span class="text-danger">*</span></th>
                          <th style="width: 120px">Thuế GTGT</th>
                          <th style="width: 150px">Tổng tiền</th>
                          <th style="width: 120px">Số hóa đơn</th>
                          <th style="width: 80px">Thao tác</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(detail, index) in form.details" :key="index">
                          <td class="text-center">{{ index + 1 }}</td>
                          <td>
                            <textarea
                              v-model="detail.description"
                              class="form-control"
                              :class="{ 'is-invalid': form.errors[`details.${index}.description`] }"
                              rows="1"
                              placeholder="Nhập nội dung"
                            ></textarea>
                            <div v-if="form.errors[`details.${index}.description`]" class="invalid-feedback">
                              {{ form.errors[`details.${index}.description`] }}
                            </div>
                          </td>
                          <td>
                            <input
                              :value="formatNumber(detail.amount_before_tax)"
                              @input="handleAmountInput(detail, 'amount_before_tax', $event)"
                              type="text"
                              class="form-control text-right"
                              :class="{ 'is-invalid': form.errors[`details.${index}.amount_before_tax`] }"
                              placeholder="0"
                            />
                            <div v-if="form.errors[`details.${index}.amount_before_tax`]" class="invalid-feedback">
                              {{ form.errors[`details.${index}.amount_before_tax`] }}
                            </div>
                          </td>
                          <td>
                            <input
                              :value="formatNumber(detail.tax_amount)"
                              @input="handleAmountInput(detail, 'tax_amount', $event)"
                              type="text"
                              class="form-control text-right"
                              placeholder="0"
                            />
                          </td>
                          <td>
                            <input
                              :value="formatNumber(detail.total_amount)"
                              type="text"
                              class="form-control text-right"
                              readonly
                            />
                          </td>
                          <td>
                            <input
                              v-model="detail.invoice_number"
                              type="text"
                              class="form-control"
                              placeholder="Số hóa đơn"
                            />
                          </td>
                          <td class="text-center">
                            <button
                              v-if="form.details.length > 1"
                              @click="removeDetail(index)"
                              type="button"
                              class="btn btn-sm btn-danger"
                            >
                              <i class="fas fa-trash"></i>
                            </button>
                          </td>
                        </tr>
                      </tbody>
                      <tfoot>
                        <tr class="bg-light font-weight-bold">
                          <th colspan="2" class="text-right">TỔNG CỘNG:</th>
                          <th class="text-right">{{ formatNumber(getTotalBeforeTax()) }}</th>
                          <th class="text-right">{{ formatNumber(getTotalTax()) }}</th>
                          <th class="text-right text-danger">{{ formatNumber(getTotalAmount()) }}</th>
                          <th></th>
                          <th></th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                  <button @click="addDetail" type="button" class="btn btn-sm btn-success my-2">
                    <i class="fas fa-plus"></i> Thêm dòng
                  </button>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Lý do <span class="text-danger">*</span></label>
                    <textarea
                      v-model="form.reason"
                      class="form-control"
                      :class="{ 'is-invalid': form.errors.reason }"
                      rows="2"
                      placeholder="Lý do thanh toán"
                    ></textarea>
                    <div v-if="form.errors.reason" class="invalid-feedback">{{ form.errors.reason }}</div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-2">
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
                <div class="col-md-2">
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

              <div class="form-group mt-3">
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
              <Link :href="route('payment-requests.index')" class="btn btn-secondary ml-2">
                <i class="fas fa-times"></i> Hủy
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
