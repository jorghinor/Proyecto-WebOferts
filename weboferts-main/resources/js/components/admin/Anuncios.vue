<template>
  <v-app id="inspire">
    <navbar></navbar>
    <v-main>
      <v-container>
        <v-card>
          <v-toolbar height="80px">
            <v-toolbar-title>Anuncios</v-toolbar-title>
            <v-spacer></v-spacer>
            <nuevoanuncio @updateList="nuevoAnuncio"></nuevoanuncio>
          </v-toolbar>
          <v-divider></v-divider>
          <v-row class="px-3 pt-3 pb-1">
            <v-col cols="12" md="8" class="py-1">
              <v-text-field
                v-model="filterText"
                outlined
                dense
                clearable
                hide-details
                prepend-inner-icon="mdi-magnify"
                label="Filtrar por titulo"
                @input="onFiltersChanged"
                @click:clear="clearTextFilter"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4" class="py-1">
              <v-select
                v-model="filterEstado"
                :items="estadoOptions"
                outlined
                dense
                hide-details
                label="Estado"
                @change="onFiltersChanged"
              ></v-select>
            </v-col>
          </v-row>
          <v-sheet
            v-if="!anuncios"
            :color="`grey ${theme.isDark ? 'darken-2' : 'lighten-4'}`"
            class="px-3 pt-3 pb-3"
          >
            <v-skeleton-loader
              class="mx-auto"
              max-width="90vh"
              type="table-heading, table-thead, table-tbody, table-tfoot"
            ></v-skeleton-loader>
          </v-sheet>

          <template v-else style="height: 90vh">
            <div v-for="anuncio in anuncios" :key="anuncio.id">
                <v-row
                    no-gutters class="py-1"
                    :class="getAnuncioRowClass(anuncio)"
                >
                  <v-col cols="6" sm="1" md="1">
                      <div class="px-2 font-weight-thin caption">Fila</div>
                      <div class="py-1 px-2 font-weight-light">{{ anuncio.cuenta }}</div>
                      <div class="px-2 caption grey--text text--darken-1">ID {{ anuncio.id }}</div>
                  </v-col>
                  <v-col cols="6" sm="2" md="2">
                      <div class="caption font-weight-thin caption">Foto</div>
                      <v-avatar size="44" class="mt-1">
                        <img :src="getAnuncioPreview(anuncio)" alt="Foto anuncio" />
                      </v-avatar>
                  </v-col>
                  <v-col cols="6" sm="3" md="3">
                      <div class="caption font-weight-thin caption">Negocio</div>
                      <div>{{ (anuncio.negocio && anuncio.negocio.nnegocio) || 'Negocio no disponible' }}</div>
                      <div v-if="!hasNegocio(anuncio)" class="caption warning--text text--darken-2 font-weight-bold">
                        Registro huérfano
                      </div>
                  </v-col>
                  <v-col cols="8" sm="2" md="2">
                      <div class="caption font-weight-thin caption">Título</div>
                      <div class="font-weight-regular">{{ anuncio.titulo }}</div>
                      <div class="caption font-weight-thin caption">{{ anuncio.created_at | fecha }}</div>
                  </v-col>
                  <v-col cols="2" sm="2" md="2">
                      <div class="caption font-weight-thin caption">Estado</div>
                      <div>
                      <v-chip v-if="anuncio.estadoanuncio=='activo'" class="success secondary--text" small>{{ anuncio.estadoanuncio }}</v-chip>
                      <v-chip dark v-else class="danger" small>{{ anuncio.estadoanuncio }}</v-chip>
                      </div>
                  </v-col>
                  <v-col cols="2" sm="2" md="2">
                      <div class="caption">Opciones</div>
                      <div class="acciones-anuncio">
                        <v-tooltip top>
                          <template v-slot:activator="{ on, attrs }">
                            <v-btn
                              small
                              outlined
                              color="primary"
                              class="accion-btn"
                              v-bind="attrs"
                              v-on="on"
                              @click="verDetalle(anuncio)"
                            >
                              <v-icon small>mdi-eye-outline</v-icon>
                            </v-btn>
                          </template>
                          <span>Ver detalle</span>
                        </v-tooltip>

                        <v-tooltip top>
                          <template v-slot:activator="{ on, attrs }">
                            <v-btn
                              small
                              color="primary"
                              class="accion-btn"
                              v-bind="attrs"
                              v-on="on"
                              @click="editItem(anuncio)"
                            >
                              <v-icon small>mdi-pencil-outline</v-icon>
                            </v-btn>
                          </template>
                          <span>Editar anuncio</span>
                        </v-tooltip>

                        <v-tooltip top>
                          <template v-slot:activator="{ on, attrs }">
                            <v-btn
                              small
                              class="accion-btn accion-estado"
                              :class="anuncio.estadoanuncio=='activo' ? 'estado-desactivar' : 'estado-activar'"
                              v-bind="attrs"
                              v-on="on"
                              @click="cambiarEstado(anuncio)"
                            >
                              <v-icon small class="white--text">
                                {{ anuncio.estadoanuncio=='activo' ? 'mdi-power' : 'mdi-power-plug' }}
                              </v-icon>
                            </v-btn>
                          </template>
                          <span>{{ anuncio.estadoanuncio=='activo' ? 'Desactivar anuncio' : 'Activar anuncio' }}</span>
                        </v-tooltip>
                      </div>
                  </v-col>
                </v-row>
                <v-divider></v-divider>
            </div>
          </template>

          <!-- Edit Dialog -->
          <v-dialog v-model="dialog" max-width="960px" scrollable>
            <v-card class="anuncio-edit-card">
              <v-card-title class="anuncio-edit-header">
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>
              <v-card-text class="anuncio-edit-body">
                <v-container>
                  <v-form ref="editForm" v-model="formValid" lazy-validation>
                    <v-row>
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="editedItem.titulo"
                          label="Título del Anuncio*"
                          :rules="requiredRule"
                        ></v-text-field>
                        <v-textarea
                          v-model="editedItem.descripcion"
                          label="Descripción*"
                          :rules="requiredRule"
                        ></v-textarea>
                        <v-text-field
                          :value="editedNegocioName"
                          label="Negocio"
                          disabled
                        ></v-text-field>
                        <v-select
                          v-model="editedItem.producto_id"
                          :items="productosDisponibles"
                          item-text="nombre"
                          item-value="id"
                          label="Producto*"
                          :rules="requiredRule"
                          :loading="loadingDialogData"
                          :disabled="loadingDialogData || !productosDisponibles.length"
                        ></v-select>
                        <v-select
                          v-model="selectedPaquetes"
                          :items="paquetesDisponibles"
                          item-text="titulo"
                          item-value="id"
                          label="Paquetes activos"
                          multiple
                          chips
                          clearable
                          :loading="loadingDialogData"
                        >
                          <template v-slot:item="{ item }">
                            {{ item.titulo }} - {{ item.costo }} Bs.
                          </template>
                        </v-select>
                      </v-col>
                      <v-col cols="12" md="6">
                        <div class="anuncio-image-toolbar mb-3">
                          <div class="subtitle-2">Imagen del anuncio</div>
                          <div class="anuncio-image-toolbar-actions">
                            <clipperimagedialog
                              ref="anuncioEditor"
                              button-text="Cambiar imagen"
                              button-icon="mdi-image-edit-outline"
                              button-color="deep-orange darken-1"
                              :button-small="false"
                              button-class="anuncio-image-btn"
                              confirm-text="Guardar imagen del anuncio"
                              @setImage="setImagen"
                            ></clipperimagedialog>
                            <v-btn
                              v-if="newImage || savingImage"
                              color="success"
                              class="font-weight-bold"
                              large
                              :disabled="!newImage"
                              :loading="savingImage"
                              @click="saveImageOnly"
                            >
                              Reintentar guardar imagen
                            </v-btn>
                          </div>
                        </div>
                        <div v-if="imagenError" class="caption danger--text mb-2">
                          {{ imagenError }}
                        </div>
                        <v-alert
                          dense
                          outlined
                          type="info"
                          color="primary"
                          class="mb-3"
                        >
                          Dentro del selector verás <strong>Seleccionar imagen</strong> y <strong>Guardar imagen del anuncio</strong> en la misma fila.
                        </v-alert>
                        <v-img
                          :src="imagePreview || getDefaultImage()"
                          contain
                          height="220"
                          class="grey lighten-4 rounded-lg"
                        ></v-img>
                        <div class="caption mt-2">
                          {{ savingImage ? 'Guardando imagen del anuncio...' : (newImage ? 'Si falla el guardado automático, usa Reintentar guardar imagen.' : 'Puedes reemplazar la imagen actual del anuncio.') }}
                        </div>
                      </v-col>
                    </v-row>
                  </v-form>
                </v-container>
              </v-card-text>
              <v-card-actions class="anuncio-edit-actions">
                <div class="caption grey--text text--darken-1 anuncio-edit-hint">
                  {{ newImage ? 'Hay una nueva imagen pendiente de guardar.' : 'Los cambios se aplican con el botón Guardar cambios.' }}
                </div>
                <v-spacer></v-spacer>
                <v-btn color="blue grey darken-1" outlined @click="close">Cancelar</v-btn>
                <v-btn color="primary" large elevation="2" class="font-weight-bold px-6" :loading="saving" @click="save">Guardar cambios</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <v-dialog v-model="dialogDetalle" max-width="820">
            <v-card v-if="anuncioDetalle">
              <v-toolbar dense flat color="primary">
                <v-toolbar-title class="white--text">Detalle de Anuncio</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon dark @click="dialogDetalle = false">
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-toolbar>
              <v-card-text class="pt-4">
                <v-row dense>
                  <v-col cols="12" md="5">
                    <v-carousel
                      v-if="anuncioDetalle.fotos && anuncioDetalle.fotos.length"
                      hide-delimiters
                      height="220"
                    >
                      <v-carousel-item
                        v-for="foto in anuncioDetalle.fotos"
                        :key="foto.id"
                      >
                        <v-img :src="getImage(foto.url)" contain height="220"></v-img>
                      </v-carousel-item>
                    </v-carousel>
                    <v-img
                      v-else
                      :src="getDefaultImage()"
                      contain
                      height="220"
                    ></v-img>
                  </v-col>
                  <v-col cols="12" md="7">
                    <v-row dense>
                      <v-col cols="12"><strong>Titulo:</strong> {{ anuncioDetalle.titulo || '-' }}</v-col>
                      <v-col cols="12"><strong>Descripcion:</strong> {{ anuncioDetalle.descripcion || '-' }}</v-col>
                      <v-col cols="12" sm="6"><strong>Negocio:</strong> {{ (anuncioDetalle.negocio && anuncioDetalle.negocio.nnegocio) || '-' }}</v-col>
                      <v-col cols="12" sm="6">
                        <strong>Estado:</strong>
                        <v-chip
                          x-small
                          :class="anuncioDetalle.estadoanuncio=='activo' ? 'success black--text' : 'danger white--text'"
                        >
                          {{ anuncioDetalle.estadoanuncio || '-' }}
                        </v-chip>
                      </v-col>
                      <v-col cols="12" sm="6"><strong>Creado:</strong> {{ formatDate(anuncioDetalle.created_at) }}</v-col>
                      <v-col cols="12" sm="6"><strong>Actualizado:</strong> {{ formatDate(anuncioDetalle.updated_at) }}</v-col>
                    </v-row>
                  </v-col>
                </v-row>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn text color="grey darken-1" @click="dialogDetalle = false">Cerrar</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <div class="text-center py-2 graysuave" v-if="pagination != null">
            <v-btn
              small
              class="mx-2 py-2"
              :disabled="prevDisabled"
              @click="previo"
            >
              <v-icon small large>mdi-arrow-left-circle</v-icon>
            </v-btn>
            <v-btn small class="mx-2 py-2"
              >{{ pagination.actual_page + 1 }}/{{
                pagination.last_page
              }}</v-btn
            >
            <v-btn
              small
              class="mx-2 py-2"
              :disabled="nextDisabled"
              @click="siguiente"
            >
              <v-icon small large>mdi-arrow-right-circle</v-icon>
            </v-btn>
          </div>
        </v-card>
        <v-snackbar v-model="snackbar" :timeout="6000" top color="success">
          <p class="title blackblue--text float-left">{{ mensaje }}</p>
          <v-btn
            small
            icon
            class="success black--text float-right"
            text
            @click="snackbar = false"
            >x</v-btn
          >
        </v-snackbar>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import NuevoAnuncio from './NuevoAnuncio.vue';
