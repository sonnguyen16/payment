<script setup>
import { Head, useForm } from '@inertiajs/vue3'

defineProps({
  canResetPassword: Boolean,
  status: String
})

const form = useForm({
  email: '',
  password: '',
  remember: false
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password')
  })
}
</script>

<template>
  <Head title="Đăng nhập" />

  <div class="login-page">
    <div class="login-box">
      <div class="card">
        <div class="card-body login-card-body">
          <div class="login-logo flex justify-center">
            <img src="/logo.png" alt="Logo" class="w-24 h-24" />
          </div>
          <div v-if="status" class="alert alert-success">
            {{ status }}
          </div>

          <form @submit.prevent="submit">
            <div class="form-group">
              <div class="input-group">
                <input
                  v-model="form.email"
                  type="email"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.email }"
                  placeholder="Email"
                  required
                  autofocus
                />
                <div class="input-group-append">
                  <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                  </div>
                </div>
              </div>
              <div v-if="form.errors.email" class="text-danger small mt-1">
                {{ form.errors.email }}
              </div>
            </div>

            <div class="form-group">
              <div class="input-group">
                <input
                  v-model="form.password"
                  type="password"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.password }"
                  placeholder="Mật khẩu"
                  required
                />
                <div class="input-group-append">
                  <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                  </div>
                </div>
              </div>
              <div v-if="form.errors.password" class="text-danger small mt-1">
                {{ form.errors.password }}
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block" :disabled="form.processing">
                  <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
}

.login-box {
  width: 360px;
}

.login-logo {
  font-size: 35px;
  text-align: center;
  margin-bottom: 25px;
  font-weight: 300;
  color: #fff;
}

.login-logo b {
  font-weight: 600;
}

.card {
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
  border: none;
}

.login-card-body {
  padding: 20px;
  border-radius: 10px;
}

.login-box-msg {
  margin: 0;
  padding: 0 20px 20px;
  text-align: center;
}

.input-group {
  display: flex;
  width: 100%;
}

.input-group .form-control {
  flex: 1;
  border-right: 0;
}

.input-group-append {
  display: flex;
}

.input-group-text {
  background-color: transparent;
  border-left: 0;
  display: flex;
  align-items: center;
  padding: 0.375rem 0.75rem;
}

.form-control:focus {
  border-color: #80bdff;
  box-shadow: none;
}

.form-control:focus + .input-group-append .input-group-text {
  border-color: #80bdff;
}
</style>
