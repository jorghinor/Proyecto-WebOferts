<template>
<section class="featured-lis section-padding">
    <div class="container">
        <div class="row">
            <div class="col-md-12 wow fadeIn" data-wow-delay="0.5s">
                <h3 class="section-title neon-title-recomendado text-center">Anuncios Recomendados</h3>
                <div v-if="loading" class="cards-status cards-loading">Cargando anuncios recomendados...</div>
                <div v-else-if="loadError" class="cards-status cards-error">{{ loadError }}</div>
                <div v-else-if="!anuncios.length" class="cards-status cards-empty">No hay anuncios recomendados disponibles.</div>
                <div id="new-products" class="owl-carousel">
                  <div class="item" v-for="anuncio in anuncios" :key="anuncio.id">
                    <div
                      class="product-item recomendado-card"
                      @mousemove="onCardMove($event)"
                      @mouseleave="onCardLeave($event)"
                    >
                      <div class="carousel-thumb boxi">
                        <div class="featured-pill">Recomendado</div>
                        <div v-if="isNuevo(anuncio)" class="fresh-pill">Nuevo</div>
                        <div
                          v-if="anuncio.paquete!=null"
                          :class="['ribbon', 'ribbon-top-left', ribbonShapeClass(anuncio.paquete)]"
                          v-bind:style="{ '--c': anuncio.paquete.color, '--ribbon-color': anuncio.paquete.color }"
                        >
                          <span :data-label="ribbonText(anuncio.paquete)"></span>
                        </div>
                        <img
                          class="card-img-top"
                          :src="getMainPhoto(anuncio)"
                          alt=""
                          @click="showDetails(anuncio)"
                          style="cursor: pointer;"
                        />
                        <div class="overlay"></div>
                      </div>
                      <div class="product-content">
                        <h3 class="product-title text-truncate-2">
                          <a
                            class="wf-focusable"
                            :href="getLink(anuncio.id)">
                              {{anuncio.titulo}}
                          </a>
                        </h3>
                        <div class="business-header">
                          <div class="business-logo-shell">
                            <img
                              class="business-logo-mini"
                              :src="getBusinessLogo(anuncio.negocio && anuncio.negocio.logo)"
                              :alt="anuncio.negocio && anuncio.negocio.nnegocio ? anuncio.negocio.nnegocio : 'Negocio'"
                            >
                            <span class="business-logo-tooltip">
                              <strong>{{ anuncio.negocio && anuncio.negocio.nnegocio ? anuncio.negocio.nnegocio : 'Negocio aliado' }}</strong>
                              <small>{{ anuncio.negocio && anuncio.negocio.ciudad ? anuncio.negocio.ciudad : 'Bolivia' }}</small>
                            </span>
                          </div>
                          <div class="business-copy">
                            <p class="business-name text-truncate">{{ anuncio.negocio && anuncio.negocio.nnegocio ? anuncio.negocio.nnegocio : 'Negocio aliado' }}</p>
                            <span class="business-location text-truncate">
                              <i class="lni-map-marker"></i>
                              {{ anuncio.negocio && anuncio.negocio.ciudad ? anuncio.negocio.ciudad : 'Bolivia' }}
                            </span>
                          </div>
                        </div>
                        <span class="price">{{ anuncio.producto ? anuncio.producto.precio_regular : '0.00' }} Bs.-</span>
                        <div class="meta">
                          <span class="icon-wrap">
                            <i class="lni-star-filled"></i>
                            <i class="lni-star-filled"></i>
                            <i class="lni-star-filled"></i>
                            <i class="lni-star"></i>
                            <i class="lni-star"></i>
                          </span>
                          <span class="count-review"> <span>1</span> Valoracion </span>
                        </div>
                        <div class="card-text address-line text-truncate">
                          <div class="float-left">
                            <a href="#"
                              ><i class="lni-map-marker"></i>
                              {{anuncio.negocio.dir}}</a>
                          </div>
                          <div class="float-right">
                            <div class="icon">
                              <i class="lni-heart"></i>
                            </div>
                          </div>
                        </div>
                        <div class="card-footer-actions d-flex justify-content-between align-items-center">
                          <!-- Botón de compra nativo de la plantilla -->
                          <button @click="addToCart(anuncio)" class="btn btn-common btn-sm action-buy-btn">
                            <i class="lni-shopping-basket action-icon"></i> Comprar
                          </button>

                          <div class="float-right">
                            <a :href="'https://wa.me/' + anuncio.negocio.celular" target="_blank" class="btn btn-whatsapp action-whatsapp-btn" style="color: #FFF;">
                              <svg class="action-icon action-whatsapp-icon" version="1.1"  width="17px" height="17px" fill="white"  id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                  viewBox="0 0 64 64" style="enable-background: #fff;" xml:space="preserve">
                                <g id="WA_Logo">
                                  <g>
                                    <path d="M54.6,9.3C48.6,3.3,40.6,0,32.1,0C14.7,0,0.4,14.2,0.4,31.7c0,5.6,1.5,11,4.2,15.9L0.1,64L17,59.6
                                      c4.6,2.5,9.8,3.9,15.2,3.9l0,0l0,0c17.5-0.1,31.7-14.3,31.7-31.8C63.9,23.3,60.5,15.3,54.6,9.3z M32.1,58.1L32.1,58.1
                                      c-4.7,0-9.4-1.3-13.5-3.7l-1-0.6l-10,2.6l2.7-9.7l-0.6-1c-2.6-4.2-4-9.1-4-14c0-14.5,11.8-26.3,26.4-26.3c7,0,13.7,2.8,18.6,7.7
                                      s7.7,11.6,7.7,18.7C58.5,46.3,46.7,58.1,32.1,58.1z M46.6,38.4c-0.8-0.4-4.7-2.3-5.5-2.5c-0.7-0.3-1.3-0.4-1.8,0.4
                                      c-0.5,0.8-2.1,2.5-2.5,3.1c-0.5,0.5-0.9,0.6-1.7,0.2c-0.8-0.4-3.3-1.2-6.4-4c-2.3-2.1-4-4.7-4.4-5.5c-0.5-0.8-0.1-1.2,0.4-1.6
                                      c0.4-0.4,0.8-0.9,1.2-1.4c0.4-0.5,0.5-0.8,0.8-1.3c0.3-0.5,0.1-1-0.1-1.4s-1.8-4.3-2.5-5.9c-0.6-1.6-1.3-1.3-1.8-1.3
                                      c-0.5,0-1,0-1.5,0c-0.5,0-1.4,0.2-2.1,1c-0.7,0.8-2.8,2.7-2.8,6.6s2.8,7.6,3.3,8.2c0.4,0.5,5.6,8.6,13.5,12
                                      c1.9,0.8,3.4,1.3,4.5,1.7c1.9,0.6,3.6,0.5,5,0.3c1.6-0.2,4.7-1.9,5.4-3.8c0.6-1.8,0.6-3.5,0.5-3.8C47.9,38.9,47.4,38.7,46.6,38.4z
                                      "/>
                                  </g>
                                </g>
                              </svg>
                              {{anuncio.negocio.celular}}</a>
                          </div>
                        </div>
                        <div class="card-text quick-trust">
                          <span><i class="lni-check-mark-circle"></i> Comercio visible</span>
                          <span><i class="lni-eye"></i> Recomendado por sistema</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="anuncioModal" ref="anuncioModalRef" tabindex="-1" role="dialog" aria-labelledby="anuncioModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" v-if="selectedAnuncio">
          <div class="modal-header">
            <h5 class="modal-title" id="anuncioModalLabel">{{ selectedAnuncio.titulo }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <img v-if="selectedAnuncio" :src="getMainPhoto(selectedAnuncio)" class="img-fluid rounded mx-auto d-block mb-4" alt="Imagen del anuncio">

            <p class="lead">{{ selectedAnuncio.descripcion }}</p>

            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Contacto:
                <a :href="'https://wa.me/' + selectedAnuncio.negocio.celular" target="_blank" class="btn btn-whatsapp btn-sm" style="color: #FFF;">
                  <svg version="1.1" width="17px" height="17px" fill="white" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 64 64" style="enable-background: #fff;" xml:space="preserve"><g id="WA_Logo"><g><path d="M54.6,9.3C48.6,3.3,40.6,0,32.1,0C14.7,0,0.4,14.2,0.4,31.7c0,5.6,1.5,11,4.2,15.9L0.1,64L17,59.6 c4.6,2.5,9.8,3.9,15.2,3.9l0,0l0,0c17.5-0.1,31.7-14.3,31.7-31.8C63.9,23.3,60.5,15.3,54.6,9.3z M32.1,58.1L32.1,58.1 c-4.7,0-9.4-1.3-13.5-3.7l-1-0.6l-10,2.6l2.7-9.7l-0.6-1c-2.6-4.2-4-9.1-4-14c0-14.5,11.8-26.3,26.4-26.3c7,0,13.7,2.8,18.6,7.7 s7.7,11.6,7.7,18.7C58.5,46.3,46.7,58.1,32.1,58.1z M46.6,38.4c-0.8-0.4-4.7-2.3-5.5-2.5c-0.7-0.3-1.3-0.4-1.8,0.4 c-0.5,0.8-2.1,2.5-2.5,3.1c-0.5,0.5-0.9,0.6-1.7,0.2c-0.8-0.4-3.3-1.2-6.4-4c-2.3-2.1-4-4.7-4.4-5.5c-0.5-0.8-0.1-1.2,0.4-1.6 c0.4-0.4,0.8-0.9,1.2-1.4c0.4-0.5,0.5-0.8,0.8-1.3c0.3-0.5,0.1-1-0.1-1.4s-1.8-4.3-2.5-5.9c-0.6-1.6-1.3-1.3-1.8-1.3 c-0.5,0-1,0-1.5,0c-0.5,0-1.4,0.2-2.1,1c-0.7,0.8-2.8,2.7-2.8,6.6s2.8,7.6,3.3,8.2c0.4,0.5,5.6,8.6,13.5,12 c1.9,0.8,3.4,1.3,4.5,1.7c1.9,0.6,3.6,0.5,5,0.3c1.6-0.2,4.7-1.9,5.4-3.8c0.6-1.8,0.6-3.5,0.5-3.8C47.9,38.9,47.4,38.7,46.6,38.4z"/></g></g></svg>
                  {{ selectedAnuncio.negocio.celular }}
                </a>
              </li>
              <li class="list-group-item">
                <small class="text-muted">Publicado: {{ selectedAnuncio.created_at }}</small>
              </li>
            </ul>
          </div>
          <div class="modal-footer">
            <button @click="addToCartFromModal" class="btn btn-common mr-auto">
              <i class="lni-shopping-basket"></i> Agregar al Carrito
            </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

</section>
</template>

<script>
// INICIO: Importar jQuery y el plugin de modal de Bootstrap de forma explícita
import $ from 'jquery';
import 'bootstrap/js/dist/modal';
import { getAnuncioCardImage } from '../utils/anuncioImage';

export default {
  name: "Recomendados",
  data() {
    return {
      anuncios: [],
      selectedAnuncio: null,
      loading: false,
      loadError: "",
    };
  },
  methods: {
    getLink(id) {
      return window.location.origin + "/anuncio/detalle/"+id
    },
    showDetails(anuncio) {
      this.selectedAnuncio = anuncio;
      this.$nextTick(() => {
        $(this.$refs.anuncioModalRef).modal('show');
      });
    },
    getMainPhoto(anuncio) {
      return getAnuncioCardImage(anuncio);
    },
    resolveImagePath(image) {
      if (!image) {
        return window.location.origin + '/assets/img/logo.png';
      }

      if (image.startsWith('http://') || image.startsWith('https://')) {
        return image;
      }

      const normalized = image.startsWith('/') ? image : `/${image}`;
      return window.location.origin + normalized;
    },
    getBusinessLogo(image) {
      if (!image || image === 'default') {
        return window.location.origin + '/assets/img/logo.png';
      }

      if (image.startsWith('http://') || image.startsWith('https://')) {
        return image;
      }

      const normalized = image.startsWith('/') ? image : `/${image}`;
      if (normalized.includes('/storage/images/') && !normalized.includes('/thumbnail/')) {
        return window.location.origin + normalized.substring(0, normalized.lastIndexOf('/')) + '/thumbnail/' + normalized.substring(normalized.lastIndexOf('/') + 1);
      }

      return this.resolveImagePath(normalized);
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
    onCardMove(event) {
      const card = event.currentTarget;
      if (!card) return;
      const rect = card.getBoundingClientRect();
      const x = event.clientX - rect.left;
      const y = event.clientY - rect.top;
      const mx = ((x / rect.width) - 0.5) * 10;
      const my = ((y / rect.height) - 0.5) * 10;
      card.style.setProperty('--mx', `${mx.toFixed(2)}px`);
      card.style.setProperty('--my', `${my.toFixed(2)}px`);
    },
    onCardLeave(event) {
      const card = event.currentTarget;
      if (!card) return;
      card.style.setProperty('--mx', '0px');
      card.style.setProperty('--my', '0px');
    },
    isNuevo(anuncio) {
      if (!anuncio || !anuncio.created_at) return false;
      const created = new Date(anuncio.created_at);
      if (isNaN(created.getTime())) return false;
      const diff = Date.now() - created.getTime();
      return diff >= 0 && diff <= 1000 * 60 * 60 * 24 * 7;
    },
    addToCart(anuncio) {
      this.$root.$emit('add-to-cart', {
        id: anuncio.id,
        titulo: anuncio.titulo,
        precio: anuncio.producto ? anuncio.producto.precio_regular : 0,
        foto: this.getMainPhoto(anuncio),
        negocio_nombre: anuncio.negocio.nnegocio,
        negocio_celular: anuncio.negocio.celular
      });
    },
    addToCartFromModal() {
      if (this.selectedAnuncio) {
        this.addToCart(this.selectedAnuncio);
        $(this.$refs.anuncioModalRef).modal('hide');
      }
    }
  },
  mounted() {
    this.loading = true;
    this.loadError = "";
    axios({
      url: window.location.origin + "/recomendados",
      method: "GET",
    })
      .then((resp) => {
        this.anuncios = Array.isArray(resp.data) ? resp.data : [];
      })
      .catch((error) => {
        console.log(error);
        this.loadError = "No se pudo cargar recomendados. Intenta recargar.";
      })
      .finally(() => {
        this.loading = false;
      });
  },
}
</script>

<style scoped>
  .btn-whatsapp{
    background-color: #25D366;
  }
  .neon-title-recomendado {
    color: #eafff2 !important;
    display: table;
    margin: 0 auto 14px;
    position: relative;
    overflow: hidden;
    padding: 8px 20px;
    border: 4px solid #5bff9f;
    border-radius: 14px;
    background: linear-gradient(90deg, rgba(29, 104, 66, 0.78) 0%, rgba(22, 89, 71, 0.72) 100%);
    box-shadow: inset 0 0 14px rgba(91, 255, 159, 0.35), 0 0 24px rgba(91, 255, 159, 0.45), 0 6px 14px rgba(10, 35, 22, 0.38);
    text-shadow:
      0 0 7px rgba(81, 255, 143, 0.9),
      0 0 14px rgba(81, 255, 143, 0.65),
      0 0 22px rgba(64, 255, 202, 0.55),
      0 4px 10px rgba(8, 25, 16, 0.55);
    animation: neonPulseRecomendado 2.6s ease-in-out infinite;
  }
  .neon-title-recomendado::after {
    content: "";
    position: absolute;
    top: 0;
    left: -60%;
    width: 40%;
    height: 100%;
    background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.75) 50%, rgba(255,255,255,0) 100%);
    transform: skewX(-24deg);
    animation: titleShine 3s ease-in-out infinite;
  }
  .cards-status {
    margin: 6px auto 16px;
    padding: 8px 12px;
    border-radius: 12px;
    max-width: 520px;
    text-align: center;
    font-size: 13px;
    border: 1px solid;
  }
  .cards-loading {
    color: #5b4760;
    background: rgba(255, 255, 255, 0.85);
    border-color: #e9cadb;
  }
  .cards-empty {
    color: #6d4e5f;
    background: rgba(255, 250, 253, 0.9);
    border-color: #e8ccdc;
  }
  .cards-error {
    color: #8a1647;
    background: rgba(255, 233, 243, 0.9);
    border-color: #ff9ec6;
  }
  .recomendado-card {
    --mx: 0px;
    --my: 0px;
    border: 2px solid #f49ac8;
    border-radius: 16px;
    box-shadow: 0 14px 28px rgba(67, 26, 86, 0.30);
    overflow: hidden;
    transition: transform 220ms ease, box-shadow 220ms ease, border-color 220ms ease;
    animation: cardFadeIn 360ms ease both;
    background: #fff;
    height: 100%; /* Asegura que la tarjeta ocupe toda la altura disponible */
    display: flex;
    flex-direction: column;
  }
  .recomendado-card:hover {
    transform: translateY(-6px) scale(1.01);
    border-color: #ff7fc1;
    box-shadow: 0 20px 38px rgba(53, 14, 74, 0.42);
  }
  .recomendado-card .product-content {
    background: linear-gradient(180deg, #fffafd 0%, #fff4fa 100%);
    flex-grow: 1; /* Permite que el contenido crezca para llenar el espacio */
    display: flex;
    flex-direction: column;
    padding: 15px;
  }
  .recomendado-card .carousel-thumb {
    overflow: hidden;
    position: relative;
    height: 200px; /* Altura fija para el contenedor de la imagen */
  }
  .featured-pill {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 4;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    color: #fff;
    background: linear-gradient(90deg, #ff4aa0 0%, #d765ff 100%);
    box-shadow: 0 8px 18px rgba(61, 19, 90, 0.45);
    overflow: hidden;
  }
  .fresh-pill {
    position: absolute;
    left: 10px;
    top: 10px;
    z-index: 5;
    padding: 3px 9px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 800;
    color: #fff;
    letter-spacing: 0.2px;
    background: linear-gradient(90deg, #4ed889 0%, #1cc96f 100%);
    box-shadow: 0 8px 16px rgba(15, 87, 48, 0.34);
  }
  .quick-trust {
    margin-top: auto; /* Empuja este elemento al fondo */
    padding-top: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    color: #6f5a66;
    gap: 8px;
  }
  .quick-trust i {
    margin-right: 4px;
    color: #4fd28b;
  }
  .recomendado-card .product-title {
    margin-bottom: 5px;
    height: 44px; /* Altura fija para 2 líneas de título */
    overflow: hidden;
  }
  .recomendado-card .product-title a {
    transition: color var(--wf-speed-fast) ease, text-shadow var(--wf-speed-fast) ease;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .recomendado-card .product-title a:hover {
    color: #cc2f7d;
    text-shadow: 0 0 7px rgba(255, 102, 219, 0.35);
  }
  .recomendado-card .product-title a:focus-visible {
    outline: 3px solid rgba(255, 102, 219, 0.4);
    outline-offset: 2px;
    border-radius: 4px;
  }
  .featured-pill::after {
    content: "";
    position: absolute;
    top: 0;
    left: -55%;
    width: 46%;
    height: 100%;
    background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.75) 50%, rgba(255,255,255,0) 100%);
    transform: skewX(-24deg);
    animation: pillShine 2.7s ease-in-out infinite;
  }
  .recomendado-card .card-img-top {
    transition: transform 300ms ease, filter 300ms ease;
    transform: translate3d(var(--mx), var(--my), 0) scale(1.03);
    width: 100%;
    height: 100%; /* Ocupa todo el contenedor */
    object-fit: cover;
  }
  .recomendado-card:hover .card-img-top {
    transform: translate3d(calc(var(--mx) * 1.2), calc(var(--my) * 1.2), 0) scale(1.08);
    filter: saturate(1.07);
  }
  .text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .business-name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 2px;
    font-weight: 700;
    color: #6d2150;
  }
  .business-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
  }
  .business-copy {
    min-width: 0;
    flex: 1;
  }
  .business-location {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    max-width: 100%;
    color: #91627b;
    font-size: 12px;
  }
  .business-logo-shell {
    width: 52px;
    height: 52px;
    min-width: 52px;
    position: relative;
    cursor: pointer;
    border-radius: 50%;
    padding: 4px;
    background: linear-gradient(135deg, #fff6fb 0%, #ffd6ec 45%, #ffc0e0 100%);
    box-shadow: 0 10px 24px rgba(167, 52, 113, 0.22);
    border: 1px solid rgba(255, 255, 255, 0.9);
    animation: logoFloat 3.2s ease-in-out infinite;
    transition: transform 220ms ease, box-shadow 220ms ease;
  }
  .business-logo-mini {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
    border-radius: 50%;
    background: #fff;
    animation: logoPulse 2.8s ease-in-out infinite;
  }
  .business-logo-tooltip {
    position: absolute;
    left: 50%;
    bottom: calc(100% + 10px);
    transform: translateX(-50%) translateY(8px) scale(0.96);
    opacity: 0;
    pointer-events: none;
    min-width: 150px;
    padding: 8px 10px;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(109, 33, 80, 0.96) 0%, rgba(201, 67, 134, 0.96) 100%);
    color: #fff;
    box-shadow: 0 14px 26px rgba(86, 24, 61, 0.28);
    text-align: center;
    z-index: 10;
    transition: opacity 180ms ease, transform 180ms ease;
  }
  .business-logo-tooltip strong,
  .business-logo-tooltip small {
    display: block;
    line-height: 1.25;
  }
  .business-logo-tooltip small {
    margin-top: 2px;
    color: rgba(255, 255, 255, 0.86);
    font-size: 11px;
  }
  .business-logo-tooltip::after {
    content: '';
    position: absolute;
    left: 50%;
    top: 100%;
    transform: translateX(-50%);
    border-width: 6px;
    border-style: solid;
    border-color: rgba(201, 67, 134, 0.96) transparent transparent transparent;
  }
  .recomendado-card:hover .business-logo-shell {
    transform: translateY(-3px) scale(1.08) rotate(4deg);
    box-shadow: 0 16px 28px rgba(167, 52, 113, 0.3);
  }
  .business-logo-shell:hover .business-logo-tooltip,
  .business-logo-shell:focus-within .business-logo-tooltip {
    opacity: 1;
    transform: translateX(-50%) translateY(0) scale(1);
  }
  .recomendado-card:hover .business-logo-mini {
    filter: saturate(1.12) brightness(1.03);
  }
  .address-line {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 10px;
  }
  .card-footer-actions {
    margin-top: auto;
    padding-top: 10px;
    border-top: 1px solid rgba(0,0,0,0.05);
  }
  .action-buy-btn,
  .action-whatsapp-btn {
    position: relative;
    overflow: hidden;
    border-radius: 999px;
    transition: transform 180ms ease, box-shadow 180ms ease, filter 180ms ease;
  }
  .action-buy-btn::after,
  .action-whatsapp-btn::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,0.16) 45%, transparent 100%);
    transform: translateX(-120%);
    transition: transform 360ms ease;
    pointer-events: none;
  }
  .action-buy-btn {
    box-shadow: 0 10px 18px rgba(214, 51, 132, 0.22);
  }
  .action-whatsapp-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 10px 18px rgba(37, 211, 102, 0.24);
  }
  .action-buy-btn:hover,
  .action-whatsapp-btn:hover {
    transform: translateY(-2px) scale(1.03);
    filter: brightness(1.03);
  }
  .action-buy-btn:hover::after,
  .action-whatsapp-btn:hover::after {
    transform: translateX(120%);
  }
  .action-buy-btn:hover {
    box-shadow: 0 14px 24px rgba(214, 51, 132, 0.3);
  }
  .action-whatsapp-btn:hover {
    box-shadow: 0 14px 24px rgba(37, 211, 102, 0.32);
  }
  .action-icon {
    transition: transform 180ms ease;
  }
  .action-buy-btn:hover .action-icon {
    transform: translateX(2px) scale(1.08);
  }
  .action-whatsapp-btn:hover .action-whatsapp-icon {
    transform: scale(1.1) rotate(-6deg);
  }
  ::v-deep .owl-item.active .recomendado-card,
  ::v-deep .owl-item.center .recomendado-card {
    box-shadow: 0 0 0 2px rgba(255, 127, 193, 0.55), 0 0 24px rgba(170, 85, 255, 0.48), 0 18px 36px rgba(53, 14, 74, 0.42);
  }
  @keyframes pillShine {
    0% {
      left: -55%;
    }
    100% {
      left: 130%;
    }
  }
  @keyframes cardFadeIn {
    from {
      opacity: 0;
      transform: translateY(8px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  @keyframes logoFloat {
    0%, 100% {
      transform: translateY(0);
    }
    50% {
      transform: translateY(-4px);
    }
  }
  @keyframes logoPulse {
    0%, 100% {
      box-shadow: 0 0 0 0 rgba(255, 111, 180, 0.18);
    }
    50% {
      box-shadow: 0 0 0 7px rgba(255, 111, 180, 0);
    }
  }
  @keyframes neonPulseRecomendado {
    0%, 100% {
      text-shadow:
        0 0 7px rgba(81, 255, 143, 0.9),
        0 0 14px rgba(81, 255, 143, 0.65),
        0 0 22px rgba(64, 255, 202, 0.55),
        0 4px 10px rgba(12, 37, 24, 0.45);
    }
    50% {
      text-shadow:
        0 0 10px rgba(81, 255, 143, 1),
        0 0 20px rgba(81, 255, 143, 0.8),
        0 0 30px rgba(64, 255, 202, 0.7),
        0 5px 12px rgba(12, 37, 24, 0.5);
    }
  }
  @keyframes titleShine {
    0% { left: -60%; }
    100% { left: 130%; }
  }

  @media (max-width: 767px) {
    .quick-trust {
      flex-direction: column;
      align-items: flex-start;
    }
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
  .ribbon span::after {
    color: #fff2b0;
    -webkit-text-stroke: 0;
    text-shadow:
      0 2px 4px rgba(0, 0, 0, 0.75),
      0 0 12px rgba(255, 160, 0, 1),
      0 0 24px rgba(255, 110, 0, 0.98),
      0 0 40px rgba(255, 80, 0, 0.9);
    animation: ribbonGlow 2s ease-in-out infinite;
  }

  .ribbon span {
  background-image:
    linear-gradient(90deg, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0.08) 18%, rgba(255,255,255,0.08) 30%, rgba(255,255,255,0.08) 40%, rgba(255,255,255,0.08) 65%, rgba(255,255,255,0) 85%, rgba(255,255,255,0) 100%),
    linear-gradient(90deg, rgba(0,0,0,0.22) 0%, rgba(0,0,0,0) 60%);
    -webkit-mask-image: linear-gradient(90deg, rgba(0,0,0,1) 0%, rgba(0,0,0,0.9) 20%, rgba(0,0,0,0.7) 45%, rgba(0,0,0,0.4) 70%, rgba(0,0,0,0) 100%);
    mask-image: linear-gradient(90deg, rgba(0,0,0,1) 0%, rgba(0,0,0,0.9) 20%, rgba(0,0,0,0.7) 45%, rgba(0,0,0,0.4) 70%, rgba(0,0,0,0) 100%);
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
</style>