import ClipperImageDialog from '../ClipperImageDialog.vue';

export default {
  inject: {
      theme: {
        default: { isDark: false },
      },
  },
  components: {
    'nuevoanuncio': NuevoAnuncio,
    'clipperimagedialog': ClipperImageDialog,
  },
  name: "adminanuncios",
  data() {
    return {
      anuncios: null,
      snackbar: false,
      mensaje: "",
      page: 0,
      pages: 15,
      pagination: {},
      prevDisabled: true,
      nextDisabled: true,
      filterText: "",
      filterEstado: "todos",
      estadoOptions: [
        { text: "Todos", value: "todos" },
        { text: "Activos", value: "activo" },
        { text: "Inactivos", value: "inactivo" },
      ],
      dialog: false,
      formValid: true,
      saving: false,
      savingImage: false,
      loadingDialogData: false,
      editedIndex: -1,
      editedItem: {
        id: null,
        titulo: '',
        descripcion: '',
        negocio_id: null,
        producto_id: null,
      },
      defaultItem: {
        id: null,
        titulo: '',
        descripcion: '',
        negocio_id: null,
        producto_id: null,
      },
      negocios: [],
      productosDisponibles: [],
      paquetesDisponibles: [],
      selectedPaquetes: [],
      imagePreview: null,
      temporaryImagePreviewUrl: null,
      newImage: null,
      imagenError: '',
      requiredRule: [(v) => !!v || 'Este campo es requerido'],
      dialogDetalle: false,
      anuncioDetalle: null,
    };
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Nuevo Anuncio' : 'Editar Anuncio'
    },
    editedNegocioName() {
      const negocio = this.negocios.find((item) => item.id === this.editedItem.negocio_id)
        || this.editedItem.negocio;
      return (negocio && negocio.nnegocio) || 'Negocio no disponible';
    },
  },
  watch: {
    dialog(val) {
      val || this.close()
    },
  },
  methods: {
    nuevoAnuncio(data) {
      this.anuncios.unshift(data);
      this.mensaje = "Anuncio registrado con exito";
      this.snackbar = true;
    },
    fetchAnuncios() {
      this.anuncios = null;
      this.total = 0;
      let formData = {
        page: this.page,
        pages: this.pages,
        texto: this.filterText,
        estado: this.filterEstado,
      };
      axios({
        url: window.location.origin + "/admin/lista/anuncios",
        data: formData,
        method: "POST",
      })
        .then((resp) => {
          this.anuncios = resp.data.result.data;
          this.pagination = resp.data.result.pagination;
          this.prevDisabled = this.pagination.pre_page === null;
          this.nextDisabled = this.pagination.next_page === null;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getImage(image) {
      if (!image || typeof image !== 'string') {
        return this.getDefaultImage();
      }

      if (image.startsWith('blob:')) {
        return image;
      }

      const normalizedImage = this.normalizeImageUrl(image);
      if (!normalizedImage.includes('/storage/images/')) {
        return normalizedImage;
      }

      return normalizedImage.substring(0, normalizedImage.lastIndexOf('/')) + '/thumbnail/' + normalizedImage.substring(normalizedImage.lastIndexOf('/') + 1);
    },
    getDefaultImage() {
      return window.location.origin + "/assets/img/logo.png";
    },
    normalizeImageUrl(url) {
      if (!url || typeof url !== 'string') {
        return this.getDefaultImage();
      }
      if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('blob:')) {
        return url;
      }
      if (url.startsWith('/')) {
        return window.location.origin + url;
      }
      return window.location.origin + '/' + url.replace(/^\/+/, '');
    },
    getAnuncioPreview(anuncio) {
      if (!anuncio || !anuncio.fotos || !anuncio.fotos.length || !anuncio.fotos[0].url) {
        return this.getDefaultImage();
      }
      return this.normalizeImageUrl(anuncio.fotos[0].url);
    },
    hasNegocio(anuncio) {
      return !!(anuncio && anuncio.negocio && anuncio.negocio.nnegocio);
    },
    getAnuncioRowClass(anuncio) {
      return this.hasNegocio(anuncio) ? '' : 'anuncio-row-warning';
    },
    revokeTemporaryImagePreview() {
      if (this.temporaryImagePreviewUrl) {
        URL.revokeObjectURL(this.temporaryImagePreviewUrl);
        this.temporaryImagePreviewUrl = null;
      }
    },
    setImagePreview(url, { temporary = false } = {}) {
      if (temporary) {
        this.revokeTemporaryImagePreview();
        this.temporaryImagePreviewUrl = url;
        this.imagePreview = url;
        return;
      }

      this.revokeTemporaryImagePreview();
      this.imagePreview = url ? this.normalizeImageUrl(url) : this.getDefaultImage();
    },
    updateAnuncioPhotoInMemory(anuncio, foto) {
      if (!anuncio || !foto || !foto.url) {
        return;
      }

      const fotoActualizada = {
        ...(Array.isArray(anuncio.fotos) && anuncio.fotos.length ? anuncio.fotos[0] : {}),
        ...foto,
      };

      if (Array.isArray(anuncio.fotos) && anuncio.fotos.length) {
        anuncio.fotos.splice(0, 1, fotoActualizada);
        return;
      }

      this.$set(anuncio, 'fotos', [fotoActualizada]);
    },
    siguiente() {
      this.page = Number(this.pagination.actual_page + 1);
      this.fetchAnuncios();
    },
    previo() {
      this.page = Number(this.pagination.actual_page - 1);
      if (this.page >= 0) {
        this.fetchAnuncios();
      }
    },
    onFiltersChanged() {
      this.page = 0;
      this.fetchAnuncios();
    },
    clearTextFilter() {
      this.filterText = "";
      this.onFiltersChanged();
    },
    actualizarAnuncio(data){
      this.anuncios.map( element =>{
        if(element.id==data.id){
          if(data.state){
            element.estadoanuncio = 'activo'
          }else{
            element.estadoanuncio = 'inactivo'
          }
          return element
        }
      })
    },
    verDetalle(anuncio) {
      this.anuncioDetalle = JSON.parse(JSON.stringify(anuncio));
      this.dialogDetalle = true;
    },
    cambiarEstado(anuncio) {
      const nuevoEstado = anuncio.estadoanuncio === "activo" ? "inactivo" : "activo";
      axios
        .put(window.location.origin + "/admin/anuncios/state/" + anuncio.id, {
          state: nuevoEstado === "activo",
        })
        .then((resp) => {
          if (resp.data && resp.data.success) {
            anuncio.estadoanuncio = nuevoEstado;
            if (this.anuncioDetalle && this.anuncioDetalle.id === anuncio.id) {
              this.anuncioDetalle.estadoanuncio = nuevoEstado;
            }
            this.mensaje = `Anuncio ${nuevoEstado === "activo" ? "activado" : "desactivado"} con exito`;
            this.snackbar = true;
          }
        })
        .catch((error) => {
          console.log(error);
          alert("No se pudo actualizar el estado del anuncio.");
        });
    },
    async loadDialogData() {
      this.loadingDialogData = true;
      try {
        const [negociosResponse, paquetesResponse] = await Promise.all([
          axios.get(window.location.origin + '/admin/neglist'),
          axios.get(window.location.origin + '/admin/paqlist'),
        ]);
        this.negocios = Array.isArray(negociosResponse.data) ? negociosResponse.data : [];
        this.paquetesDisponibles = Array.isArray(paquetesResponse.data)
          ? paquetesResponse.data
          : (paquetesResponse.data.data || []);
        this.syncProductosDisponibles();
      } catch (error) {
        console.error('Error loading anuncio editor data:', error.response || error);
      } finally {
        this.loadingDialogData = false;
      }
    },
    syncProductosDisponibles() {
      const negocio = this.negocios.find((item) => item.id === this.editedItem.negocio_id);
      const productos = negocio && Array.isArray(negocio.productos)
        ? [...negocio.productos]
        : [];

      if (this.editedItem.producto && this.editedItem.producto.id && !productos.some((item) => item.id === this.editedItem.producto.id)) {
        productos.unshift(this.editedItem.producto);
      }

      this.productosDisponibles = productos;
    },
    async setImagen(photo) {
      const payload = photo && typeof photo === 'object' && !(photo instanceof Blob) ? photo : null;
      const blob = payload && payload.blob ? payload.blob : (photo instanceof Blob ? photo : null);
      const previewUrl = payload && typeof payload.previewUrl === 'string'
        ? payload.previewUrl
        : (blob ? URL.createObjectURL(blob) : null);

      if (blob && previewUrl) {
        this.newImage = blob;
        this.setImagePreview(previewUrl, { temporary: previewUrl.startsWith('blob:') });
      } else if (typeof photo === 'string') {
        this.newImage = null;
        this.setImagePreview(photo);
      } else {
        this.newImage = null;
        this.setImagePreview(null);
      }

      this.imagenError = '';

      if (this.newImage) {
        await this.uploadPendingImage({ showSuccessMessage: true, refreshList: true });
      }
    },
    async uploadPendingImage({ showSuccessMessage = false, refreshList = true } = {}) {
      if (!this.newImage) {
        if (showSuccessMessage) {
          this.imagenError = 'Primero selecciona la imagen y pulsa "Guardar imagen del anuncio".';
        }
        return false;
      }

      this.savingImage = true;

      try {
        const formData = new FormData();
        formData.append('imagen', this.newImage);
        formData.append('a_id', this.editedItem.id);

        const response = await axios.post(window.location.origin + '/admin/files/anuncio', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });

        const fotoGuardada = response && response.data && response.data.foto ? response.data.foto : null;
        if (fotoGuardada && fotoGuardada.url) {
          this.setImagePreview(fotoGuardada.url);
          this.updateAnuncioPhotoInMemory(this.editedItem, fotoGuardada);

          const anuncioActual = Array.isArray(this.anuncios)
            ? this.anuncios.find((item) => item.id === this.editedItem.id)
            : null;

          this.updateAnuncioPhotoInMemory(anuncioActual, fotoGuardada);

          if (this.anuncioDetalle && this.anuncioDetalle.id === this.editedItem.id) {
            this.updateAnuncioPhotoInMemory(this.anuncioDetalle, fotoGuardada);
          }
        }

        this.newImage = null;
        this.imagenError = '';

        if (showSuccessMessage) {
          this.mensaje = 'Imagen del anuncio actualizada con exito';
          this.snackbar = true;
        }

        if (refreshList) {
          this.fetchAnuncios();
        }

        return true;
      } catch (error) {
        console.error('Error uploading anuncio image:', error.response || error);
        this.imagenError = 'No se pudo guardar la imagen del anuncio.';
        return false;
      } finally {
        this.savingImage = false;
      }
    },
    async saveImageOnly() {
      await this.uploadPendingImage({ showSuccessMessage: true, refreshList: true });
    },
    async editItem(item) {
      this.editedIndex = this.anuncios.indexOf(item);
      this.editedItem = Object.assign({}, item, {
        titulo: item.titulo || '',
        descripcion: item.descripcion || '',
        negocio_id: item.negocio_id || (item.negocio && item.negocio.id) || null,
        producto_id: item.producto_id || (item.producto && item.producto.id) || null,
      });
      this.selectedPaquetes = Array.isArray(item.paquetes_activos)
        ? item.paquetes_activos.map((paqueteId) => Number(paqueteId))
        : [];
      this.setImagePreview(item.fotos && item.fotos.length ? item.fotos[0].url : null);
      this.newImage = null;
      this.imagenError = '';
      this.dialog = true;
      await this.loadDialogData();
    },
    close() {
      this.dialog = false;
      this.$nextTick(() => {
        if (this.$refs.editForm) {
          this.$refs.editForm.resetValidation();
        }
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
        this.productosDisponibles = [];
        this.selectedPaquetes = [];
        this.revokeTemporaryImagePreview();
        this.imagePreview = null;
        this.newImage = null;
        this.savingImage = false;
        this.imagenError = '';
      });
    },
    async save() {
      if (this.$refs.editForm && !this.$refs.editForm.validate()) {
        return;
      }

      this.saving = true;
      try {
        if (this.newImage) {
          const imageSaved = await this.uploadPendingImage({ showSuccessMessage: false, refreshList: false });
          if (!imageSaved) {
            this.saving = false;
            return;
          }
        }

        await axios.put(`/admin/anuncios/${this.editedItem.id}`, {
          titulo: this.editedItem.titulo,
          descripcion: this.editedItem.descripcion,
          producto_id: this.editedItem.producto_id,
          paquetes: this.selectedPaquetes,
        });

        this.mensaje = 'Anuncio actualizado con exito';
        this.snackbar = true;
        this.close();
        this.fetchAnuncios();
      } catch (error) {
        console.error('Error updating anuncio:', error.response || error);
        alert('No se pudo actualizar el anuncio.');
      } finally {
        this.saving = false;
      }
    },
    formatDate(value) {
      if (!value) return "-";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return value;
      return date.toLocaleString();
    },
  },
  created() {
    this.fetchAnuncios();
  },
  filters: {
      fecha: function(value) {
          return value.substring(0,10);;
      }
  }
};
</script>

