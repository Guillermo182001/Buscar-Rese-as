/**
 * Servicio de caché para almacenar temporalmente datos y reducir llamadas a la API
 */
class CacheService {
    constructor(expirationTime = 15 * 60 * 1000) {
      // 15 minutos por defecto
      this.cache = new Map()
      this.expirationTime = expirationTime
    }
  
    /**
     * Obtiene un valor de la caché
     * @param {string} key - Clave para buscar
     * @returns {any|null} - Valor almacenado o null si no existe o expiró
     */
    get(key) {
      if (!this.cache.has(key)) return null
  
      const cachedItem = this.cache.get(key)
      const now = new Date().getTime()
  
      // Verificar si el item ha expirado
      if (now > cachedItem.expiry) {
        this.delete(key)
        return null
      }
  
      return cachedItem.value
    }
  
    /**
     * Almacena un valor en la caché
     * @param {string} key - Clave para almacenar
     * @param {any} value - Valor a almacenar
     * @param {number} [customExpiry] - Tiempo de expiración personalizado en ms
     */
    set(key, value, customExpiry = null) {
      const expiry = new Date().getTime() + (customExpiry || this.expirationTime)
      this.cache.set(key, { value, expiry })
  
      // Para debugging
      console.log(`Caché: Almacenado "${key}" hasta ${new Date(expiry).toLocaleTimeString()}`)
    }
  
    /**
     * Elimina un valor de la caché
     * @param {string} key - Clave a eliminar
     */
    delete(key) {
      this.cache.delete(key)
    }
  
    /**
     * Limpia toda la caché
     */
    clear() {
      this.cache.clear()
    }
  
    /**
     * Limpia los elementos expirados de la caché
     */
    cleanExpired() {
      const now = new Date().getTime()
      for (const [key, item] of this.cache.entries()) {
        if (now > item.expiry) {
          this.delete(key)
        }
      }
    }
  }
  
  // Exportar una instancia única (singleton)
  export default new CacheService()