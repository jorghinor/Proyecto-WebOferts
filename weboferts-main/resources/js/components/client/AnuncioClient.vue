<template>
  <v-app id="inspire">
    <navbarclient :pageTitle="'Mis Anuncios'"></navbarclient>

    <v-main>
      <v-container>
        <v-card class="elevation-2" v-if="negocio != null" >
          <v-toolbar height="80px" flat>
              <v-toolbar-title>Anuncios - {{ negocio.nnegocio }}</v-toolbar-title>
              <v-spacer></v-spacer>
              <nuevoanuncio
                :negocio="negocio"
                :paquetes="paquetes"
                @addNew="agregar"
              ></nuevoanuncio>
          </v-toolbar>
          <v-divider></v-divider>
          <v-sheet
              v-if="!anuncios"
              :color="`grey ${theme.isDark ? 'darken-2' : 'lighten-4'}`"
              class="px-3 pt-3 pb-3"
            >
              <v-skeleton-loader
                class="mx-auto"
                max-width="90vh"
                type="table-heading, table-thead, table-tbody, table-tfoot"
              ></v-skeleton-loader>
          </v-sheet>
          <v-card-text v-else>
            <v-row dense>
              <v-col
                v-for="(anuncio, index) in anuncios"
                :key="anuncio.codigo"
                cols="12" sm="6" md="4" lg="3"
              >
                <div class="boxi mb-4">
                 <template v-if="anuncio.paquetes!=null">
                    <template
                        v-for=" paquete in anuncio.paquetes"
                    >
                      <div
                          v-if="paquete.tipo=='esquinero'"
                          :class="['ribbon', 'ribbon-top-left', ribbonShapeClass(paquete)]"
                          :style="{ '--ribbon-color': paquete.color }"
                          :key="paquete.id"
                      >
                          <span
                            v-bind:style="{ 'background-color': paquete.color }"
                            :data-label="ribbonText(paquete)"
                          ></span>
                      </div>
                    </template>
                 </template>

                 <cardclient
                  :anuncio="anuncio"
                  :negocio="negocio"
                  @update-list="onUpdateList(index, $event)"
                 >
                 </cardclient>

                </div>
              </v-col>
            </v-row>
          </v-card-text>
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
    <footerclient></footerclient>
  </v-app>
</template>

<script>
export default {
  inject: {
    theme: {
      default: { isDark: false },
    },
  },
  name: "PaqueteClient",
  data() {
    return {
      drawer: null,
      paquete: {
        titulo: "",
        descripcion: "",
        costo: "",
      },
      snackbar: false,
      mensaje: "",
      paquetes: [],
      negocio: null,
      anuncios: [],
    };
  },

  methods: {
    comprarPaq(paque) {
      this.paquetes.map((paq) => {
        if (paq.id == paque.id) {
          paq.codigo = paque.codigo;
        }
      });

      this.mensaje = "Paquete comprado con exito!";
      this.snackbar = true;
    },
    fetchAnuncios() {
      this.anuncios = null;
      this.total = 0;
      let formData = {
        page: this.page,
        pages: this.pages,
        cuenta: this.pagination.cuenta,
      };
      axios({
        url: window.location.origin + "/user/lista/anuncios",
        data: formData,
        method: "POST",
      })
        .then((resp) => {
          this.anuncios = resp.data.result.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    agregar(anuncio){
      this.anuncios.push(anuncio)
      this.mensaje = "Anuncio creado con Exito!"
      this.snackbar = true
    },
    onUpdateList(index, anuncioActualizado) {
        // Replace the old announcement with the updated one
        this.anuncios.splice(index, 1, anuncioActualizado);
        this.mensaje = "Anuncio actualizado con Exito!"
        this.snackbar = true
    },
    ribbonShapeClass(paquete) {
      if (!paquete) return "";
      const parts = [];
      if (typeof paquete === "string") {
        parts.push(paquete);
      } else {
        if (paquete.label) parts.push(paquete.label);
        if (paquete.titulo) parts.push(paquete.titulo);
        if (paquete.nombre) parts.push(paquete.nombre);
        if (paquete.name) parts.push(paquete.name);
      }
      const normalized = parts
        .join(" ")
        .toString()
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");
      if (normalized.includes("premium")) return "ribbon--star";
      if (
        normalized.includes("basico") ||
        normalized.includes("basica") ||
        normalized.includes("basic") ||
        normalized.includes("promocion") ||
        normalized.includes("promocional") ||
        normalized.includes("promo")
      )
        return "ribbon--triangle";
      return "";
    },
    ribbonText(paquete) {
      if (!paquete) return "";
      const asText = (value) => (value == null ? "" : String(value)).trim();
      const label = asText(paquete.label);
      const titulo = asText(paquete.titulo);
      const nombre = asText(paquete.nombre);
      const name = asText(paquete.name);
      const descripcion = asText(paquete.descripcion);
      const combined = [label, titulo, nombre, name, descripcion].filter(Boolean).join(" ");
      const match = combined.match(/\b\d{1,3}\s?%\b/);
      const percent = match ? match[0].replace(/\s+/g, "") : "";
      const base = label || titulo || nombre || name;
      if (percent && base && !base.includes(percent)) return `${base}\n${percent}`;
      if (!base && percent) return percent;
      return base || percent || "";
    }
  },

  mounted() {
    axios({
      url: window.location.origin + "/user/mipaquete",
      method: "GET",
    })
      .then((resp) => {
        this.paquetes = resp.data.data.paquetes;
        this.negocio = resp.data.data.negocio;
        this.anuncios = resp.data.data.anuncios;
      })
      .catch((error) => {
        console.log(error);
      });
  },
};
</script>

<style>
  .sin{
    text-transform: none !important;
  }


</style>
