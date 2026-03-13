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

      <v-card persistent fullscreen max-width="300px">
        <v-list>
          <v-list-item>
            <v-list-item-avatar>
              <v-icon>mdi-package-variant</v-icon>
            </v-list-item-avatar>

            <v-list-item-content>
              <v-list-item-title>Detalle Paquete</v-list-item-title>
              <v-list-item-subtitle>Paquete Disponible</v-list-item-subtitle>
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

        <v-list dense>
          <v-list-item>
            <div class="caption">Título: {{paquete.titulo}}</div>
          </v-list-item>
          <v-list-item>
            <div class="caption">Descripción: {{paquete.descripcion}}</div>
          </v-list-item>
          <v-list-item>
            <div class="caption">Costo: {{paquete.costo}} Bs.</div>
          </v-list-item>
          <v-list-item>
            <div class="caption">Tiempo: {{paquete.tiempo}} meses</div>
          </v-list-item>
          <v-list-item>
            <div class="caption">Tipo-Paquete: {{paquete.tipopaquete}}</div>
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
  name: "DetallePaquete",  
  props: {
    paquete: Object,
  },  
    data: () => ({
      fav: true,
      menu: false,
      message: false,
      hints: true,

      //paquetes: [],
      titulo: "",
      descripcion: "",
      costo: "",
      tiempo: "",
      orden: "",
      //fecha_limite: "",
      tipopaquete: "",
      label: "",
      color: "",
      id: 0,
      errorPaquete: "",
      estado: false,
      snackbar: false,
      mensaje: "",

      dialog: false,
      lazy: false,
      valid: false,
      disabled: false,
      loading: false,

      requiredRule: [(v) => !!v || "Este Campo es requerido"],
      errorTitulo: "",
      foto1: "",
    }),
   methods: {
    actualizar() {
      if (this.$refs.form.validate()) {
        this.loading = true;

        let fData = {
          titulo: this.titulo,
          descripcion: this.descripcion,
          costo: this.costo,
          tiempo: this.tiempo,
          orden: this.orden,
          //fecha_limite: this.fecha_limite,
          tipopaquete: this.tipopaquete,
          estado: this.miestado,
          label: this.label,
          color: this.color,
        };

        axios({
          url: window.location.origin +"/admin/paquetes/" + this.paquete.id,
          data: fData,
          method: "PUT",
        })
          .then((resp) => {
            if (resp.data.success) {
              fData.titulo = resp.data.data
              fData.id = this.paquete.id
              this.loading = false
              this.$emit("editPaq", fData);
              this.dialog = false
            }
          })
          .catch((err) => {
            if (err.response.status == 422) {
              this.errorNombre = "Ya existe " + this.titulo + ", elija otro";
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
    this.titulo = this.paquete.titulo;
    this.descripcion = this.paquete.descripcion;
    this.costo = this.paquete.costo;
    this.tiempo = this.paquete.tiempo;
    this.orden = this.paquete.orden;
    //this.fecha_limite = this.paquete.fecha_limite;
    this.tipopaquete = this.paquete.tipopaquete;
    this.label = this.paquete.label;
    this.color = this.paquete.color;
    if(this.paquete.estado=='activo'){
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