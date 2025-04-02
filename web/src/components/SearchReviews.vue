<script>
import apiClient from '@/config/api';
import { API_CONFIG } from '@/config/api';
import cacheService from '@/services/cacheService';
import ReviewCarousel from './ReviewCarousel.vue';
import PaginationControls from './PaginationControls.vue';

export default {
  name: 'SearchReviews',
  components: {
    ReviewCarousel,
    PaginationControls
  },
  data() {
    return {
      googleReviews: [],
      facebookReviews: [],
      googlePlaceId: '', 
      facebookPageId: '',
      loading: false,
      errorMessage: '',
      // Paginación
      currentPage: 1,
      itemsPerPage: 5,
      // Estado de caché
      cacheStatus: {
        google: false,
        facebook: false
      },
      // Pestaña activa
      activeTab: 'google'
    };
  },
  computed: {
    /**
     * Reseñas de Google paginadas
     */
    paginatedGoogleReviews() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.googleReviews.slice(start, end);
    },
    
    /**
     * Total de páginas para las reseñas de Google
     */
    totalGooglePages() {
      return Math.ceil(this.googleReviews.length / this.itemsPerPage);
    },

    /**
     * Todas las reseñas combinadas
     */
    allReviews() {
      return [...this.googleReviews, ...this.facebookReviews];
    },

    /**
     * Calificación promedio de Google
     */
    googleRating() {
      return this.calculateAverage(this.googleReviews);
    },

    /**
     * Calificación promedio de Facebook
     */
    facebookRating() {
      return this.calculateAverage(this.facebookReviews);
    },

    /**
     * Calificación promedio general
     */
    overallRating() {
      return this.calculateAverage(this.allReviews);
    }
  },
  methods: {
    /**
     * Cambia la pestaña activa
     */
    changeTab(tab) {
      this.activeTab = tab;
      this.errorMessage = '';
    },

    /**
     * Calcula el promedio de calificaciones
     */
    calculateAverage(reviews) {
      if (!reviews || reviews.length === 0) return '0.0';
      
      const sum = reviews.reduce((total, review) => {
        return total + (review.rating || 0);
      }, 0);
      
      return (sum / reviews.length).toFixed(1);
    },

    /**
     * Cambia la página actual
     */
    handlePageChange(page) {
      this.currentPage = page;
      // Desplazarse al inicio de las reseñas
      this.$nextTick(() => {
        const reviewsElement = document.querySelector('.reviews-container');
        if (reviewsElement) {
          reviewsElement.scrollIntoView({ behavior: 'smooth' });
        }
      });
    },
    
    /**
     * Busca reseñas de Google con caché y paginación
     */
    async fetchGoogleReviews() {
      if (!this.googlePlaceId) {
        this.errorMessage = 'Por favor ingresa un Google Place ID';
        return;
      }
      
      this.loading = true;
      this.errorMessage = '';
      this.currentPage = 1;
      
      // Verificar caché primero
      const cacheKey = `google_reviews_${this.googlePlaceId}`;
      const cachedData = cacheService.get(cacheKey);
      
      if (cachedData) {
        this.googleReviews = cachedData;
        this.cacheStatus.google = true;
        this.loading = false;
        return;
      }
      
      try {
        const response = await apiClient.get(API_CONFIG.ENDPOINTS.GOOGLE_REVIEWS, {
          params: { place_id: this.googlePlaceId }
        });
        
        this.googleReviews = response.data || [];
        this.cacheStatus.google = false;
        
        // Guardar en caché si hay resultados
        if (this.googleReviews.length > 0) {
          cacheService.set(cacheKey, this.googleReviews);
        }
        
        if (this.googleReviews.length === 0) {
          this.errorMessage = 'No se encontraron reseñas para este Place ID.';
        }
      } catch (error) {
        console.error('Error al obtener reseñas de Google:', error);
        this.errorMessage = 'Error al obtener las reseñas: ' + 
          (error.response?.data?.message || error.message);
      } finally {
        this.loading = false;
      }
    },
    
    /**
     * Busca reseñas de Facebook con caché
     */
    async fetchFacebookReviews() {
      if (!this.facebookPageId) {
        this.errorMessage = 'Por favor ingresa un Facebook Page ID';
        return;
      }
      
      this.loading = true;
      this.errorMessage = '';
      
      // Verificar caché primero
      const cacheKey = `facebook_reviews_${this.facebookPageId}`;
      const cachedData = cacheService.get(cacheKey);
      
      if (cachedData) {
        this.facebookReviews = cachedData;
        this.cacheStatus.facebook = true;
        this.loading = false;
        return;
      }
      
      try {
        const response = await apiClient.get(API_CONFIG.ENDPOINTS.FACEBOOK_REVIEWS, {
          params: { page_id: this.facebookPageId }
        });
        
        this.facebookReviews = response.data || [];
        this.cacheStatus.facebook = false;
        
        // Guardar en caché si hay resultados
        if (this.facebookReviews.length > 0) {
          cacheService.set(cacheKey, this.facebookReviews);
        }
        
        if (this.facebookReviews.length === 0) {
          this.errorMessage = 'No se encontraron reseñas para este Page ID.';
        }
      } catch (error) {
        console.error('Error al obtener reseñas de Facebook:', error);
        this.errorMessage = 'Error al obtener las reseñas de Facebook: ' + 
          (error.response?.data?.message || error.message);
      } finally {
        this.loading = false;
      }
    },
    
    /**
     * Limpia la caché para una búsqueda específica
     */
    clearCache(type) {
      if (type === 'google' && this.googlePlaceId) {
        cacheService.delete(`google_reviews_${this.googlePlaceId}`);
        this.cacheStatus.google = false;
        this.fetchGoogleReviews();
      } else if (type === 'facebook' && this.facebookPageId) {
        cacheService.delete(`facebook_reviews_${this.facebookPageId}`);
        this.cacheStatus.facebook = false;
        this.fetchFacebookReviews();
      }
    }
  }
}
</script>

