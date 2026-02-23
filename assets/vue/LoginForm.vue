<template>
  <div class="login-container">
    <div class="card p-4 shadow mx-auto" style="max-width: 400px;">
      <h3 class="mb-4 text-center">Аукцион: Вход</h3>
      
      <form @submit.prevent="login">
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
          <div class="position-relative">
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              class="form-control pe-5"
              placeholder="Пароль"
              required
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
        </div>
        
        <button type="submit" class="btn btn-primary w-100" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
          <span v-if="loading">Вход...</span>
          <span v-else>Войти</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: { email: '', password: '' },
      showPassword: false,
      error: null,
      loading: false
    }
  },
  methods: {
    async login() {
      this.loading = true
      this.error = null
      
      try {
        const response = await fetch('/api/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form)
        })

        const data = await response.json()

        if (response.ok) {
          localStorage.setItem('jwt_token', data.token)
          window.location.href = '/auction/lots'
        } else {
          this.error = data.error || 'Неверный email или пароль'
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
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
