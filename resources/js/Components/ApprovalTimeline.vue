<script setup>
defineProps({
  histories: Array
})

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

const formatChangeValue = (field, value) => {
  if (!value) return 'Trống'

  // Format dates
  if (field === 'expected_date') {
    return new Date(value).toLocaleDateString('vi-VN')
  }

  // Format money
  if (field === 'amount') {
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND'
    }).format(value)
  }

  // Format type
  if (field === 'type') {
    const types = {
      advance: 'Tạm ứng',
      payment_proposal: 'Đề xuất thanh toán',
      other_expense: 'Chi phí khác'
    }
    return types[value] || value
  }

  // Format priority
  if (field === 'priority') {
    return value === 'urgent' ? 'Gấp' : 'Bình thường'
  }

  return value
}

const getFieldLabel = (field) => {
  const labels = {
    type: 'Loại phiếu',
    amount: 'Số tiền',
    description: 'Mô tả',
    reason: 'Lý do',
    expected_date: 'Ngày dự kiến',
    priority: 'Ưu tiên',
    update_reason: 'Lý do sửa',
    category_id: 'Danh mục',
    project_id: 'Dự án',
    details: 'Chi tiết thanh toán'
  }
  return labels[field] || field
}

const formatMoney = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const getActionIcon = (action) => {
  const icons = {
    created: 'fa-plus-circle',
    submitted: 'fa-paper-plane',
    approved: 'fa-check-circle',
    rejected: 'fa-times-circle',
    cancelled: 'fa-ban',
    updated: 'fa-edit',
    deleted: 'fa-trash',
    payment_processed: 'fa-money-bill-wave'
  }
  // Default to 'updated' if no action specified (for update_histories)
  return icons[action] || icons['updated']
}

const getActionColor = (action) => {
  const colors = {
    created: 'text-info',
    submitted: 'text-primary',
    approved: 'text-success',
    rejected: 'text-danger',
    cancelled: 'text-warning',
    updated: 'text-warning',
    deleted: 'text-dark',
    payment_processed: 'text-success'
  }
  // Default to 'updated' color if no action specified
  return colors[action] || colors['updated']
}

const getActionLabel = (action) => {
  const labels = {
    created: 'Tạo phiếu',
    submitted: 'Gửi duyệt',
    approved: 'Phê duyệt',
    rejected: 'Từ chối',
    cancelled: 'Hủy',
    updated: 'Chỉnh sửa',
    deleted: 'Xóa',
    payment_processed: 'Đã thanh toán'
  }
  // Default to 'Chỉnh sửa' if no action specified
  return labels[action] || labels['updated']
}
</script>

