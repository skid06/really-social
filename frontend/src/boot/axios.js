// src/boot/axios.js
import { boot } from 'quasar/wrappers'
import axios from 'axios'

// Set base URL for API requests
axios.defaults.baseURL = 'http://localhost:8000'
axios.defaults.withCredentials = true

// Request interceptor for API calls
axios.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  },
)

// Response interceptor for API calls
axios.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config

    // Redirect to login if 401 Unauthorized
    if (error.response && error.response.status === 401 && !originalRequest._retry) {
      localStorage.removeItem('token')
      window.location.href = '/login'
    }

    return Promise.reject(error)
  },
)

export default boot(({ app }) => {
  app.config.globalProperties.$axios = axios
})

export { axios }
