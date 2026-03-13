<template>
  <v-app id="inspire">
    <navbar></navbar>
    <v-main>
      <v-container>
        <v-card>
          <v-toolbar height="80px">
            <v-toolbar-title>Gestión de Pedidos</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn color="success" class="white--text" @click="dialogCreate = true">
              <v-icon left>mdi-plus</v-icon> Nuevo Pedido
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
                label="Buscar por Cliente o ID"
                @keyup.enter="fetchPedidos"
                @click:clear="search = ''; fetchPedidos()"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4" class="py-1">
               <v-btn color="success" class="white--text" @click="fetchPedidos" block>Buscar</v-btn>
            </v-col>
          </v-row>

          <v-data-table
            :headers="headers"
            :items="pedidos"
            :loading="loading"
            :options.sync="options"
            :server-items-length="totalPedidos"
            class="elevation-0"
          >
            <template v-slot:item.total="{ item }">
              <span class="font-weight-bold">{{ item.total }} Bs.</span>
            </template>
            <template v-slot:item.created_at="{ item }">
              {{ formatDate(item.created_at) }}
            </template>
            <template v-slot:item.estado="{ item }">
              <v-chip :color="getStateColor(item.estado)" dark small>
                {{ item.estado }}
              </v-chip>
            </template>
            <template v-slot:item.actions="{ item }">
              <div class="d-flex">
                <v-tooltip top>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn small outlined color="primary" class="mr-2" v-bind="attrs" v-on="on" @click="viewDetails(item)">
                      <v-icon small>mdi-eye-outline</v-icon>
                    </v-btn>
                  </template>
                  <span>Ver Detalles</span>
                </v-tooltip>

                <v-tooltip top>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn small outlined color="orange" class="mr-2" v-bind="attrs" v-on="on" @click="editState(item)">
                      <v-icon small>mdi-pencil-outline</v-icon>
                    </v-btn>
                  </template>
                  <span>Cambiar Estado</span>
                </v-tooltip>

                <v-tooltip top>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn small outlined color="red" v-bind="attrs" v-on="on" @click="deleteItem(item)">
                      <v-icon small>mdi-delete-outline</v-icon>
                    </v-btn>
                  </template>
                  <span>Eliminar</span>
                </v-tooltip>
              </div>
            </template>
          </v-data-table>
        </v-card>

        <!-- Modal Crear Pedido -->
        <v-dialog v-model="dialogCreate" max-width="500px">
          <v-card>
            <v-card-title class="headline">Nuevo Pedido Manual</v-card-title>
            <v-card-text>
              <v-form ref="formCreate" v-model="validCreate">
                <v-text-field
                  v-model="newPedido.nombre_cliente"
                  label="Nombre Cliente"
                  :rules="[v => !!v || 'Requerido']"
                  outlined
                  dense
                ></v-text-field>
                <v-text-field
                  v-model="newPedido.telefono_cliente"
                  label="Teléfono"
                  outlined
                  dense
                ></v-text-field>
                <v-text-field
                  v-model="newPedido.total"
                  label="Total (Bs)"
                  type="number"
                  :rules="[v => !!v || 'Requerido']"
                  outlined
                  dense
                ></v-text-field>
                <v-select
                  v-model="newPedido.estado"
                  :items="['pendiente', 'pagado', 'completado']"
                  label="Estado Inicial"
                  outlined
                  dense
                ></v-select>
              </v-form>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="grey" text @click="dialogCreate = false">Cancelar</v-btn>
              <v-btn color="success" text @click="saveNewPedido" :disabled="!validCreate">Guardar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <!-- Modal de Detalles del Pedido -->
        <v-dialog v-model="dialog" max-width="700px">
          <v-card v-if="selectedPedido">
            <v-toolbar dense flat color="primary">
              <v-toolbar-title class="white--text">Detalles del Pedido #{{ selectedPedido.id }}</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon dark @click="dialog = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>
            <v-card-text class="pt-4">
              <v-row>
                <v-col cols="6">
                  <p><strong>Cliente:</strong> {{ selectedPedido.nombre_cliente }}</p>
                  <p><strong>Teléfono:</strong> {{ selectedPedido.telefono_cliente || 'No especificado' }}</p>
                </v-col>
                <v-col cols="6" class="text-right">
                  <p><strong>Fecha:</strong> {{ formatDate(selectedPedido.created_at) }}</p>
                  <p><strong>Estado:</strong> <v-chip small :color="getStateColor(selectedPedido.estado)" dark>{{ selectedPedido.estado }}</v-chip></p>
                </v-col>
              </v-row>

              <v-divider class="my-3"></v-divider>

              <v-simple-table dense>
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">Producto</th>
                      <th class="text-center">Cantidad</th>
                      <th class="text-right">Precio Unit.</th>
                      <th class="text-right">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="detalle in selectedPedido.detalles" :key="detalle.id">
                      <td>{{ detalle.nombre_producto }}</td>
                      <td class="text-center">{{ detalle.cantidad }}</td>
                      <td class="text-right">{{ detalle.precio_unitario }} Bs.</td>
                      <td class="text-right">{{ detalle.subtotal }} Bs.</td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="3" class="text-right font-weight-bold">Total:</td>
                      <td class="text-right font-weight-bold">{{ selectedPedido.total }} Bs.</td>
                    </tr>
                  </tfoot>
                </template>
              </v-simple-table>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="grey darken-1" text @click="dialog = false">Cerrar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <!-- Modal Editar Estado -->
        <v-dialog v-model="dialogState" max-width="400px">
          <v-card>
            <v-card-title class="headline">Cambiar Estado</v-card-title>
            <v-card-text>
              <v-select
                v-model="newState"
                :items="['pendiente', 'pagado', 'completado', 'cancelado']"
                label="Estado del Pedido"
                outlined
              ></v-select>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="grey" text @click="dialogState = false">Cancelar</v-btn>
              <v-btn color="success" text @click="saveState">Guardar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <!-- Modal Confirmar Eliminación -->
        <v-dialog v-model="dialogDelete" max-width="400px">
          <v-card>
            <v-card-title class="headline">¿Eliminar Pedido?</v-card-title>
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
  name: "AdminPedidos",
  data() {
    return {
      search: '',
      pedidos: [],
      loading: true,
      totalPedidos: 0,
      options: {},
      dialog: false,
      dialogState: false,
      dialogDelete: false,
      dialogCreate: false,
      validCreate: false,
      selectedPedido: null,
      newState: '',
      snackbar: false,
      mensaje: '',
      newPedido: {
        nombre_cliente: '',
        telefono_cliente: '',
        total: '',
        estado: 'pendiente'
      },
      headers: [
        { text: 'ID', value: 'id' },
        { text: 'Cliente', value: 'nombre_cliente' },
        { text: 'Total', value: 'total' },
        { text: 'Fecha', value: 'created_at' },
        { text: 'Estado', value: 'estado' },
        { text: 'Acciones', value: 'actions', sortable: false },
      ],
    };
  },
  watch: {
    options: {
      handler() {
        this.fetchPedidos();
      },
      deep: true,
    },
  },
  methods: {
    fetchPedidos() {
      this.loading = true;
      const { page, itemsPerPage } = this.options;

      axios.post('/admin/lista/pedidos', {
        page: page - 1,
        pages: itemsPerPage,
        texto: this.search,
      })
      .then(response => {
        this.pedidos = response.data.result.data;
        this.totalPedidos = response.data.result.pagination.total;
      })
      .catch(error => {
        console.error("Error cargando pedidos:", error);
      })
      .finally(() => {
        this.loading = false;
      });
    },
    saveNewPedido() {
      if (!this.$refs.formCreate.validate()) return;

      axios.post('/admin/pedidos', this.newPedido) // Asumiendo ruta resource o custom
        .then(() => {
          this.fetchPedidos();
          this.dialogCreate = false;
          this.mensaje = "Pedido creado correctamente";
          this.snackbar = true;
          this.newPedido = { nombre_cliente: '', telefono_cliente: '', total: '', estado: 'pendiente' };
        })
        .catch(error => {
          console.error("Error creando pedido:", error);
        });
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
      return new Date(dateString).toLocaleDateString('es-ES', options);
    },
    getStateColor(estado) {
      if (estado === 'pendiente') return 'orange';
      if (estado === 'pagado') return 'blue';
      if (estado === 'completado') return 'green';
      if (estado === 'cancelado') return 'red';
      return 'grey';
    },
    viewDetails(item) {
      this.selectedPedido = item;
      this.dialog = true;
    },
    editState(item) {
      this.selectedPedido = item;
      this.newState = item.estado;
      this.dialogState = true;
    },
    saveState() {
      axios.put(`/admin/pedidos/${this.selectedPedido.id}`, { estado: this.newState })
        .then(() => {
          this.fetchPedidos();
          this.dialogState = false;
          this.mensaje = "Estado actualizado correctamente";
          this.snackbar = true;
        });
    },
    deleteItem(item) {
      this.selectedPedido = item;
      this.dialogDelete = true;
    },
    confirmDelete() {
      axios.delete(`/admin/pedidos/${this.selectedPedido.id}`)
        .then(() => {
          this.fetchPedidos();
          this.dialogDelete = false;
          this.mensaje = "Pedido eliminado correctamente";
          this.snackbar = true;
        });
    }
  }
};
</script>
