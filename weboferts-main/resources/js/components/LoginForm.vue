<template>
  <section class="login section-padding">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-12 col-xs-12">
          <div v-if="error" class="alert alert-danger" role="alert">
            {{ mensaje }}
          </div>
          <div class="login-form login-area">
            <h3>Autentificación Requerida ccc</h3>
            <form role="form" class="login-form" @submit.prevent="submit">
              <div class="form-group">
                <div class="input-icon">
                  <i class="lni-user"></i>
                  <input
                    type="email"
                    class="form-control1 form-control-lg nocap"
                    placeholder="E-mail"
                    autocomplete="off"
                    required
                    v-model="username"
                  />
                </div>
              </div>
              <div class="form-group">
                <div class="input-icon">
                  <i class="lni-lock"></i>
                  <input
                    type="password"
                    class="form-control1 form-control-lg"
                    placeholder="Password"
                    required
                    v-model="clave"
                  />
                </div>
              </div>
              <div class="form-group mb-3">
                <a class="forgetpassword" href="forgot-password.html"
                  >Olvidó su Contraseña?</a
                >
              </div>
              <div class="text-center">
                <button 
                    class="btn btn-common log-btn"
                    type="submit"
                    :disabled="disabled"
                >
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
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "loginform",
  data() {
    return {
      username: "",
      clave: "",
      error: false,
      buttonText: "Ingresar",
      mensaje: "E-mail ó Password son incorrectos",
      loading: false,
      disabled: false
    };
  },
  methods: {
    submit() {
      if (this.username !== "" && this.clave !== "") {
        this.loading = true
        this.disabled = true  
        this.buttonText = "Registrando...";

        let fData = {
          email: this.username,
          password: this.clave,
        };
        axios({
          url: "/api/login",
          data: fData,
          method: "POST",
        })
          .then((resp) => {
            if (resp.data.success) {

              const token = resp.data.token;
              const user = resp.data.user;
              localStorage.setItem("token", token);
              localStorage.setItem("user", JSON.stringify(user));
              axios.defaults.headers.common["Authorization"] = token;
              this.loading = false;
              this.buttonText = "Registrar";
              this.email = "";
              this.clave = "";
              window.location.href = '/admin'
            }
          })
          .catch((err) => {
            this.error = true
            if (err.response.status == 401) {
              this.mensaje = "E-mail ó Password son incorrectos";
            }
           if (err.response.status == 500) {
              this.mensaje = "Sucedió un error intente más tarde";
            }
            this.buttonText = "Ingresar";
            this.disabled = false
            this.loading = false
          });
      }
    },
  },
};
</script>

<style lang="scss" scoped>
</style>