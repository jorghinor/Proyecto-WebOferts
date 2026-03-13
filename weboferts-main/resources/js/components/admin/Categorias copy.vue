<template>
  <div class="py-5">
    <div class="row">
      <button class="btn btn-large btn-success" @click="abrirNuevo">Nuevo</button>
    </div>

    <div class="row">
      <h1>Categorias</h1>
      <div class="table-responsive">
        <table
          class="table col-sm-12 table-bordered table-striped table-condensed cf"
        >
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Nombre Categoria</th>
              <th scope="col">Icono</th>
              <th scope="col">Url</th>
              <th scope="col">#Negocios</th>
              <th scope="col">Estado</th>
              <th scope="col">Opciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(categoria, index) in categorias" :key="categoria.id">
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ categoria.cname }}</td>
              <td>{{ categoria.icon }}</td>
              <td>{{ categoria.url }}</td>
              <td>{{ categoria.cuantos }}</td>

              <td v-if="categoria.cstate == 'active'">
                <span class="badge badge-success">
                  {{ categoria.cstate }}
                </span>
              </td>
              <td v-else>
                <span class="badge badge-danger">
                  {{ categoria.cstate }}
                </span>
              </td>

              <td>
                <button
                  type="button"
                  class="btn btn-sm btn-primary"
                  @click="abrirModal(categoria)"
                >
                  Editar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div
      class="modal fade"
      id="newModal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="newModalTitle"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <form @submit.prevent="crear">
            <div class="modal-header">
              <h5 class="modal-title">Nueva Categoria</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="name">Nombre Categoria</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="cname"
                  aria-describedby="cateHelp"
                  placeholder="Nombre de categoria"
                  autocomplete="off"
                  required
                />
                <small
                  v-if="errorCategoria != ''"
                  class="form-text text-muted"
                  style="color: red !important"
                  >{{ errorCategoria }}</small
                >
              </div>
              <div class="form-group">
                <label for="icono">Icono</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="icon"
                  placeholder="Icono"
                />
              </div>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                data-dismiss="modal"
              >
                Cancelar
              </button>
              <button type="submit" class="btn btn-primary">Crear</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div
      class="modal fade"
      id="editModal"
      tabindex="-1"
      role="dialog"
      aria-labelledby="editModalTitle"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <form @submit.prevent="update">
            <div class="modal-header">
              <h5 class="modal-title" id="editModalTitle">Editar Categoria</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label for="name">Nombre Categoria</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="ecname"
                  aria-describedby="cateHelp"
                  placeholder="Nombre de categoria"
                  autocomplete="off"
                  required
                />
                <small
                  v-if="errorCategoria != ''"
                  class="form-text text-muted"
                  style="color: red !important"
                  >{{ errorCategoria }}</small
                >
              </div>
              <div class="form-group">
                <label for="icono">Icono</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="eicon"
                  placeholder="Icono"
                />
              </div>
              <div class="form-group form-check">
                <input
                  v-model="estado"
                  type="checkbox"
                  class="form-check-input"
                  id="cstate"
                />
                <label class="form-check-label" for="cstate">¿Activo?</label>
              </div>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                data-dismiss="modal"
              >
                Cancelar
              </button>
              <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "admincategorias",
  data() {
    return {
      categorias: [],
      cname: "",
      icon: "",
      ecname: "",
      eicon: "",
      id: 0,
      errorCategoria: "",
      estado: false,
    };
  },

  methods: {
    crear() {
      if (this.cname != "") {
        let fData = {
          cname: this.cname,
          icon: this.icon,
        };

        axios({
          url: "admin/categorias",
          data: fData,
          method: "POST",
        })
          .then((resp) => {
            if (resp.data.success) {
              fData.cstate = "active"
              fData.cuantos = 0
              fData.id = resp.data.data.id;
              fData.url = resp.data.data.url;
              this.categorias.push(fData);
              this.cname = "";
              this.icon = "";
              $("#newModal").modal("hide");
              alert("Categoria Registrada con exito!");
            }
          })
          .catch((err) => {
            if (err.response.status == 422) {
              console.log(err.response);
            }
          });
      }
    },
    abrirModal(categoria) {
      this.ecname = categoria.cname;
      this.eicon = categoria.icon;
      this.id = categoria.id;
      if (categoria.cstate == "active") {
        this.estado = true;
      } else {
        this.estado = false;
      }
      $("#editModal").modal("show");
    },
    update() {
      if (this.ecname != "" && this.id != 0) {
        let fData = {
          cname: this.ecname,
          icon: this.eicon,
          id: this.id,
          state: this.estado,
        };

        axios({
          url: "/admin/categorias/" + this.id,
          data: fData,
          method: "PUT",
        })
          .then((resp) => {
            if (resp.data.success) {
              this.categorias.map((cat) => {
                if (cat.id == this.id) {
                  cat.cname = this.ecname;
                  cat.icon = this.eicon;
                  cat.url = resp.data.data;
                  if (this.estado) {
                    cat.cstate = "active";
                  } else {
                    cat.cstate = "inactive";
                  }
                }
              });

              this.ecname = "";
              this.eicon = "";
              this.id = 0;
              $("#editModal").modal("hide");
              alert("Categoria Actualizada con exito!");
            }
          })
          .catch((err) => {
            if (err.response.status == 422) {
              //console.log(err.response);
              this.errorCategoria = "Ya existe " + this.ecname + ", elija otro";
            }
          });
      }
    },
    abrirNuevo(){
      this.cname = ""
      this.icon = ""
      $("#newModal").modal("show");
    }
  },

  created() {
    axios.get(window.location.origin+"/admin/catlist").then((res) => {
      this.categorias = res.data;
    });

  },
};
</script>

<style lang="scss" scoped>
</style>