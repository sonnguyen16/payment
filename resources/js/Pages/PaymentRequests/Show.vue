<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ApprovalTimeline from '@/Components/ApprovalTimeline.vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  request: Object,
  can: Object
})

const formatMoney = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('vi-VN')
}

const formatDateTime = (datetime) => {
  const d = new Date(datetime)
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  const hours = d.getHours()
  const minutes = String(d.getMinutes()).padStart(2, '0')
  const ampm = hours >= 12 ? 'PM' : 'AM'
  const displayHours = hours % 12 || 12
  return `${day}/${month}/${year} ${displayHours}:${minutes} ${ampm}`
}

const submitForApproval = async () => {
  const result = await Swal.fire({
    title: 'Xác nhận gửi duyệt?',
    text: 'Sau khi gửi, bạn không thể chỉnh sửa phiếu này',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Gửi duyệt',
    cancelButtonText: 'Hủy'
  })

  if (result.isConfirmed) {
    useForm({}).post(route('payment-requests.submit', props.request.id))
  }
}

const cancelRequest = async () => {
  const { value: reason } = await Swal.fire({
    title: 'Hủy phiếu',
    input: 'textarea',
    inputLabel: 'Nhập lý do hủy phiếu',
    inputPlaceholder: 'Lý do hủy...',
    inputAttributes: {
      'aria-label': 'Nhập lý do hủy phiếu'
    },
    showCancelButton: true,
    confirmButtonText: 'Hủy phiếu',
    cancelButtonText: 'Đóng',
    confirmButtonColor: '#d33',
    inputValidator: (value) => {
      if (!value) {
        return 'Vui lòng nhập lý do hủy!'
      }
    }
  })

  if (reason) {
    useForm({ reason }).post(route('payment-requests.cancel', props.request.id))
  }
}

const approveRequest = async () => {
  const { value: note } = await Swal.fire({
    title: 'Phê duyệt phiếu',
    input: 'textarea',
    inputLabel: 'Ghi chú (không bắt buộc)',
    inputPlaceholder: 'Nhập ghi chú...',
    showCancelButton: true,
    confirmButtonText: 'Phê duyệt',
    cancelButtonText: 'Hủy',
    confirmButtonColor: '#28a745'
  })

  if (note !== undefined) {
    useForm({ note }).post(route('approvals.approve', props.request.id))
  }
}

const rejectRequest = async () => {
  const { value: reason } = await Swal.fire({
    title: 'Từ chối phiếu',
    input: 'textarea',
    inputLabel: 'Nhập lý do từ chối',
    inputPlaceholder: 'Lý do từ chối...',
    showCancelButton: true,
    confirmButtonText: 'Từ chối',
    cancelButtonText: 'Hủy',
    confirmButtonColor: '#dc3545',
    inputValidator: (value) => {
      if (!value) {
        return 'Vui lòng nhập lý do từ chối!'
      }
    }
  })

  if (reason) {
    useForm({ reason }).post(route('approvals.reject', props.request.id))
  }
}

const uploadForm = useForm({
  files: [],
  type: 'invoice'
})

const handleFileSelect = (event) => {
  uploadForm.files = Array.from(event.target.files)
}

const uploadDocuments = () => {
  if (uploadForm.files.length === 0) {
    Swal.fire('Lỗi', 'Vui lòng chọn file', 'error')
    return
  }

  const formData = new FormData()
  uploadForm.files.forEach((file, index) => {
    formData.append(`files[${index}]`, file)
  })
  formData.append('type', uploadForm.type)

  uploadForm.post(route('documents.store', props.request.id), {
    forceFormData: true,
    onSuccess: () => {
      uploadForm.reset()
      document.getElementById('fileInput').value = ''
      Swal.fire('Thành công', 'Tài liệu đã được tải lên', 'success')
    }
  })
}

