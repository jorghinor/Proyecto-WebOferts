 <template>
 <div class="text-left">
    <v-menu
      v-model="menu"
      :close-on-content-click="false"
      :nudge-width="200"
      offset-x
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn dark x-small color="blue" fab v-bind="attrs" v-on="on">
          <v-icon>mdi-ballot-outline</v-icon>
        </v-btn>
      </template>

      <v-card persistent max-width="300px">
        <v-list>
          <v-list-item>
            <v-list-item-avatar>
              <v-icon>mdi-package-variant</v-icon>
            </v-list-item-avatar>

            <v-list-item-content>
              <v-list-item-title>Detalle Producto</v-list-item-title>
              <v-list-item-subtitle>Producto Disponible</v-list-item-subtitle>
            </v-list-item-content>

            <v-list-item-action>
              <v-btn
                :class="fav ? 'blue--text' : ''"
                icon
                @click="fav = !fav"
              >
                <v-icon>mdi-diamond</v-icon>
              </v-btn>
            </v-list-item-action>
          </v-list-item>
        </v-list>

        <v-divider></v-divider>

        <!-- Imagen del Producto -->
        <v-img
          :src="producto.imagen || '/assets/img/logo.png'"
          height="200px"
          contain
          class="grey lighten-4"
        ></v-img>

        <v-list dense>
          <v-list-item>
            <div class="caption">Nombre: {{producto.nombre}}</div>
          </v-list-item>
          <v-list-item>
            <div class="caption">Descripción: {{producto.descripcion}}</div>
          </v-list-item>
          <v-list-item>
            <div class="caption">Precio Regular: {{producto.precio_regular}} Bs.</div>
          </v-list-item>
          <v-list-item>
            <div class="caption">Precio Oferta: {{producto.precio_oferta}} meses</div>
          </v-list-item>
          <v-list-item>
            <div class="caption">Tipo-Producto: {{producto.tipoproducto}}</div>
          </v-list-item>
        </v-list>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            text
            @click="menu = false"
          >
            Cerrar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-menu>
  </div>
</template>
<script>
export default {
  name: "DetalleProducto",
  props: {
    producto: Object,
  },
    data: () => ({
      fav: true,
      menu: false,
      message: false,
      hints: true,
      nombre: "",
      descripcion: "",
      precio_regular: "",
      precio_oferta: "",
      tipoproducto: "",
      label: "",
      color: "",
      id: 0,
      estado_prod: false,
      snackbar: false,
      mensaje: "",

      dialog: false,
      lazy: false,
      valid: false,
      disabled: false,
      loading: false,

      //requiredRule: [(v) => !!v || "Este Campo es requerido"],
      //errorNombre: "",
      //foto1: "",
    }),
   methods: {
    actualizar() {
      if (this.$refs.form.validate()) {
        this.loading = true;

        let fData = {
          nombre: this.nombre,
          descripcion: this.descripcion,
          precio_regular: this.precio_regular,
          precio_oferta: this.precio_oferta,
          tipoproducto: this.tipoproducto,
          estado_prod: this.miestado,
          label: this.label,
          color: this.color,
        };

        axios({
          url: window.location.origin +"/client/productos/" + this.producto.id,
          data: fData,
          method: "PUT",
        })
          .then((resp) => {
            if (resp.data.success) {
              fData.nombre = resp.data.data
              fData.id = this.producto.id
              this.loading = false
              this.$emit("editProd", fData);
              this.dialog = false
            }
          })
          .catch((err) => {
            if (err.response.status == 422) {
              this.errorNombre = "Ya existe " + this.nombre + ", elija otro";
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
  },

  created() {
    this.nombre = this.producto.nombre;
    this.descripcion = this.producto.descripcion;
    this.precio_regular = this.producto.precio_regular;
    this.precio_oferta = this.producto.precio_oferta;
    this.tipoproducto = this.producto.tipoproducto;
    this.label = this.producto.label;
    this.color = this.producto.color;
    if(this.producto.estado_prod=='activo'){
        this.miestado=true
        this.estadoLabel = "Activo"
    }else{
        this.miestado = false
        this.estadoLabel = "Inactivo"
    }
  },

 //watch: {
    //dialog(val) {},
  //},
}
</script>
