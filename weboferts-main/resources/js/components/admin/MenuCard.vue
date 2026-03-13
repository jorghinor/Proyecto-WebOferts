<template>
  <v-row justify="center">
    <v-dialog
      v-model="dialog"
      persistent
      max-width="500px"
      :fullscreen="fullscreen"
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn icon v-bind="attrs" v-on="on">
          <v-icon>mdi-eye</v-icon>
        </v-btn>
      </template>
      <v-card>
        <v-system-bar window dark>
          <v-chip x-small>{{ anuncio.titulo }}</v-chip>
          <v-spacer></v-spacer>
          <v-btn x-small icon @click="fullscreen = false">
            <v-icon>mdi-view-compact</v-icon>
          </v-btn>
          <v-btn x-small icon @click="fullscreen = true">
            <v-icon>mdi-aspect-ratio</v-icon>
          </v-btn>
          <v-btn x-small icon>
            <v-icon @click="dialog = false">mdi-close</v-icon>
          </v-btn>
        </v-system-bar>
        <v-card-text>
          <div class="boxi">
            <div
              v-if="paqueteEsquinero != null"
              :class="['ribbon', 'ribbon-top-left', ribbonShapeClass(paqueteEsquinero)]"
              :style="{ '--ribbon-color': paqueteEsquinero.color }"
            >
              <span
                v-bind:style="{ 'background-color': paqueteEsquinero.color }"
                :data-label="ribbonText(paqueteEsquinero)"
              ></span>
            </div>
            <template v-if="anuncio.fotos != []">
              <v-img
                v-for="foto in anuncio.fotos"
                :key="foto.id"
                :src="foto.url"
              >
              </v-img>
            </template>
            <div class="subtitle1 font-weight-black px-1">
              {{ anuncio.titulo.substring(0, 50) }}
            </div>
            <div
              class="px-1 font-weight-thin"
              v-if="anuncio.descripcion.length > 75"
            >
              {{ anuncio.descripcion.substring(0, 75) }}&nbsp;[...]
            </div>
            <div v-else>
              {{ anuncio.descripcion }}
            </div>
            <div class="caption">{{ negocio.nnegocio }}</div>
            <div class="px-2 py-2">
              <v-chip
                v-for="cate in negocio.categorias"
                :key="cate.id"
                x-small
                class="grey white--text"
                >{{ cate.cname }}</v-chip
              >
            </div>
            <v-divider class="mx-4"></v-divider>
            <div class="px-1 py-1">
              <v-btn small icon disabled>
                <v-icon>mdi-share-variant</v-icon>
              </v-btn>
              <v-btn 
                disabled
                small rounded class="success">
                {{ negocio.celular }}<v-icon>mdi-whatsapp</v-icon>
              </v-btn>
            </div>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-switch
            v-model="estado"
            :label="estadoAnuncio"
            @change="cambio"
          ></v-switch>
          <v-spacer></v-spacer>
          <v-btn color="grey darken-1" text rounded @click="dialog = false">
            Cerrar
          </v-btn>
          <v-btn 
            class="success"
            :loading="loading"
            @click="save"
          >
          Cambiar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
export default {
  name: "VistaAnuncio",
  props: {
    anuncio: Object,
    negocio: Object,
  },
  data() {
    return {
      dialog: false,
      image: null,
      imageDefault: window.location.origin + "/storage/images/default.jpeg",
      adquiridos: [],
      fullscreen: false,
      paqueteEsquinero: null,
      ribbonColor: "#fff",
      imagenError: "",
      estado: false,
      estadoAnuncio: "Inactivo",
      loading: false
    }
  },
  methods: {
    cambio() {
      if(this.estado){
        this.estadoAnuncio = 'Activo'
      } else{
        this.estadoAnuncio = 'Inactivo'
      }
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
    },
    save(){
        this.loading = true
        let fData = {
          'state': this.estado,
          'negocio': this.negocio.id
        }

        axios({
          url: window.location.origin + "/admin/anuncios/state/" + this.anuncio.id,
          data: fData,
          method: "PUT",
        }).then((resp) => {
            this.loading = false
            this.dialog = false
            fData.id = this.anuncio.id
            this.$emit("updateAnuncio", fData)
        }).catch((err) => {
            this.loading = false
            console.log(err.response)
        })
    }
  },
  created() {
    if (this.anuncio.paquetes != [] || this.anuncio.paquetes != null) {
      let esquinero = this.anuncio.paquetes.find(
        (element) => element.tipo == "esquinero"
      );
      this.paqueteEsquinero = esquinero;
    }
    if(this.anuncio.estadoanuncio=='inactivo'){
      this.estadoAnuncio = 'Inactivo'
      this.estado = false
    }else{
      this.estadoAnuncio = 'Activo'
      this.estado = true
    }
  },
};
</script>

