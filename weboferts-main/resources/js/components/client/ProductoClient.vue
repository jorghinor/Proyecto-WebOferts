<template>
  <v-app id="inspire">

    <navbarclient :pageTitle="'Mis Productos'"></navbarclient>

    <v-main>
      <v-container>
        <v-card class="elevation-2">
          <v-toolbar height="80px" flat>
            <v-toolbar-title>Gestión de Productos</v-toolbar-title>
            <v-spacer></v-spacer>
            <nuevoproducto :negocio="negocio" @updateList="nuevoProd"></nuevoproducto>
          </v-toolbar>
          <v-divider></v-divider>

          <v-simple-table>
            <template v-slot:default>
              <thead>
                <tr>
                  <th class="text-left">##</th>
                  <th class="text-left">Imagen</th>
                  <th class="text-left">Nombre</th>
                  <th class="text-left">Precio (Bs.)</th>
                  <th class="text-left">Tipo Producto</th>
                  <th class="text-left">Estado</th>
                  <th class="text-left">Opciones</th>
                </tr>
              </thead>
              <tbody v-if="negocio != null">
                <tr v-for="(producto, index) in productos" :key="producto.id">
                  <td>{{ index + 1 }}</td>
                  <td>
                    <v-avatar size="40" rounded>
                      <img :src="producto.imagen || '/assets/img/logo.png'" alt="Producto">
                    </v-avatar>
                  </td>
                  <td>{{ producto.nombre }}</td>
                  <td><v-chip>{{ producto.precio_oferta }}</v-chip></td>
                  <td>{{ producto.tipoproducto }}</td>
                  <td>
                    <v-chip
                      v-if="producto.estado_prod == 'activo'"
                      class="success black--text ma-2"
                      small
                      >activo</v-chip
                    >
                    <v-chip v-else class="danger white--text ma-2" small
                      >inactivo</v-chip
                    >
                  </td>
                  <td>
                   <v-btn-toggle
                    v-model="text"
                    tile
                    color="deep-purple accent-3"
                    group
                    dense
                   >
                   <v-btn value="left" small>
                    <editarproducto
                       :negocio="negocio"
                       :producto="producto"
                       @editProd="editarProd"
                     ></editarproducto>
                   </v-btn>

                   <v-btn value="center" small>
                    <detalleproducto
                       :producto="producto"
                    ></detalleproducto>
                    </v-btn>
                   </v-btn-toggle>
                 </td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-card>

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
      </v-container>
    </v-main>
    <footerclient></footerclient>
  </v-app>
</template>

<script>

export default {
  name: "clientProductos",
  data() {
    return {
      //producto: {
      nombre: "",
      descripcion: "",
      precio_oferta: 0,
      precio_regular: 0,
      tipoproducto: "",
      estado_prod: false,
      id: 0,
      //},
      errorProducto: "",
      snackbar: false,
      mensaje: "",
      drawer: null,
      text: "",
      productos: [],
      //negocios: [],
      negocio: null,
    };
  },

  methods: {
    nuevoProd(data) {
      this.productos.push(data);
      this.mensaje = "Producto registrado con exito";
      this.snackbar = true;
    },
    editarProd(produ) {
      this.productos.map((prod) => {
        if (prod.id == produ.id) {
          prod.nombre = produ.nombre;
          prod.descripcion = produ.descripcion;
          prod.precio_oferta = produ.precio_oferta;
          prod.precio_regular = produ.precio_regular;
          prod.tipoproducto = produ.tipoproducto;
          if (produ.imagen !== undefined) {
            prod.imagen = produ.imagen;
          }
          prod.estado_prod = produ.estado_prod;
        }
      });

      this.mensaje = "Producto Actualizado!";
      this.snackbar = true;
    },
  },

  created() {
    //axios.get(window.location.origin + "/client/prodlist").then((res) => {
    axios.post(window.location.origin + "/user/lista/productos").then((res) => {
      this.negocio = res.data.result.negocio
      this.productos = res.data.result.productos
    });
  },
};
</script>
