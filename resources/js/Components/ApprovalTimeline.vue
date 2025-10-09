<script setup>
defineProps({
  histories: Array
})

const formatDateTime = (datetime) => {
  return new Date(datetime).toLocaleString('vi-VN')
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
    update_reason: 'Lý do sửa'
  }
  return labels[field] || field
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
  return icons[action] || 'fa-circle'
}

const getActionColor = (action) => {
  const colors = {
    created: 'text-info',
    submitted: 'text-primary',
    approved: 'text-success',
    rejected: 'text-danger',
    cancelled: 'text-warning',
    updated: 'text-secondary',
    deleted: 'text-dark',
    payment_processed: 'text-success'
  }
  return colors[action] || 'text-muted'
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
  return labels[action] || action
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
        <div class="timeline-body" v-if="history.changes && history.changes.length > 0">
          <p class="mb-1"><strong>Thay đổi:</strong></p>
          <ul class="mb-0">
            <li v-for="(change, index) in history.changes" :key="index">
              <strong>{{ getFieldLabel(change.field) }}:</strong>
              <span class="text-muted">{{ formatChangeValue(change.field, change.old_value) }}</span>
              →
              <span class="text-success">{{ formatChangeValue(change.field, change.new_value) }}</span>
            </li>
          </ul>
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
