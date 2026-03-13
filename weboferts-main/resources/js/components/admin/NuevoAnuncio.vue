<template>
  <v-row justify="center">
    <v-dialog v-model="dialog" persistent max-width="800px">
      <template v-slot:activator="{ on, attrs }">
        <v-btn class="success black--text" dark large v-bind="attrs" v-on="on">
          Nuevo Anuncio
        </v-btn>
      </template>
      <v-card>
        <v-card-title>
          <span class="headline">Nuevo Anuncio (Admin)</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form ref="form" v-model="valid" :lazy-validation="lazy">
              <v-row>
                <v-col cols="12" sm="6">
                  <v-select
                    label="Seleccionar Negocio*"
                    v-model="selectedNegocio"
                    :items="negocios"
                    item-text="nnegocio"
                    item-value="id"
                    :rules="requiredRule"
                    @change="onNegocioChange"
                    rounded
                    filled
                    required
                  ></v-select>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-select
                    label="Seleccionar Producto*"
                    v-model="selectedProducto"
                    :items="productos"
                    item-text="nombre"
                    item-value="id"
                    :rules="requiredRule"
                    :disabled="!selectedNegocio"
                    rounded
                    filled
                    required
                  ></v-select>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    label="Título del Anuncio*"
                    autocomplete="off"
                    v-model="titulo"
                    :rules="requiredRule"
                    rounded
                    filled
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-textarea
                    label="Descripción*"
                    v-model="descripcion"
                    :rules="requiredRule"
                    autocomplete="off"
                    rounded
                    filled
                    required
                  ></v-textarea>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-select
                    label="Seleccionar Paquete*"
                    v-model="selectedPaquete"
                    :items="paquetes"
                    item-text="titulo"
                    item-value="id"
                    :rules="requiredRule"
                    rounded
                    filled
                    required
                  >
                    <template v-slot:item="{ item }">
                      {{ item.titulo }} - {{ item.costo }} Bs. ({{ item.tiempo }} días)
                    </template>
                    <template v-slot:selection="{ item }">
                      {{ item.titulo }} - {{ item.costo }} Bs.
                    </template>
                  </v-select>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-file-input
                    v-model="imagen"
                    label="Imagen del Anuncio"
                    accept="image/*"
                    prepend-icon="mdi-camera"
                    rounded
                    filled
                    show-size
                    truncate-length="15"
                  ></v-file-input>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
          <small>*campo obligatorio</small>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="dialog = false">
            Cerrar
          </v-btn>
          <v-btn
            color="primary"
            rounded
            @click="crear"
            :disabled="!valid"
            :loading="loading"
          >
            Crear Anuncio
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
export default {
  name: "NuevoAnuncioAdmin",
  data() {
    return {
      dialog: false,
      lazy: false,
      valid: false,
      loading: false,

      titulo: "",
      descripcion: "",
      selectedNegocio: null,
      selectedProducto: null,
      selectedPaquete: null, // Nuevo campo para paquete
      imagen: null,

      negocios: [],
      productos: [],
      paquetes: [], // Lista de paquetes

      requiredRule: [(v) => !!v || "Este campo es requerido"],
    };
  },
  methods: {
    fetchNegocios() {
      axios.get(window.location.origin + "/admin/neglist")
        .then((res) => {
          this.negocios = res.data || [];
        })
        .catch((err) => console.error(err));
    },
    fetchPaquetes() {
      axios.get(window.location.origin + "/admin/paqlist")
        .then((res) => {
          // La respuesta de paqlist puede variar, ajusta según sea necesario
          // Asumiendo que devuelve un array de paquetes directamente o dentro de 'data'
          this.paquetes = Array.isArray(res.data) ? res.data : (res.data.data || []);
        })
        .catch((err) => console.error(err));
    },
    onNegocioChange() {
      this.selectedProducto = null;
      this.productos = [];
      if (this.selectedNegocio) {
        const negocio = this.negocios.find(n => n.id === this.selectedNegocio);
        if (negocio && negocio.productos) {
          this.productos = negocio.productos;
        }
      }
    },
    crear() {
      if (this.$refs.form.validate()) {
        this.loading = true;

        let formData = new FormData();
        formData.append('titulo', this.titulo);
        formData.append('descripcion', this.descripcion);
        formData.append('negocio_id', this.selectedNegocio);
        formData.append('producto_id', this.selectedProducto);
        formData.append('paquete_id', this.selectedPaquete); // Enviar paquete

        if (this.imagen) {
          formData.append('imagen', this.imagen);
        }

        axios.post(window.location.origin + "/admin/anuncios", formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
        })
          .then((resp) => {
            if (resp.data.success) {
              this.dialog = false;
              this.$emit("updateList", resp.data.data);
              // Limpiar campos
              this.titulo = "";
              this.descripcion = "";
              this.selectedNegocio = null;
              this.selectedProducto = null;
              this.selectedPaquete = null;
              this.imagen = null;
              this.$refs.form.reset();
            }
          })
          .catch((err) => {
            console.error(err);
          })
          .finally(() => {
            this.loading = false;
          });
      }
    },
  },
  watch: {
    dialog(val) {
      if (val) {
        this.fetchNegocios();
        this.fetchPaquetes(); // Cargar paquetes al abrir el diálogo
      } else {
        this.$refs.form.reset();
        this.selectedNegocio = null;
        this.selectedProducto = null;
        this.selectedPaquete = null;
        this.imagen = null;
      }
    }
  }
};
</script>
