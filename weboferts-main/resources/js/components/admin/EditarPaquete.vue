<template>
  <div class="editar-paquete-inline">
    <v-dialog v-model="dialog" persistent max-width="600px">
      <template v-slot:activator="{ on, attrs }">
        <v-btn x-small fab class="accion-btn-editar-paq" v-bind="attrs" v-on="on">
          <v-icon>mdi-pencil</v-icon>
        </v-btn>
      </template>
      <v-card>
        <v-card-title>
          <span class="headline">Editar Paquete</span>
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
                    v-model="descripcion"
                    autocomplete="off"
                    rounded
                    filled
                    rows="2"
                  ></v-textarea>
                </v-col>
                <v-col cols="12" md="4" sm="4">
                  <v-text-field dense
                    label="Costo"
                    v-model="costo"
                    autocomplete="off"
                    rounded
                    filled
                    suffix="Bs."
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4" sm="4">
                  <v-text-field dense
                    label="Tiempo"
                    v-model="tiempo"
                    autocomplete="off"
                    rounded
                    filled
                    suffix="meses"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4" sm="4">
                  <v-text-field dense
                    label="Orden"
                    v-model="orden"
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
                <v-col cols="12">
                  <v-switch
                    v-model="miestado"
                    :label="estadoLabel"
                    @change="cambiar"
                  ></v-switch>
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
  </div>
</template>

<script>
export default {
  name: "EditarPaquete",
  props: {
    paquete: Object,
  },

  data() {
    return {
      dialog: false,
      lazy: false,
      valid: false,
      disabled: false,
      loading: false,

      titulo: "",
      descripcion: "",
      costo: "",
      tiempo: "",
      orden: "",
      //fecha_limite: "",

      requiredRule: [(v) => !!v || "Este Campo es requerido"],
      errorTitulo: "",

      miestado: false,
      estadoLabel: "Inactivo",

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
    actualizar() {
      if (this.$refs.form.validate()) {
        this.loading = true;

        let fData = {
          titulo: this.titulo,
          descripcion: this.descripcion,
          costo: this.costo,
          tiempo: this.tiempo,
          orden: this.orden,
          //fecha_limite: this.fecha_limite,
          tipopaquete: this.tipopaquete,
          estado: this.miestado,
          label: this.label,
          color: this.color,
        };

        axios({
          url: window.location.origin +"/admin/paquetes/" + this.paquete.id,
          data: fData,
          method: "PUT",
        })
          .then((resp) => {
            if (resp.data.success) {
              const updated = resp.data && resp.data.data ? resp.data.data : fData;
              const payload = {
                ...fData,
                ...updated,
                id: this.paquete.id,
              };
              this.loading = false;
              this.$emit("editPaq", payload);
              this.dialog = false;
            }
          })
          .catch((err) => {
            if (err.response.status == 422) {
              this.errorTitulo = "Ya existe " + this.titulo + ", elija otro";
            }
          });
      }
    },
    cambiar(){
        if(this.miestado){
            this.estadoLabel = "Activo"
        }else{
            this.estadoLabel = "Inactivo"
        }
    },
    setColor(color) {
      this.color=color
    },
  },

  created() {
    this.titulo = this.paquete.titulo || '';
    this.descripcion = this.paquete.descripcion || '';
    this.costo = this.paquete.costo || '';
    this.tiempo = this.paquete.tiempo || '';
    this.orden = this.paquete.orden || '';
    this.tipopaquete = this.paquete.tipopaquete || '';
    this.label = this.paquete.label || '';
    this.color = this.paquete.color || '';
    if(this.paquete.estado=='activo'){
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
<style scoped>
.editar-paquete-inline {
  display: inline-flex;
  align-items: center;
}
.accion-btn-editar-paq {
  min-width: 30px !important;
  width: 30px !important;
  height: 30px !important;
}
</style>
