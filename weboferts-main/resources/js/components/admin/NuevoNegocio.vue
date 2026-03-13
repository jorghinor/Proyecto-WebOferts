<template>
  <v-row justify="center">
    <v-dialog v-model="dialog" persistent fullscreen max-width="600px">
      <template v-slot:activator="{ on, attrs }">
        <v-btn class="success black--text" dark large v-bind="attrs" v-on="on">
          Nuevo Negocio
        </v-btn>
      </template>
      <v-card>
        <v-card-title>
          <span class="headline">Nuevo Negocio</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-form ref="form" v-model="valid" :lazy-validation="lazy">
              <v-row dense no-gutters>
                <v-col cols="12" sm="6" md="6" class="px-2">
                  <v-row dense no-gutters>
                    <v-col cols="12">
                      <v-text-field
                        label="Nombre*"
                        autocomplete="off"
                        v-model="nnegocio"
                        :rules="requiredRule"
                        :error-messages="errorNombre"
                        :disabled="disabled"
                        rounded
                        required
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        :disabled="disabled"
                        label="Página Web"
                        type="url"
                        v-model="web"
                        autocomplete="off"
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="4" md="4">
                      <v-text-field
                        :disabled="disabled"
                        label="Nit"
                        v-model="nit"
                        autocomplete="off"
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="8" md="8">
                      <v-text-field
                      :disabled="disabled"
                        label="Dirección"
                        v-model="dir"
                        autocomplete="off"
                        :rules="requiredRule"
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        :disabled="disabled"
                        label="Ubicación (Gmaps)"
                        type="url"
                        v-model="gmaps"
                        autocomplete="off"
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <location-picker
                        :latitude="latitude"
                        :longitud="longitud"
                        :gmaps="gmaps"
                        :disabled="disabled"
                        :visible="dialog"
                        @location-selected="applyLocation"
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        label="Latitud"
                        v-model="latitude"
                        readonly
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        label="Longitud"
                        v-model="longitud"
                        readonly
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        :disabled="disabled"
                        label="Teléfono"
                        v-model="telefonos"
                        autocomplete="off"
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        :disabled="disabled"
                        label="Celular"
                        v-model="celular"
                        autocomplete="off"
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                        <v-select
                          v-model="cats"
                          :items="categorias"
                          :disabled="disabled"
                          :rules="rules.select2"
                          required
                          :error-messages="errorCategoria"
                          chips
                          label="Categorias"
                          item-text="cname"
                          item-value="id"
                          multiple
                          rounded
                          outlined
                        ></v-select>
                    </v-col>
                    <v-col cols="12">
                      <v-switch
                        :disabled="disabled"
                        v-model="delivery"
                        label="¿Serivicio Delivery?"
                      ></v-switch>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        :disabled="disabled"
                        label="Username(E-mail)"
                        type="email"
                        v-model="email"
                        :rules = "emailRules"
                        autocomplete="off"
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        :disabled="disabled"
                        label="Password"
                        type="password"
                        v-model="clave"
                        :error-messages="claveErrors"
                        :rules="[
                          (v) => !!v || 'Password es un campo requerido',
                          (v) => v.length >= 6 || 'Minimo 6 caracteres',
                        ]"
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        :disabled="disabled"
                        label="Repetir Password"
                        type="password"
                        v-model="reclave"
                        :error-messages="reclaveErrors"
                        :rules="[
                          clave === reclave || 'Passwords no coiciden',
                          (v) => v.length >= 6 || 'Minimo 6 caracteres',
                        ]"
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-col>
                <v-col cols="12" sm="6" md="6">
                  <div v-if="foto != ''">
                    <v-img :src="foto"></v-img>
                  </div>
                  <clipperimage2
                    ref="clipper1"
                    key="clipper1"
                    v-else
                    @setPhoto="setFoto"
                    @disabledForm="desactivarForm"
                    @cancel="cancelar"
                  >
                  </clipperimage2>
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
//import { VueCropper } from "vue-cropper"

