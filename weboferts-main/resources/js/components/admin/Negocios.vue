<template>
  <v-app id="inspire">
    <navbar></navbar>
    <v-main>
      <v-container>
        <v-card>
          <v-toolbar height="80px">
            <v-toolbar-title>Negocios</v-toolbar-title>
            <v-spacer></v-spacer>
            <nuevonegocio
              @updateList="nuevoNeg"
              :categorias="categorias"
            ></nuevonegocio>
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
                label="Filtrar por negocio, direccion, nit o telefono"
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
            v-if="!negocios"
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
          <div v-for=" (negocio) in negocios" :key="negocio.id">
            <v-row no-gutters class="py-1">
              <v-col cols="6" sm="1" md="1">
                <div class="px-2 font-weight-thin">##</div>
                <div class="py-4 px-2 font-weight-light">{{ negocio.cuenta }}.-</div>
              </v-col>
              <v-col cols="6" sm="1" md="1" class="py-3">
                <v-avatar>
                  <img :src="getImage(negocio.logo)" alt="" />
                </v-avatar>
              </v-col>
              <v-col cols="6" sm="2" md="2" class="px-2 text-left">
                <div class="font-weight-thin caption">Nombre Negocio</div>
                <div class="py-1 font-weight-light caption">
                  {{ negocio.nnegocio }}
                </div>
                <v-divider class="d-md-none"></v-divider>
              </v-col>
              <v-col cols="6" sm="2" md="2" class="px-2text-left">
                <div class="font-weight-thin caption">Dirección</div>
                <div class="py-1 font-weight-light caption">
                  {{ negocio.dir }}
                </div>
                <v-divider class="d-md-none"></v-divider>
              </v-col>
              <v-col cols="6" sm="2" md="2" class="px-2 text-left">
                <div class="font-weight-thin caption">Telefonos</div>
                <v-chip x-small color="primary">{{ negocio.telefonos }}</v-chip>
                <v-chip x-small color="secundary">{{ negocio.celular }}</v-chip>
                <v-divider class="d-md-none"></v-divider>
              </v-col>
              <v-col cols="6" sm="2" md="2" class="px-2 text-left">
                <div class="font-weight-thin caption">Categoria(s)</div>
                <div class="py-1 font-weight-light">
                  <v-chip
                    v-for="cat in negocio.categorias"
                    :key="cat.id"
                    x-small
                    class="success black--text"
                    >{{ cat.cname }}</v-chip
                  >
                </div>
                <v-divider class="d-md-none"></v-divider>
              </v-col>
              <v-col cols="12" sm="2" md="2" class="pt-2 text-center">
                <div class="acciones-negocio">
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        small
                        outlined
                        color="primary"
                        class="accion-btn"
                        v-bind="attrs"
                        v-on="on"
                        @click="verDetalle(negocio)"
                      >
                        <v-icon small>mdi-eye-outline</v-icon>
                      </v-btn>
                    </template>
                    <span>Ver detalle</span>
                  </v-tooltip>

                  <editarnegocio
                    :negocio="negocio"
                    :categorias="categorias"
                    class="accion-editar"
                    @updateNegocio="actualizarNegocio"
                  ></editarnegocio>

                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        small
                        class="accion-btn accion-estado"
                        :class="negocio.estadonegocio === 'activo' ? 'estado-desactivar' : 'estado-activar'"
                        v-bind="attrs"
                        v-on="on"
                        @click="cambiarEstado(negocio)"
                      >
                        <v-icon small class="white--text">
                          {{ negocio.estadonegocio === 'activo' ? 'mdi-power' : 'mdi-power-plug' }}
                        </v-icon>
                      </v-btn>
                    </template>
                    <span>{{ negocio.estadonegocio === 'activo' ? 'Desactivar negocio' : 'Activar negocio' }}</span>
                  </v-tooltip>
                </div>
              </v-col>
            </v-row>
            <v-divider></v-divider>
          </div>
        </template>

          <div class="text-center py-2 graysuave" v-if="pagination != null">
            <v-btn
              small
              class="mx-2 py-2"
              :disabled="prevDisabled"
              @click="previo"
            >
              <v-icon small large>mdi-arrow-left-circle</v-icon>
            </v-btn>
            <v-btn small class="mx-2 py-2">{{
              pagination.actual_page + 1
            }}/{{pagination.last_page}}</v-btn>
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

        <v-dialog v-model="dialogDetalle" max-width="760">
          <v-card v-if="negocioDetalle">
            <v-toolbar dense flat color="primary">
              <v-toolbar-title class="white--text">Detalle del Negocio</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon dark @click="dialogDetalle = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>
            <v-card-text class="pt-4">
              <v-row>
                <v-col cols="12" md="4" class="text-center">
                  <v-avatar size="120">
                    <img :src="getImageFull(negocioDetalle.logo)" alt="Logo negocio" />
                  </v-avatar>
                  <div class="mt-3">
                    <v-chip
                      small
                      :color="negocioDetalle.estadonegocio === 'activo' ? 'success' : 'error'"
                      class="white--text"
                    >
                      {{ negocioDetalle.estadonegocio === 'activo' ? 'Activo' : 'Inactivo' }}
                    </v-chip>
                  </div>
                </v-col>
                <v-col cols="12" md="8">
                  <v-row dense>
                    <v-col cols="12" sm="6">
                      <strong>Nombre:</strong> {{ negocioDetalle.nnegocio || '-' }}
                    </v-col>
                    <v-col cols="12" sm="6">
                      <strong>NIT:</strong> {{ negocioDetalle.nit || '-' }}
                    </v-col>
                    <v-col cols="12">
                      <strong>Direccion:</strong> {{ negocioDetalle.dir || '-' }}
                    </v-col>
                    <v-col cols="12" sm="6">
                      <strong>Telefonos:</strong> {{ negocioDetalle.telefonos || '-' }}
                    </v-col>
                    <v-col cols="12" sm="6">
                      <strong>Celular:</strong> {{ negocioDetalle.celular || '-' }}
                    </v-col>
                    <v-col cols="12" sm="6">
                      <strong>Delivery:</strong> {{ negocioDetalle.delivery ? 'Si' : 'No' }}
                    </v-col>
                    <v-col cols="12" sm="6">
                      <strong>Web:</strong> {{ negocioDetalle.web || '-' }}
                    </v-col>
                    <v-col cols="12">
                      <strong>Google Maps:</strong>
                      <a
                        v-if="negocioDetalle.gmaps"
                        :href="negocioDetalle.gmaps"
                        target="_blank"
                        rel="noopener noreferrer"
                      >
                        Abrir ubicacion
                      </a>
                      <span v-else>-</span>
                    </v-col>
                    <v-col cols="12">
                      <strong>Categorias:</strong>
                      <div class="mt-1">
                        <v-chip
                          v-for="cat in (negocioDetalle.categorias || [])"
                          :key="cat.id"
                          x-small
                          class="mr-1 mb-1 success black--text"
                        >
                          {{ cat.cname }}
                        </v-chip>
                        <span v-if="!(negocioDetalle.categorias || []).length">-</span>
                      </div>
                    </v-col>
                    <v-col cols="12" sm="6">
                      <strong>Creado:</strong> {{ formatDate(negocioDetalle.created_at) }}
                    </v-col>
                    <v-col cols="12" sm="6">
                      <strong>Actualizado:</strong> {{ formatDate(negocioDetalle.updated_at) }}
                    </v-col>
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
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  //inject: ["theme"],
  inject: {
      theme: {
        default: { isDark: false },
      },
    },
  name: "adminegocios",
  data() {
    return {
      drawer: null,
      negocios: null,
      id: 0,
      snackbar: false,
      mensaje: "",
      page: 0,
      pages: 50,
      pagination: {
        cuenta: 1
      },
      prevDisabled: true,
      nextDisabled: true,
      categorias: [],
      dialogDetalle: false,
      negocioDetalle: null,
      filterText: "",
      filterEstado: "todos",
      estadoOptions: [
        { text: "Todos", value: "todos" },
        { text: "Activos", value: "activo" },
        { text: "Inactivos", value: "inactivo" },
      ],
    };
  },
  methods: {
    nuevoNeg(data) {
      this.negocios.push(data);
      this.mensaje = "Negocio registrado con exito";
      this.snackbar = true;
    },
    actualizarNegocio(data) {
      this.negocios.map((neg) => {
        if (neg.id == data.id) {
          neg.nnegocio = data.nnegocio;
          neg.nit = data.nit;
          neg.dir = data.dir;
          neg.gmaps = data.gmaps;
          neg.latitude = data.latitude;
          neg.longitud = data.longitud;
          neg.telefonos = data.telefonos;
          neg.celular = data.celular;
          neg.delivery = data.delivery;
          neg.web = data.web;
          neg.logo = data.logo;
        }
      });
      this.mensaje = "Negocio Actualizado!";
      this.snackbar = true;
    },
    resolveImagePath(image) {
      if (image.startsWith("http://") || image.startsWith("https://")) {
        return image;
      }

      const normalized = image.startsWith("/") ? image : `/${image}`;
      return window.location.origin + normalized;
    },
    getImage(image) {
      if (image == "" || image == null || image === "default") {
        return window.location.origin + "/assets/img/logo.png";
      }

      if (image.startsWith("http://") || image.startsWith("https://")) {
        return image;
      }

      const normalized = image.startsWith("/") ? image : `/${image}`;
      if (normalized.includes("/storage/images/") && !normalized.includes("/thumbnail/")) {
        return window.location.origin + normalized.substring(0, normalized.lastIndexOf("/")) + "/thumbnail/" + normalized.substring(normalized.lastIndexOf("/") + 1);
      }

      return this.resolveImagePath(normalized);
    },
    getImageFull(image) {
      if (image == "" || image == null || image === "default") {
        return window.location.origin + "/assets/img/logo.png";
      }
      return this.resolveImagePath(image);
    },
    formatDate(value) {
      if (!value) return "-";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return value;
      return date.toLocaleString();
    },
    verDetalle(negocio) {
      this.negocioDetalle = JSON.parse(JSON.stringify(negocio));
      this.dialogDetalle = true;
    },
    cambiarEstado(negocio) {
      const nuevoEstado = negocio.estadonegocio === "activo" ? "inactivo" : "activo";
      axios
        .put(window.location.origin + "/admin/negocios/state/" + negocio.id, {
          state: nuevoEstado,
        })
        .then((resp) => {
          if (resp.data && resp.data.success) {
            negocio.estadonegocio = nuevoEstado;
            if (this.negocioDetalle && this.negocioDetalle.id === negocio.id) {
              this.negocioDetalle.estadonegocio = nuevoEstado;
            }
            this.mensaje = `Negocio ${nuevoEstado === "activo" ? "activado" : "desactivado"} con exito`;
            this.snackbar = true;
          }
        })
        .catch((error) => {
          console.log(error);
          alert("No se pudo actualizar el estado del negocio.");
        });
    },
    siguiente() {
      this.page = Number(this.pagination.actual_page + 1);
      this.cuenta = Number(this.pagination.cuenta)
      this.fetchNegocios();
    },
    previo() {
      this.page = Number(this.pagination.actual_page - 1);
      if (this.page >= 0) {
        this.fetchNegocios();
      }
    },
    fetchNegocios() {
      //pruebas
      this.negocios = null;
      this.total = 0;
      let formData = {
        page: this.page,
        pages: this.pages,
        cuenta: this.pagination.cuenta,
        texto: this.filterText,
        estado: this.filterEstado,
      };
      axios({
        url: window.location.origin + "/admin/lista/negocios",
        data: formData,
        method: "POST",
      })
        .then((resp) => {
          this.negocios = resp.data.result.data;
          this.categorias = resp.data.result.categorias;
          this.pagination = {
            first_page: resp.data.result.pagination.first_page,
            actual_page: resp.data.result.pagination.actual_page,
            next_page: resp.data.result.pagination.next_page,
            total: resp.data.result.pagination.total,
            pre_page: resp.data.result.pagination.pre_page,
            pages: resp.data.result.pagination.pages,
            last_page: resp.data.result.pagination.last_page,
            cuenta: resp.data.result.pagination.cuenta,
          };
          this.prevDisabled = this.pagination.pre_page === null;
          this.nextDisabled = this.pagination.next_page === null;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    onFiltersChanged() {
      this.page = 0;
      this.fetchNegocios();
    },
    clearTextFilter() {
      this.filterText = "";
      this.onFiltersChanged();
    },
  },

  created() {
    this.fetchNegocios();
  },
};
</script>

<style scoped>
.acciones-negocio {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  flex-wrap: nowrap;
  min-height: 38px;
}
.accion-btn {
  min-width: 30px !important;
  width: 30px !important;
  height: 30px !important;
  padding: 0 !important;
}
.accion-editar {
  display: inline-flex;
  align-items: center;
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
@media (max-width: 960px) {
  .acciones-negocio {
    justify-content: flex-start;
    margin-top: 4px;
  }
}
</style>
