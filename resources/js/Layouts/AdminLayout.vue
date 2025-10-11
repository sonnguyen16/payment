<template>
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <!-- <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button">
            <i class="fas fa-bars"></i>
          </a>
        </li> -->
        <li>
          <h2 class="text-lg text-gray-500 ms-3">Quản lý thu chi</h2>
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
      <div class="sidebar">
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
            <li class="nav-item" v-if="!isAdmin">
              <Link
                :href="route('projects.index')"
                class="nav-link"
                :class="{ active: $page.component.startsWith('Projects') }"
              >
                <i class="nav-icon fas fa-project-diagram"></i>
                <p>Dự án</p>
              </Link>
            </li>
            <li class="nav-item" v-if="!isAdmin">
              <Link
                :href="route('categories.index')"
                class="nav-link"
                :class="{ active: $page.component.startsWith('Categories') }"
              >
                <i class="nav-icon fas fa-tags"></i>
                <p>Danh mục</p>
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
      <strong>Quản lý thu chi &copy; 2025</strong>
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

const isAdmin = computed(() => {
  const user = page.props.auth.user
  return user?.roles?.some((role) => role.name === 'admin') || false
})
</script>
