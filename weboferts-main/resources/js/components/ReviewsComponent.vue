<template>
  <div class="reviews-section mt-5">
    <h3 class="section-title mb-4">Opiniones de Clientes</h3>

    <!-- Resumen de Calificación -->
    <div class="d-flex align-center mb-5 review-summary">
      <div class="text-center mr-5">
        <h1 class="display-3 font-weight-bold primary--text mb-0">{{ average }}</h1>
        <v-rating
          :value="parseFloat(average)"
          color="amber"
          dense
          half-increments
          readonly
          size="20"
        ></v-rating>
        <span class="caption grey--text">{{ count }} opiniones</span>
      </div>

      <div class="flex-grow-1">
        <v-btn
          color="primary"
          outlined
          rounded
          @click="showForm = !showForm"
          v-if="!showForm"
        >
          <v-icon left>lni-pencil</v-icon> Escribir una opinión
        </v-btn>
      </div>
    </div>

    <!-- Formulario de Reseña -->
    <v-expand-transition>
      <div v-if="showForm" class="review-form mb-5 pa-4 grey lighten-5 rounded">
        <h4 class="subtitle-1 mb-3">Tu experiencia</h4>
        <v-form ref="form" v-model="valid">

          <!-- Campo Nombre para Invitados -->
          <v-text-field
            v-model="newReview.reviewer_name"
            label="Tu Nombre"
            outlined
            dense
            :rules="[v => !!v || 'El nombre es requerido']"
            class="mb-2"
          ></v-text-field>

          <div class="mb-2">
            <span class="subtitle-2 mr-2">Calificación:</span>
            <v-rating
              v-model="newReview.rating"
              color="amber"
              hover
              size="24"
            ></v-rating>
          </div>

          <v-textarea
            v-model="newReview.comment"
            label="Cuéntanos qué te pareció..."
            outlined
            rows="3"
            :rules="[v => !!v || 'El comentario es requerido', v => (v && v.length <= 500) || 'Máximo 500 caracteres']"
            counter="500"
          ></v-textarea>

          <div class="d-flex justify-end mt-2">
            <v-btn text @click="showForm = false" class="mr-2">Cancelar</v-btn>
            <v-btn
              color="primary"
              :loading="submitting"
              :disabled="!valid || newReview.rating === 0"
              @click="submitReview"
            >
              Publicar
            </v-btn>
          </div>
        </v-form>
      </div>
    </v-expand-transition>

    <!-- Lista de Reseñas -->
    <div class="reviews-list">
      <div v-if="reviews.length === 0" class="text-center py-5 grey--text">
        <v-icon size="48" color="grey lighten-2">lni-comments</v-icon>
        <p class="mt-2">Aún no hay opiniones. ¡Sé el primero en comentar!</p>
      </div>

      <v-card
        v-for="review in reviews"
        :key="review.id"
        class="mb-3 elevation-1"
        outlined
      >
        <v-card-text>
          <div class="d-flex justify-space-between align-start">
            <div class="d-flex align-center">
              <v-avatar color="indigo lighten-4" size="40" class="mr-3">
                <span class="indigo--text font-weight-bold">{{ getInitials(review.user_name) }}</span>
              </v-avatar>
              <div>
                <div class="subtitle-2 font-weight-bold text--primary">{{ review.user_name }}</div>
                <div class="caption grey--text">{{ formatDate(review.created_at) }}</div>
              </div>
            </div>
            <v-rating
              :value="review.rating"
              color="amber"
              dense
              readonly
              size="14"
            ></v-rating>
          </div>
          <p class="body-2 mt-3 mb-0 text--primary">
            {{ review.comment }}
          </p>
        </v-card-text>
      </v-card>
    </div>

    <v-snackbar v-model="snackbar.show" :color="snackbar.color" timeout="3000">
      {{ snackbar.text }}
      <template v-slot:action="{ attrs }">
        <v-btn text v-bind="attrs" @click="snackbar.show = false">Cerrar</v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script>
export default {
  name: "ReviewsComponent",
  props: {
    negocioId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      reviews: [],
      average: 0,
      count: 0,
      showForm: false,
      valid: false,
      submitting: false,
      newReview: {
        reviewer_name: '',
        rating: 0,
        comment: ''
      },
      snackbar: {
        show: false,
        text: '',
        color: 'success'
      }
    };
  },
  methods: {
    fetchReviews() {
      axios.get(`/reviews/${this.negocioId}`)
        .then(response => {
          this.reviews = response.data.reviews;
          this.average = response.data.average;
          this.count = response.data.count;
        })
        .catch(error => {
          console.error("Error cargando reseñas:", error);
        });
    },
    submitReview() {
      if (!this.$refs.form.validate() || this.newReview.rating === 0) return;

      this.submitting = true;

      axios.post('/reviews', {
        negocio_id: this.negocioId,
        reviewer_name: this.newReview.reviewer_name,
        rating: this.newReview.rating,
        comment: this.newReview.comment
      })
      .then(response => {
        this.showSnackbar(response.data.message, 'success');
        this.showForm = false;
        this.newReview = { reviewer_name: '', rating: 0, comment: '' };
        this.fetchReviews(); // Recargar lista
      })
      .catch(error => {
        this.showSnackbar('Error al guardar tu opinión', 'error');
        console.error(error);
      })
      .finally(() => {
        this.submitting = false;
      });
    },
    getInitials(name) {
      if (!name) return '?';
      return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('es-ES', options);
    },
    showSnackbar(text, color) {
      this.snackbar.text = text;
      this.snackbar.color = color;
      this.snackbar.show = true;
    }
  },
  mounted() {
    this.fetchReviews();
  }
};
</script>

<style scoped>
.review-summary {
  border-bottom: 1px solid #eee;
  padding-bottom: 20px;
}
.section-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #333;
  border-left: 4px solid #e91e63;
  padding-left: 15px;
}
</style>
