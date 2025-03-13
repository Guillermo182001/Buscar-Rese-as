// Configuración centralizada para las APIs
export const API_CONFIG = {
    BASE_URL: process.env.VUE_APP_API_BASE_URL || "http://127.0.0.1:8000/api",
    TIMEOUT: 10000, // 10 segundos
    RETRY_ATTEMPTS: 2,
    ENDPOINTS: {
      TEST: "/test",
      GOOGLE_REVIEWS: "/reviews/google",
      FACEBOOK_REVIEWS: "/reviews/facebook",
      TEST_APIS: "/test-apis",
    },
  }
  
  // Cliente Axios configurado
  import axios from "axios"
  
  const apiClient = axios.create({
    baseURL: API_CONFIG.BASE_URL,
    timeout: API_CONFIG.TIMEOUT,
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
  })
  
  // Interceptor para manejar errores globalmente
  apiClient.interceptors.response.use(
    (response) => response,
    async (error) => {
      const originalRequest = error.config
  
      // Reintentar la solicitud si falla (hasta RETRY_ATTEMPTS veces)
      if (
        error.response &&
        error.response.status >= 500 &&
        !originalRequest._retry &&
        originalRequest._retryCount < API_CONFIG.RETRY_ATTEMPTS
      ) {
        originalRequest._retry = true
        originalRequest._retryCount = (originalRequest._retryCount || 0) + 1
  
        // Esperar antes de reintentar (backoff exponencial)
        await new Promise((resolve) => setTimeout(resolve, 1000 * originalRequest._retryCount))
  
        return apiClient(originalRequest)
      }
  
      return Promise.reject(error)
    },
  )
  
  export default apiClient
  
  