const getStatusLabel = (status) => {
  const labels = {
    draft: 'Nháp',
    pending_department_head: 'Chờ Trưởng BP',
    pending_accountant: 'Chờ Kế toán',
    pending_ceo: 'Chờ TGĐ',
    pending_payment: 'Chờ thanh toán',
    paid: 'Đã thanh toán',
    rejected: 'Bị từ chối',
    cancelled: 'Đã hủy',
    deleted: 'Đã xóa'
  }
  return labels[status] || status
}

const getStatusBadgeClass = (status) => {
  const classes = {
    draft: 'badge-secondary',
    pending_department_head: 'badge-warning',
    pending_accountant: 'badge-info',
    pending_ceo: 'badge-primary',
    pending_payment: 'badge-success',
    paid: 'badge-success',
    rejected: 'badge-danger',
    cancelled: 'badge-dark',
    deleted: 'badge-dark'
  }
  return classes[status] || 'badge-secondary'
}

// Computed properties for button visibility
const canApproveOrReject = computed(() => {
  return !['cancelled', 'rejected', 'paid', 'deleted'].includes(props.request.status)
})

const canCancel = computed(() => {
  return !['cancelled', 'paid', 'deleted'].includes(props.request.status)
})

const canUpdate = computed(() => {
  // Chỉ cho phép sửa khi ở trạng thái draft
  // Sau khi trưởng phòng duyệt (pending_accountant trở đi) thì không cho sửa nữa
  return props.request.status === 'draft'
})

const getTotalBeforeTax = computed(() => {
  if (!props.request.details || props.request.details.length === 0) return 0
  return props.request.details.reduce((sum, detail) => sum + parseFloat(detail.amount_before_tax || 0), 0)
})

const getTotalTax = computed(() => {
  if (!props.request.details || props.request.details.length === 0) return 0
  return props.request.details.reduce((sum, detail) => sum + parseFloat(detail.tax_amount || 0), 0)
})

const getDocumentTypeLabel = (type) => {
  const labels = {
    invoice: 'Hóa đơn',
    receipt: 'Biên lai',
    contract: 'Hợp đồng',
    other: 'Khác'
  }
  return labels[type] || type
}

// Format update_histories to add action field for ApprovalTimeline component
const formattedUpdateHistories = computed(() => {
  if (!props.request.update_histories) return []
  return props.request.update_histories.map((history) => ({
    ...history,
    action: 'updated' // Add action field for timeline component
  }))
})
</script>