<template>
  <div class="reviews-app">
    <!-- Encabezado con pestañas de plataformas -->
    <header class="review-header">
      <div class="container">
        <h1 class="title">Buscar Reseñas</h1>
        <p class="subtitle">Encuentra reseñas de tus lugares y negocios favoritos</p>
        
        <div class="platform-tabs">
          <button 
            class="platform-tab" 
            :class="{ active: activeTab === 'google' }"
            @click="changeTab('google')"
          >
            <img src="/img/google-logo.svg" alt="Google" class="platform-logo" />
            <span>Google</span>
          </button>
          <button 
            class="platform-tab" 
            :class="{ active: activeTab === 'facebook' }"
            @click="changeTab('facebook')"
          >
            <img src="/img/facebook-logo.svg" alt="Facebook" class="platform-logo" />
            <span>Facebook</span>
          </button>
          <button 
            class="platform-tab" 
            :class="{ active: activeTab === 'all' }"
            @click="changeTab('all')"
          >
            <span>Todas las reseñas</span>
          </button>
        </div>
      </div>
    </header>

    <div class="reviews-container">
      <!-- Estadísticas de calificaciones -->
      <div class="ratings-summary" v-if="googleReviews.length || facebookReviews.length">
        <div class="stats-container">
          <div class="stat-item all" :class="{ active: activeTab === 'all' }" @click="changeTab('all')">
            <span class="stat-label">Todas las reseñas</span>
            <span class="stat-value">{{ overallRating }}</span>
          </div>
          
          <div class="stat-item google" :class="{ active: activeTab === 'google' }" @click="changeTab('google')">
            <img src="/img/google-logo.svg" alt="Google" class="platform-logo" />
            <span class="stat-value">{{ googleRating }}</span>
          </div>
          
          <div class="stat-item facebook" :class="{ active: activeTab === 'facebook' }" @click="changeTab('facebook')">
            <img src="/img/facebook-logo.svg" alt="Facebook" class="platform-logo" />
            <span class="stat-value">{{ facebookRating }}</span>
          </div>
        </div>
        
        <div class="overall-rating">
          <h3>Calificación general</h3>
          <div class="rating-display">
            <span class="rating-number">{{ overallRating }}</span>
            <div class="stars">
              <span v-for="i in 5" :key="i" class="star" :class="{ 'filled': i <= Math.round(overallRating) }">★</span>
            </div>
            <span class="review-count">({{ allReviews.length }})</span>
          </div>
        </div>
      </div>

      <!-- Sección de Google -->
      <div v-if="activeTab === 'google' || activeTab === 'all'" class="search-section">
        <h2 class="section-title">Buscar reseñas de Google</h2>
        <div class="search-box">
          <div class="input-group">
            <div class="input-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
            </div>
            <input 
              v-model="googlePlaceId" 
              placeholder="Introduce Google Place ID"
              class="search-input"
              :disabled="loading"
            />
          </div>
          <button 
            @click="fetchGoogleReviews"
            class="search-button google"
            :disabled="loading || !googlePlaceId"
          >
            <span v-if="loading && activeTab === 'google'">
              <svg class="spinner" viewBox="0 0 50 50">
                <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
              </svg>
            </span>
            <span v-else>Buscar</span>
          </button>
        </div>

        <div v-if="cacheStatus.google" class="cache-notice">
          <span>Mostrando resultados almacenados en caché</span>
          <button @click="clearCache('google')" class="refresh-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M23 4v6h-6"></path>
              <path d="M1 20v-6h6"></path>
              <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10"></path>
              <path d="M20.49 15a9 9 0 0 1-14.85 3.36L1 14"></path>
            </svg>
            Actualizar
          </button>
        </div>

        <p v-if="errorMessage && activeTab === 'google'" class="error-message">{{ errorMessage }}</p>

        <div v-if="loading && activeTab === 'google'" class="loading-container">
          <div class="spinner-large"></div>
          <p>Cargando reseñas...</p>
        </div>
        
        <div v-else-if="googleReviews.length" class="reviews-results">
          <div>
            <ReviewCarousel 
              :reviews="paginatedGoogleReviews" 
            />
            
            <PaginationControls
              :current-page="currentPage"
              :total-pages="totalGooglePages"
              @page-change="handlePageChange"
            />
          </div>
        </div>
        
        <p v-else-if="!loading && activeTab === 'google'" class="no-reviews">
          No hay reseñas de Google disponibles.
        </p>
      </div>

      <!-- Sección de Facebook -->
      <div v-if="activeTab === 'facebook' || activeTab === 'all'" class="search-section">
        <h2 class="section-title">Buscar reseñas de Facebook</h2>
        <div class="search-box">
          <div class="input-group">
            <div class="input-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
            </div>
            <input 
              v-model="facebookPageId" 
              placeholder="Introduce Facebook Page ID"
              class="search-input"
              :disabled="loading"
            />
          </div>
          <button 
            @click="fetchFacebookReviews"
            class="search-button facebook"
            :disabled="loading || !facebookPageId"
          >
            <span v-if="loading && activeTab === 'facebook'">
              <svg class="spinner" viewBox="0 0 50 50">
                <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
              </svg>
            </span>
            <span v-else>Buscar</span>
          </button>
        </div>

        <div v-if="cacheStatus.facebook" class="cache-notice">
          <span>Mostrando resultados almacenados en caché</span>
          <button @click="clearCache('facebook')" class="refresh-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M23 4v6h-6"></path>
              <path d="M1 20v-6h6"></path>
              <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10"></path>
              <path d="M20.49 15a9 9 0 0 1-14.85 3.36L1 14"></path>
            </svg>
            Actualizar
          </button>
        </div>

        <p v-if="errorMessage && activeTab === 'facebook'" class="error-message">{{ errorMessage }}</p>

        <div v-if="loading && activeTab === 'facebook'" class="loading-container">
          <div class="spinner-large"></div>
          <p>Cargando reseñas...</p>
        </div>
        
        <div v-else-if="facebookReviews.length" class="facebook-reviews">
          <div v-for="(review, index) in facebookReviews" 
               :key="index"
               class="review-card">
            <div class="review-header">
              <div class="reviewer-info">
                <img 
                  :src="review.reviewer_picture || '/img/default-avatar.png'" 
                  :alt="review.reviewer_name"
                  class="reviewer-avatar"
                />
                <div>
                  <h3 class="reviewer-name">{{ review.reviewer_name }}</h3>
                  <div class="review-meta">
                    <div class="rating">
                      <div class="stars">
                        <span v-for="i in 5" :key="i" class="star" :class="{ 'filled': i <= review.rating }">★</span>
                      </div>
                      <span class="rating-value">{{ review.rating || 'N/A' }}/5</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="platform-badge facebook">
                <img src="/img/facebook-logo.svg" alt="Facebook" class="platform-logo small" />
              </div>
            </div>
            <div class="review-content">
              <p>{{ review.review_text || 'Sin texto' }}</p>
            </div>
          </div>
        </div>
        
        <p v-else-if="!loading && activeTab === 'facebook'" class="no-reviews">
          No hay reseñas de Facebook disponibles.
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Estilos generales */
.reviews-app {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
  color: #333;
  background-color: #f5f7fa;
}

