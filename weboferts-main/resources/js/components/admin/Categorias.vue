<template>
  <v-app id="inspire">
    <navbar></navbar>

    <v-main>
      <v-container>
        <v-card>
          <v-toolbar height="80px">
            <v-toolbar-title>Categorias</v-toolbar-title>
            <v-spacer></v-spacer>
            <nuevacategoria @updateList="nuevaCat"></nuevacategoria>
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
                label="Filtrar por nombre, url o icono"
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
            v-if="!categorias"
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
                    <th class="text-left">Nombre Categoria</th>
                    <th class="text-left">Icono</th>
                    <th class="text-left">Url</th>
                    <th class="text-left"># Negocios</th>
                    <th class="text-left">Estado</th>
                    <th class="text-left">Opciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="categoria in categoriasCounter" :key="categoria.id">
                    <td>{{ categoria.counter }}.-</td>
                    <td>{{ categoria.cname }}</td>
                    <td>
                      <div class="table-icon-badge" :style="getCategoryIconStyle(categoria)">
                        <v-icon small color="white">{{ getIconName(categoria) }}</v-icon>
                      </div>
                    </td>
                    <td>{{ categoria.url }}</td>
                    <td>{{ categoria.cuantos }}</td>
                    <td>
                      <v-chip
                        v-if="categoria.cstate == 'active'"
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
                      <div class="acciones-categoria">
                        <v-tooltip top>
                          <template v-slot:activator="{ on, attrs }">
                            <v-btn
                              small
                              outlined
                              color="primary"
                              class="accion-btn"
                              v-bind="attrs"
                              v-on="on"
                              @click="verDetalle(categoria)"
                            >
                              <v-icon small>mdi-eye-outline</v-icon>
                            </v-btn>
                          </template>
                          <span>Ver detalle</span>
                        </v-tooltip>

                        <editarcategoria
                          :categoria="categoria"
                          class="accion-editar"
                          @editCat="editarCat"
                        ></editarcategoria>

                        <v-tooltip top>
                          <template v-slot:activator="{ on, attrs }">
                            <v-btn
                              small
                              class="accion-btn accion-estado"
                              :class="categoria.cstate == 'active' ? 'estado-desactivar' : 'estado-activar'"
                              v-bind="attrs"
                              v-on="on"
                              @click="cambiarEstado(categoria)"
                            >
                              <v-icon small class="white--text">
                                {{ categoria.cstate == 'active' ? 'mdi-power' : 'mdi-power-plug' }}
                              </v-icon>
                            </v-btn>
                          </template>
                          <span>{{ categoria.cstate == 'active' ? 'Desactivar categoria' : 'Activar categoria' }}</span>
                        </v-tooltip>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="categoriasCounter.length === 0">
                    <td colspan="7" class="text-center py-4">Sin resultados para los filtros aplicados.</td>
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

        <v-dialog v-model="dialogDetalle" max-width="560">
          <v-card v-if="categoriaDetalle">
            <v-toolbar dense flat color="primary">
              <v-toolbar-title class="white--text">Detalle de Categoria</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon dark @click="dialogDetalle = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>
            <v-card-text class="pt-4 text-center">
              <v-avatar size="98" class="mb-3 icono-detalle-bg" :style="getCategoryIconStyle(categoriaDetalle, true)">
                <v-icon x-large color="white">{{ getIconName(categoriaDetalle) }}</v-icon>
              </v-avatar>
              <div class="subtitle-1 font-weight-bold">{{ categoriaDetalle.cname || "-" }}</div>
              <div class="caption mb-2">Slug: {{ categoriaDetalle.url || "-" }}</div>
              <v-chip
                small
                :color="categoriaDetalle.cstate == 'active' ? 'success' : 'error'"
                class="white--text mb-2"
              >
                {{ categoriaDetalle.cstate == 'active' ? 'Activo' : 'Inactivo' }}
              </v-chip>
              <div class="body-2 mb-1"><strong>Icono definido:</strong> {{ categoriaDetalle.icon || "-" }}</div>
              <div class="body-2"><strong># Negocios:</strong> {{ categoriaDetalle.cuantos || 0 }}</div>
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
import { getCategoryTheme } from '../../utils/categoryTheme';