export default {
  name: "NuevoNegocio",
  props: {
    categorias: Array
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
      latitude: "",
      longitud: "",
      telefonos: "",
      celular: "",
      delivery: false,
      web: "",
      logo: "",
      email: "",
      emailRules: [
        (v) => !!v || 'E-mail es requerido',
        (v) => !!v || "E-mail es requerido",
        (v) => /.+@.+\..+/.test(v) || "E-mail invalido",
      ],

      clave: "",
      claveErrors: "",
      reclave: "",
      reclaveErrors: "",

      requiredRule: [(v) => !!v || "Campo es requerido"],
      errorNombre: "",

      option: {
        img: "",
        size: 2,
        full: false,
        outputType: "jpeg",
        canMove: true,
        fixedBox: false,
        original: true,
        canMoveBox: true,
        autoCrop: true,
        autoCropWidth: 600,
        autoCropHeight: 600,
        centerBox: false,
        high: true,
      },

      cats: [],
      errorCategoria: "",
      rules: {
        select: [(v) => !!v || "Categoria es requirida"],
        select2: [(v) =>  v.length>0 || "Al menos una Categoria es requirida"],
      },
      foto: "",
      disabled: false,
      result: ""
    }
  },

  methods: {
    crear() {
      if (this.$refs.form.validate()) {
        this.loading = true
        if (this.result != "") {
            const formData = new FormData()
            //let foto1 = this.$refs.clipper.result
            formData.append("imagen", this.result)
            formData.append("type", "jpg")
            axios.post(window.location.origin + "/admin/files", formData, {
                headers: { "Content-Type": "multipart/form-data" },
            }).then((response) => {
                this.createNegocio(response.data.imageName)
            }).catch((err) => {
                console.error(err)
            })

        } else {
          this.createNegocio("")
        }
      }
    },

    createNegocio(respuesta) {
      let fData = {
        nnegocio: this.nnegocio,
        nit: this.nit,
        dir: this.dir,
        gmaps: this.gmaps,
        latitude: this.latitude,
        longitud: this.longitud,
        telefonos: this.telefonos,
        celular: this.celular,
        delivery: this.delivery,
        web: this.web,
        logo: respuesta,
        email: this.email,
        clave: this.clave,
        clave_confirmation: this.reclave,
        cats: this.cats
      }

      axios({
        url: window.location.origin + "/admin/negocios",
        data: fData,
        method: "POST",
      })
        .then((resp) => {
          if (resp.data.success) {
            fData.cuantos = 0
            fData.id = resp.data.data.id
            fData.web = resp.data.data.web
            this.nnegocio = ""
            this.nit = ""
            this.dir = ""
            this.gmaps = ""
            this.latitude = ""
            this.longitud = ""
            this.telefonos = ""
            this.celular = ""
            this.delivery = ""
            this.web = ""
            this.logo = ""
            this.email = ""
            this.clave = ""
            this.reclave = ""
            this.loading = false
            this.dialog = false
            this.foto = ""
            this.result = ""
            this.$refs.clipper1.reset
            this.$emit("updateList", fData)
          }
        })
        .catch((err) => {
          if (err.response.status == 422) {
            //console.log(err.response)
            this.errorNombre = "Ya existe " + this.cname + ", elija otro"
            this.loading = false
          }
        })
    },

    setFoto(picture, result){
      this.foto = picture
      this.result = result
      this.disabled = false
    },
    applyLocation(location) {
      this.latitude = location.latitude
      this.longitud = location.longitud
      this.gmaps = location.gmaps
    },
    cancelar(){
      this.disabled = false
    },
    desactivarForm(){
      this.disabled = true
    }
  },

  watch: {
    dialog(val) {},
  },
}
</script>
<style scoped>
input[type="file"] {
  display: none;
}
</style>