<template>
  <div class="timeline">
    <div v-for="history in histories" :key="history.id">
      <i class="fas" :class="[getActionIcon(history.action), getActionColor(history.action)]"></i>
      <div class="timeline-item">
        <span class="time">
          <i class="fas fa-clock"></i>
          {{ formatDateTime(history.created_at) }}
        </span>
        <h3 class="timeline-header">
          <strong>{{ history.user.name }}</strong> -
          <span :class="getActionColor(history.action)">{{ getActionLabel(history.action) }}</span>
        </h3>
        <div class="timeline-body" v-if="history.reason">
          <p class="mb-0"><strong>Lý do:</strong> {{ history.reason }}</p>
        </div>
        <div class="timeline-body" v-if="history.changes && Object.keys(history.changes).length > 0">
          <p class="mb-1"><strong>Thay đổi:</strong></p>
          <div v-for="(change, field) in history.changes" :key="field">
            <!-- Nếu là details (chi tiết thanh toán) -->
            <div v-if="field === 'details' && change.details">
              <strong class="text-primary">{{ getFieldLabel(field) }}:</strong>
              <div class="ml-3 mt-2">
                <!-- Các dòng đã thêm -->
                <div v-if="change.details.added && change.details.added.length > 0" class="mb-2 table-responsive">
                  <span class="badge badge-success">Đã thêm {{ change.details.added.length }} dòng</span>
                  <table class="table table-sm table-bordered mt-1">
                    <thead class="bg-success text-white">
                      <tr>
                        <th style="width: 40%">Nội dung</th>
                        <th>Tiền chưa thuế</th>
                        <th>Thuế</th>
                        <th>Tổng</th>
                        <th>Số HĐ</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(detail, idx) in change.details.added" :key="'add-' + idx">
                        <td>{{ detail.description }}</td>
                        <td class="text-right">{{ formatMoney(detail.amount_before_tax) }}</td>
                        <td class="text-right">{{ formatMoney(detail.tax_amount) }}</td>
                        <td class="text-right">{{ formatMoney(detail.total_amount) }}</td>
                        <td>{{ detail.invoice_number || '-' }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!-- Các dòng đã xóa -->
                <div v-if="change.details.removed && change.details.removed.length > 0" class="mb-2 table-responsive">
                  <span class="badge badge-danger">Đã xóa {{ change.details.removed.length }} dòng</span>
                  <table class="table table-sm table-bordered mt-1">
                    <thead class="bg-danger text-white">
                      <tr>
                        <th style="width: 40%">Nội dung</th>
                        <th>Tiền chưa thuế</th>
                        <th>Thuế</th>
                        <th>Tổng</th>
                        <th>Số HĐ</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(detail, idx) in change.details.removed" :key="'rem-' + idx">
                        <td>{{ detail.description }}</td>
                        <td class="text-right">{{ formatMoney(detail.amount_before_tax) }}</td>
                        <td class="text-right">{{ formatMoney(detail.tax_amount) }}</td>
                        <td class="text-right">{{ formatMoney(detail.total_amount) }}</td>
                        <td>{{ detail.invoice_number || '-' }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!-- Các dòng đã sửa -->
                <div v-if="change.details.modified && change.details.modified.length > 0" class="mb-2">
                  <span class="badge badge-warning">Đã sửa {{ change.details.modified.length }} dòng</span>
                  <div v-for="(mod, idx) in change.details.modified" :key="'mod-' + idx" class="mt-2">
                    <small class="text-muted">Dòng {{ idx + 1 }}:</small>
                    <table class="table table-sm table-bordered mt-1">
                      <thead class="bg-warning">
                        <tr>
                          <th style="width: 15%">Trạng thái</th>
                          <th style="width: 30%">Nội dung</th>
                          <th>Tiền chưa thuế</th>
                          <th>Thuế</th>
                          <th>Tổng</th>
                          <th>Số HĐ</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr class="bg-light">
                          <td><span class="badge badge-secondary">Cũ</span></td>
                          <td>{{ mod.old.description }}</td>
                          <td class="text-right">{{ formatMoney(mod.old.amount_before_tax) }}</td>
                          <td class="text-right">{{ formatMoney(mod.old.tax_amount) }}</td>
                          <td class="text-right">{{ formatMoney(mod.old.total_amount) }}</td>
                          <td>{{ mod.old.invoice_number || '-' }}</td>
                        </tr>
                        <tr class="bg-success text-white">
                          <td><span class="badge badge-success">Mới</span></td>
                          <td>{{ mod.new.description }}</td>
                          <td class="text-right">{{ formatMoney(mod.new.amount_before_tax) }}</td>
                          <td class="text-right">{{ formatMoney(mod.new.tax_amount) }}</td>
                          <td class="text-right">{{ formatMoney(mod.new.total_amount) }}</td>
                          <td>{{ mod.new.invoice_number || '-' }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- Các trường khác -->
            <div v-else class="mb-1">
              <strong>{{ getFieldLabel(field) }}:</strong>
              <span class="text-danger">{{ change.old }}</span>
              →
              <span class="text-success">{{ change.new }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div>
      <i class="fas fa-clock bg-gray"></i>
    </div>
  </div>
</template>

<style scoped>
.timeline {
  position: relative;
  margin: 0 0 30px 0;
  padding: 0;
  list-style: none;
}

.timeline:before {
  content: '';
  position: absolute;
  top: 0;
  bottom: 0;
  width: 4px;
  background: #dee2e6;
  left: 31px;
  margin: 0;
}

.timeline > div {
  position: relative;
  margin-right: 10px;
  margin-bottom: 15px;
}

.timeline > div > .fas {
  position: absolute;
  left: 18px;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  text-align: center;
  line-height: 30px;
  font-size: 15px;
  background: #fff;
  z-index: 2;
}

.timeline-item {
  margin-left: 60px;
  background: #fff;
  border: 1px solid #dee2e6;
  border-radius: 3px;
  padding: 10px;
}

.timeline-item .time {
  color: #999;
  float: right;
  font-size: 12px;
}

.timeline-item .timeline-header {
  margin: 0;
  color: #555;
  border-bottom: 1px solid #f4f4f4;
  padding-bottom: 5px;
  font-size: 16px;
  line-height: 1.1;
}

.timeline-item .timeline-body {
  padding: 10px 0 0 0;
}
</style>
