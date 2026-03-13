<template>
  <v-row justify="center">
    <v-dialog v-model="dialog" persistent max-width="600px">
      <template v-slot:activator="{ on, attrs }">
        <v-btn large v-bind="attrs" v-on="on" class="primary"> 
          Comprar
        </v-btn>
        <!--<v-btn
              color="primary"
              rounded
              @click="compra"
              :disabled="!valid"
              :loading="loading"
        >
              Comprar
        </v-btn>-->
      </template>
      <v-card>
        <v-card-title>
          <span class="headline">Comprar Paquete</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form ref="form" v-model="valid" :lazy-validation="lazy">
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    label="#Factura*"
                    autocomplete="off"
                    v-model="codigo"
                    :rules="requiredRule"
                    :error-messages="errorNombre"
                    rounded
                    required
                    filled
                  ></v-text-field>
                </v-col>
                <!--<v-col cols="12">
                  <v-text-field
                    label="#Paquete*"
                    autocomplete="off"
                    v-model="paquete_id"
                    :rules="requiredRule"
                    :error-messages="errorNombre"
                    rounded
                    required
                    filled
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    label="Costo"
                    v-model="costo"
                    autocomplete="off"
                    rounded
                    filled
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-switch
                    v-model="estado"
                    :label="estadoLabel"
                    @change="cambiar"
                  ></v-switch>
                </v-col>-->
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
            @click="submit"
            :disabled="!valid"
            :loading="loading"
          >
            Guardar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
export default {
  name: "ComprarPaquete",
  props: {
    paquete: Object,
    negocio_id: Number,
  },

  data() {
    return {
      dialog: false,
      lazy: false,
      valid: false,
      disabled: false,
      loading: false,

      codigo: "",
      
      id: 0,
      //costo: "",
      precio: "",
      tiempo: "",
      fecha_fin: "",

      snackbar: false,
      mensaje: "",

      requiredRule: [(v) => !!v || "Este Campo es requerido"],
      errorNombre: "",

      estado: false,
      estadoLabel: "Inactivo"
    };
  },

  methods: {
    submit() {

      if (this.$refs.form.validate()) {
        this.loading = true;

        let fData = {
          //id: this.paquete.id,
          //costo: this.costo,
          estado: this.estado,
          precio: this.paquete.costo,

          codigo: this.codigo,
          paquete_id: this.paquete.id,

          tiempo: this.paquete.tiempo,
          fecha_fin: this.paquete.fecha_limite,
          orden: this.paquete.orden,
          negocio_id: this.negocio_id
        };

        axios({
          url: window.location.origin +"/user/paquetes/add",          
          data: fData,
          method: "POST",
        })
          .then((resp) => {
            if (resp.data.success && resp.data.data != null) {

              this.loading = false
              this.$emit("compraPaq", fData);
              this.dialog = false
            }
          })
          .catch((err) => {
            if (err.response.status == 422) {
              this.errorNombre = "Ya existe " + this.codigo + ", elija otro";
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

  watch: {
    dialog(val) {},
  },
  /*created() {
    axios.get(window.location.origin + "/user/paquetelist").then((res) => {
    this.paquetes = res.data.paquetes;
    });
  },*/
};
</script>