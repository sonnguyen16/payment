<template>
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item block lg:hidden">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button">
            <i class="fas fa-bars"></i>
          </a>
        </li>
        <li class="flex items-center gap-2">
          <img src="/logo.png" alt="Logo" class="w-10 h-10 ms-3" />
          <h2 class="text-lg text-gray-500 hidden lg:block">Chấn Hưng Ltd</h2>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <span class="nav-link">{{ $page.props.auth.user.name }}</span>
        </li>
        <li class="nav-item">
          <Link :href="route('logout')" method="post" as="button" class="nav-link">
            <i class="fas fa-sign-out-alt"></i> Đăng xuất
          </Link>
        </li>
      </ul>
    </nav>

    <!-- Main Sidebar -->
    <aside class="main-sidebar border-gray-500">
      <!-- Sidebar -->
      <div class="sidebar sidebar-light">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
            <li class="nav-item" v-if="!isAdmin">
              <Link :href="route('dashboard')" class="nav-link" :class="{ active: $page.component === 'Dashboard' }">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </Link>
            </li>
            <li class="nav-item" v-if="!isAdmin">
              <Link
                :href="route('payment-requests.index')"
                class="nav-link"
                :class="{ active: $page.component.startsWith('PaymentRequests') }"
              >
                <i class="nav-icon fas fa-file-invoice"></i>
                <p>Phiếu đề xuất</p>
              </Link>
            </li>
            <li class="nav-item" v-if="!isAdmin">
              <Link
                :href="route('expense-vouchers.index')"
                class="nav-link"
                :class="{ active: $page.component.startsWith('ExpenseVouchers') }"
              >
                <i class="nav-icon fas fa-receipt"></i>
                <p>Phiếu chi</p>
              </Link>
            </li>
            <li class="nav-item" v-if="canApprove && !isAdmin">
              <Link
                :href="route('approvals.index')"
                class="nav-link"
                :class="{ active: $page.component === 'Approvals/Index' }"
              >
                <i class="nav-icon fas fa-check-circle"></i>
                <p>Phê duyệt</p>
              </Link>
            </li>
            <li class="nav-item" v-if="canViewReports && !isAdmin">
              <Link
                :href="route('reports.payment-requests')"
                class="nav-link"
                :class="{ active: $page.component.startsWith('Reports') }"
              >
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>Báo cáo</p>
              </Link>
            </li>
            <!-- Admin Menu -->
            <li class="nav-item" v-if="isAdmin">
              <Link
                :href="route('admin.users.index')"
                class="nav-link"
                :class="{ active: $page.component.startsWith('Admin/Users') }"
              >
                <i class="nav-icon fas fa-users"></i>
                <p>Người dùng</p>
              </Link>
            </li>
            <li class="nav-item" v-if="isAdmin">
              <Link
                :href="route('admin.offices.index')"
                class="nav-link"
                :class="{ active: $page.component.startsWith('Admin/Offices') }"
              >
                <i class="nav-icon fas fa-building"></i>
                <p>Văn phòng</p>
              </Link>
            </li>
            <li class="nav-item" v-if="isAdmin">
              <Link
                :href="route('admin.departments.index')"
                class="nav-link"
                :class="{ active: $page.component.startsWith('Admin/Departments') }"
              >
                <i class="nav-icon fas fa-sitemap"></i>
                <p>Bộ phận</p>
              </Link>
            </li>
            <li class="nav-item" v-if="isAdmin">
              <Link
                :href="route('admin.projects.index')"
                class="nav-link"
                :class="{ active: $page.component.startsWith('Projects') }"
              >
                <i class="nav-icon fas fa-project-diagram"></i>
                <p>Dự án</p>
              </Link>
            </li>
            <li class="nav-item" v-if="isAdmin">
              <Link
                :href="route('admin.categories.index')"
                class="nav-link"
                :class="{ active: $page.component.startsWith('Categories') }"
              >
                <i class="nav-icon fas fa-tags"></i>
                <p>Danh mục</p>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
      <slot />
    </div>

    <!-- Footer -->
    <footer class="main-footer">
      <strong class="text-[12px] md:text-md">Copyright &copy; 2025 Chan Hung Corporation. All Rights Reserved</strong>
    </footer>
  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()

const canApprove = computed(() => {
  const user = page.props.auth.user
  return user?.roles?.some((role) => ['department_head', 'accountant', 'ceo'].includes(role.name)) || false
})

const canViewReports = computed(() => {
  const user = page.props.auth.user
  return user?.roles?.some((role) => ['accountant', 'ceo'].includes(role.name)) || false
})

const isAdmin = computed(() => {
  const user = page.props.auth.user
  return user?.roles?.some((role) => role.name === 'admin') || false
})
</script>
