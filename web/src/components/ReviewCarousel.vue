<script>
import { Swiper, SwiperSlide } from 'swiper/vue';
// Importar los módulos necesarios de Swiper
import { Navigation, Pagination } from 'swiper/modules';
// Importar los estilos de Swiper
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

export default {
  name: 'ReviewCarousel',
  components: {
    Swiper,
    SwiperSlide,
  },
  props: {
    reviews: {
      type: Array,
      required: true,
    },
  },
  setup() {
    // Configurar los módulos que usará Swiper
    return {
      modules: [Navigation, Pagination],
    };
  },
};
</script>

<template>
  <swiper
    :slides-per-view="1"
    :space-between="20"
    :pagination="{ clickable: true }"
    :navigation="{ 
      nextEl: '.swiper-button-next', 
      prevEl: '.swiper-button-prev' 
    }"
    :modules="modules"
    class="carousel"
  >
    <swiper-slide v-for="(review, index) in reviews" :key="index" class="review-slide">
      <div class="review-card">
        <img 
          :src="review.profile_photo_url || '/img/default-avatar.png'" 
          :alt="review.author_name" 
          class="review-photo" 
        />
        <h3>{{ review.author_name }}</h3>
        <div class="rating">
          <span v-for="i in 5" :key="i" class="star" :class="{ 'filled': i <= review.rating }">★</span>
          <span class="rating-text">{{ review.rating }}/5</span>
        </div>
        <p class="review-text">{{ review.text }}</p>
        <span class="review-time">{{ review.relative_time_description }}</span>
      </div>
    </swiper-slide>
    
    <!-- Botones de navegación personalizados -->
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
  </swiper>
</template>

<style scoped>
.carousel {
  width: 100%;
  max-width: 600px;
  margin: auto;
  position: relative;
  padding: 0 40px; /* Espacio para los botones de navegación */
}

.review-card {
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 10px;
  text-align: center;
  background-color: white;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s;
}

.review-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.review-photo {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #f0f0f0;
  margin-bottom: 10px;
}

.rating {
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 10px 0;
}

.star {
  color: #e5e7eb;
  font-size: 18px;
}

.star.filled {
  color: #fbbf24;
}

.rating-text {
  margin-left: 8px;
  font-weight: bold;
  color: #666;
}

.review-text {
  margin: 15px 0;
  color: #4b5563;
  line-height: 1.6;
}

.review-time {
  display: block;
  font-size: 12px;
  color: #9ca3af;
  margin-top: 10px;
}

/* Estilos para los botones de navegación */
:deep(.swiper-button-next),
:deep(.swiper-button-prev) {
  background-color: white;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  color: #333; /* Color de las flechas */
}

:deep(.swiper-button-next:after),
:deep(.swiper-button-prev:after) {
  font-size: 18px; /* Tamaño de las flechas */
}

:deep(.swiper-button-next:hover),
:deep(.swiper-button-prev:hover) {
  background-color: #f8f8f8;
}

:deep(.swiper-button-disabled) {
  opacity: 0.3;
}

:deep(.swiper-pagination-bullet) {
  background-color: #666;
}

:deep(.swiper-pagination-bullet-active) {
  background-color: #333;
}
</style>
