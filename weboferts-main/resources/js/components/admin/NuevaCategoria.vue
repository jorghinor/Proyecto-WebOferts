<template>
  <v-row justify="center">
    <v-dialog v-model="dialog" persistent max-width="600px">
      <template v-slot:activator="{ on, attrs }">
        <v-btn class="success black--text" dark large v-bind="attrs" v-on="on">
          Nueva Categoria
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

  name: "NuevaCategoria",
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
    };
  },

  methods: {
    crear() {
      if (this.$refs.form.validate()) {
        this.loading = true;
        let fData = {
          cname: this.cname,
          icon: this.icon,
        };

        axios({
          url: window.location.origin+"/admin/categorias",
          data: fData,
          method: "POST",
        })
          .then((resp) => {
            if (resp.data.success) {
              fData.cstate = "active";
              fData.cuantos = 0;
              fData.id = resp.data.data.id;
              fData.url = resp.data.data.url;              
              this.cname = "";
              this.icon = "";
              this.loading = false;
              this.dialog = false
              this.$emit('updateList',fData)
            }
          })
          .catch((err) => {
            if (err.response.status == 422) {
              //console.log(err.response);
              this.errorNombre = "Ya existe " + this.cname + ", elija otro";
              this.loading = false;
            }
          });
      }

    },
  },

  watch: {
    dialog(val) {},
  },
};
</script>