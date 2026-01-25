<template>
  <div class="login-container">
    <div class="card p-4 shadow mx-auto" style="max-width: 400px;">
      <h3 class="mb-4 text-center">Аукцион: Регистрация</h3>

      <form @submit.prevent="register">
        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <div class="mb-3">
          <input
              v-model="form.email"
              type="email"
              class="form-control"
              placeholder="Email"
              required
          >
        </div>

        <div class="mb-3">
          <input
              v-model="form.password"
              type="password"
              class="form-control"
              placeholder="Пароль"
              required
          >
        </div>

        <div class="mb-3">
          <input
              v-model="form.repeatPassword"
              type="password"
              class="form-control"
              placeholder="Повторите пароль"
              required
          >
        </div>

        <div class="mb-4 form-check">
          <input
              v-model="form.agreeTerms"
              type="checkbox"
              class="form-check-input"
              id="agreeTerms"
              required
          >
          <label class="form-check-label small" for="agreeTerms">
            Согласен с условиями
          </label>
        </div>

        <button type="submit" class="btn btn-success w-100" :disabled="loading || !isValid">
          <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
          <span v-if="loading">Регистрация...</span>
          <span v-else>Зарегистрироваться</span>
        </button>

        <div class="text-center mt-3">
          <router-link to="/login" class="text-decoration-none small text-muted">
            Уже есть аккаунт? Войти
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: {
        email: '',
        password: '',
        repeatPassword: '',
        agreeTerms: false
      },
      error: null,
      loading: false
    }
  },
  computed: {
    isValid() {
      return this.form.email &&
          this.form.password === this.form.repeatPassword &&
          this.form.password.length >= 6 &&
          this.form.agreeTerms
    }
  },
  methods: {
    async register() {
      if (!this.isValid) {
        this.error = 'Заполните все поля правильно'
        return
      }

      this.loading = true
      this.error = null

      try {
        const response = await fetch('/api/register', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form)
        })

        const data = await response.json()

        if (response.ok) {
          // Автологин после регистрации
          localStorage.setItem('jwt_token', data.token)
          window.location.href = '/auction/lots'
        } else {
          this.error = data.message || data.error || 'Ошибка регистрации'
        }
      } catch (err) {
        this.error = 'Ошибка сервера'
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
</style>
