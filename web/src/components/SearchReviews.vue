<script>
import axios from 'axios';
import ReviewCarousel from './ReviewCarousel.vue';

export default {
  name: 'SearchReviews',
  components: {
    ReviewCarousel
  },
  data() {
    return {
      googleReviews: [],
      facebookReviews: [],
      googlePlaceId: '', 
      facebookPageId: '',
      loading: false,
      errorMessage: '' 
    };
  },
  methods: {
    async fetchGoogleReviews() {
      if (!this.googlePlaceId) {
        this.errorMessage = 'Por favor ingresa un Google Place ID';
        return;
      }
      this.loading = true;
      this.errorMessage = '';
      try {
        const response = await axios.get('http://localhost:8000/api/reviews/google', {
          params: { place_id: this.googlePlaceId }
        });
        this.googleReviews = response.data.reviews || [];
      } catch (error) {
        console.error('Error al obtener reseñas de Google:', error);
        this.errorMessage = 'Error al obtener las reseñas.';
      } finally {
        this.loading = false;
      }
    },
    async fetchFacebookReviews() {
      if (!this.facebookPageId) {
        alert('Por favor ingresa un Facebook Page ID');
        return;
      }
      try {
        const response = await axios.get('http://localhost:8000/api/reviews/facebook', {
          params: { page_id: this.facebookPageId }
        });
        this.facebookReviews = response.data || [];
      } catch (error) {
        console.error('Error al obtener reseñas de Facebook:', error);
      }
    }
  }
}
</script>

<template>
  <div>
    <!-- Buscador para Google -->
    <h2>Buscar reseñas de Google</h2>
    <input v-model="googlePlaceId" placeholder="Introduce Google Place ID" />
    <button @click="fetchGoogleReviews">Buscar</button>

    <p v-if="errorMessage" class="error">{{ errorMessage }}</p>

    <div v-if="loading">Cargando reseñas...</div>
    <ReviewCarousel v-else-if="googleReviews.length" :reviews="googleReviews" />
    <p v-else>No hay reseñas de Google disponibles.</p>

    <!-- Buscador para Facebook -->
    <h2>Buscar reseñas de Facebook</h2>
    <input v-model="facebookPageId" placeholder="Introduce Facebook Page ID" />
    <button @click="fetchFacebookReviews">Buscar</button>

    <ul v-if="facebookReviews.length">
      <li v-for="(review, index) in facebookReviews" :key="index">
        <p><strong>{{ review.reviewer?.name }}</strong></p>
        <p>Rating: {{ review.rating ?? 'N/A' }}/5</p>
        <p>{{ review.review_text ?? 'Sin texto' }}</p>
      </li>
    </ul>
    <p v-else>No hay reseñas de Facebook disponibles.</p>
  </div>
</template>

<style scoped>
h1 {
  font-size: 1.5rem;
  margin-bottom: 1rem;
}
ul {
  list-style: none;
  padding: 0;
}
li {
  margin-bottom: 1rem;
  padding: 1rem;
  border: 1px solid #ddd;
  border-radius: 8px;
}

input {
  padding: 8px;
  margin-right: 8px;
}

button {
  padding: 8px 12px;
  cursor: pointer;
}

.error {
  color: red;
  margin-top: 10px;
}
</style>