<style>
/* box ribbon */
.boxi {
  position: relative;
  /*     max-width: 600px;
    width: 90%;
    height: 400px; */
  background: #fff;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
  background-color: transparent;
}

/* common */
.ribbon {
  width: 150px;
  height: 150px;
  overflow: hidden;
  position: absolute;
  z-index: 1;
}
.ribbon::before,
.ribbon::after {
  position: absolute;
  z-index: -1;
  content: "";
  display: block;
  /* border: 5px solid #c91f82; */
  border: 5px solid var(--ribbon-color, #2980b9);
}
.ribbon span {
  position: absolute;
  display: block;
  width: 225px;
  padding: 15px 0;
  /* background-color: #3498db; */
  background-color: var(--ribbon-color, #3498db);
  background-image:
    linear-gradient(90deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.08) 18%, rgba(255,255,255,0.08) 30%, rgba(255,255,255,0.08) 40%, rgba(255,255,255,0.08) 65%, rgba(255,255,255,0) 85%, rgba(255,255,255,0) 100%),
    linear-gradient(90deg, rgba(0,0,0,0.22) 0%, rgba(0,0,0,0) 60%);
    -webkit-mask-image: linear-gradient(90deg, rgba(0,0,0,1) 0%, rgba(0,0,0,0.9) 20%, rgba(0,0,0,0.7) 45%, rgba(0,0,0,0.4) 70%, rgba(0,0,0,0) 100%);
    mask-image: linear-gradient(90deg, rgba(0,0,0,1) 0%, rgba(0,0,0,0.9) 20%, rgba(0,0,0,0.7) 45%, rgba(0,0,0,0.4) 70%, rgba(0,0,0,0) 100%);
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
  color: transparent;
  font: 700 18px/1 "Lato", sans-serif;
  text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
  text-transform: uppercase;
  text-align: center;
  white-space: pre-line;
}
.ribbon span::after {
  content: attr(data-label);
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff2b0;
  -webkit-text-stroke: 0;
  text-shadow:
    0 2px 4px rgba(0, 0, 0, 0.75),
    0 0 12px rgba(255, 160, 0, 1),
    0 0 24px rgba(255, 110, 0, 0.98),
    0 0 40px rgba(255, 80, 0, 0.9);
  text-transform: uppercase;
  text-align: center;
  white-space: pre-line;
  pointer-events: none;
  animation: ribbonGlow 2s ease-in-out infinite;
}

.ribbon--triangle::before,
.ribbon--triangle::after,
.ribbon--star::before,
.ribbon--star::after {
  display: none;
}
.ribbon--triangle span {
  width: 160px;
  height: 160px;
}
.ribbon--star span {
  width: 100px;
  height: 100px;
  padding: 0;
  top: 6px;
  left: 0;
  right: auto;
  transform: none;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  line-height: 1;
  text-transform: uppercase;
  text-align: center;
  background-color: var(--ribbon-color, #3498db);
  box-sizing: border-box;
  transform: none;
}
.ribbon--triangle span {
  align-items: flex-start;
  justify-content: flex-start;
  text-align: left;
  position: absolute !important;
  left: 10px !important;
  top: 10px !important;
  transform: none !important;
  width: 110px;
  height: 110px;
  padding: 8px;
  font-size: 16px;
}
.ribbon--triangle span {
    background-image:
      linear-gradient(135deg, transparent 48%, rgba(255,255,255,0.4) 50%, transparent 52%),
      linear-gradient(90deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.03) 35%, rgba(255,255,255,0.02) 70%, rgba(255,255,255,0) 100%),
      linear-gradient(90deg, rgba(0,0,0,0.16) 0%, rgba(0,0,0,0) 60%);
  }
  .ribbon--star span {
    background-image:
      linear-gradient(90deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.03) 35%, rgba(255,255,255,0.02) 70%, rgba(255,255,255,0) 100%),
      linear-gradient(90deg, rgba(0,0,0,0.16) 0%, rgba(0,0,0,0) 60%);
  }
