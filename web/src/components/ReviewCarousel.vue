<script>
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/swiper-bundle.css';

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
};
</script>

<template>
  <swiper
    :slides-per-view="1"
    :space-between="20"
    :pagination="{ clickable: true }"
    navigation
    class="carousel"
  >
    <swiper-slide v-for="(review, index) in reviews" :key="index" class="review-slide">
      <div class="review-card">
        <img :src="review.profile_photo_url" alt="Author's photo" class="review-photo" />
        <h3>{{ review.author_name }}</h3>
        <p>{{ review.text }}</p>
        <span>Rating: ⭐ {{ review.rating }}</span>
      </div>
    </swiper-slide>
  </swiper>
</template>

<style scoped>
.carousel {
  width: 100%;
  max-width: 600px;
  margin: auto;
}
.review-card {
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 10px;
  text-align: center;
}
.review-photo {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
}
</style>
