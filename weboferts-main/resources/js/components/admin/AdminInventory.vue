<template>
  <v-app id="inspire">
    <navbar></navbar>
    <v-main>
      <v-container>
        <v-card>
          <v-toolbar height="80px">
            <v-toolbar-title>Gestión de Inventario</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn color="primary" class="mr-2" href="/admin/inventory/pdf" target="_blank">
              <v-icon left>mdi-file-pdf</v-icon> Reporte PDF
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
                label="Buscar Producto"
                @keyup.enter="fetchInventory"
                @click:clear="search = ''; fetchInventory()"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4" class="py-1">
               <v-btn color="success" class="white--text" @click="fetchInventory" block>Buscar</v-btn>
            </v-col>
          </v-row>

          <v-data-table
            :headers="headers"
            :items="products"
            :loading="loading"
            :options.sync="options"
            :server-items-length="totalProducts"
            class="elevation-0"
          >
            <template v-slot:item.stock="{ item }">
              <v-chip :color="getStockColor(item.stock)" dark small>
                {{ item.stock }}
              </v-chip>
            </template>
            <template v-slot:item.negocio="{ item }">
              {{ item.negocio ? item.negocio.nnegocio : 'N/A' }}
            </template>
            <template v-slot:item.actions="{ item }">
              <div class="d-flex">
                <v-tooltip top>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn small outlined color="primary" class="mr-2" v-bind="attrs" v-on="on" @click="openHistory(item)">
                      <v-icon small>mdi-history</v-icon>
                    </v-btn>
                  </template>
                  <span>Ver Kardex</span>
                </v-tooltip>

                <v-tooltip top>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn small outlined color="green" v-bind="attrs" v-on="on" @click="openAddStock(item)">
                      <v-icon small>mdi-plus-box</v-icon>
                    </v-btn>
                  </template>
                  <span>Agregar Stock</span>
                </v-tooltip>
              </div>
            </template>
          </v-data-table>
        </v-card>

        <!-- Modal Agregar Stock -->
        <v-dialog v-model="dialogAdd" max-width="400px">
          <v-card>
            <v-card-title class="headline">Reponer Stock</v-card-title>
            <v-card-text>
              <p>Producto: <strong>{{ selectedProduct ? selectedProduct.nombre : '' }}</strong></p>
              <v-form ref="formAdd" v-model="validAdd">
                <v-text-field
                  v-model="addForm.quantity"
                  label="Cantidad a agregar"
                  type="number"
                  :rules="[v => !!v || 'Requerido', v => v > 0 || 'Debe ser mayor a 0']"
                  outlined
                  dense
                ></v-text-field>
                <v-text-field
                  v-model="addForm.reason"
                  label="Motivo / Referencia"
                  placeholder="Ej: Compra Factura 123"
                  :rules="[v => !!v || 'Requerido']"
                  outlined
                  dense
                ></v-text-field>
              </v-form>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="grey" text @click="dialogAdd = false">Cancelar</v-btn>
              <v-btn color="success" text @click="saveStock" :disabled="!validAdd">Guardar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <!-- Modal Historial (Kardex) -->
        <v-dialog v-model="dialogHistory" max-width="700px">
          <v-card>
            <v-toolbar dense flat color="primary">
              <v-toolbar-title class="white--text">Kardex: {{ selectedProduct ? selectedProduct.nombre : '' }}</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon dark @click="dialogHistory = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>
            <v-card-text class="pt-4">
              <v-simple-table dense>
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">Fecha</th>
                      <th class="text-left">Tipo</th>
                      <th class="text-left">Referencia</th>
                      <th class="text-right">Cantidad</th>
                      <th class="text-right">Saldo</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="mov in movements" :key="mov.id">
                      <td>{{ formatDate(mov.created_at) }}</td>
                      <td>
                        <v-chip x-small :color="getMovColor(mov.type)" dark>{{ mov.type }}</v-chip>
                      </td>
                      <td>{{ mov.reference }}</td>
                      <td class="text-right font-weight-bold" :class="mov.quantity > 0 ? 'green--text' : 'red--text'">
                        {{ mov.quantity > 0 ? '+' : '' }}{{ mov.quantity }}
                      </td>
                      <td class="text-right">{{ mov.stock_balance }}</td>
                    </tr>
                    <tr v-if="movements.length === 0">
                      <td colspan="5" class="text-center">No hay movimientos registrados.</td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-card-text>
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
  name: "AdminInventory",
  data() {
    return {
      search: '',
      products: [],
      loading: true,
      totalProducts: 0,
      options: {},
      dialogAdd: false,
      dialogHistory: false,
      validAdd: false,
      selectedProduct: null,
      movements: [],
      addForm: {
        quantity: '',
        reason: ''
      },
      snackbar: false,
      mensaje: '',
      headers: [
        { text: 'ID', value: 'id' },
        { text: 'Producto', value: 'nombre' },
        { text: 'Negocio', value: 'negocio' },
        { text: 'Stock Actual', value: 'stock' },
        { text: 'Precio', value: 'precio_regular' },
        { text: 'Acciones', value: 'actions', sortable: false },
      ],
    };
  },
  watch: {
    options: {
      handler() {
        this.fetchInventory();
      },
      deep: true,
    },
  },
  methods: {
    fetchInventory() {
      this.loading = true;
      const { page, itemsPerPage } = this.options;

      axios.post('/admin/lista/inventory', {
        page: page - 1,
        pages: itemsPerPage,
        texto: this.search,
      })
      .then(response => {
        this.products = response.data.result.data;
        this.totalProducts = response.data.result.pagination.total;
      })
      .catch(error => {
        console.error("Error cargando inventario:", error);
      })
      .finally(() => {
        this.loading = false;
      });
    },
    getStockColor(stock) {
      if (stock <= 5) return 'red';
      if (stock <= 20) return 'orange';
      return 'green';
    },
    getMovColor(type) {
      if (type === 'sale') return 'red';
      if (type === 'restock') return 'green';
      return 'blue';
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
      return new Date(dateString).toLocaleDateString('es-ES', options);
    },
    openAddStock(item) {
      this.selectedProduct = item;
      this.addForm = { quantity: '', reason: '' };
      this.dialogAdd = true;
    },
    saveStock() {
      if (!this.$refs.formAdd.validate()) return;

      axios.post(`/admin/inventory/${this.selectedProduct.id}/add`, this.addForm)
        .then(() => {
          this.fetchInventory();
          this.dialogAdd = false;
          this.mensaje = "Stock actualizado correctamente";
          this.snackbar = true;
        })
        .catch(error => {
          console.error("Error actualizando stock:", error);
          alert("Error al actualizar stock.");
        });
    },
    openHistory(item) {
      this.selectedProduct = item;
      this.movements = [];
      this.dialogHistory = true;

      axios.get(`/admin/inventory/${item.id}/movements`)
        .then(response => {
          this.movements = response.data.data;
        })
        .catch(error => {
          console.error("Error cargando historial:", error);
        });
    }
  }
};
</script>