.ribbon--triangle span::after {
  inset: auto !important;
  position: absolute !important;
  top: 34px !important;
  left: 14px !important;
  align-items: flex-start !important;
  justify-content: flex-start !important;
  text-align: left !important;
  padding: 6px !important;
  font-size: 14px !important;
}
@keyframes ribbonGlow {
  0%, 100% {
    text-shadow:
      0 2px 4px rgba(0, 0, 0, 0.75),
      0 0 12px rgba(255, 160, 0, 1),
      0 0 24px rgba(255, 110, 0, 0.98),
      0 0 40px rgba(255, 80, 0, 0.9);
  }
  50% {
    text-shadow:
      0 2px 5px rgba(0, 0, 0, 0.85),
      0 0 12px rgba(255, 170, 20, 1),
      0 0 26px rgba(255, 90, 0, 0.95),
      0 0 40px rgba(255, 40, 0, 0.8);
  }
}
.ribbon--triangle span {
  clip-path: polygon(0 0, 100% 0, 0 100%);
}
.ribbon--star span {
  clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
}

/* top left*/
.ribbon-top-left {
  top: -10px;
  left: -10px;
}
.ribbon-top-left::before,
.ribbon-top-left::after {
  border-top-color: transparent;
  border-left-color: transparent;
}
.ribbon-top-left::before {
  top: 0;
  right: 0;
}
.ribbon-top-left::after {
  bottom: 0;
  left: 0;
}
.ribbon-top-left span {
  right: -25px;
  top: 30px;
  transform: rotate(-45deg);
}

.ribbon.ribbon--triangle.ribbon-top-left span,
.ribbon.ribbon--star.ribbon-top-left span {
  right: auto;
  left: 8px;
  top: 8px;
  transform: none;
}

/* top right*/
.ribbon-top-right {
  top: -10px;
  right: -10px;
}
.ribbon-top-right::before,
.ribbon-top-right::after {
  border-top-color: transparent;
  border-right-color: transparent;
}
.ribbon-top-right::before {
  top: 0;
  left: 0;
}
.ribbon-top-right::after {
  bottom: 0;
  right: 0;
}
.ribbon-top-right span {
  left: -25px;
  top: 30px;
  transform: rotate(45deg);
}

/* bottom left*/
.ribbon-bottom-left {
  bottom: -10px;
  left: -10px;
}
.ribbon-bottom-left::before,
.ribbon-bottom-left::after {
  border-bottom-color: transparent;
  border-left-color: transparent;
}
.ribbon-bottom-left::before {
  bottom: 0;
  right: 0;
}
.ribbon-bottom-left::after {
  top: 0;
  left: 0;
}
.ribbon-bottom-left span {
  right: -25px;
  bottom: 30px;
  transform: rotate(225deg);
}

/* bottom right*/
.ribbon-bottom-right {
  bottom: -10px;
  right: -10px;
}
.ribbon-bottom-right::before,
.ribbon-bottom-right::after {
  border-bottom-color: transparent;
  border-right-color: transparent;
}
.ribbon-bottom-right::before {
  bottom: 0;
  left: 0;
}
.ribbon-bottom-right::after {
  top: 0;
  right: 0;
}
.ribbon-bottom-right span {
  left: -25px;
  bottom: 30px;
  transform: rotate(-225deg);
}
</style>
