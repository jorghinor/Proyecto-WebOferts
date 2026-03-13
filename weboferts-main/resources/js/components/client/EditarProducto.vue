<template>
  <v-row justify="center">
    <v-dialog v-model="dialog" persistent max-width="600px">
      <template v-slot:activator="{ on, attrs }">
        <v-btn x-small fab v-bind="attrs" v-on="on">
          <v-icon>mdi-pencil</v-icon>
        </v-btn>
      </template>
      <v-card>
        <v-card-title>
          <span class="headline">Editar Producto</span>
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
                  <v-switch
                    v-model="miestado"
                    :label="estadoLabel"
                    @change="cambiar"
                  ></v-switch>
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
                    Si no cambias la imagen, el producto conserva la actual.
                  </small>
                  <small v-if="errorImagen" class="red--text d-block mt-2">
                    {{ errorImagen }}
                  </small>
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
            @click="actualizar"
            :disabled="!valid"
            :loading="loading"
          >
            Actualizar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
export default {
  name: "EditarProducto",
  props: {
    producto: Object,
  },

  data() {
    return {
      dialog: false,
      lazy: false,
      valid: false,
      disabled: false,
      loading: false,

      nombre: "",
      //descripcion: "",
      precio_oferta: 0,

      requiredRule: [(v) => !!v || "Este Campo es requerido"],
      errorNombre: "",
      descripcion: "",
      errorDesc: "",
      requiredRule: [(v) => !!v || "Este Campo es requerido"],
      precio_regular: 0,
      precio_oferta: 0,
      tipoproducto: "",
      id: 0,
      imagenPreview: "",
      imagenBlob: null,
      errorImagen: "",

      miestado: false,
      estadoLabel: "Inactivo",

      //label: '',
        //rules: [v => v.length <= 12 || 'Max 12 characters'],
        //wordsRules: [v => v.trim().split(' ').length <= 5 || 'Max 5 words'],
    };
  },

  methods: {
    setPhoto(preview, blob) {
      this.imagenPreview = preview;
      this.imagenBlob = blob;
      this.errorImagen = "";
    },
    cancelarFoto() {
      this.imagenPreview = this.producto.imagen || "";
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
    async actualizar() {
      if (this.$refs.form.validate()) {
        this.loading = true;
        this.errorImagen = "";

        try {
          let imagen = null;
          if (this.imagenBlob) {
            imagen = await this.subirImagen();
          }

          let fData = {
            nombre: this.nombre,
            descripcion: this.descripcion,
            precio_oferta: this.precio_oferta,
            precio_regular: this.precio_regular,
            state: this.miestado,
            tipoproducto: this.tipoproducto,
          };

          if (imagen) {
            fData.imagen = imagen;
          }

          const resp = await axios({
            url: window.location.origin + "/user/productos/actualizar/" + this.producto.id,
            data: fData,
            method: "POST",
          });

          if (resp.data.success) {
            this.loading = false;
            this.$emit("editProd", resp.data.data);
            this.dialog = false;
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
    cambiar(){
        if(this.miestado){
            this.estadoLabel = "Activo"
        }else{
            this.estadoLabel = "Inactivo"
        }
    },
  },

  created() {
    this.nombre = this.producto.nombre;
    this.descripcion = this.producto.descripcion;
    this.precio_oferta = this.producto.precio_oferta;
    this.precio_regular = this.producto.precio_regular;
    this.tipoproducto = this.producto.tipoproducto;
    this.imagenPreview = this.producto.imagen || "";
    if(this.producto.estado_prod == 'activo'){
        this.miestado=true
        this.estadoLabel = "Activo"
    }else{
        this.miestado = false
        this.estadoLabel = "Inactivo"
    }
  },

  watch: {
    dialog(val) {},
  },
};
</script>
