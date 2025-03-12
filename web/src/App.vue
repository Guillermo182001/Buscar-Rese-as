<script>
import axios from 'axios';
import SearchReviews from './components/SearchReviews.vue';
import ReviewCarousel from './components/ReviewCarousel.vue';

export default {
  name: 'App',
  components: {
    SearchReviews,
    ReviewCarousel
  },
  data() {
    return {
      message: '',
      googleReviews: [],
      loading: true,
    };
  },
  async mounted() {
    await this.fetchTestMessage();
    await this.fetchGoogleReviews();
  },
  methods: {
    async fetchTestMessage() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/test');
        this.message = response.data.message;
      } catch (error) {
        console.error('Error al conectar con la API:', error);
      } finally {
        this.loading = false;
      }
    },
    async fetchGoogleReviews() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/reviews/google');
        this.googleReviews = response.data.reviews || [];
      } catch (error) {
        console.error('Error fetching Google reviews:', error);
      } 
    },
  },
};
</script>

<template>
  <div id="app">
    <section>
      <h1>Prueba de conexión:</h1>
      <p>{{ message }}</p>
    </section>

    <section>
      <h1>Reseñas de Google</h1>
      <p v-if="loading">Cargando reseñas...</p>
      <ReviewCarousel v-else :reviews="googleReviews" />
    </section>

    <section>
      <SearchReviews />
    </section>
  </div>
</template>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  text-align: center;
  margin-top: 60px;
}
</style>
