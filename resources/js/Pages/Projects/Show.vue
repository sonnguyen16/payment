<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  project: Object,
  payment_requests: Array,
  can: Object
})

const formatMoney = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const project = ref(props.project.data)

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('vi-VN')
}

const deleteProject = async () => {
  const result = await Swal.fire({
    title: 'Xác nhận xóa?',
    text: 'Bạn có chắc chắn muốn xóa dự án này?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Xóa',
    cancelButtonText: 'Hủy'
  })

  if (result.isConfirmed) {
    useForm({}).delete(route('admin.projects.destroy', project.value.id))
  }
}
</script>

<template>
  <Head :title="`Dự án: ${project.name}`" />

  <AdminLayout>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <div class="">
              <Link v-if="can.update" :href="route('admin.projects.edit', project.id)" class="btn btn-warning mr-2">
                <i class="fas fa-edit"></i> Chỉnh sửa
              </Link>
              <!-- <button v-if="can.delete" @click="deleteProject" class="btn btn-danger mr-2">
                <i class="fas fa-trash"></i> Xóa
              </button> -->
              <Link :href="route('admin.projects.index')" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Thông tin dự án</h3>
              </div>
              <div class="card-body">
                <table class="table table-sm">
                  <tr>
                    <th>Mã dự án:</th>
                    <td>{{ project.code }}</td>
                  </tr>
                  <tr>
                    <th>Trạng thái:</th>
                    <td>
                      <span class="badge badge-success" v-if="project.status === 'active'">Đang thực hiện</span>
                      <span class="badge badge-secondary" v-else-if="project.status === 'completed'">Hoàn thành</span>
                      <span class="badge badge-danger" v-else>Đã hủy</span>
                    </td>
                  </tr>
                  <tr>
                    <th>Ngày bắt đầu:</th>
                    <td>
                      {{ formatDate(project.start_date) }}
                    </td>
                  </tr>
                  <tr>
                    <th>Ngày kết thúc:</th>
                    <td>
                      {{ formatDate(project.end_date) }}
                    </td>
                  </tr>
                </table>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Ngân sách</h3>
              </div>
              <div class="card-body">
                <p>
                  <strong>Tổng ngân sách:</strong><br />
                  <span class="text-primary h4">{{ formatMoney(project.budget) }}</span>
                </p>
                <p>
                  <strong>Đã chi:</strong><br />
                  <span class="text-warning h4">{{ formatMoney(project.spent) }}</span>
                </p>
                <p>
                  <strong>Còn lại:</strong><br />
                  <span :class="project.is_over_budget ? 'text-danger h4' : 'text-success h4'">
                    {{ formatMoney(project.remaining_budget) }}
                  </span>
                </p>

                <div class="progress mb-2" style="height: 25px">
                  <div
                    class="progress-bar"
                    :class="
                      project.budget_utilization_percentage > 100
                        ? 'bg-danger'
                        : project.budget_utilization_percentage > 80
                        ? 'bg-warning'
                        : 'bg-success'
                    "
                    :style="`width: ${Math.min(project.budget_utilization_percentage || 0, 100)}%`"
                  >
                    <strong>{{ (project.budget_utilization_percentage || 0).toFixed(1) }}%</strong>
                  </div>
                </div>

                <div class="alert alert-danger" v-if="project.is_over_budget">
                  <i class="fas fa-exclamation-triangle"></i>
                  Dự án đã vượt ngân sách!
                </div>
                <div class="alert alert-warning" v-else-if="(project.budget_utilization_percentage || 0) > 80">
                  <i class="fas fa-exclamation-circle"></i>
                  Ngân sách sắp hết!
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-8">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Mô tả</h3>
              </div>
              <div class="card-body">
                <p>
                  {{ project.description || 'Chưa có mô tả' }}
                </p>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Phiếu đề xuất liên quan</h3>
              </div>
              <div class="card-body p-0">
                <table class="table">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Người tạo</th>
                      <th>Số tiền</th>
                      <th>Trạng thái</th>
                      <th>Ngày tạo</th>
                      <th>Thao tác</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="request in payment_requests" :key="request.id">
                      <td>#{{ request.id }}</td>
                      <td>{{ request.user.name }}</td>
                      <td>
                        {{ formatMoney(request.amount) }}
                      </td>
                      <td>
                        <StatusBadge :status="request.status" />
                      </td>
                      <td>
                        {{ formatDate(request.created_at) }}
                      </td>
                      <td>
                        <Link :href="route('payment-requests.show', request.id)" class="btn btn-sm btn-info">
                          <i class="fas fa-eye"></i>
                        </Link>
                      </td>
                    </tr>
                    <tr v-if="payment_requests.length === 0">
                      <td colspan="6" class="text-center">Chưa có phiếu đề xuất nào</td>
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
