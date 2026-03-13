<template>
  <v-dialog v-model="dialog" persistent max-width="800px">
    <template v-slot:activator="{ on, attrs }">
      <v-btn icon color="primary" v-bind="attrs" v-on="on">
        <v-icon>mdi-pencil</v-icon>
      </v-btn>
    </template>
    <v-card>
      <v-card-title>
        <span class="headline">Editar Anuncio</span>
      </v-card-title>
      <v-card-text>
        <v-container>
          <v-form ref="form" v-model="valid">
            <v-row>
              <v-col cols="12" sm="6">
                <v-text-field
                  label="Título*"
                  v-model="editableAnuncio.titulo"
                  :rules="[v => !!v || 'Título es requerido']"
                  required
                ></v-text-field>
                <v-textarea
                  label="Descripción*"
                  v-model="editableAnuncio.descripcion"
                  :rules="[v => !!v || 'Descripción es requerida']"
                  required
                ></v-textarea>

                <!-- Product and Paquete Selectors -->
                <v-select
                  v-model="editableAnuncio.producto_id"
                  :items="productos"
                  item-text="nombre"
                  item-value="id"
                  label="Producto"
                  :rules="[v => !!v || 'Producto es requerido']"
                  required
                ></v-select>

                <v-divider></v-divider>
                <div>Paquetes</div>
                <v-expansion-panels>
                  <v-expansion-panel
                    v-for="paquete in paketes"
                    :key="paquete.id"
                  >
                    <v-expansion-panel-header color="secondary">
                      <v-row>
                        <v-col cols="8" md="7" sm="7">
                          <div class="caption white--text">
                            {{ paquete.titulo }}
                          </div>
                        </v-col>
                        <v-col cols="4" md="5" sm="5">
                          <div class="caption"></div>
                          <v-chip class="success secondary--text">{{
                            paquete.costo
                          }}</v-chip
                          >&nbsp;<v-chip x-small>Bs</v-chip
                          >
                        </v-col>
                      </v-row>
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                        <div class="py-2">
                          <v-chip :color="paquete.color" class="white--text px-6">{{paquete.label}}</v-chip>
                        </div>
                        <div class="py-2">{{ paquete.descripcion }}</div>
                        <v-divider class="py-3"></v-divider>
                            <v-btn
                              v-if="paquete.estado == 'activo'"
                              small
                              class="primary"
                              @click="comprar(paquete)"
                              >Comprar
                            </v-btn>
                            <template v-if="paquete.estado == 'comprado'">
                                    <v-chip class="success mr-4">
                                        <v-icon left> mdi-check </v-icon>
                                        Comprado
                                    </v-chip>
                                    <v-btn
                                      fab
                                      x-small
                                      @click="quitarCompra(paquete.id)"
                                    >
                                      <v-icon color="danger">mdi-delete</v-icon>
                                    </v-btn>
                            </template>
                    </v-expansion-panel-content>
                  </v-expansion-panel>
                </v-expansion-panels>

              </v-col>
              <v-col cols="12" sm="6">
                <v-row>
                  <v-col cols="12">
                      <v-row v-if="imagenError!==''">
                        <v-col>
                           <div class="subtitle1 danger--text">{{imagenError}}</div>
                        </v-col>
                      </v-row>
                      <v-row>
                        <v-col cols="6">Vista Previa</v-col>
                        <v-col cols="6">
                          <clipperimagedialog
                            ref="anuncioEditor"
                            @setImage="setImagen"
                            confirmText="Guardar imagen"
                          ></clipperimagedialog>
                        </v-col>
                      </v-row>
                      <div class="boxi" >
                        <div
                            v-if="paqueteEsquinero!=null"
                            :class="['ribbon', 'ribbon-top-left', ribbonShapeClass(paqueteEsquinero)]"
                            :style="{ '--ribbon-color': paqueteEsquinero.color }">
                            <span
                              v-bind:style="{ 'background-color': paqueteEsquinero.color }"
                              :data-label="ribbonText(paqueteEsquinero)"
                            ></span>
                        </div>
                        <v-img
                          v-if="imagePreview != null" :src="imagePreview">
                        </v-img>
                        <div class="subtitle1 font-weight-black px-1">{{ (editableAnuncio.titulo || '').substring(0, 50) }}</div>

                          <div class="px-1 font-weight-thin" v-if="(editableAnuncio.descripcion || '').length > 75">
                            {{ (editableAnuncio.descripcion || '').substring(0, 75) }}&nbsp;[...]
                          </div>
                          <div v-else>
                            {{ editableAnuncio.descripcion || '' }}
                          </div>
                        <div class="caption">{{ (negocio && negocio.nnegocio) || '' }}</div>
                        <div class="px-2 py-2">
                          <v-chip
                            v-for="cate in (negocio && negocio.categorias || [])"
                            :key="cate.id"
                            x-small
                            class="grey white--text"
                            >{{ cate.cname }}</v-chip
                          >
                        </div>
                        <v-divider class="mx-4"></v-divider>
                        <div>
                          <v-btn small icon>
                            <v-icon>mdi-share-variant</v-icon>
                          </v-btn>
                          <v-btn small rounded class="success">
                            {{ (negocio && negocio.celular) || '' }}<v-icon>mdi-whatsapp</v-icon>
                          </v-btn>
                        </div>
                      </div>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-form>
        </v-container>
        <small>*campo obligatorio</small>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue darken-1" text @click="dialog = false">Cerrar</v-btn>
        <v-btn color="primary" rounded @click="actualizar" :disabled="!valid" :loading="loading">Actualizar</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import clipperimagedialog from '../ClipperImageDialog.vue';

