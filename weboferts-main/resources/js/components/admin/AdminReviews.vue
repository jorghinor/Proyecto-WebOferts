<template>
  <v-app id="inspire">
    <navbar></navbar>
    <v-main>
      <v-container>
        <v-card>
          <v-toolbar height="80px">
            <v-toolbar-title>Gestión de Reseñas</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn color="success" class="white--text" @click="openCreateDialog">
              <v-icon left>mdi-plus</v-icon> Nueva Reseña
            </v-btn>
          </v-toolbar>
          <v-divider></v-divider>

          <v-row class="px-3 pt-3 pb-1">
            <v-col cols="12" md="8" class="py-1">
              <v-text-field
                v-model="search"
                outlined
                dense
                clearable
                hide-details
                prepend-inner-icon="mdi-magnify"
                label="Buscar por Comentario, Usuario o Negocio"
                @keyup.enter="fetchReviews"
                @click:clear="search = ''; fetchReviews()"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4" class="py-1">
               <v-btn color="success" class="white--text" @click="fetchReviews" block>Buscar</v-btn>
            </v-col>
          </v-row>

          <v-data-table
            :headers="headers"
            :items="reviews"
            :loading="loading"
            :options.sync="options"
            :server-items-length="totalReviews"
            class="elevation-0"
          >
            <template v-slot:item.rating="{ item }">
              <v-rating :value="item.rating" color="amber" dense readonly size="14"></v-rating>
            </template>
            <template v-slot:item.created_at="{ item }">
              {{ formatDate(item.created_at) }}
            </template>
            <template v-slot:item.reviewer_name="{ item }">
              {{ item.user ? item.user.name : (item.reviewer_name || 'Anónimo') }}
            </template>
            <template v-slot:item.negocio="{ item }">
              {{ item.negocio ? item.negocio.nnegocio : 'N/A' }}
            </template>
            <template v-slot:item.actions="{ item }">
              <div class="d-flex">
                <v-tooltip top>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn small outlined color="primary" class="mr-2" v-bind="attrs" v-on="on" @click="viewDetails(item)">
                      <v-icon small>mdi-eye-outline</v-icon>
                    </v-btn>
                  </template>
                  <span>Ver Detalle</span>
                </v-tooltip>

                <v-tooltip top>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn small outlined color="orange" class="mr-2" v-bind="attrs" v-on="on" @click="editItem(item)">
                      <v-icon small>mdi-pencil-outline</v-icon>
                    </v-btn>
                  </template>
                  <span>Editar Reseña</span>
                </v-tooltip>

                <v-tooltip top>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn small outlined color="red" v-bind="attrs" v-on="on" @click="deleteItem(item)">
                      <v-icon small>mdi-delete-outline</v-icon>
                    </v-btn>
                  </template>
                  <span>Eliminar Reseña</span>
                </v-tooltip>
              </div>
            </template>
          </v-data-table>
        </v-card>

        <!-- Modal Crear/Editar Reseña -->
        <v-dialog v-model="dialogForm" max-width="500px">
          <v-card>
            <v-card-title class="headline">{{ formTitle }}</v-card-title>
            <v-card-text>
              <v-form ref="form" v-model="validForm">
                <v-text-field
                  v-if="editedIndex === -1"
                  v-model="editedItem.negocio_id"
                  label="ID Negocio"
                  type="number"
                  :rules="[v => !!v || 'Requerido']"
                  outlined
                  dense
                ></v-text-field>
                <v-text-field
                  v-model="editedItem.reviewer_name"
                  label="Nombre Autor"
                  :rules="[v => !!v || 'Requerido']"
                  outlined
                  dense
                  :disabled="editedIndex > -1 && !!editedItem.user_id"
                ></v-text-field>
                <div class="mb-2">
                  <span class="subtitle-2">Calificación:</span>
                  <v-rating v-model="editedItem.rating" color="amber" hover></v-rating>
                </div>
                <v-textarea
                  v-model="editedItem.comment"
                  label="Comentario"
                  outlined
                  rows="3"
                  :rules="[v => !!v || 'Requerido']"
                ></v-textarea>
              </v-form>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="grey" text @click="close">Cancelar</v-btn>
              <v-btn color="success" text @click="save" :disabled="!validForm">Guardar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <!-- Modal Ver Detalle -->
        <v-dialog v-model="dialogDetail" max-width="500px">
          <v-card v-if="selectedReview">
            <v-toolbar dense flat color="primary">
              <v-toolbar-title class="white--text">Detalle de Reseña #{{ selectedReview.id }}</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon dark @click="dialogDetail = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>
            <v-card-text class="pt-4">
              <p><strong>Negocio:</strong> {{ selectedReview.negocio ? selectedReview.negocio.nnegocio : 'N/A' }}</p>
              <p><strong>Autor:</strong> {{ selectedReview.user ? selectedReview.user.name : (selectedReview.reviewer_name || 'Anónimo') }}</p>
              <p><strong>Fecha:</strong> {{ formatDate(selectedReview.created_at) }}</p>
              <div class="d-flex align-center mb-2">
                <strong class="mr-2">Calificación:</strong>
                <v-rating :value="selectedReview.rating" color="amber" dense readonly size="18"></v-rating>
              </div>
              <v-divider class="my-2"></v-divider>
              <p class="font-italic">"{{ selectedReview.comment }}"</p>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="grey darken-1" text @click="dialogDetail = false">Cerrar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <!-- Modal Confirmar Eliminación -->
        <v-dialog v-model="dialogDelete" max-width="400px">
          <v-card>
            <v-card-title class="headline">¿Eliminar Reseña?</v-card-title>
            <v-card-text>Esta acción no se puede deshacer.</v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="grey" text @click="dialogDelete = false">Cancelar</v-btn>
              <v-btn color="red" text @click="confirmDelete">Eliminar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-snackbar v-model="snackbar" :timeout="4000" top color="success">
          {{ mensaje }}
          <template v-slot:action="{ attrs }">
            <v-btn text v-bind="attrs" @click="snackbar = false">Cerrar</v-btn>
          </template>
        </v-snackbar>

      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  name: "AdminReviews",
  data() {
    return {
      search: '',
      reviews: [],
      loading: true,
      totalReviews: 0,
      options: {},
      dialogDelete: false,
      dialogForm: false,
      dialogDetail: false,
      validForm: false,
      selectedReview: null,
      snackbar: false,
      mensaje: '',
      editedIndex: -1,
      editedItem: {
        id: null,
        negocio_id: '',
        reviewer_name: '',
        rating: 5,
        comment: '',
        user_id: null
      },
      defaultItem: {
        id: null,
        negocio_id: '',
        reviewer_name: 'Admin',
        rating: 5,
        comment: '',
        user_id: null
      },
      headers: [
        { text: 'ID', value: 'id' },
        { text: 'Usuario', value: 'reviewer_name' },
        { text: 'Negocio', value: 'negocio' },
        { text: 'Calificación', value: 'rating' },
        { text: 'Comentario', value: 'comment' },
        { text: 'Fecha', value: 'created_at' },
        { text: 'Acciones', value: 'actions', sortable: false },
      ],
    };
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Nueva Reseña' : 'Editar Reseña';
    },
  },
  watch: {
    options: {
      handler() {
        this.fetchReviews();
      },
      deep: true,
    },
    dialogForm(val) {
      val || this.close();
    },
  },
  methods: {
    fetchReviews() {
      this.loading = true;
      const { page, itemsPerPage } = this.options;

      axios.post('/admin/lista/reviews', {
        page: page - 1,
        pages: itemsPerPage,
        texto: this.search,
      })
      .then(response => {
        this.reviews = response.data.result.data;
        this.totalReviews = response.data.result.pagination.total;
      })
      .catch(error => {
        console.error("Error cargando reseñas:", error);
      })
      .finally(() => {
        this.loading = false;
      });
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'short', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('es-ES', options);
    },
    openCreateDialog() {
      this.editedItem = Object.assign({}, this.defaultItem);
      this.editedIndex = -1;
      this.dialogForm = true;
    },
    editItem(item) {
      this.editedIndex = this.reviews.indexOf(item);
      this.editedItem = Object.assign({}, item);
      // Ajustar nombre si viene de relación
      if (!this.editedItem.reviewer_name && this.editedItem.user) {
          this.editedItem.reviewer_name = this.editedItem.user.name;
      }
      this.dialogForm = true;
    },
    viewDetails(item) {
      this.selectedReview = item;
      this.dialogDetail = true;
    },
    deleteItem(item) {
      this.selectedReview = item;
      this.dialogDelete = true;
    },
    close() {
      this.dialogForm = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    save() {
      if (!this.$refs.form.validate()) return;

      if (this.editedIndex > -1) {
        // Editar
        axios.put(`/admin/reviews/${this.editedItem.id}`, this.editedItem)
          .then(() => {
            this.fetchReviews();
            this.mensaje = "Reseña actualizada correctamente";
            this.snackbar = true;
            this.close();
          })
          .catch(error => {
            console.error("Error actualizando reseña:", error);
            alert("Error al actualizar.");
          });
      } else {
        // Crear
        axios.post('/admin/reviews/create', this.editedItem)
          .then(() => {
            this.fetchReviews();
            this.mensaje = "Reseña creada correctamente";
            this.snackbar = true;
            this.close();
          })
          .catch(error => {
            console.error("Error creando reseña:", error);
            alert("Error al crear reseña. Verifica el ID del negocio.");
          });
      }
    },
    confirmDelete() {
      axios.delete(`/admin/reviews/${this.selectedReview.id}`)
        .then(() => {
          this.fetchReviews();
          this.dialogDelete = false;
          this.mensaje = "Reseña eliminada correctamente";
          this.snackbar = true;
        });
    }
  }
};
</script>
