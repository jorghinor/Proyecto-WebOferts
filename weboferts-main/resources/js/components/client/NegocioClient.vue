<template>
  <v-app id="inspire">
    <navbarclient :pageTitle="'Mi Negocio'"></navbarclient>
    <v-main>
      <v-container>
        <v-btn
          color="primary"
          rounded
          style="text-transform: none !important"
          small
          @click="disabled = !disabled"
          :loading="loading"
        >
          Editar
        </v-btn>
        <v-card class="elevation-0">
          <v-card-text>
            <v-form ref="form" v-model="valid" :lazy-validation="lazy">
              <v-row dense no-gutters>
                <v-col cols="12" sm="6" md="6" class="px-2">
                  <v-row dense no-gutters>
                    <v-col cols="12">
                      <v-text-field
                        label="Nombre*"
                        autocomplete="off"
                        v-model="negocio.nnegocio"
                        :disabled="disabled"
                        :rules="requiredRule"
                        :error-messages="errorNombre"
                        rounded
                        required
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        label="Página Web"
                        type="url"
                        v-model="negocio.web"
                        :disabled="disabled"
                        autocomplete="off"
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="4" md="4">
                      <v-text-field
                        label="Nit"
                        v-model="negocio.nit"
                        :disabled="disabled"
                        autocomplete="off"
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="8" md="8">
                      <v-text-field
                        label="Dirección"
                        v-model="negocio.dir"
                        :disabled="disabled"
                        autocomplete="off"
                        :rules="requiredRule"
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        label="Ciudad"
                        v-model="negocio.ciudad"
                        :disabled="disabled"
                        autocomplete="off"
                        :rules="requiredRule"
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        label="Ubicación (Gmaps)"
                        type="url"
                        v-model="negocio.gmaps"
                        :disabled="disabled"
                        autocomplete="off"
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <location-picker
                        :latitude="negocio.latitude"
                        :longitud="negocio.longitud"
                        :gmaps="negocio.gmaps"
                        :disabled="disabled"
                        @location-selected="applyLocation"
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        label="Latitud"
                        v-model="negocio.latitude"
                        readonly
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        label="Longitud"
                        v-model="negocio.longitud"
                        readonly
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        label="Teléfono"
                        v-model="negocio.telefonos"
                        :disabled="disabled"
                        autocomplete="off"
                        rounded
                        outlined
                        dense
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        label="Celular"
                        v-model="negocio.celular"
                        :disabled="disabled"
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
                        v-model="delivery"
                        :disabled="disabled"
                        label="¿Servicio Delivery?"
                      ></v-switch>
                    </v-col>
                  </v-row>
                </v-col>
                <v-col cols="12" sm="6" md="6">
                  <div v-if="disabled">
                    <v-img v-if="foto != ''" :src="foto"></v-img>
                  </div>
                  <clipperimage
                    v-else
                    @setPhoto="setFoto"
                    @cancel="cancelar"
                  >
                  </clipperimage>
                </v-col>
              </v-row>
              <v-row class="py-6">
                <v-col>
                  <v-btn color="blue darken-1" text @click="volver" style="text-transform: none !important">
                    Cerrar
                  </v-btn>
                  <v-btn
                    color="primary"
                    rounded
                    @click="confirmar = true"
                    :disabled="!valid"
                    style="text-transform: none !important"
                  >
                    Actualizar
                  </v-btn>
                </v-col>
              </v-row>
            </v-form>
            <small>*campo obligatorio</small>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
          </v-card-actions>
        </v-card>
      </v-container>
    </v-main>
    <v-snackbar v-model="snackbar" :timeout="6000" top color="success">
      <p class="title blackblue--text float-left">{{ mensaje }}</p>
      <v-btn
        small
        icon
        class="success black--text float-right"
        text
        @click="snackbar = false"
        >x</v-btn
      >
    </v-snackbar>

    <v-dialog v-model="confirmar" max-width="290">
      <v-card>
        <v-card-title class="headline"> ¿Guardar Cambios? </v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="green darken-1"
            text @click="volver">
          Cancelar </v-btn>
          <v-btn
            color="green darken-1"
            text @click="update"
            :loading="loading"
          >
            Guardar </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <footerclient></footerclient>
  </v-app>
</template>

