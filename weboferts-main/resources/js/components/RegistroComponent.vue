<template>
  <div class="register-form login-area p-4">
    <h3 class="product-title">Registro</h3>
    <div v-if="mensaje != ''" :class="alerta" role="alert">
      {{ mensaje }}
    </div>
    <form class="login-form" @submit.prevent="submit">
      <div class="form-group">
        <div class="input-icon">
          <i class="lni-user"></i>
          <input
            type="text"
            class="form-control1 form-control-lg"
            placeholder="nombre completo"
            autocomplete="off"
            v-model="nombre"
          />
        </div>
        <small v-if="nombreError != ''" class="form-text text-muted">
          <span class="danger1">{{ nombreError }}</span>
        </small>
      </div>
      <div class="form-group">
        <div class="input-icon">
          <i class="lni-envelope"></i>
          <input
            type="email"
            class="form-control1 form-control-lg nocap"
            placeholder="Email"
            autocomplete="off"
            v-model="email"
          />
        </div>
        <small v-if="emailError != ''" class="form-text text-muted">
          <span class="danger1">{{ emailError }}</span>
        </small>
      </div>
      <div class="form-group">
        <div class="input-icon">
          <i class="lni-lock"></i>
          <input
            type="password"
            class="form-control1 form-control-lg nocap"
            placeholder="Password"
            v-model="clave"
          />
        </div>
        <small v-if="claveError != ''" class="form-text text-muted">
          <span class="danger1">{{ claveError }}</span>
        </small>
      </div>
      <div class="form-group">
        <div class="input-icon">
          <i class="lni-lock"></i>
          <input
            type="password"
            class="form-control1 form-control-lg nocap"
            placeholder="Repetir Password"
            v-model="rClave"
          />
        </div>
        <small v-if="rClaveError != ''" class="form-text text-muted">
          <span class="danger1">{{ rClaveError }}</span>
        </small>
      </div>
      <div class="form-group mb-3">
        <div class="checkbox">
          <input type="checkbox" v-model="terms" value="rememberme" />
          <label>Al registrarme, acepto nuestros Terminos y Condiciones</label>
        </div>
        <small v-if="errorTerms != ''" class="form-text text-muted">
          <span class="danger1">{{ errorTerms }}</span>
        </small>
      </div>
      <div class="text-center">
        <button class="btn btn-common log-btn btn-lg btn-block" type="submit">
          <span
            v-if="loading"
            class="spinner-grow spinner-grow-sm"
            role="status"
            aria-hidden="true"
          ></span>
          {{ buttonText }}
        </button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: "registro",
  data() {
    return {
      nombre: "",
      nombreError: "",
      email: "",
      emailError: "",
      clave: "",
      claveError: "",
      rClave: "",
      rClaveError: "",
      terms: false,
      errorTerms: "",
      loading: false,
      buttonText: "Registrar",
      valid: false,
      mensaje: "",
      alerta: "alert alert-success",
    };
  },
  methods: {
    submit() {
      if (this.nombre == "") {
        this.nombreError = "Nombre es campo obligatorio";
        this.valid = false;
      } else {
        this.nombreError = "";
        if (this.email == "") {
          this.emailError = "Email es campo obligatorio";
          this.valid = false;
        } else {
          this.emailError = "";
          if (this.clave == "") {
            this.claveError = "Password es campo obligatorio";
            this.valid = false;
          } else {
            this.claveError = "";
            if (this.rClave == "") {
              this.rClaveError = "Repetir Password es campo obligatorio";
              this.valid = false;
            } else {
              this.rClaveError = "";
              if (this.clave.length < 6) {
                this.claveError = "al menos 6 carácteres";
                this.valid = false;
              } else {
                this.claveError = "";
                if (this.rClave.length < 6) {
                  this.rClaveError = "al menos 6 carácteres";
                  this.valid = false;
                } else {
                  this.rClaveError = "";
                  if (this.clave !== this.rClave) {
                    this.rClaveError = "Passwords no corresponden";
                    this.valid = false;
                  } else {
                    if (!this.terms) {
                      this.errorTerms =
                        "Debe aceptar los terminos y condiciones";
                      this.valid = false;
                    } else {
                      this.errorTerms = "";
                      this.valid = true;
                      this.loading = true;
                      if (this.valid) {
                        let fData = {
                          nombres: this.nombre,
                          email: this.email,
                          clave: this.clave,
                          clave_confirmation: this.rClave,
                        };
                        this.buttonText = "Registrando...";
                        //return new Promise((resolve, reject) => {
                        axios({
                          url: "/registro",
                          data: fData,
                          method: "POST",
                        })
                          .then((resp) => {
                            if (resp.data.success) {
                              this.loading = false;
                              this.alerta = "alert alert-success";
                              this.mensaje =
                                "Felicidades por registrarse, revise " +
                                this.email +
                                " para verificar su cuenta";
                              this.buttonText = "Registrar";
                              this.nombre = "";
                              this.email = "";
                              this.clave = "";
                              this.rClave = "";
                              this.terms = false;
                            }
                          })
                          .catch((err) => {
                            console.log(err.response);
                            if (err.response.status == 422) {
                              this.loading = false;
                              this.alerta = "alert alert-danger";
                              this.mensaje = this.email + " Ya esta en uso";
                              this.buttonText = "Registrar";
                            }
                          });
                        //});
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    },
  },

  watch: {
    nombre(newValue, oldValue) {
      if (newValue == "") {
        this.nombreError = "Nombre es campo obligatorio";
        this.valid = false;
      } else {
        this.nombreError = "";
        this.valid = true;
      }
    },
    email(newValue, oldValue) {
      if (newValue == "") {
        this.emailError = "Email es campo obligatorio";
        this.valid = false;
      } else {
        if (
          /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(
            newValue
          )
        ) {
          this.emailError = "";
          this.valid = true;
        } else {
          this.emailError = "No es un email valido";
          this.valid = false;
        }
      }
    },

    clave(newValue, oldValue) {
      if (newValue.length < 6) {
        this.claveError = "Minimo 6 caracteres";
        this.valid = false;
      } else {
        if(this.rClave!==""){
          if(newValue!==this.rClave){
            this.claveError = "Passwords no coinciden";
            this.valid = false;
          }else {
            this.claveError = "";
            this.rClaveError = "";
            this.valid = true;
          }
        }else{
          this.claveError = "";
          this.valid = true;
        }
      }
    },

    rClave(newValue, oldValue) {
      if (newValue.length < 6) {
        this.rClaveError = "Minimo 6 caracteres";
        this.valid = false;
      } else {
        if(this.clave!==""){
          if(newValue!==this.clave){
            this.rClaveError = "Passwords no coinciden";
            this.valid = false;
          } else {
            this.rClaveError = "";
            this.claveError = "";
            this.valid = true;
          }
        } else {
          this.rClaveError = "";
          this.valid = true;
        }
      }
    },
  },
};
</script>

<style lang="scss" scoped>
</style>