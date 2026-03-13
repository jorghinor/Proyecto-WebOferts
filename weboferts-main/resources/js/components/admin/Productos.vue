<template>
  <v-app id="inspire">
    <navbar></navbar>

    <v-main>
      <v-container>
        <v-card>
          <v-toolbar height="80px">
            <v-toolbar-title>Productos</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-chip small color="info" class="black--text">
              Creacion gestionada por Cliente
            </v-chip>
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
                label="Filtrar por nombre o negocio"
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
            v-if="!productos"
            :color="`grey ${theme.isDark ? 'darken-2' : 'lighten-4'}`"
            class="px-3 pt-3 pb-3"
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
                  <th class="text-left">Foto</th>
                  <th class="text-left">Nombre</th>
                  <th class="text-left">Negocio</th>
                  <th class="text-left">Precio Regular (Bs.)</th>
                  <th class="text-left">Precio Oferta (Bs.)</th>
                  <th class="text-left">Tipo</th>
                  <th class="text-left"># Anuncios</th>
                  <th class="text-left">Estado</th>
                  <th class="text-left">Opciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="producto in productosCounter" :key="producto.id">
                  <td>{{ producto.counter }}.-</td>
                  <td>
                    <v-avatar size="42" tile class="producto-avatar">
                      <img
                        v-if="producto.foto_url"
                        :src="resolveImageUrl(producto.foto_url)"
                        alt="Foto producto"
                      />
                      <span v-else class="avatar-fallback">Sin</span>
                    </v-avatar>
                  </td>
                  <td>{{ producto.nombre }}</td>
                  <td>{{ producto.negocio_nombre || '-' }}</td>
                  <td><v-chip small>{{ producto.precio_regular || 0 }}</v-chip></td>
                  <td><v-chip small>{{ producto.precio_oferta || 0 }}</v-chip></td>
                  <td>{{ producto.tipoproducto }}</td>
                  <td>{{ producto.anuncios_count || 0 }}</td>
                  <td>
                    <v-chip
                      v-if="producto.estado_prod == 'activo'"
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
                    <div class="acciones-producto">
                      <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            small
                            outlined
                            color="primary"
                            class="accion-btn"
                            v-bind="attrs"
                            v-on="on"
                            @click="verDetalle(producto)"
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
                            @click="abrirEditar(producto)"
                          >
                            <v-icon small>mdi-pencil-outline</v-icon>
                          </v-btn>
                        </template>
                        <span>Editar producto</span>
                      </v-tooltip>

                      <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            small
                            class="accion-btn accion-estado"
                            :class="producto.estado_prod == 'activo' ? 'estado-desactivar' : 'estado-activar'"
                            v-bind="attrs"
                            v-on="on"
                            @click="cambiarEstado(producto)"
                          >
                            <v-icon small class="white--text">
                              {{ producto.estado_prod == 'activo' ? 'mdi-power' : 'mdi-power-plug' }}
                            </v-icon>
                          </v-btn>
                        </template>
                        <span>{{ producto.estado_prod == 'activo' ? 'Desactivar producto' : 'Activar producto' }}</span>
                      </v-tooltip>
                    </div>
                  </td>
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
            <v-btn small class="mx-2 py-2">{{
              pagination.actual_page + 1
            }}</v-btn>
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
          <v-btn small icon class="success black--text float-right" text @click="snackbar = false">x</v-btn>
        </v-snackbar>

        <v-dialog v-model="dialogDetalle" max-width="640">
          <v-card v-if="productoDetalle">
            <v-toolbar dense flat color="primary">
              <v-toolbar-title class="white--text">Detalle de Producto</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon dark @click="dialogDetalle = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>
            <v-card-text class="pt-4">
              <v-row dense>
                <v-col cols="12" class="pb-2">
                  <div class="detalle-foto-wrap">
                    <img
                      v-if="productoDetalle.foto_url"
                      :src="resolveImageUrl(productoDetalle.foto_url)"
                      alt="Foto producto detalle"
                      class="detalle-foto"
                    />
                    <div v-else class="detalle-foto-empty">Sin imagen</div>
                  </div>
                </v-col>
                <v-col cols="12" sm="6"><strong>Nombre:</strong> {{ productoDetalle.nombre || '-' }}</v-col>
                <v-col cols="12" sm="6"><strong>Negocio:</strong> {{ productoDetalle.negocio_nombre || '-' }}</v-col>
                <v-col cols="12"><strong>Descripcion:</strong> {{ productoDetalle.descripcion || '-' }}</v-col>
                <v-col cols="12" sm="4"><strong>Precio regular:</strong> Bs {{ productoDetalle.precio_regular || 0 }}</v-col>
                <v-col cols="12" sm="4"><strong>Precio oferta:</strong> Bs {{ productoDetalle.precio_oferta || 0 }}</v-col>
                <v-col cols="12" sm="4"><strong>Tipo:</strong> {{ productoDetalle.tipoproducto || '-' }}</v-col>
                <v-col cols="12" sm="6"><strong># Anuncios:</strong> {{ productoDetalle.anuncios_count || 0 }}</v-col>
                <v-col cols="12" sm="6">
                  <strong>Estado:</strong>
                  <v-chip x-small :class="productoDetalle.estado_prod=='activo' ? 'success black--text' : 'danger white--text'">
                    {{ productoDetalle.estado_prod || '-' }}
                  </v-chip>
                </v-col>
                <v-col cols="12" sm="6"><strong>Creado:</strong> {{ formatDate(productoDetalle.created_at) }}</v-col>
                <v-col cols="12" sm="6"><strong>Actualizado:</strong> {{ formatDate(productoDetalle.updated_at) }}</v-col>
              </v-row>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn text color="grey darken-1" @click="dialogDetalle = false">Cerrar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="dialogEditar" max-width="620">
          <v-card>
            <v-toolbar dense flat color="primary">
              <v-toolbar-title class="white--text">Editar Producto</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon dark @click="dialogEditar = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>
            <v-card-text class="pt-4">
              <v-form ref="formEditarProducto" v-model="validEditarProducto">
                <v-row dense>
                  <v-col cols="12">
                    <v-text-field
                      label="Nombre"
                      v-model="editProducto.nombre"
                      :rules="requiredRule"
                      outlined
                      dense
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-textarea
                      label="Descripcion"
                      v-model="editProducto.descripcion"
                      outlined
                      dense
                      rows="2"
                    ></v-textarea>
                  </v-col>
                  <v-col cols="12" sm="4">
                    <v-text-field
                      label="Precio regular"
                      v-model="editProducto.precio_regular"
                      type="number"
                      outlined
                      dense
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="4">
                    <v-text-field
                      label="Precio oferta"
                      v-model="editProducto.precio_oferta"
                      type="number"
                      outlined
                      dense
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="4">
                    <v-text-field
                      label="Tipo producto"
                      v-model="editProducto.tipoproducto"
                      :rules="requiredRule"
                      outlined
                      dense
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field
                      label="Negocio"
                      :value="editProducto.negocio_nombre || '-'"
                      disabled
                      outlined
                      dense
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-switch
                      v-model="editProducto.estadoSwitch"
                      :label="editProducto.estadoSwitch ? 'Activo' : 'Inactivo'"
                    ></v-switch>
                  </v-col>
                </v-row>
              </v-form>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn text color="grey darken-1" @click="dialogEditar = false">Cancelar</v-btn>
              <v-btn color="primary" :loading="savingProducto" :disabled="!validEditarProducto" @click="guardarProducto">
                Guardar
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  inject: {
    theme: {
      default: { isDark: false },
    },
  },
  name: "adminProductos",
  data() {
    return {
      productos: null,
      snackbar: false,
      mensaje: "",
      page: 0,
      pages: 10,
      pagination: null,
      prevDisabled: true,
      nextDisabled: true,
      productosAll: [],
      filterText: "",
      filterEstado: "todos",
      estadoOptions: [
        { text: "Todos", value: "todos" },
        { text: "Activos", value: "activo" },
        { text: "Inactivos", value: "inactivo" },
      ],
      dialogDetalle: false,
      productoDetalle: null,
      dialogEditar: false,
      validEditarProducto: false,
      savingProducto: false,
      editProducto: {
        id: null,
        nombre: "",
        descripcion: "",
        precio_regular: 0,
        precio_oferta: 0,
        tipoproducto: "",
        negocio_nombre: "",
        estadoSwitch: true,
      },
      requiredRule: [(v) => !!v || "Campo requerido"],
    };
  },
  methods: {
    fetchProductos() {
      this.productos = null;
      axios
        .get(window.location.origin + "/admin/prodlist")
        .then((res) => {
          this.productosAll = Array.isArray(res.data) ? res.data : [];
          this.syncProductosPage();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    syncProductosPage() {
      const total = this.filteredProductos.length;
      const lastPage = total > 0 ? Math.ceil(total / this.pages) - 1 : 0;

      if (this.page < 0) this.page = 0;
      if (this.page > lastPage) this.page = lastPage;

      const start = this.page * this.pages;
      const end = start + this.pages;
      this.productos = this.filteredProductos.slice(start, end);

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
      this.syncProductosPage();
    },
    clearTextFilter() {
      this.filterText = "";
      this.applyFilters();
    },
    siguiente() {
      if (this.nextDisabled) return;
      this.page = Number(this.pagination.actual_page + 1);
      this.syncProductosPage();
    },
    previo() {
      if (this.prevDisabled) return;
      this.page = Number(this.pagination.actual_page - 1);
      this.syncProductosPage();
    },
    verDetalle(producto) {
      this.productoDetalle = JSON.parse(JSON.stringify(producto));
      this.dialogDetalle = true;
    },
    abrirEditar(producto) {
      this.editProducto = {
        id: producto.id,
        nombre: producto.nombre || "",
        descripcion: producto.descripcion || "",
        precio_regular: producto.precio_regular || 0,
        precio_oferta: producto.precio_oferta || 0,
        tipoproducto: producto.tipoproducto || "",
        negocio_nombre: producto.negocio_nombre || "",
        estadoSwitch: producto.estado_prod === "activo",
      };
      this.dialogEditar = true;
    },
    guardarProducto() {
      if (!this.$refs.formEditarProducto || !this.$refs.formEditarProducto.validate()) return;
      this.savingProducto = true;
      const payload = {
        nombre: this.editProducto.nombre,
        descripcion: this.editProducto.descripcion,
        precio_regular: this.editProducto.precio_regular,
        precio_oferta: this.editProducto.precio_oferta,
        tipoproducto: this.editProducto.tipoproducto,
        estado_prod: this.editProducto.estadoSwitch ? "activo" : "inactivo",
      };
      axios
        .put(window.location.origin + "/admin/productos/" + this.editProducto.id, payload)
        .then((resp) => {
          if (resp.data && resp.data.success) {
            const index = this.productos.findIndex((p) => p.id === this.editProducto.id);
            if (index > -1) {
              this.productos[index].nombre = payload.nombre;
              this.productos[index].descripcion = payload.descripcion;
              this.productos[index].precio_regular = payload.precio_regular;
              this.productos[index].precio_oferta = payload.precio_oferta;
              this.productos[index].tipoproducto = payload.tipoproducto;
              this.productos[index].estado_prod = payload.estado_prod;
            }
            if (this.productoDetalle && this.productoDetalle.id === this.editProducto.id) {
              this.productoDetalle.nombre = payload.nombre;
              this.productoDetalle.descripcion = payload.descripcion;
              this.productoDetalle.precio_regular = payload.precio_regular;
              this.productoDetalle.precio_oferta = payload.precio_oferta;
              this.productoDetalle.tipoproducto = payload.tipoproducto;
              this.productoDetalle.estado_prod = payload.estado_prod;
            }
            this.mensaje = "Producto actualizado con exito";
            this.snackbar = true;
            this.dialogEditar = false;
          }
        })
        .catch((error) => {
          console.log(error);
          alert("No se pudo actualizar el producto.");
        })
        .finally(() => {
          this.savingProducto = false;
        });
    },
    cambiarEstado(producto) {
      const nuevoEstado = producto.estado_prod === "activo" ? "inactivo" : "activo";
      axios
        .put(window.location.origin + "/admin/productos/state/" + producto.id, {
          state: nuevoEstado,
        })
        .then((resp) => {
          if (resp.data && resp.data.success) {
            producto.estado_prod = nuevoEstado;
            if (this.productoDetalle && this.productoDetalle.id === producto.id) {
              this.productoDetalle.estado_prod = nuevoEstado;
            }
            this.mensaje = `Producto ${nuevoEstado === "activo" ? "activado" : "desactivado"} con exito`;
            this.snackbar = true;
          }
        })
        .catch((error) => {
          console.log(error);
          alert("No se pudo actualizar el estado del producto.");
        });
    },
    formatDate(value) {
      if (!value) return "-";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return value;
      return date.toLocaleString();
    },
    resolveImageUrl(url) {
      if (!url) return "";
      if (url.startsWith("http://") || url.startsWith("https://")) return url;
      if (url.startsWith("/")) return window.location.origin + url;
      return window.location.origin + "/" + url;
    },
  },
  computed: {
    filteredProductos: function() {
      const all = Array.isArray(this.productosAll) ? this.productosAll : [];
      const text = (this.filterText || "").toString().trim().toLowerCase();
      const estado = this.filterEstado || "todos";
      return all.filter((p) => {
        const matchesText = !text ||
          ((p.nombre || "").toLowerCase().includes(text)) ||
          ((p.negocio_nombre || "").toLowerCase().includes(text));
        const matchesEstado = estado === "todos" || (p.estado_prod || "") === estado;
        return matchesText && matchesEstado;
      });
    },
    productosCounter: function() {
      if (!this.productos || !this.pagination) return [];
      var counter = Number(this.pagination.actual_page * this.pages + 1);
      var res = this.productos.map(function(producto) {
        producto.counter = counter;
        counter++;
        return producto;
      });
      return res;
    },
  },
  created() {
    this.fetchProductos();
  },
};
</script>

<style scoped>
.acciones-producto {
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
.accion-estado {
  border: none !important;
}
.estado-desactivar {
  background: linear-gradient(90deg, #c9ced4 0%, #8d969f 100%) !important;
}
.estado-activar {
  background: linear-gradient(90deg, #87d6aa 0%, #4dae79 100%) !important;
}
.producto-avatar {
  border: 1px solid #d7dce2;
  background: #f2f4f7;
}
.avatar-fallback {
  font-size: 11px;
  color: #7f8b99;
}
.detalle-foto-wrap {
  width: 100%;
  max-height: 220px;
  overflow: hidden;
  border-radius: 8px;
  border: 1px solid #e3e7ec;
  background: linear-gradient(135deg, #f6f8fb 0%, #edf2f9 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}
.detalle-foto {
  width: 100%;
  max-height: 220px;
  object-fit: cover;
}
.detalle-foto-empty {
  color: #7c8795;
  font-size: 13px;
  padding: 24px;
}
.table-responsive {
  width: 100%;
  overflow-x: auto;
}
@media (max-width: 960px) {
  .acciones-producto {
    justify-content: flex-start;
    margin-top: 4px;
  }
}
</style>