export default {
  name: "EditarAnuncioClient",
  components: {
      clipperimagedialog
  },
  props: {
    anuncio: Object,
    negocio: Object, // Add negocio prop as it's used in the template
  },
  data() {
    return {
      dialog: false,
      valid: true,
      loading: false,
      editableAnuncio: {},
      newImage: null,
      imagePreview: null,
      productos: [], // To store the list of products
      paketes: [],  // To store the list of paquetes (renamed from 'paquetes' to avoid conflict with prop)
      adquiridos: [],
      paqueteEsquinero: null,
      ribbonColor: '#fff',
      imagenError: '',
      imageDefault: window.location.origin + "/storage/images/default.jpeg",
    };
  },
  methods: {
    async loadInitialData() {
        try {
            const response = await axios.get("/user/mipaquete");
            this.productos = response.data.data.negocio.productos;
            // Initialize paketes with 'estado' property
            this.paketes = response.data.data.paquetes.map(p => ({ ...p, estado: 'activo' }));

            // Mark already acquired packages as 'comprado'
            if (this.anuncio.paquetes && this.anuncio.paquetes.length > 0) {
                this.anuncio.paquetes.forEach(anuncioPaquete => {
                    const foundPaquete = this.paketes.find(p => p.id === anuncioPaquete.paquete_id);
                    if (foundPaquete) {
                        foundPaquete.estado = 'comprado';
                        this.adquiridos.push(foundPaquete);
                        if (anuncioPaquete.tipo === 'esquinero') {
                            this.paqueteEsquinero = foundPaquete;
                            this.ribbonColor = foundPaquete.color;
                        }
                    }
                });
            }

        } catch (error) {
            console.error("Error loading initial data:", error.response);
        }
    },
    setImagen(photo) {
        if (typeof photo === "string") {
          this.imagePreview = photo;
        } else if (photo instanceof Blob) {
          this.imagePreview = URL.createObjectURL(photo);
        } else {
          this.imagePreview = this.imageDefault;
        }
        this.newImage = this.$refs.anuncioEditor.result;
        this.imagenError = ''; // Clear image error on new selection
    },
    async actualizar() {
      if (this.$refs.form.validate()) {
        this.loading = true;

        // Ensure an image is selected
        if (!this.imagePreview || this.imagePreview === this.imageDefault) {
          this.imagenError = 'Seleccione una imagen';
          this.loading = false;
          return;
        }

        // 1. If there is a new image, upload it first
        if (this.newImage) {
            const formData = new FormData();
            formData.append("imagen", this.newImage);
            formData.append("type", "jpg");
            formData.append("a_id", this.anuncio.id);

            try {
                await axios.post("/user/files/anuncio", formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                });
            } catch (error) {
                console.error("Error uploading image:", error.response);
                this.loading = false;
                return; // Stop if image upload fails
            }
        }

        // 2. Update the text fields, product, and packages
        const url = `/user/anuncios/` + this.editableAnuncio.id;
        try {
            const response = await axios.put(url, {
                ...this.editableAnuncio,
                paquetes: this.adquiridos.map(p => p.id) // Send only package IDs
            });
            this.loading = false;
            this.dialog = false;
            this.$emit('anuncio-actualizado', response.data.data);
        } catch (error) {
            console.error("Error updating anuncio:", error.response);
            this.loading = false;
        }
      }
    },
    comprar(paquete) {
      this.adquiridos.push(paquete);
      this.paketes.map((pak) => {
        if (pak.id == paquete.id) {
          pak.estado = "comprado";
        }
      });
      if(paquete.tipopaquete=='esquinero'){
        this.paqueteEsquinero = paquete;
        this.ribbonColor = paquete.color;
      }
    },
    quitarCompra(id) {
      this.paketes.map((pak) => {
        if (pak.id == id) {
          pak.estado = "activo";
          let result = this.adquiridos.filter((paquete) => {
            return paquete.id !== id;
          });
          this.adquiridos = result;
          if(this.paqueteEsquinero && this.paqueteEsquinero.id === id) {
            this.paqueteEsquinero = null;
            this.ribbonColor = '#fff';
          }
        }
      });
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
    normalizeImageUrl(url) {
      if (!url) return this.imageDefault;
      if (typeof url !== "string") return this.imageDefault;
      if (url.startsWith("http://") || url.startsWith("https://") || url.startsWith("blob:")) {
        return url;
      }
      if (url.startsWith("/")) {
        return url;
      }
      return `/storage/images/${url}`;
    },
  },
  watch: {
    dialog(val) {
      if (val) {
        // When the dialog opens, copy the prop to a local editable object
        this.editableAnuncio = { ...this.anuncio };

        // Ensure titulo and descripcion are strings
        this.editableAnuncio.titulo = this.editableAnuncio.titulo || '';
        this.editableAnuncio.descripcion = this.editableAnuncio.descripcion || '';

        // Set the initial product ID
        this.editableAnuncio.producto_id = this.anuncio.producto_id;

        // Set the initial image preview
        if (this.anuncio.fotos && this.anuncio.fotos.length > 0) {
            this.imagePreview = this.normalizeImageUrl(this.anuncio.fotos[0].url);
        } else {
            this.imagePreview = this.imageDefault;
        }
        this.newImage = null; // Reset new image
        this.imagenError = ''; // Clear any previous image error

        // Load products and packages for the dropdowns and expansion panels
        this.loadInitialData();
      } else {
        // Reset form and data when dialog closes
        this.$refs.form.resetValidation();
        this.editableAnuncio = {};
        this.productos = [];
        this.paketes = [];
        this.adquiridos = [];
        this.paqueteEsquinero = null;
        this.ribbonColor = '#fff';
        this.newImage = null;
        this.imagePreview = null;
        this.imagenError = '';
      }
    },
  },
};
</script>

<style scoped>
/* Add any specific styles for this component here */
/* box ribbon */
.boxi {
  position: relative;
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
  border: 5px solid var(--ribbon-color, #2980b9);
}
.ribbon span {
  position: absolute;
  display: block;
  width: 225px;
  padding: 15px 0;
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
</style>
