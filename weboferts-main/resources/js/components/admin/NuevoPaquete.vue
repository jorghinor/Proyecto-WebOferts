<template>
  <v-row justify="center">
    <v-dialog v-model="dialog" persistent max-width="600px">
      <template v-slot:activator="{ on, attrs }">
        <v-btn class="success black--text" dark large v-bind="attrs" v-on="on">
          Nuevo Paquete
        </v-btn>
      </template>
      <v-card>
        <v-card-title>
          <span class="headline">Nuevo Paquete</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form ref="form" v-model="valid" :lazy-validation="lazy">
              <v-row dense>
                <v-col cols="12">
                  <v-text-field dense
                    label="Título*"
                    autocomplete="off"
                    v-model="titulo"
                    :rules="requiredRule"
                    :error-messages="errorTitulo"
                    rounded
                    required
                    filled
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
                <v-col cols="12" md="4" sm="4">
                  <v-text-field dense
                    label="Costo"
                    :rules="[numberRule]"
                    :error-messages="errorCosto"
                    v-model="costo"
                    autocomplete="off"
                    rounded
                    filled
                    suffix="Bs."
                    type="number"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4" sm="4">
                  <v-text-field dense
                    label="Tiempo"
                    v-model="tiempo"
                    :rules="[numberRule]"
                    :error-messages="errorTiempo"
                    autocomplete="off"
                    rounded
                    filled
                    suffix="Meses"
                    type="number"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4" sm="4">
                  <v-text-field dense
                    label="Orden"
                    v-model="orden"
                    :rules="[numberRule]"
                    :error-messages="errorOrden"
                    autocomplete="off"
                    rounded
                    filled
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-radio-group
                    v-model="tipopaquete"
                    row
                  >
                  <v-radio
                   label="Esquinero"
                   value="esquinero"
                  ></v-radio>
                  <v-radio
                   label="Seccion"
                   value="seccion"
                  ></v-radio>
                 </v-radio-group>
                </v-col>
                <v-col cols="12" md="6" sm="6">
                  <v-text-field dense
                    label="Etiqueta"
                    v-model="label"
                    autocomplete="off"
                    :rules="rules"
                    counter="12"
                    hint="This field uses counter prop"
                    rounded
                    filled
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6" sm="6">
                  <v-row no-gutters>
                   <v-col cols="9">
                    <v-text-field dense
                    label="Color"
                    v-model="color"
                    autocomplete="off"
                    disabled
                    rounded
                    filled
                    ></v-text-field>
                   </v-col>
                   <v-col cols="3">
                     <colorpicker @cambiar="setColor"></colorpicker>
                   </v-col>
                  </v-row>
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
            Crear
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
export default {

  name: "NuevoPaquete",
  data() {
    return {
      dialog: false,
      lazy: false,
      valid: false,
      disabled: false,
      loading: false,

      titulo: "",
      errorTitulo: "",
      requiredRule: [(v) => !!v || "Este Campo es requerido"],
      descripcion: "",
      errorDesc: "",
      requiredRule: [(v) => !!v || "Este Campo es requerido"],
      costo: "",
      errorCosto: "",
       numberRule: v  => {
         if (!v.trim()) return true;
         if (!isNaN(parseFloat(v)) && v >= 0 && v <= 999) return true;
         return 'Number has to be between 0 and 999';
          },
      tiempo: "",
      errorTiempo: "",
       numberRule: v  => {
         if (!v.trim()) return true;
         if (!isNaN(parseFloat(v)) && v >= 0 && v <= 999) return true;
         return 'Number has to be between 0 and 999';
          },
      orden: "",
      errorOrden: "",
       numberRule: v  => {
         if (!v.trim()) return true;
         if (!isNaN(parseFloat(v)) && v >= 0 && v <= 999) return true;
         return 'Number has to be between 0 and 999';
          },
      //fecha_limite: "",
      tipopaquete: "",
      label: "",
      color: "",
      picker: null,
      row: null,

      label: '',
        rules: [v => v.length <= 12 || 'Max 12 characters'],
        wordsRules: [v => v.trim().split(' ').length <= 5 || 'Max 5 words'], 
    };
  },

  methods: {
    crear() {
      if (this.$refs.form.validate()) {
        this.loading = true;
        let fData = {
          titulo: this.titulo,
          descripcion: this.descripcion,
          costo: this.costo,
          tiempo: this.tiempo,
          orden: this.orden,
          fecha_limite: this.fecha_limite,
          tipopaquete: this.tipopaquete,
          label: this.label,
          color: this.color,
        };

        axios({
          url: window.location.origin+"/admin/paquetes",
          data: fData,
          method: "POST",
        })
          .then((resp) => {
            if (resp.data.success) {
              fData.estado = "activo";
              fData.id = resp.data.data.id;
              fData.titulo = resp.data.data.titulo;              
              this.descripcion = "";
              this.costo = "";
              this.tiempo = "";
              this.orden = "";
              this.fecha_limite = "";
              this.tipopaquete = "";
              this.label = "";
              this.color = "";
              this.loading = false;
              this.dialog = false
              this.$emit('updateList',fData)
            }
          })
          .catch((err) => {
            if (err.response.status == 422) {
              //console.log(err.response);
              this.errorNombre = "Ya existe " + this.titulo + ", elija otro";
              this.loading = false;
            }
          });
      }

    },
    setColor(color) {
      this.color=color
    },
  },

  watch: {
    dialog(val) {},
  },
};
</script>