import { createApp } from 'vue'
import 'bootstrap/dist/css/bootstrap.min.css'
import './styles/app.css'
import LoginForm from './vue/LoginForm.vue'
import RegisterForm from './vue/RegisterForm.vue'

const app = createApp({})
app.component('login-form', LoginForm)
app.component('register-form', RegisterForm)
app.mount('#app')
