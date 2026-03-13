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
          <span class="headline">Nuevo Negocio</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form ref="form" v-model="valid" :lazy-validation="lazy">
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    label="Nombre*"
                    autocomplete="off"
                    v-model="nnegocio"
                    :rules="requiredRule"
                    :error-messages="errorNombre"
                    rounded
                    required
                    filled
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    label="Nit"
                    v-model="nit"
                    autocomplete="off"
                    rounded
                    filled
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    label="Dirección"
                    v-model="dir"
                    autocomplete="off"
                    rounded
                    filled
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    label="Ubicación"
                    v-model="gmaps"
                    autocomplete="off"
                    rounded
                    filled
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    label="Teléfono"
                    v-model="telefonos"
                    autocomplete="off"
                    rounded
                    filled
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    label="Celular"
                    v-model="celular"
                    autocomplete="off"
                    rounded
                    filled
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    label="Delivery"
                    v-model="delivery"
                    autocomplete="off"
                    rounded
                    filled
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    label="Página Web"
                    v-model="web"
                    autocomplete="off"
                    rounded
                    filled
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    label="Logo"
                    v-model="logo"
                    autocomplete="off"
                    rounded
                    filled
                  ></v-text-field>
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
  name: "EditarNegocio",
  props: {
    negocio: Object,
  },

  data() {
    return {
      dialog: false,
      lazy: false,
      valid: false,
      disabled: false,
      loading: false,

      nnegocio: "",
      nit: "",
      dir: "",
      gmaps: "",
      telefonos: "",
      celular: "",
      delivery: "",
      web: "",
      logo: "",

      requiredRule: [(v) => !!v || "Este Campo es requerido"],
      errorNombre: "",

      estado: false,
      estadoLabel: "Inactivo"
    };
  },

  methods: {
    actualizar() {
      if (this.$refs.form.validate()) {
        this.loading = true;

        let fData = {
          nnegocio: this.nnegocio,
          nit: this.nit,
          dir: this.dir,
          gmaps: this.gmaps,
          telefonos: this.telefonos,
          celular: this.celular,
          delivery: this.delivery,
          web: this.web,
          logo: this.logo,
          state: this.estado,
        };

        axios({
          url: window.location.origin +"/admin/negocios/" + this.negocio.id,
          data: fData,
          method: "PUT",
        })
          .then((resp) => {
            if (resp.data.success) {
              fData.web = resp.data.data
              fData.id = this.negocio.id
              this.loading = false
              this.$emit("editNeg", fData);
              this.dialog = false
            }
          })
          .catch((err) => {
            if (err.response.status == 422) {
              this.errorNombre = "Ya existe " + this.nnegocio + ", elija otro";
            }
          });
      }
    },
    cambiar(){
        if(this.estado){
            this.estadoLabel = "Activo"
        }else{
            this.estadoLabel = "Inactivo"
        }
    }
  },

  created() {
      this.nnegocio = this.negocio.nnegocio;
      this.nit = this.negocio.nit;
      this.dir = this.negocio.dir;
      this.gmaps = this.negocio.gmaps;
      this.telefonos = this.negocio.telefonos;
      this.celular = this.negocio.celular;
      this.delivery = this.negocio.delivery;
      this.web = this.negocio.web;
      this.logo = this.negocio.logo;
  },

  watch: {
    dialog(val) {},
  },
};
</script>