export default {
  name: "adminCategorias",
  data() {
    return {
      categorias: null,
      categoriasAll: [],
      snackbar: false,
      mensaje: "",
      dialogDetalle: false,
      categoriaDetalle: null,
      page: 0,
      pages: 10,
      pagination: null,
      prevDisabled: true,
      nextDisabled: true,
      filterText: "",
      filterEstado: "todos",
      estadoOptions: [
        { text: "Todos", value: "todos" },
        { text: "Activos", value: "active" },
        { text: "Inactivos", value: "inactive" },
      ],
    };
  },

  methods: {
    getCategoryTheme(category) {
      return getCategoryTheme(category.icon || category.url || category.cname);
    },
    getIconName(category) {
      return this.getCategoryTheme(category).icon;
    },
    getCategoryIconStyle(category, detailed = false) {
      const theme = this.getCategoryTheme(category || {});
      return {
        background: `linear-gradient(180deg, rgba(255,255,255,0.24) 0%, rgba(255,255,255,0.08) 100%), ${theme.gradient}`,
        boxShadow: detailed ? `0 18px 30px ${theme.shadow}` : `0 10px 18px ${theme.shadow}`,
        border: '1px solid rgba(255,255,255,0.38)',
      };
    },
    fetchCategorias() {
      this.categorias = null;
      axios.get(window.location.origin + "/admin/catlist").then((res) => {
        this.categoriasAll = Array.isArray(res.data) ? res.data : [];
        this.syncCategoriasPage();
      });
    },
    syncCategoriasPage() {
      const total = this.filteredCategorias.length;
      const lastPage = total > 0 ? Math.ceil(total / this.pages) - 1 : 0;
      if (this.page < 0) this.page = 0;
      if (this.page > lastPage) this.page = lastPage;

      const start = this.page * this.pages;
      const end = start + this.pages;
      this.categorias = this.filteredCategorias.slice(start, end);

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
      this.syncCategoriasPage();
    },
    clearTextFilter() {
      this.filterText = "";
      this.applyFilters();
    },
    siguiente() {
      if (this.nextDisabled) return;
      this.page = Number(this.pagination.actual_page + 1);
      this.syncCategoriasPage();
    },
    previo() {
      if (this.prevDisabled) return;
      this.page = Number(this.pagination.actual_page - 1);
      this.syncCategoriasPage();
    },
    nuevaCat(data) {
      this.categoriasAll.unshift(data);
      this.applyFilters();
      this.mensaje = "Categoria registrada con exito";
      this.snackbar = true;
    },
    editarCat(cate) {
      this.categoriasAll = this.categoriasAll.map((cat) => {
        if (cat.id == cate.id) {
          cat.cname = cate.cname;
          cat.icon = cate.icon;
          cat.url = cate.url;
          cat.cstate = cate.state ? "active" : "inactive";
        }
        return cat;
      });
      this.syncCategoriasPage();
      this.mensaje = "Categoria actualizada";
      this.snackbar = true;
    },
    verDetalle(categoria) {
      this.categoriaDetalle = JSON.parse(JSON.stringify(categoria));
      this.dialogDetalle = true;
    },
    cambiarEstado(categoria) {
      const nuevoEstado = categoria.cstate == "active" ? "inactive" : "active";
      axios
        .put(window.location.origin + "/admin/categorias/state/" + categoria.id, {
          state: nuevoEstado,
        })
        .then((resp) => {
          if (resp.data && resp.data.success) {
            categoria.cstate = nuevoEstado;
            this.categoriasAll = this.categoriasAll.map((cat) => {
              if (cat.id == categoria.id) cat.cstate = nuevoEstado;
              return cat;
            });
            if (this.categoriaDetalle && this.categoriaDetalle.id == categoria.id) {
              this.categoriaDetalle.cstate = nuevoEstado;
            }
            this.syncCategoriasPage();
            this.mensaje = `Categoria ${nuevoEstado == "active" ? "activada" : "desactivada"} con exito`;
            this.snackbar = true;
          }
        })
        .catch((error) => {
          console.log(error);
          alert("No se pudo actualizar el estado de la categoria.");
        });
    },
  },

  computed: {
    filteredCategorias: function() {
      const all = Array.isArray(this.categoriasAll) ? this.categoriasAll : [];
      const text = (this.filterText || "").toString().trim().toLowerCase();
      const estado = this.filterEstado || "todos";

      return all.filter((c) => {
        const matchesText =
          !text ||
          (c.cname || "").toLowerCase().includes(text) ||
          (c.url || "").toLowerCase().includes(text) ||
          (c.icon || "").toLowerCase().includes(text);
        const matchesEstado = estado === "todos" || (c.cstate || "") === estado;
        return matchesText && matchesEstado;
      });
    },
    categoriasCounter: function() {
      if (!this.categorias || !this.pagination) return [];
      let counter = Number(this.pagination.actual_page * this.pages + 1);
      return this.categorias.map((categoria) => {
        categoria.counter = counter;
        counter++;
        return categoria;
      });
    },
  },

  created() {
    this.fetchCategorias();
  },
};
</script>
<style scoped>
.table-responsive {
  width: 100%;
  overflow-x: auto;
}
.acciones-categoria {
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
.icono-detalle-bg {
  background: linear-gradient(180deg, #f7fbff 0%, #eef5fb 100%);
  border: 1px solid #d8e6f2;
}

.table-icon-badge {
  width: 40px;
  height: 40px;
  border-radius: 14px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

@media (max-width: 960px) {
  .acciones-categoria {
    justify-content: flex-start;
    margin-top: 4px;
  }
}
</style>
