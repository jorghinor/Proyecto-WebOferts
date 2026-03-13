<template>
  <div class="editar-categoria-inline">
    <v-dialog v-model="dialog" persistent max-width="600px">
      <template v-slot:activator="{ on, attrs }">
        <v-btn x-small fab class="accion-btn-editar-cat" v-bind="attrs" v-on="on">
          <v-icon>mdi-pencil</v-icon>
        </v-btn>
      </template>
      <v-card>
        <v-card-title>
          <span class="headline">Nueva Categoria</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form ref="form" v-model="valid" :lazy-validation="lazy">
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    label="Nombre*"
                    autocomplete="off"
                    v-model="cname"
                    :rules="requiredRule"
                    :error-messages="errorNombre"
                    rounded
                    required
                    filled
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    label="Icono"
                    v-model="icon"
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
  name: "EditarCategoria",
  props: {
    categoria: Object,
  },

  data() {
    return {
      dialog: false,
      lazy: false,
      valid: false,
      disabled: false,
      loading: false,

      cname: "",
      icon: "",

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
          cname: this.cname,
          icon: this.icon,
          state: this.estado,
        };

        axios({
          url: window.location.origin +"/admin/categorias/" + this.categoria.id,
          data: fData,
          method: "PUT",
        })
          .then((resp) => {
            if (resp.data.success) {
              fData.url = resp.data.data
              fData.id = this.categoria.id
              this.loading = false
              this.$emit("editCat", fData);
              this.dialog = false
            }
          })
          .catch((err) => {
            if (err.response.status == 422) {
              this.errorNombre = "Ya existe " + this.cname + ", elija otro";
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
    this.cname = this.categoria.cname;
    this.icon = this.categoria.icon;
    if(this.categoria.cstate=='active'){
        this.estado=true
        this.estadoLabel = "Activo"
    }else{
        this.estado = false
        this.estadoLabel = "Inactivo"
    }
  },

  watch: {
    dialog(val) {},
  },
};
</script>
<style scoped>
.editar-categoria-inline {
  display: inline-flex;
  align-items: center;
}
.accion-btn-editar-cat {
  min-width: 30px !important;
  width: 30px !important;
  height: 30px !important;
}
</style>
