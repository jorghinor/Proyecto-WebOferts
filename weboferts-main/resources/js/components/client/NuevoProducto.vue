<template>
  <v-row justify="center">
    <v-dialog v-model="dialog" persistent max-width="600px">
      <template v-slot:activator="{ on, attrs }">
        <v-btn class="success black--text" dark large v-bind="attrs" v-on="on">
          Nuevo Producto
        </v-btn>
      </template>
      <v-card>
        <v-card-title>
          <span class="headline">Nuevo Producto</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form ref="form" v-model="valid" :lazy-validation="lazy">
              <v-row dense>
                <v-col cols="12">
                  <v-text-field dense
                    label="Nombre*"
                    autocomplete="off"
                    v-model="nombre"
                    :rules="requiredRule"
                    :error-messages="errorNombre"
                    rounded
                    required
                    filled
                    style="text-transform: capitalize;"
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-textarea dense
                    label="Descripción"
                    :rules="requiredRule"
                    :error-messages="errorDesc"
                    v-model="descripcion"
                    rounded
                    filled
                    rows="2"
                  ></v-textarea>
                </v-col>
                <v-col cols="12">
                  <v-text-field dense
                    label="Precio Regular"
                    v-model="precio_regular"
                    autocomplete="off"
                    rounded
                    filled
                    suffix="Bs."
                    type="number"
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field dense
                    label="Precio Oferta"
                    v-model="precio_oferta"
                    autocomplete="off"
                    rounded
                    filled
                    suffix="Bs."
                    type="number"
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field dense
                    label="Tipo Producto"
                    autocomplete="off"
                    v-model="tipoproducto"
                    rounded
                    required
                    filled
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <div class="subtitle-2 mb-2">Imagen del producto</div>
                  <v-img
                    :src="imagenPreview || '/assets/img/logo.png'"
                    max-height="180"
                    contain
                    class="grey lighten-4 rounded-lg mb-3"
                  ></v-img>
                  <clipperimage2
                    @setPhoto="setPhoto"
                    @cancel="cancelarFoto"
                  ></clipperimage2>
                  <small class="d-block mt-2 grey--text text--darken-1">
                    Opcional. Si no cargas una imagen propia, el sistema seguirá usando la del anuncio asociado cuando exista.
                  </small>
                  <small v-if="errorImagen" class="red--text d-block mt-2">
                    {{ errorImagen }}
                  </small>
                </v-col>
              </v-row>
            </v-form>
          </v-container>
          <small>*campo obligatorio</small>
          <small v-if="!negocio || !negocio.id" class="red--text d-block mt-2">
            Debes registrar tu negocio antes de crear productos.
          </small>
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
            :disabled="!valid || !negocio || !negocio.id"
            :loading="loading"
          >
            Crear
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
export default {

  name: "NuevoProducto",
  props: {
    //negocio_id: Integer,
    negocio: Object,
  },
  data() {
    return {
      dialog: false,
      lazy: false,
      valid: false,
      disabled: false,
      loading: false,

      nombre: "",
      errorNombre: "",
      requiredRule: [(v) => !!v || "Este Campo es requerido"],
      descripcion: "",
      errorDesc: "",
      //requiredRule: [(v) => !!v || "Este Campo es requerido"],
      precio_regular: 0,
      precio_oferta: 0,
      //negocio_id: 0,
      id: 0,
      //errorCosto: "",
       //numberRule: v  => {
         //if (!v.trim()) return true;
         //if (!isNaN(parseFloat(v)) && v >= 0 && v <= 999) return true;
         //return 'Number has to be between 0 and 999';
          //},
      tipoproducto: "",
      negocios: [],
      imagenPreview: "",
      imagenBlob: null,
      errorImagen: "",
    };
  },

  methods: {
    setPhoto(preview, blob) {
      this.imagenPreview = preview;
      this.imagenBlob = blob;
      this.errorImagen = "";
    },
    cancelarFoto() {
      this.imagenPreview = "";
      this.imagenBlob = null;
    },
    async subirImagen() {
      if (!this.imagenBlob) {
        return null;
      }

      const formData = new FormData();
      formData.append("imagen", this.imagenBlob, "producto.jpg");

      const resp = await axios.post(window.location.origin + "/user/files", formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });

      return resp.data.imageName;
    },
    resetForm() {
      this.nombre = "";
      this.descripcion = "";
      this.precio_oferta = "";
      this.precio_regular = "";
      this.tipoproducto = "";
      this.imagenPreview = "";
      this.imagenBlob = null;
      this.errorImagen = "";
      this.errorNombre = "";
      if (this.$refs.form) {
        this.$refs.form.resetValidation();
      }
    },
    async crear() {
      if (this.$refs.form.validate()) {
        this.loading = true;
        this.errorImagen = "";

        try {
          let imagen = null;
          if (this.imagenBlob) {
            imagen = await this.subirImagen();
          }

          const fData = {
            nombre: this.nombre,
            descripcion: this.descripcion,
            precio_oferta: this.precio_oferta,
            precio_regular: this.precio_regular,
            tipoproducto: this.tipoproducto,
            n_id: this.negocio.id,
          };

          if (imagen) {
            fData.imagen = imagen;
          }

          const resp = await axios({
            url: window.location.origin + "/user/productos/crear",
            data: fData,
            method: "POST",
          });

          if (resp.data.success) {
            this.resetForm();
            this.loading = false;
            this.dialog = false;
            this.$emit("updateList", resp.data.data);
          }
        } catch (err) {
          if (err.response && err.response.status == 422) {
            this.errorNombre = "Ya existe " + this.nombre + ", elija otro";
          } else if (err.response && err.response.data && err.response.data.message && err.response.data.message.imagen) {
            this.errorImagen = err.response.data.message.imagen[0];
          } else {
            this.errorImagen = "No se pudo subir o guardar la imagen del producto.";
          }
          this.loading = false;
        }
      }

    },
  },

  watch: {
    dialog(val) {},
  },
};
</script>