<style lang="scss" scoped>
.acciones-anuncio {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: nowrap;
}
.accion-btn {
  min-width: 30px !important;
  width: 30px !important;
  height: 30px !important;
  padding: 0 !important;
}
.accion-estado {
  border: none !important;
}
.estado-desactivar {
  background: linear-gradient(90deg, #c9ced4 0%, #8d969f 100%) !important;
}
.estado-activar {
  background: linear-gradient(90deg, #87d6aa 0%, #4dae79 100%) !important;
}
.anuncio-row-warning {
  background: rgba(245, 158, 11, 0.08);
  border-left: 4px solid rgba(245, 158, 11, 0.9);
}
.anuncio-edit-card {
  display: flex;
  flex-direction: column;
  max-height: 88vh;
}
.anuncio-edit-header {
  position: sticky;
  top: 0;
  z-index: 2;
  background: #fff;
  border-bottom: 1px solid rgba(15, 23, 42, 0.08);
}
.anuncio-edit-body {
  overflow-y: auto;
  padding-top: 16px;
}
.anuncio-image-btn {
  text-transform: none !important;
  font-weight: 700;
}
.anuncio-image-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}
.anuncio-image-toolbar-actions {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-end;
  gap: 10px;
}
.anuncio-edit-actions {
  position: sticky;
  bottom: 0;
  z-index: 2;
  background: #fff;
  border-top: 1px solid rgba(15, 23, 42, 0.08);
  padding: 16px 24px;
}
.anuncio-edit-hint {
  max-width: 280px;
}
@media (max-width: 960px) {
  .acciones-anuncio {
    justify-content: flex-start;
    margin-top: 4px;
  }
  .anuncio-image-toolbar {
    flex-direction: column;
    align-items: stretch;
  }
  .anuncio-image-toolbar-actions {
    justify-content: stretch;
  }
  .anuncio-image-toolbar-actions > * {
    flex: 1 1 100%;
  }
  .anuncio-edit-actions {
    flex-wrap: wrap;
    gap: 12px;
  }
  .anuncio-edit-hint {
    max-width: 100%;
    width: 100%;
  }
}
</style>