.reviews-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

/* Encabezado y pestañas */
.review-header {
  background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
  padding: 2rem 0;
  border-bottom: 1px solid #e0e0e0;
  margin-bottom: 2rem;
}

.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 0 1rem;
  text-align: center;
}

.title {
  font-size: 2.5rem;
  color: #333;
  margin-bottom: 0.5rem;
}

.subtitle {
  font-size: 1.2rem;
  color: #666;
  margin-bottom: 2rem;
}

.platform-tabs {
  display: flex;
  justify-content: center;
  gap: 1rem;
  margin-top: 2rem;
  border-bottom: 1px solid #e0e0e0;
  padding-bottom: 0.5rem;
}

.platform-tab {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 2rem;
  cursor: pointer;
  transition: all 0.2s ease;
  font-weight: 500;
}

.platform-tab:hover {
  background: #f5f5f5;
  transform: translateY(-2px);
}

.platform-tab.active {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.platform-tab:nth-child(1).active {
  border-color: #4285f4;
  color: #4285f4;
}

.platform-tab:nth-child(2).active {
  border-color: #1877f2;
  color: #1877f2;
}

.platform-tab:nth-child(3).active {
  border-color: #333;
  color: #333;
}

/* Logos de plataformas */
.platform-logo {
  width: 24px;
  height: 24px;
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
}

.platform-logo.small {
  width: 16px;
  height: 16px;
}

/* Estadísticas de calificaciones */
.ratings-summary {
  background-color: #f9fafb;
  border-radius: 0.75rem;
  margin-bottom: 2rem;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.stats-container {
  display: flex;
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 1rem;
  margin-bottom: 1rem;
  overflow-x: auto;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 2rem;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
  margin-right: 0.5rem;
}

.stat-item:hover {
  background-color: #f3f4f6;
}

.stat-item.active {
  background-color: white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.stat-item.all.active {
  border: 1px solid #e5e7eb;
}

.stat-item.google.active {
  border: 1px solid #4285f4;
  color: #4285f4;
}

.stat-item.facebook.active {
  border: 1px solid #1877f2;
  color: #1877f2;
}

.stat-label {
  font-weight: 500;
}

.stat-value {
  font-weight: 600;
}

.overall-rating {
  text-align: center;
  padding: 1rem 0;
}

.overall-rating h3 {
  font-size: 1.25rem;
  color: #374151;
  margin-bottom: 0.5rem;
}

.rating-display {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.rating-number {
  font-size: 2rem;
  font-weight: 700;
  color: #111827;
}

.stars {
  display: flex;
}

.star {
  font-size: 1.5rem;
  color: #e5e7eb;
}

.star.filled {
  color: #fbbf24;
}

.review-count {
  color: #6b7280;
  font-size: 0.875rem;
}

/* Secciones de búsqueda */
.search-section {
  margin-bottom: 40px;
  background-color: #fff;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.section-title {
  font-size: 1.5rem;
  color: #333;
  margin-bottom: 1.5rem;
  text-align: center;
  border-bottom: 2px solid #f0f0f0;
  padding-bottom: 0.75rem;
}

/* Caja de búsqueda */
.search-box {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.input-group {
  position: relative;
  flex: 1;
}

.input-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
}

.search-input {
  width: 100%;
  padding: 12px 12px 12px 2.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 6px;
  font-size: 1rem;
  transition: all 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #4285f4;
  box-shadow: 0 0 0 3px rgba(66, 133, 244, 0.2);
}

.search-input:disabled {
  background-color: #f9f9f9;
  cursor: not-allowed;
}

.search-button {
  padding: 0 1.5rem;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  color: white;
  cursor: pointer;
  transition: all 0.2s;
  min-width: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.search-button.google {
  background-color: #4285f4;
}

.search-button.facebook {
  background-color: #1877f2;
}

.search-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.search-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Mensajes y notificaciones */
.error-message {
  margin-top: 0.75rem;
  color: #dc2626;
  font-size: 0.875rem;
  background-color: #fee2e2;
  padding: 0.75rem 1rem;
  border-radius: 0.375rem;
  border-left: 4px solid #dc2626;
}

.cache-notice {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 0.75rem;
  padding: 0.5rem 1rem;
  background-color: #e6f7ff;
  border: 1px solid #91d5ff;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  color: #0050b3;
}

.refresh-button {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  background-color: transparent;
  border: 1px solid #1890ff;
  color: #1890ff;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.75rem;
  cursor: pointer;
  transition: all 0.2s;
}

.refresh-button:hover {
  background-color: #1890ff;
  color: white;
}

.no-reviews {
  text-align: center;
  color: #666;
  padding: 30px;
  background-color: #f5f5f5;
  border-radius: 6px;
  margin-top: 20px;
}

/* Tarjetas de reseñas */
.review-cards {
  display: grid;
  gap: 20px;
  margin-top: 20px;
}

.review-card {
  background-color: white;
  border-radius: 0.75rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
  transition: transform 0.2s, box-shadow 0.2s;
  margin-bottom: 1rem;
  border: 1px solid #f0f0f0;
}

.review-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.05), 0 4px 6px rgba(0, 0, 0, 0.05);
}

.review-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.reviewer-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.reviewer-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #f0f0f0;
}

.reviewer-name {
  font-size: 1rem;
  font-weight: 600;
  margin: 0 0 0.25rem 0;
  color: #333;
}

.review-meta {
  display: flex;
  align-items: center;
  gap: 1rem;
  font-size: 0.875rem;
  color: #666;
}

.rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.rating .stars {
  display: flex;
}

.rating .star {
  font-size: 1rem;
}

.rating-value {
  font-weight: 600;
}

.review-time {
  color: #9ca3af;
  font-size: 0.75rem;
}

.platform-badge {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 50%;
}

.platform-badge.google {
  background-color: #f8fafc;
  border: 1px solid #e2e8f0;
}

.platform-badge.facebook {
  background-color: #f0f4ff;
  border: 1px solid #dbeafe;
}

.review-content {
  color: #4b5563;
  line-height: 1.6;
}

.review-content p {
  margin: 0;
}

/* Cargando */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  color: #6b7280;
}

.spinner-large {
  border: 4px solid rgba(0, 0, 0, 0.1);
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border-left-color: #4285f4;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

.spinner {
  animation: rotate 2s linear infinite;
  width: 20px;
  height: 20px;
}

.path {
  stroke: white;
  stroke-linecap: round;
  animation: dash 1.5s ease-in-out infinite;
}

@keyframes rotate {
  100% {
    transform: rotate(360deg);
  }
}

@keyframes dash {
  0% {
    stroke-dasharray: 1, 150;
    stroke-dashoffset: 0;
  }
  50% {
    stroke-dasharray: 90, 150;
    stroke-dashoffset: -35;
  }
  100% {
    stroke-dasharray: 90, 150;
    stroke-dashoffset: -124;
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
  .title {
    font-size: 2rem;
  }
  
  .subtitle {
    font-size: 1rem;
  }
  
  .platform-tabs {
    flex-direction: column;
    align-items: center;
  }
  
  .platform-tab {
    width: 100%;
    justify-content: center;
  }
  
  .stats-container {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .stat-item {
    width: 100%;
    justify-content: space-between;
  }
}

@media (max-width: 640px) {
  .search-box {
    flex-direction: column;
  }
  
  .search-button {
    width: 100%;
    padding: 0.75rem;
  }
  
  .reviewer-info {
    gap: 0.5rem;
  }
  
  .reviewer-avatar {
    width: 40px;
    height: 40px;
  }
  
  .review-meta {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.25rem;
  }
}
</style>

