<template>
  <div class="login-container">
    <div class="card p-4 shadow mx-auto" style="max-width: 400px;">
      <h3 class="mb-4 text-center">Аукцион: Регистрация</h3>

      <form @submit.prevent="register" autocomplete="off">
        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <div class="mb-3">
          <input
              v-model="form.email"
              type="email"
              class="form-control"
              :class="{ 'is-invalid': touched.email && !form.email }"
              placeholder="Email"
              autocomplete="off"
              @blur="touched.email = true"
          >
          <div v-if="touched.email && !form.email" class="invalid-feedback">
            Email обязателен
          </div>
        </div>

        <div class="mb-3">
          <div class="position-relative">
            <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                class="form-control pe-5"
                :class="{ 'is-invalid': touched.password && passwordError }"
                placeholder="Пароль (минимум 6 символов)"
                autocomplete="new-password"
                @blur="touched.password = true"
            >
            <button
                class="password-toggle"
                type="button"
                @click="showPassword = !showPassword"
                :title="showPassword ? 'Скрыть пароль' : 'Показать пароль'"
            >
              <svg v-if="!showPassword" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
              </svg>
            </button>
          </div>
          <div v-if="touched.password && passwordError" class="invalid-feedback d-block">
            {{ passwordError }}
          </div>
        </div>

        <div class="mb-3">
          <div class="position-relative">
            <input
                v-model="form.repeatPassword"
                :type="showRepeatPassword ? 'text' : 'password'"
                class="form-control pe-5"
                :class="{ 'is-invalid': touched.repeatPassword && repeatPasswordError }"
                placeholder="Повторите пароль"
                @blur="touched.repeatPassword = true"
            >
            <button
                class="password-toggle"
                type="button"
                @click="showRepeatPassword = !showRepeatPassword"
                :title="showRepeatPassword ? 'Скрыть пароль' : 'Показать пароль'"
            >
              <svg v-if="!showRepeatPassword" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
              </svg>
            </button>
          </div>
          <div v-if="touched.repeatPassword && repeatPasswordError" class="invalid-feedback d-block">
            {{ repeatPasswordError }}
          </div>
        </div>

        <div class="mb-4 form-check">
          <input
              v-model="form.agreeTerms"
              type="checkbox"
              class="form-check-input"
              :class="{ 'is-invalid': touched.agreeTerms && !form.agreeTerms }"
              id="agreeTerms"
              @change="touched.agreeTerms = true"
          >
          <label class="form-check-label small" for="agreeTerms">
            Согласен с условиями
          </label>
          <div v-if="touched.agreeTerms && !form.agreeTerms" class="invalid-feedback d-block">
            Необходимо согласиться с условиями
          </div>
        </div>

        <button type="submit" class="btn btn-success w-100" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
          <span v-if="loading">Регистрация...</span>
          <span v-else>Зарегистрироваться</span>
        </button>

        <div class="text-center mt-3">
          <a href="/login" class="text-decoration-none small text-muted">
            Уже есть аккаунт? Войти
          </a>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  mounted() {
    console.log('✅ RegisterForm component mounted!')

    // Принудительная очистка полей формы
    this.form.email = ''
    this.form.password = ''
    this.form.repeatPassword = ''
    this.form.agreeTerms = false

    // Очистка native значений полей
    this.$nextTick(() => {
      const inputs = this.$el.querySelectorAll('input')
      inputs.forEach(input => {
        input.value = ''
        if (input.type === 'checkbox') {
          input.checked = false
        }
      })
    })
  },
  data() {
    return {
      form: {
        email: '',
        password: '',
        repeatPassword: '',
        agreeTerms: false
      },
      touched: {
        email: false,
        password: false,
        repeatPassword: false,
        agreeTerms: false
      },
      showPassword: false,
      showRepeatPassword: false,
      error: null,
      loading: false
    }
  },
  computed: {
    passwordError() {
      if (!this.form.password) {
        return 'Пароль обязателен'
      }
      if (this.form.password.length < 6) {
        return 'Пароль должен быть не менее 6 символов'
      }
      return null
    },
    repeatPasswordError() {
      if (!this.form.repeatPassword) {
        return 'Повторите пароль'
      }
      if (this.form.password !== this.form.repeatPassword) {
        return 'Пароли не совпадают'
      }
      return null
    },
    isValid() {
      return this.form.email &&
          this.form.password.length >= 6 &&
          this.form.password === this.form.repeatPassword &&
          this.form.agreeTerms
    }
  },
  methods: {
    async register() {
      console.log('🚀 Register button clicked!', this.form)
      console.log('isValid:', this.isValid)

      // Пометить все поля как touched для отображения ошибок
      this.touched.email = true
      this.touched.password = true
      this.touched.repeatPassword = true
      this.touched.agreeTerms = true

      if (!this.isValid) {
        this.error = 'Пожалуйста, исправьте ошибки в форме'
        console.log('❌ Form invalid')
        return
      }

      this.loading = true
      this.error = null

      try {
        const response = await fetch('/api/register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            email: this.form.email,
            password: this.form.password
          })
        })

        const data = await response.json()
        console.log('Registration response:', { status: response.status, data })

        if (response.ok) {
          // Автологин после регистрации
          localStorage.setItem('jwt_token', data.token)
          window.location.href = '/auction/lots'
        } else {
          // Обработка разных форматов ошибок
          if (data.details && Array.isArray(data.details)) {
            this.error = data.details.join(', ')
          } else {
            this.error = data.error || data.message || 'Ошибка регистрации'
          }
          console.error('Registration error:', this.error)
        }
      } catch (err) {
        console.error('Registration exception:', err)
        this.error = 'Ошибка сервера: ' + err.message
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.btn-success {
  background: linear-gradient(45deg, #28a745, #20c997);
  border: none;
}

.btn-success:hover {
  transform: translateY(-1px);
  box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
}

.password-toggle {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  background: none;
  color: #6c757d;
  cursor: pointer;
  padding: 4px;
  line-height: 1;
  display: flex;
  align-items: center;
  justify-content: center;
}

.password-toggle:hover {
  color: #495057;
}

.password-toggle:focus {
  outline: none;
}

.password-toggle svg {
  display: block;
}
</style>
