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
      }
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
    }
  },
  methods: {
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
  <div class="reviews-container">
    <!-- Buscador para Google -->
    <div class="search-section">
      <h2>Buscar reseñas de Google</h2>
      <div class="search-box">
        <input 
          v-model="googlePlaceId" 
          placeholder="Introduce Google Place ID"
          class="search-input"
        />
        <button 
          @click="fetchGoogleReviews"
          class="search-button"
          :disabled="loading"
        >
          {{ loading ? 'Buscando...' : 'Buscar' }}
        </button>
      </div>

      <div v-if="cacheStatus.google" class="cache-notice">
        <span>Mostrando resultados almacenados en caché</span>
        <button @click="clearCache('google')" class="refresh-button">
          Actualizar
        </button>
      </div>

      <p v-if="errorMessage" class="error">{{ errorMessage }}</p>

      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <span>Cargando reseñas...</span>
      </div>
      
      <div v-else-if="googleReviews.length" class="reviews-results">
        <ReviewCarousel 
          :reviews="paginatedGoogleReviews" 
        />
        
        <PaginationControls
          :current-page="currentPage"
          :total-pages="totalGooglePages"
          @page-change="handlePageChange"
        />
      </div>
      
      <p v-else-if="!loading && !errorMessage" class="no-reviews">
        No hay reseñas de Google disponibles.
      </p>
    </div>

    <!-- Buscador para Facebook -->
    <div class="search-section">
      <h2>Buscar reseñas de Facebook</h2>
      <div class="search-box">
        <input 
          v-model="facebookPageId" 
          placeholder="Introduce Facebook Page ID"
          class="search-input"
        />
        <button 
          @click="fetchFacebookReviews"
          class="search-button"
          :disabled="loading"
        >
          {{ loading ? 'Buscando...' : 'Buscar' }}
        </button>
      </div>

      <div v-if="cacheStatus.facebook" class="cache-notice">
        <span>Mostrando resultados almacenados en caché</span>
        <button @click="clearCache('facebook')" class="refresh-button">
          Actualizar
        </button>
      </div>

      <div v-if="facebookReviews.length" class="facebook-reviews">
        <div v-for="(review, index) in facebookReviews" 
             :key="index"
             class="review-card">
          <div class="reviewer-info">
            <img 
              v-if="review.reviewer_picture" 
              :src="review.reviewer_picture" 
              :alt="review.reviewer_name"
              class="reviewer-photo"
            />
            <strong>{{ review.reviewer_name }}</strong>
          </div>
          <div class="rating">
            Rating: {{ review.rating ?? 'N/A' }}/5
          </div>
          <p class="review-text">{{ review.review_text ?? 'Sin texto' }}</p>
        </div>
      </div>
      <p v-else-if="!loading && !errorMessage" class="no-reviews">
        No hay reseñas de Facebook disponibles.
      </p>
    </div>
  </div>
</template>

<style scoped>
.reviews-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.search-section {
  margin-bottom: 40px;
  background-color: #f9f9f9;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

h2 {
  font-size: 1.5rem;
  color: #333;
  margin-bottom: 1rem;
  border-bottom: 2px solid #eaeaea;
  padding-bottom: 10px;
}

.search-box {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.search-input {
  flex: 1;
  padding: 12px;
  border: 2px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.search-input:focus {
  border-color: #007bff;
  outline: none;
  box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.search-button {
  padding: 10px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s;
  font-weight: 600;
}

.search-button:hover {
  background-color: #0056b3;
}

.search-button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

.error {
  color: #dc3545;
  padding: 10px;
  border-radius: 4px;
  margin: 10px 0;
  background-color: rgba(220,53,69,0.1);
  border-left: 4px solid #dc3545;
}

.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 30px;
  color: #666;
}

.spinner {
  border: 4px solid rgba(0, 0, 0, 0.1);
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border-left-color: #007bff;
  animation: spin 1s linear infinite;
  margin-bottom: 10px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.no-reviews {
  text-align: center;
  color: #666;
  padding: 20px;
  background-color: #f5f5f5;
  border-radius: 6px;
}

.facebook-reviews {
  display: grid;
  gap: 20px;
  margin-top: 20px;
}

.review-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 20px;
  background-color: #fff;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.review-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.reviewer-info {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}

.reviewer-photo {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #f0f0f0;
}

.rating {
  color: #f8c41b;
  margin-bottom: 10px;
  font-weight: 600;
}

.review-text {
  color: #333;
  line-height: 1.5;
}

.cache-notice {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background-color: #e6f7ff;
  border: 1px solid #91d5ff;
  border-radius: 4px;
  padding: 8px 12px;
  margin-bottom: 15px;
  font-size: 0.9rem;
  color: #0050b3;
}

.refresh-button {
  background-color: transparent;
  border: 1px solid #1890ff;
  color: #1890ff;
  padding: 4px 8px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.8rem;
  transition: all 0.2s;
}

.refresh-button:hover {
  background-color: #1890ff;
  color: white;
}

.reviews-results {
  margin-top: 20px;
}

@media (max-width: 600px) {
  .search-box {
    flex-direction: column;
  }
  
  .search-button {
    width: 100%;
  }
}
</style>