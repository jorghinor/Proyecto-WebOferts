<template>
  <v-app id="inspire">
    <navbar></navbar>

    <v-main>
      <v-container>
        <v-card>
          <v-toolbar height="80px">
            <v-toolbar-title>Paquetes</v-toolbar-title>
            <v-spacer></v-spacer>
            <nuevopaquete @updateList="nuevoPaq"></nuevopaquete>
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
                label="Filtrar por titulo, tipo o etiqueta"
                @input="applyFilters"
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
                @change="applyFilters"
              ></v-select>
            </v-col>
          </v-row>

          <v-sheet
            v-if="!paquetes"
            class="px-3 pt-3 pb-3"
            color="grey lighten-4"
          >
            <v-skeleton-loader
              class="mx-auto"
              max-width="95vh"
              type="table-heading, table-thead, table-tbody, table-tfoot"
            ></v-skeleton-loader>
          </v-sheet>

          <div class="table-responsive" v-else>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-left">##</th>
                    <th class="text-left">Titulo</th>
                    <th class="text-left">Costo (Bs.)</th>
                    <th class="text-left">Tiempo (Meses)</th>
                    <th class="text-left">Orden</th>
                    <th class="text-left">Tipo</th>
                    <th class="text-left">Etiqueta</th>
                    <th class="text-left">Estado</th>
                    <th class="text-left">Opciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="paquete in paquetesCounter" :key="paquete.id">
                    <td>{{ paquete.counter }}.-</td>
                    <td>{{ paquete.titulo }}</td>
                    <td>{{ paquete.costo }}</td>
                    <td>{{ paquete.tiempo }}</td>
                    <td>{{ paquete.orden }}</td>
                    <td>{{ paquete.tipopaquete }}</td>
                    <td><v-chip small :color="paquete.color">{{ paquete.label }}</v-chip></td>
                    <td>
                      <v-chip
                        v-if="paquete.estado == 'activo'"
                        class="success black--text"
                        x-small
                      >
                        activo
                      </v-chip>
                      <v-chip v-else class="danger white--text" x-small>
                        inactivo
                      </v-chip>
                    </td>
                    <td>
                      <div class="acciones-paquete">
                        <v-tooltip top>
                          <template v-slot:activator="{ on, attrs }">
                            <v-btn
                              small
                              outlined
                              color="primary"
                              class="accion-btn"
                              v-bind="attrs"
                              v-on="on"
                              @click="verDetalle(paquete)"
                            >
                              <v-icon small>mdi-eye-outline</v-icon>
                            </v-btn>
                          </template>
                          <span>Ver detalle</span>
                        </v-tooltip>

                        <editarpaquete
                          :paquete="paquete"
                          class="accion-editar"
                          @editPaq="editarPaq"
                        ></editarpaquete>

                        <v-tooltip top>
                          <template v-slot:activator="{ on, attrs }">
                            <v-btn
                              small
                              class="accion-btn accion-estado"
                              :class="paquete.estado == 'activo' ? 'estado-desactivar' : 'estado-activar'"
                              v-bind="attrs"
                              v-on="on"
                              @click="cambiarEstado(paquete)"
                            >
                              <v-icon small class="white--text">
                                {{ paquete.estado == 'activo' ? 'mdi-power' : 'mdi-power-plug' }}
                              </v-icon>
                            </v-btn>
                          </template>
                          <span>{{ paquete.estado == 'activo' ? 'Desactivar paquete' : 'Activar paquete' }}</span>
                        </v-tooltip>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="paquetesCounter.length === 0">
                    <td colspan="9" class="text-center py-4">Sin resultados para los filtros aplicados.</td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </div>

          <div class="text-center py-2 graysuave" v-if="pagination != null">
            <v-btn
              small
              class="mx-2 py-2"
              :disabled="prevDisabled"
              @click="previo"
            >
              <v-icon small large>mdi-arrow-left-circle</v-icon>
            </v-btn>
            <v-btn small class="mx-2 py-2">{{ pagination.actual_page + 1 }}</v-btn>
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

        <v-dialog v-model="dialogDetalle" max-width="620">
          <v-card v-if="paqueteDetalle">
            <v-toolbar dense flat color="primary">
              <v-toolbar-title class="white--text">Detalle de Paquete</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon dark @click="dialogDetalle = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>
            <v-card-text class="pt-4">
              <v-row dense>
                <v-col cols="12" class="text-center">
                  <v-chip class="px-4 py-2" :color="paqueteDetalle.color || 'grey lighten-2'">
                    {{ paqueteDetalle.label || 'Etiqueta' }}
                  </v-chip>
                </v-col>
                <v-col cols="12" sm="6"><strong>Titulo:</strong> {{ paqueteDetalle.titulo || '-' }}</v-col>
                <v-col cols="12" sm="6"><strong>Tipo:</strong> {{ paqueteDetalle.tipopaquete || '-' }}</v-col>
                <v-col cols="12"><strong>Descripcion:</strong> {{ paqueteDetalle.descripcion || '-' }}</v-col>
                <v-col cols="12" sm="4"><strong>Costo:</strong> Bs {{ paqueteDetalle.costo || 0 }}</v-col>
                <v-col cols="12" sm="4"><strong>Tiempo:</strong> {{ paqueteDetalle.tiempo || 0 }} meses</v-col>
                <v-col cols="12" sm="4"><strong>Orden:</strong> {{ paqueteDetalle.orden || '-' }}</v-col>
                <v-col cols="12" sm="6">
                  <strong>Estado:</strong>
                  <v-chip
                    x-small
                    :class="paqueteDetalle.estado == 'activo' ? 'success black--text' : 'danger white--text'"
                  >
                    {{ paqueteDetalle.estado || '-' }}
                  </v-chip>
                </v-col>
                <v-col cols="12" sm="6"><strong>Color:</strong> {{ paqueteDetalle.color || '-' }}</v-col>
                <v-col cols="12" sm="6"><strong>Creado:</strong> {{ formatDate(paqueteDetalle.created_at) }}</v-col>
                <v-col cols="12" sm="6"><strong>Actualizado:</strong> {{ formatDate(paqueteDetalle.updated_at) }}</v-col>
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
  name: "adminPaquetes",
  data() {
    return {
      paquetes: null,
      paquetesAll: [],
      snackbar: false,
      mensaje: "",
      dialogDetalle: false,
      paqueteDetalle: null,
      page: 0,
      pages: 10,
      pagination: null,
      prevDisabled: true,
      nextDisabled: true,
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
    fetchPaquetes() {
      this.paquetes = null;
      axios.get(window.location.origin + "/admin/paqlist").then((res) => {
        this.paquetesAll = Array.isArray(res.data) ? res.data : [];
        this.syncPaquetesPage();
      });
    },
    syncPaquetesPage() {
      const total = this.filteredPaquetes.length;
      const lastPage = total > 0 ? Math.ceil(total / this.pages) - 1 : 0;
      if (this.page < 0) this.page = 0;
      if (this.page > lastPage) this.page = lastPage;

      const start = this.page * this.pages;
      const end = start + this.pages;
      this.paquetes = this.filteredPaquetes.slice(start, end);

      this.pagination = {
        actual_page: this.page,
        total: total,
        pages: this.pages,
        last_page: lastPage,
      };
      this.prevDisabled = this.page <= 0;
      this.nextDisabled = this.page >= lastPage || total === 0;
    },
    applyFilters() {
      this.page = 0;
      this.syncPaquetesPage();
    },
    clearTextFilter() {
      this.filterText = "";
      this.applyFilters();
    },
    siguiente() {
      if (this.nextDisabled) return;
      this.page = Number(this.pagination.actual_page + 1);
      this.syncPaquetesPage();
    },
    previo() {
      if (this.prevDisabled) return;
      this.page = Number(this.pagination.actual_page - 1);
      this.syncPaquetesPage();
    },
    nuevoPaq(data) {
      this.paquetesAll.unshift(data);
      this.applyFilters();
      this.mensaje = "Paquete registrado con exito";
      this.snackbar = true;
    },
    editarPaq(paque) {
      this.paquetesAll = this.paquetesAll.map((paq) => {
        if (paq.id == paque.id) {
          paq.titulo = paque.titulo;
          paq.descripcion = paque.descripcion;
          paq.costo = paque.costo;
          paq.tiempo = paque.tiempo;
          paq.orden = paque.orden;
          paq.tipopaquete = paque.tipopaquete;
          paq.label = paque.label;
          paq.color = paque.color;
          paq.estado = paque.estado ? "activo" : "inactivo";
        }
        return paq;
      });
      this.syncPaquetesPage();
      this.mensaje = "Paquete actualizado";
      this.snackbar = true;
    },
    verDetalle(paquete) {
      this.paqueteDetalle = JSON.parse(JSON.stringify(paquete));
      this.dialogDetalle = true;
    },
    cambiarEstado(paquete) {
      const nuevoEstado = paquete.estado == "activo" ? "inactivo" : "activo";
      axios
        .put(window.location.origin + "/admin/paquetes/state/" + paquete.id, {
          state: nuevoEstado,
        })
        .then((resp) => {
          if (resp.data && resp.data.success) {
            paquete.estado = nuevoEstado;
            this.paquetesAll = this.paquetesAll.map((paq) => {
              if (paq.id == paquete.id) paq.estado = nuevoEstado;
              return paq;
            });
            if (this.paqueteDetalle && this.paqueteDetalle.id == paquete.id) {
              this.paqueteDetalle.estado = nuevoEstado;
            }
            this.syncPaquetesPage();
            this.mensaje = `Paquete ${nuevoEstado == "activo" ? "activado" : "desactivado"} con exito`;
            this.snackbar = true;
          }
        })
        .catch((error) => {
          console.log(error);
          alert("No se pudo actualizar el estado del paquete.");
        });
    },
    formatDate(value) {
      if (!value) return "-";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return value;
      return date.toLocaleString();
    },
  },

  computed: {
    filteredPaquetes: function() {
      const all = Array.isArray(this.paquetesAll) ? this.paquetesAll : [];
      const text = (this.filterText || "").toString().trim().toLowerCase();
      const estado = this.filterEstado || "todos";

      return all.filter((p) => {
        const matchesText =
          !text ||
          (p.titulo || "").toLowerCase().includes(text) ||
          (p.tipopaquete || "").toLowerCase().includes(text) ||
          (p.label || "").toLowerCase().includes(text);
        const matchesEstado = estado === "todos" || (p.estado || "") === estado;
        return matchesText && matchesEstado;
      });
    },
    paquetesCounter: function() {
      if (!this.paquetes || !this.pagination) return [];
      let counter = Number(this.pagination.actual_page * this.pages + 1);
      return this.paquetes.map((paquete) => {
        paquete.counter = counter;
        counter++;
        return paquete;
      });
    },
  },

  created() {
    this.fetchPaquetes();
  },
};
</script>
<style scoped>
.table-responsive {
  width: 100%;
  overflow-x: auto;
}
.acciones-paquete {
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
  .acciones-paquete {
    justify-content: flex-start;
    margin-top: 4px;
  }
}
</style>
