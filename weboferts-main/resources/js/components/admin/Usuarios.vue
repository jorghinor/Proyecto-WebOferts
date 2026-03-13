<template>
  <v-app id="inspire">
    <navbar></navbar>
    <v-main>
      <v-container>
        <v-card>
          <v-toolbar height="80px">
            <v-toolbar-title>Usuarios</v-toolbar-title>
            <v-spacer></v-spacer>
            <nuevousuario @updateList="nuevoUsuario"></nuevousuario>
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
                label="Filtrar por nombre, email o negocio"
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
            v-if="!usuarios"
            :color="`grey ${theme.isDark ? 'darken-2' : 'lighten-4'}`"
            class="px-3 pt-3 pb-3"
          >
            <v-skeleton-loader
              class="mx-auto"
              max-width="95vh"
              type="table-heading, table-thead, table-tbody, table-tfoot"
            ></v-skeleton-loader>
          </v-sheet>

          <template v-else style="height: 90vh">
          <div v-for=" (usuario) in usuariosCounter" :key="usuario.id"  >
            <v-row no-gutters class="py-1">
              <v-col cols="6" sm="1" md="1">
                <div class="px-2 font-weight-thin">##</div>
                <div class="py-4 px-2 font-weight-light">{{ usuario.counter }}.-</div>
              </v-col>
              <v-col cols="6" sm="2" md="2" class="px-2 text-left">
                <div class="font-weight-thin caption">Nombre</div>
                <div class="py-1 font-weight-light caption">
                  {{ usuario.name }}
                </div>
              </v-col>
              <v-col cols="6" sm="2" md="2" class="px-2text-left">
                <div class="font-weight-thin caption">E-mail</div>
                <div class="py-1 font-weight-light caption">
                  {{ usuario.email }}
                </div>
              </v-col>
              <v-col cols="6" sm="2" md="2" class="px-2 text-left">
                <div class="font-weight-thin caption">Negocio</div>
                <v-chip small color="primary">{{ usuario.nnegocio }}</v-chip>
              </v-col>
              <v-col cols="6" sm="2" md="2" class="px-2 text-left">
                <div class="font-weight-thin caption">Estado</div>
                <v-chip v-if="usuario.estadou=='activo'" x-small class="success black--text">{{ usuario.estadou }}</v-chip>
                <v-chip v-else x-small class="danger white--text">{{ usuario.estadou }}</v-chip>
              </v-col>
              <v-col cols="12" sm="2" md="2" class="pt-2 text-center">
                <div class="acciones-usuario">
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        small
                        outlined
                        color="primary"
                        class="accion-btn"
                        v-bind="attrs"
                        v-on="on"
                        @click="verDetalle(usuario)"
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
                        @click="abrirEditar(usuario)"
                      >
                        <v-icon small>mdi-pencil-outline</v-icon>
                      </v-btn>
                    </template>
                    <span>Editar usuario</span>
                  </v-tooltip>

                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        small
                        class="accion-btn accion-estado"
                        :class="usuario.estadou=='activo' ? 'estado-desactivar' : 'estado-activar'"
                        v-bind="attrs"
                        v-on="on"
                        @click="cambiarEstado(usuario)"
                      >
                        <v-icon small class="white--text">
                          {{ usuario.estadou=='activo' ? 'mdi-power' : 'mdi-power-plug' }}
                        </v-icon>
                      </v-btn>
                    </template>
                    <span>{{ usuario.estadou=='activo' ? 'Desactivar usuario' : 'Activar usuario' }}</span>
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
          <v-card v-if="usuarioDetalle">
            <v-toolbar dense flat color="primary">
              <v-toolbar-title class="white--text">Detalle de Usuario</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon dark @click="dialogDetalle = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>
            <v-card-text class="pt-4">
              <v-row dense>
                <v-col cols="12" class="text-center">
                  <v-avatar size="86" class="avatar-usuario-bg">
                    <v-icon x-large color="primary">mdi-account-circle</v-icon>
                  </v-avatar>
                </v-col>
                <v-col cols="12" sm="6"><strong>Nombre:</strong> {{ usuarioDetalle.name || '-' }}</v-col>
                <v-col cols="12" sm="6"><strong>Email:</strong> {{ usuarioDetalle.email || '-' }}</v-col>
                <v-col cols="12" sm="6"><strong>Negocio:</strong> {{ usuarioDetalle.nnegocio || '-' }}</v-col>
                <v-col cols="12" sm="6">
                  <strong>Estado:</strong>
                  <v-chip
                    x-small
                    :class="usuarioDetalle.estadou=='activo' ? 'success black--text' : 'danger white--text'"
                  >
                    {{ usuarioDetalle.estadou || '-' }}
                  </v-chip>
                </v-col>
                <v-col cols="12" sm="6"><strong>Creado:</strong> {{ formatDate(usuarioDetalle.created_at) }}</v-col>
                <v-col cols="12" sm="6"><strong>Actualizado:</strong> {{ formatDate(usuarioDetalle.updated_at) }}</v-col>
              </v-row>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn text color="grey darken-1" @click="dialogDetalle = false">Cerrar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="dialogEditar" max-width="560">
          <v-card>
            <v-toolbar dense flat color="primary">
              <v-toolbar-title class="white--text">Editar Usuario</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon dark @click="dialogEditar = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>
            <v-card-text class="pt-4">
              <v-form ref="formEditarUsuario" v-model="validEditarUsuario">
                <v-text-field
                  label="Nombre"
                  v-model="editUsuario.name"
                  :rules="requiredRule"
                  outlined
                  dense
                ></v-text-field>
                <v-text-field
                  label="Email"
                  v-model="editUsuario.email"
                  :rules="emailRules"
                  outlined
                  dense
                ></v-text-field>
                <v-text-field
                  label="Negocio"
                  :value="editUsuario.nnegocio || '-'"
                  disabled
                  outlined
                  dense
                ></v-text-field>
                <v-switch
                  v-model="editUsuario.estadoSwitch"
                  :label="editUsuario.estadoSwitch ? 'Activo' : 'Inactivo'"
                ></v-switch>
              </v-form>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn text color="grey darken-1" @click="dialogEditar = false">Cancelar</v-btn>
              <v-btn color="primary" :loading="savingUsuario" :disabled="!validEditarUsuario" @click="guardarUsuario">
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
import NuevoUsuario from './NuevoUsuario.vue';

