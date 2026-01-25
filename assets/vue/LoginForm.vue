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
          <input 
            v-model="form.password" 
            type="password" 
            class="form-control" 
            placeholder="Пароль"
            required
          >
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
</style>