<template>
  <Head title="Chi tiết phiếu" />

  <AdminLayout>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Chi tiết phiếu #{{ request.id }}</h1>
          </div>
          <div class="col-sm-6">
            <Link :href="route('payment-requests.index')" class="btn btn-secondary float-right">
              <i class="fas fa-arrow-left"></i> Quay lại
            </Link>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Thông tin phiếu</h3>
              </div>
              <div class="card-body">
                <table class="table table-bordered">
                  <tr>
                    <th>Danh mục</th>
                    <td>
                      <div class="d-flex align-items-center" v-if="request.category">
                        <div class="category-indicator mr-2" :style="{ backgroundColor: request.category.color }"></div>
                        <span>{{ request.category.name }}</span>
                      </div>
                      <span v-else class="text-muted">Chưa phân loại</span>
                    </td>
                  </tr>
                  <tr>
                    <th>Người đề xuất</th>
                    <td>{{ request.user.name }}</td>
                  </tr>
                  <tr>
                    <th>Lý do</th>
                    <td>{{ request.reason }}</td>
                  </tr>
                  <tr>
                    <th>Ngày dự kiến</th>
                    <td>{{ formatDate(request.expected_date) }}</td>
                  </tr>
                  <tr v-if="request.paid_at">
                    <th>Ngày giải ngân</th>
                    <td>{{ formatDateTime(request.paid_at) }}</td>
                  </tr>
                  <tr v-if="request.payment_code">
                    <th>Mã TT</th>
                    <td>{{ request.payment_code }}</td>
                  </tr>
                  <tr>
                    <th>Tổng tiền</th>
                    <td class="text-danger font-weight-bold">{{ formatMoney(request.amount) }}</td>
                  </tr>
                  <tr>
                    <th>Ưu tiên</th>
                    <td>
                      <span class="badge" :class="request.priority === 'urgent' ? 'badge-danger' : 'badge-secondary'">
                        {{ request.priority === 'urgent' ? 'Gấp' : 'Bình thường' }}
                      </span>
                    </td>
                  </tr>
                  <tr v-if="request.project">
                    <th>Dự án</th>
                    <td>{{ request.project.name }} ({{ request.project.code }})</td>
                  </tr>

                  <tr>
                    <th>Ngày tạo</th>
                    <td>{{ formatDateTime(request.created_at) }}</td>
                  </tr>
                  <tr>
                    <th width="15%">Trạng thái</th>
                    <td>
                      <span class="badge badge-lg" :class="getStatusBadgeClass(request.status)">
                        {{ getStatusLabel(request.status) }}
                      </span>
                    </td>
                  </tr>
                  <tr v-if="request.rejection_reason">
                    <th>Lý do từ chối</th>
                    <td class="text-danger">{{ request.rejection_reason }}</td>
                  </tr>
                </table>
              </div>
            </div>

            <!-- Chi tiết thanh toán -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Chi tiết thanh toán</h3>
              </div>
              <div class="card-body p-0">
                <table class="table" v-if="request.details && request.details.length > 0">
                  <thead>
                    <tr>
                      <th style="width: 40px">STT</th>
                      <th>Nội dung</th>
                      <th style="width: 130px">Tiền chưa thuế</th>
                      <th style="width: 100px">Thuế GTGT</th>
                      <th style="width: 130px">Tổng tiền</th>
                      <th style="width: 100px">Số HĐ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(detail, index) in request.details" :key="detail.id">
                      <td class="text-center">{{ index + 1 }}</td>
                      <td>{{ detail.description }}</td>
                      <td class="text-right">{{ formatMoney(detail.amount_before_tax) }}</td>
                      <td class="text-right">{{ formatMoney(detail.tax_amount) }}</td>
                      <td class="text-right font-weight-bold">{{ formatMoney(detail.total_amount) }}</td>
                      <td>{{ detail.invoice_number || '-' }}</td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr class="bg-light">
                      <th colspan="2" class="text-right">Tổng chưa thuế:</th>
                      <th class="text-right">{{ formatMoney(getTotalBeforeTax) }}</th>
                      <th class="text-right">{{ formatMoney(getTotalTax) }}</th>
                      <th class="text-right text-danger">{{ formatMoney(request.amount) }}</th>
                      <th></th>
                    </tr>
                  </tfoot>
                </table>
                <div v-else class="text-center p-4 text-muted">
                  <i class="fas fa-inbox fa-2x mb-2"></i>
                  <p>Không có chi tiết thanh toán</p>
                </div>
              </div>
            </div>

            <!-- Approval History -->
            <div class="card" v-if="request.approval_histories && request.approval_histories.length > 0">
              <div class="card-header">
                <h3 class="card-title">Lịch sử phê duyệt</h3>
              </div>
              <div class="card-body">
                <ApprovalTimeline :histories="request.approval_histories" />
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <!-- Actions -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Thao tác</h3>
              </div>
              <div class="card-body">
                <div class="d-grid gap-2">
                  <a
                    :href="route('payment-requests.pdf', request.id)"
                    class="btn btn-success btn-block"
                    target="_blank"
                  >
                    <i class="fas fa-file-pdf"></i> Xuất PDF
                  </a>

                  <Link
                    v-if="can.update"
                    :href="route('payment-requests.edit', request.id)"
                    class="btn btn-warning btn-block"
                  >
                    <i class="fas fa-edit"></i> Chỉnh sửa
                  </Link>

                  <button v-if="can.update && canUpdate" @click="submitForApproval" class="btn btn-primary btn-block">
                    <i class="fas fa-paper-plane"></i>
                    {{ request.status === 'rejected' ? 'Gửi lại duyệt' : 'Gửi duyệt' }}
                  </button>

                  <button
                    v-if="can.approve && canApproveOrReject"
                    @click="approveRequest"
                    class="btn btn-success btn-block"
                  >
                    <i class="fas fa-check"></i> Phê duyệt
                  </button>

                  <button
                    v-if="can.reject && canApproveOrReject"
                    @click="rejectRequest"
                    class="btn btn-danger btn-block"
                  >
                    <i class="fas fa-times"></i> Từ chối
                  </button>

                  <button v-if="can.cancel && canCancel" @click="cancelRequest" class="btn btn-dark btn-block">
                    <i class="fas fa-ban"></i> Hủy phiếu
                  </button>
                </div>
              </div>
            </div>

            <!-- Upload Documents (After Paid) -->
            <div class="card" v-if="usePage().props.auth.user.id === request.user_id">
              <div class="card-header bg-success">
                <h3 class="card-title"><i class="fas fa-upload"></i> Upload chứng từ</h3>
              </div>
              <div class="card-body">
                <div class="alert alert-info">
                  <i class="fas fa-info-circle"></i>
                  Vui lòng upload hóa đơn, biên lai và các chứng từ liên quan
                </div>

                <div class="form-group">
                  <label>Loại tài liệu</label>
                  <select v-model="uploadForm.type" class="form-control">
                    <option value="invoice">Hóa đơn</option>
                    <option value="receipt">Biên lai</option>
                    <option value="contract">Hợp đồng</option>
                    <option value="other">Khác</option>
                  </select>
                </div>

                <div class="form-group">
                  <label>Chọn file (có thể chọn nhiều)</label>
                  <input
                    id="fileInput"
                    type="file"
                    class="form-control-file"
                    @change="handleFileSelect"
                    multiple
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                  />
                  <small class="form-text text-muted">
                    Định dạng: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG. Tối đa 10MB/file
                  </small>
                </div>

                <div v-if="uploadForm.files.length > 0" class="mb-2">
                  <strong>Đã chọn {{ uploadForm.files.length }} file:</strong>
                  <ul class="mb-0">
                    <li v-for="(file, index) in uploadForm.files" :key="index">
                      {{ file.name }} ({{ (file.size / 1024).toFixed(2) }} KB)
                    </li>
                  </ul>
                </div>

                <button
                  @click="uploadDocuments"
                  class="btn btn-success btn-block"
                  :disabled="uploadForm.processing || uploadForm.files.length === 0"
                >
                  <i class="fas fa-upload"></i>
                  {{ uploadForm.processing ? 'Đang tải lên...' : 'Upload' }}
                </button>
              </div>
            </div>

            <!-- Existing Documents -->
            <div class="card" v-if="request.documents && request.documents.length > 0">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-paperclip"></i> Tài liệu đã upload</h3>
              </div>
              <div class="card-body p-0">
                <table class="table table-sm mb-0">
                  <thead>
                    <tr>
                      <th style="width: 100px">Loại</th>
                      <th>Tài liệu</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="doc in request.documents" :key="doc.id">
                      <td class="text-center">
                        <span class="badge badge-info">{{ getDocumentTypeLabel(doc.type) }}</span>
                      </td>
                      <td>
                        <a :href="route('documents.show', doc.id)" target="_blank" class="text-primary">
                          <i class="fas fa-download"></i>
                          {{
                            doc.original_name.length > 20 ? doc.original_name.slice(0, 20) + '...' : doc.original_name
                          }}
                        </a>
                        <small class="text-muted d-block">{{ doc.size_formatted }}</small>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.category-indicator {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: 2px solid #fff;
  box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1);
}
</style>
