<template>
  <v-row justify="center">
    <v-dialog v-model="dialog" persistent max-width="600px">
      <template v-slot:activator="{ on, attrs }">
        <v-btn class="success black--text" dark large v-bind="attrs" v-on="on">
          Nuevo Usuario
        </v-btn>
      </template>
      <v-card>
        <v-card-title>
          <span class="headline">Nuevo Usuario</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form ref="form" v-model="valid" :lazy-validation="lazy">
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    label="Nombre Completo*"
                    autocomplete="off"
                    v-model="nombres"
                    :rules="requiredRule"
                    rounded
                    filled
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field
                    label="Correo Electrónico*"
                    v-model="email"
                    :rules="emailRules"
                    :error-messages="errorEmail"
                    autocomplete="off"
                    rounded
                    filled
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    label="Contraseña*"
                    v-model="clave"
                    :rules="requiredRule"
                    type="password"
                    autocomplete="new-password"
                    rounded
                    filled
                    required
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6">
                  <v-text-field
                    label="Confirmar Contraseña*"
                    v-model="clave_confirmation"
                    :rules="passwordMatch"
                    type="password"
                    autocomplete="new-password"
                    rounded
                    filled
                    required
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
  name: "NuevoUsuario",
  data() {
    return {
      dialog: false,
      lazy: false,
      valid: false,
      loading: false,

      nombres: "",
      email: "",
      clave: "",
      clave_confirmation: "",

      requiredRule: [(v) => !!v || "Este campo es requerido"],
      emailRules: [
        (v) => !!v || "E-mail es requerido",
        (v) => /.+@.+\..+/.test(v) || "E-mail invalido",
      ],
      errorEmail: "",
    };
  },
  computed: {
    passwordMatch() {
      return [
        (v) => !!v || "Confirmación es requerida",
        (v) => v === this.clave || "Las contraseñas no coinciden",
      ];
    },
  },
  methods: {
    crear() {
      if (this.$refs.form.validate()) {
        this.loading = true;
        this.errorEmail = "";

        let fData = {
          nombres: this.nombres,
          email: this.email,
          clave: this.clave,
          clave_confirmation: this.clave_confirmation,
        };

        // Usamos la ruta de registro existente o una nueva de admin si existiera
        // Como no hay store en UsuarioController, usaremos la ruta de registro pública
        // pero adaptada si es necesario, o asumimos que se creará una ruta admin.
        // Por ahora usaré /admin/usuarios asumiendo que implementaré el store.

        axios({
          url: window.location.origin + "/admin/usuarios",
          data: fData,
          method: "POST",
        })
          .then((resp) => {
            if (resp.data.success) {
              this.nombres = "";
              this.email = "";
              this.clave = "";
              this.clave_confirmation = "";
              this.loading = false;
              this.dialog = false;
              this.$emit("updateList", resp.data.data);
            }
          })
          .catch((err) => {
            this.loading = false;
            if (err.response && err.response.status == 422) {
              if (err.response.data.errors && err.response.data.errors.email) {
                 this.errorEmail = err.response.data.errors.email[0];
              }
            } else {
                console.error(err);
            }
          });
      }
    },
  },
};
</script>
