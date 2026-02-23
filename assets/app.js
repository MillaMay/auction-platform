import { createApp } from 'vue'
import 'bootstrap/dist/css/bootstrap.min.css'
import './styles/app.css'
import LoginForm from './vue/LoginForm.vue'
import RegisterForm from './vue/RegisterForm.vue'

console.log('📦 App.js loading...')

const app = createApp({})
app.component('login-form', LoginForm)
app.component('register-form', RegisterForm)

console.log('🔧 Components registered:', { LoginForm, RegisterForm })

app.mount('#app')
console.log('✅ Vue app mounted!')
