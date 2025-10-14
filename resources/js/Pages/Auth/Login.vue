<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'

defineProps({
  canResetPassword: Boolean,
  status: String
})

const form = useForm({
  email: '',
  password: '',
  remember: false,
  'g-recaptcha-response': ''
})

const recaptchaLoaded = ref(false)
const recaptchaWidgetId = ref(null)

// Load reCAPTCHA script
onMounted(() => {
  if (!window.grecaptcha) {
    const script = document.createElement('script')
    script.src = 'https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit'
    script.async = true
    script.defer = true
    document.head.appendChild(script)

    window.onRecaptchaLoad = () => {
      recaptchaLoaded.value = true
      renderRecaptcha()
    }
  } else {
    recaptchaLoaded.value = true
    renderRecaptcha()
  }
})

const renderRecaptcha = () => {
  if (window.grecaptcha && document.getElementById('recaptcha-container')) {
    recaptchaWidgetId.value = window.grecaptcha.render('recaptcha-container', {
      sitekey: import.meta.env.VITE_RECAPTCHA_SITE_KEY || '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI',
      callback: onRecaptchaSuccess,
      'expired-callback': onRecaptchaExpired
    })
  }
}

const onRecaptchaSuccess = (token) => {
  form['g-recaptcha-response'] = token
}

const onRecaptchaExpired = () => {
  form['g-recaptcha-response'] = ''
}

const submit = () => {
  if (!form['g-recaptcha-response']) {
    alert('Vui lòng xác nhận bạn không phải là robot!')
    return
  }

  form.post(route('login'), {
    onFinish: () => {
      form.reset('password')
      // Reset reCAPTCHA
      if (window.grecaptcha && recaptchaWidgetId.value !== null) {
        window.grecaptcha.reset(recaptchaWidgetId.value)
      }
    }
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

            <!-- reCAPTCHA -->
            <div class="form-group d-flex justify-content-center">
              <div id="recaptcha-container"></div>
            </div>
            <div v-if="form.errors['g-recaptcha-response']" class="text-danger small text-center mb-3">
              {{ form.errors['g-recaptcha-response'] }}
            </div>

            <div class="row">
              <div class="col-12">
                <button
                  type="submit"
                  class="btn btn-primary btn-block"
                  :disabled="form.processing || !form['g-recaptcha-response']"
                >
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