<script>
export default {
  name: "NegocioClient",

  data() {
    return {
      lazy: false,
      valid: false,
      disabled: true,
      loading: false,
      emailRules: [
        (v) => !!v || "E-mail es requerido",
        (v) => !!v || "E-mail es requerido",
        (v) => /.+@.+\..+/.test(v) || "E-mail invalido",
      ],

      requiredRule: [(v) => !!v || "Campo es requerido"],
      rules: {
        select: [(v) => !!v || "Categoria es requirida"],
        select2: [(v) =>  v.length>0 || "Al menos una Categoria es requirida"],
      },

      errorNombre: "",
      negocio: {
        nnegocio: "",
        nit: "",
        dir: "",
        ciudad: "",
        gmaps: "",
        latitude: "",
        longitud: "",
        telefonos: "",
        celular: "",
        web: "",
        //logo: "",
        email: "",
        id: 0,
      },
      delivery: false,
      snackbar: false,
      mensaje: "",
      //agregado
      compra: false,
      foto: "",
      result: "",
      tempPhotoUrl: "",
      confirmar: false,
      cats: [],
      categorias: [],
      errorCategoria: ""
    }
  },

  methods: {
    update() {
      if (this.$refs.form.validate()) {
        this.loading = true
        const hasPendingPhoto = this.result !== ""
        const negocioId = this.negocio.id ? this.negocio.id : "null"
        let fData = {
          id: negocioId,
          nnegocio: this.negocio.nnegocio,
          nit: this.negocio.nit,
          dir: this.negocio.dir,
          ciudad: this.negocio.ciudad,
          gmaps: this.negocio.gmaps,
          latitude: this.negocio.latitude,
          longitud: this.negocio.longitud,
          telefonos: this.negocio.telefonos,
          celular: this.negocio.celular,
          delivery: this.delivery,
          web: this.negocio.web,
          compra: this.negocio.compra,
          cats: this.cats
        }

        axios({
          url:
            window.location.origin + "/user/updatenegocio/" + negocioId,
          data: fData,
          method: "POST",
        })
          .then((resp) => {
            if (resp.data.success) {
              if (resp.data.negocio_id) {
                this.negocio.id = resp.data.negocio_id
              }
              fData.id = this.negocio.id

              const finishUpdate = (logo) => {
                if (logo) {
                  this.negocio.logo = logo
                  this.foto = logo
                  fData.logo = logo
                }

                this.loading = false
                this.disabled = true
                this.confirmar = false
                this.errorNombre = ""
                this.errorCategoria = ""
                this.$emit("updateNegocio", fData)
                this.mensaje = hasPendingPhoto
                  ? "Negocio e imagen guardados con exito!"
                  : "Negocio actualizado con exito!"
                this.snackbar = true
              }

              if (hasPendingPhoto) {
                const formData = new FormData()
                formData.append("imagen", this.result)
                formData.append("type", "jpg")

                axios.post(
                  window.location.origin + "/user/negocio/file/" + this.negocio.id,
                  formData,
                  {
                    headers: { "Content-Type": "multipart/form-data" },
                  }
                )
                  .then((imageResponse) => {
                    const imageName = imageResponse.data.imageName
                    this.clearPendingPhoto()
                    finishUpdate(imageName)
                  })
                  .catch((err) => {
                    console.error(err)
                    this.loading = false
                    this.confirmar = false
                    this.disabled = true
                    this.mensaje = "Se guardo el negocio, pero no se pudo guardar la imagen. Intenta nuevamente."
                    this.snackbar = true
                  })

                return
              }

              finishUpdate(this.negocio.logo || this.foto)
            }
          })
          .catch((err) => {
            if (err.response && err.response.status == 422) {
              const apiErrors = err.response.data && err.response.data.message
                ? err.response.data.message
                : {}

              if (apiErrors.nnegocio && apiErrors.nnegocio.length) {
                this.errorNombre = apiErrors.nnegocio[0]
              } else {
                this.errorNombre =
                  "Error de validacion al guardar el negocio"
              }

              if (apiErrors.cats && apiErrors.cats.length) {
                this.errorCategoria = apiErrors.cats[0]
              } else {
                this.errorCategoria = ""
              }

              this.loading = false
            } else {
              this.loading = false
            }
          })
      }
    },

    volver() {
      window.history.go(-1)
    },

    setFoto(foto, result) {
      this.revokeTempPhotoUrl()
      this.tempPhotoUrl = foto
      this.foto = foto
      this.result = result
      this.disabled = true
      this.mensaje = "Imagen lista. Ahora pulsa Guardar para guardar el negocio con la nueva imagen."
      this.snackbar = true
    },

    applyLocation(location) {
      this.negocio.latitude = location.latitude
      this.negocio.longitud = location.longitud
      this.negocio.gmaps = location.gmaps
    },

    cancelar() {
      if (this.result !== "") {
        this.clearPendingPhoto()
        this.foto = this.negocio.logo || ""
      }

      this.disabled = true
    },

    clearPendingPhoto() {
      this.result = ""
      this.revokeTempPhotoUrl()
    },

    revokeTempPhotoUrl() {
      if (this.tempPhotoUrl) {
        URL.revokeObjectURL(this.tempPhotoUrl)
        this.tempPhotoUrl = ""
      }
    },
  },

  created() {
    axios({
      url: window.location.origin + "/user/minegocio",
      method: "GET",
    })
      .then((resp) => {
        const negocio = resp.data.negocio || {}
        this.negocio = {
          ...this.negocio,
          ...negocio,
          latitude: negocio.latitude ?? "",
          longitud: negocio.longitud ?? negocio.longitude ?? "",
        }
        this.categorias = resp.data.categorias

        ;(this.negocio.categorias || []).forEach(element => {
          this.cats.push(element.id)
        })

        this.foto = this.negocio.logo
        //agregado
        if (this.negocio.delivery == 0) {
          this.delivery = false
        } else {
          this.delivery = true
        }
        //agregado
        if (this.negocio.compra == 0) {
          this.compra = false
        } else {
          this.compra = true
        }
      })
      .catch((error) => {
        console.log(error)
      })
  },

  beforeDestroy() {
    this.revokeTempPhotoUrl()
  },
}
</script>

<style scoped>
  input[type="file"] {
    display: none;
  }
  .placeholder {
    background-color: lightgray;
    padding: 1rem;
    color: white;
  }
</style>