export default {
  inject: {
      theme: {
        default: { isDark: false },
      },
  },
  components: {
    'nuevousuario': NuevoUsuario,
  },
  name: "adminegocios",
  data() {
    return {
      drawer: null,
      usuarios: [],
      id: 0,
      snackbar: false,
      mensaje: "",
      page: 0,
      pages: 50,
      pagination: null,
      prevDisabled: true,
      nextDisabled: true,
      dialogDetalle: false,
      usuarioDetalle: null,
      dialogEditar: false,
      validEditarUsuario: false,
      savingUsuario: false,
      editUsuario: {
        id: null,
        name: "",
        email: "",
        nnegocio: "",
        estadoSwitch: true,
      },
      requiredRule: [(v) => !!v || "Campo requerido"],
      emailRules: [
        (v) => !!v || "E-mail es requerido",
        (v) => /.+@.+\..+/.test(v) || "E-mail invalido",
      ],
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
    nuevoUsuario(data) {
      this.usuarios.unshift(data);
      this.mensaje = "Usuario registrado con exito";
      this.snackbar = true;
    },
    actualizarNegocio(data) {
      this.usuarios.map((usu) => {
        if (usu.id == data.id) {
          usu.name = data.name;
          usu.email = data.email;
          usu.estadou = data.estadou;
        }
      });
      this.mensaje = "Usuario Actualizado!";
      this.snackbar = true;
    },
    verDetalle(usuario) {
      this.usuarioDetalle = JSON.parse(JSON.stringify(usuario));
      this.dialogDetalle = true;
    },
    abrirEditar(usuario) {
      this.editUsuario = {
        id: usuario.id,
        name: usuario.name || "",
        email: usuario.email || "",
        nnegocio: usuario.nnegocio || "",
        estadoSwitch: usuario.estadou == "activo",
      };
      this.dialogEditar = true;
    },
    guardarUsuario() {
      if (!this.$refs.formEditarUsuario || !this.$refs.formEditarUsuario.validate()) return;
      this.savingUsuario = true;
      const payload = {
        name: this.editUsuario.name,
        email: this.editUsuario.email,
        estadou: this.editUsuario.estadoSwitch ? "activo" : "inactivo",
      };
      axios
        .put(window.location.origin + "/admin/usuarios/" + this.editUsuario.id, payload)
        .then((resp) => {
          if (resp.data && resp.data.success) {
            this.actualizarNegocio({
              id: this.editUsuario.id,
              name: payload.name,
              email: payload.email,
              estadou: payload.estadou,
            });
            if (this.usuarioDetalle && this.usuarioDetalle.id == this.editUsuario.id) {
              this.usuarioDetalle.name = payload.name;
              this.usuarioDetalle.email = payload.email;
              this.usuarioDetalle.estadou = payload.estadou;
            }
            this.dialogEditar = false;
          }
        })
        .catch((error) => {
          console.log(error);
          alert("No se pudo actualizar el usuario.");
        })
        .finally(() => {
          this.savingUsuario = false;
        });
    },
    cambiarEstado(usuario) {
      const nuevoEstado = usuario.estadou == "activo" ? "inactivo" : "activo";
      axios
        .put(window.location.origin + "/admin/usuarios/state/" + usuario.id, {
          state: nuevoEstado,
        })
        .then((resp) => {
          if (resp.data && resp.data.success) {
            usuario.estadou = nuevoEstado;
            if (this.usuarioDetalle && this.usuarioDetalle.id == usuario.id) {
              this.usuarioDetalle.estadou = nuevoEstado;
            }
            this.mensaje = `Usuario ${nuevoEstado == "activo" ? "activado" : "desactivado"} con exito`;
            this.snackbar = true;
          }
        })
        .catch((error) => {
          console.log(error);
          alert("No se pudo actualizar el estado del usuario.");
        });
    },
    formatDate(value) {
      if (!value) return "-";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return value;
      return date.toLocaleString();
    },
    getImage(image) {
      if (image == "" || image == null) {
        return window.location.origin + "/storage/images/default.jpeg";
      } else {
        return image.substring(0, image.lastIndexOf("/")) +"/thumbnail/" + image.substring(image.lastIndexOf("/") + 1)
      }
    },
    siguiente() {
      this.page = Number(this.pagination.actual_page + 1);
      this.fetchUsuarios();
    },
    previo() {
      this.page = Number(this.pagination.actual_page - 1);
      if (this.page >= 0) {
        this.fetchUsuarios();
      }
    },
    fetchUsuarios() {
      this.usuarios = null;
      this.total = 0;
      let formData = {
        page: this.page,
        pages: this.pages,
        texto: this.filterText,
        estado: this.filterEstado,
      };
      axios({
        url: window.location.origin + "/admin/usuarios/list",
        data: formData,
        method: "POST",
      })
        .then((resp) => {
          this.usuarios = resp.data.result.data;
          this.pagination = {
            first_page: resp.data.result.pagination.first_page,
            actual_page: resp.data.result.pagination.actual_page,
            next_page: resp.data.result.pagination.next_page,
            total: resp.data.result.pagination.total,
            pre_page: resp.data.result.pagination.pre_page,
            pages: resp.data.result.pagination.pages,
            last_page: resp.data.result.pagination.last_page,
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
      this.fetchUsuarios();
    },
    clearTextFilter() {
      this.filterText = "";
      this.onFiltersChanged();
    },
  },

  computed: {
    usuariosCounter: function() {
      if (!this.usuarios || !this.pagination) return [];
      var counter = Number(this.pagination.actual_page*this.pages +1);
      var res = this.usuarios.map(function(usuario) {
        usuario.counter = counter;
        counter++;
        return usuario;
      });
      return res;
    }
  },

  created() {
    this.fetchUsuarios();
  },
};
</script>
<style scoped>
.acciones-usuario {
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
.avatar-usuario-bg {
  background: linear-gradient(180deg, #f7fbff 0%, #eef5fb 100%);
  border: 1px solid #d8e6f2;
}
@media (max-width: 960px) {
  .acciones-usuario {
    justify-content: flex-start;
    margin-top: 4px;
  }
}
</style>
