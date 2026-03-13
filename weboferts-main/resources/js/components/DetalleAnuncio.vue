<template>
  <div>
    <!-- Banner Superior con Buscador -->
    <div id="hero-area">
      <div class="overlay"></div>
      <div class="container">
        <div class="row">
          <div class="col-md-12 col-sm-12 text-center">
            <div class="contents-ctg">
              <buscar :categorias="categoriasList"></buscar>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="section-padding">
      <div class="container">
        <div class="product-info row">
          <div class="col-lg-7 col-md-12 col-xs-12">
            <div class="details-box ads-details-wrapper">
              <div
                id="owl-demo"
                class="owl-carousel owl-theme"
                style="opacity: 1; display: block"
              >
                <div class="owl-wrapper-outer">
                  <div
                    class="owl-wrapper"
                    style="
                      width: 100%;
                      left: 0px;
                      display: block;
                      transition: all 0ms ease 0s;
                      transform: translate3d(0px, 0px, 0px);
                    "
                  >
                    <div
                      class="owl-item" style="width: 100%"
                      v-for="foto in anuncio.fotos"
                      :key="foto.id"
                    >
                      <div class="item">
                        <div class="product-img">
                          <img
                            class="img-fluid detail-image"
                            :src="foto.url"
                            alt=""
                          />
                        </div>
                        <span class="price">{{anuncio.producto.precio_regular}} &nbsp;bs</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-5 col-md-12 col-xs-12">
            <div class="details-box description-box">
              <div class="ads-details-info">
                <h2>{{anuncio.titulo}}</h2>
                <span class="product-name">{{anuncio.producto.nombre}}</span>
                <p class="mb-2 description-text">
                    {{anuncio.descripcion}}
                </p>
                <div class="details-meta">
                  <span
                    ><a href="#"
                      ><i class="lni-alarm-clock"></i> {{ formatDate(anuncio.created_at) }}</a
                    ></span
                  >
                  <span
                    ><a href="#"><i class="lni-map-marker"></i> {{ anuncio.negocio.ciudad || 'Bolivia' }}</a></span
                  >
                  <span
                    ><a href="#"><i class="lni-eye"></i> 299 Vistas</a></span
                  >
                </div>
                <h4 class="title-small mb-3">Dirección:</h4>
                <ul class="list-specification">
                  <li>
                    <i class="lni-check-mark-circle"></i> {{anuncio.negocio.dir}}
                  </li>
                </ul>
              </div>
              <ul class="advertisement mb-4">
                <li>
                  <p>
                    <strong><i class="lni-folder"></i> Categorias:</strong>
                     <span
                        v-for="cate in anuncio.categorias"
                        :key="cate.id"
                        class="float-right category-badge">{{cate.cname}}&nbsp;&nbsp;
                     </span>
                  </p>
                </li>
              </ul>
              <div class="ads-btn mb-4">
                <a href="#" class="btn btn-common btn-reply"
                  ><i class="lni-envelope"></i> Email</a
                >
                <a :href="'https://wa.me/' + anuncio.negocio.celular" target="_blank" class="btn btn-common btn-whatsapp"
                  ><i class="lni-phone-handset"></i>{{anuncio.negocio.celular}}</a
                >
              </div>
              <div class="share">
                <span>Compartir: </span>
                <div class="social-link">
                  <a class="facebook" href="#"
                    ><i class="lni-facebook-filled"></i
                  ></a>
                  <a class="twitter" href="#"
                    ><i class="lni-twitter-filled"></i
                  ></a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="description-info">
          <div class="row">
            <div class="col-lg-8 col-md-12 col-xs-12">
              <div class="description description-box-full">
                <h4>Descripción del Producto</h4>
                <p>
                  {{ anuncio.producto.descripcion }}
                </p>
              </div>

              <!-- Componente de Reseñas -->
              <reviews-component
                v-if="anuncio.negocio && anuncio.negocio.id"
                :negocio-id="anuncio.negocio.id"
              ></reviews-component>
            </div>

            <div class="col-lg-4 col-md-12 col-xs-12">
              <!-- Mapa de Ubicación -->
              <div class="description description-box-full" v-if="hasMap || googleMapsLink">
                <h4>Ubicación</h4>
                <div
                  v-if="hasMap"
                  ref="map"
                  class="ad-location-map"
                ></div>
                <div v-else class="map-unavailable">
                  No hay coordenadas exactas disponibles, pero puedes abrir la ubicación en Google Maps.
                </div>
                <a
                  v-if="googleMapsLink"
                  :href="googleMapsLink"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="btn btn-common btn-map-link mt-3"
                >
                  <i class="lni-map-marker"></i> Abrir en Google Maps
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Buscar from './BuscarComponent.vue';

export default {
  name: "DetalleAnuncio",
  components: {
    'buscar': Buscar,
  },
  data() {
      return {
          anuncio: {
            fotos: [],
            producto: {},
            negocio: {},
            categorias: []
          },
          anuncio_id: 0,
          categoriasList: [],
          map: null,
      }
  },
  computed: {
    normalizedCoordinates() {
      const negocio = this.anuncio.negocio || {};
      const lat = this.parseCoordinate(negocio.latitude);
      const lng = this.parseCoordinate(
        negocio.longitud !== undefined && negocio.longitud !== null
          ? negocio.longitud
          : negocio.longitude
      );

      if (lat === null || lng === null) {
        return null;
      }

      return { lat, lng };
    },
    fallbackCoordinates() {
      return this.extractCoordinatesFromMapsUrl((this.anuncio.negocio || {}).gmaps);
    },
    mapCoordinates() {
      return this.normalizedCoordinates || this.fallbackCoordinates;
    },
    mapQuery() {
      if (this.mapCoordinates) {
        return `${this.mapCoordinates.lat},${this.mapCoordinates.lng}`;
      }

      const negocio = this.anuncio.negocio || {};
      const gmapsQuery = this.extractQueryFromMapsUrl(negocio.gmaps);
      if (gmapsQuery) {
        return gmapsQuery;
      }

      return [negocio.dir, negocio.ciudad, 'Bolivia']
        .filter(Boolean)
        .join(', ');
    },
    googleMapsLink() {
      if (!this.mapQuery) {
        return '';
      }

      return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(this.mapQuery)}`;
    },
    hasMap() {
      return Boolean(this.mapCoordinates);
    },
  },
  beforeDestroy() {
    this.destroyMap();
  },
  methods: {
    fetchCategorias() {
      axios.get(window.location.origin + "/home/categorias").then((res) => {
        this.categoriasList = res.data;
      });
    },
    formatDate(dateString) {
      if (!dateString) return '';
      const options = { year: 'numeric', month: 'short', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('es-ES', options);
    },
    parseCoordinate(value) {
      if (value === undefined || value === null || value === '') {
        return null;
      }

      const parsed = parseFloat(value);
      return Number.isFinite(parsed) ? parsed : null;
    },
    extractCoordinatesFromMapsUrl(url) {
      const query = this.extractQueryFromMapsUrl(url);
      if (!query) {
        return null;
      }

      const match = query.match(/(-?[0-9]+(?:\.[0-9]+)?)\s*,\s*(-?[0-9]+(?:\.[0-9]+)?)/);
      if (!match) {
        return null;
      }

      return {
        lat: parseFloat(match[1]),
        lng: parseFloat(match[2]),
      };
    },
    extractQueryFromMapsUrl(url) {
      if (!url || typeof url !== 'string') {
        return '';
      }

      const rawUrl = url.trim();
      if (!rawUrl) {
        return '';
      }

      const candidates = [rawUrl];
      try {
        candidates.push(decodeURIComponent(rawUrl));
      } catch (e) {
        // Ignorar URLs parcialmente codificadas.
      }

      for (const candidate of candidates) {
        const atMatch = candidate.match(/@(-?[0-9]+(?:\.[0-9]+)?),(-?[0-9]+(?:\.[0-9]+)?)/);
        if (atMatch) {
          return `${atMatch[1]},${atMatch[2]}`;
        }

        const pinMatch = candidate.match(/!3d(-?[0-9]+(?:\.[0-9]+)?)!4d(-?[0-9]+(?:\.[0-9]+)?)/);
        if (pinMatch) {
          return `${pinMatch[1]},${pinMatch[2]}`;
        }
      }

      try {
        const parsedUrl = new URL(rawUrl);
        const query = parsedUrl.searchParams.get('q') || parsedUrl.searchParams.get('query') || parsedUrl.searchParams.get('ll');
        if (query) {
          return query;
        }

        const placeMatch = decodeURIComponent(parsedUrl.pathname).match(/\/place\/([^/]+)/i);
        if (placeMatch && placeMatch[1]) {
          return placeMatch[1].replace(/\+/g, ' ');
        }
      } catch (e) {
        // Si no se puede parsear como URL, dejamos que el fallback use la dirección del negocio.
      }

      return '';
    },
    destroyMap() {
      if (this.map) {
        this.map.remove();
        this.map = null;
      }
    },
    initMap() {
      if (!this.hasMap) {
        this.destroyMap();
        return;
      }

      this.$nextTick(() => {
        if (typeof L === 'undefined' || !this.$refs.map || !this.mapCoordinates) {
          return;
        }

        this.destroyMap();

        const { lat, lng } = this.mapCoordinates;
        this.map = L.map(this.$refs.map).setView([lat, lng], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; OpenStreetMap contributors'
        }).addTo(this.map);

        L.marker([lat, lng])
          .addTo(this.map)
          .bindPopup(this.anuncio.negocio && this.anuncio.negocio.nnegocio ? this.anuncio.negocio.nnegocio : 'Ubicación del negocio')
          .openPopup();

        setTimeout(() => {
          if (this.map) {
            this.map.invalidateSize();
          }
        }, 120);
      });
    }
  },
  created() {
    this.anuncio_id = window.location.pathname.split("/").pop()

    axios
      .get(window.location.origin + "/detalleanuncio/" + this.anuncio_id)
      .then((res) => {
        this.anuncio = res.data.result || this.anuncio;
        this.initMap();
      })
      .catch((err) => {
        console.error("Error loading anuncio detail:", err);
      });

    this.fetchCategorias();
  },
};
</script>

<style lang="scss" scoped>
  .contents-ctg {
    padding: 110px 0 160px;
  }

  /* Estilos para la imagen del detalle */
  .detail-image {
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    border: 4px solid #fff;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    width: 100%;
    object-fit: cover;
  }

  .detail-image:hover {
    transform: scale(1.02);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
  }

  /* Estilos para la caja de descripción lateral */
  .description-box {
    background: #fff;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 10px 25px rgba(118, 47, 78, 0.1);
    border: 1px solid #f0d8e5;
    height: 100%;
  }

  .description-box h2 {
    color: #d63384;
    font-weight: 700;
  }

  .product-name {
    color: #666;
    font-size: 16px;
    font-weight: 500;
    display: block;
    margin-bottom: 15px;
  }

  .description-text {
    color: #555;
    line-height: 1.6;
  }

  /* Estilos para la caja de descripción completa inferior */
  .description-box-full {
    background: linear-gradient(180deg, #ffffff 0%, #fffbfd 100%);
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 10px 25px rgba(118, 47, 78, 0.1);
    border: 1px solid #f0d8e5;
    margin-top: 30px;
  }

  .description-box-full h4 {
    color: #d63384;
    border-bottom: 2px solid #f8e1ee;
    padding-bottom: 10px;
    margin-bottom: 20px;
    display: inline-block;
  }

  .btn-whatsapp {
    background-color: #25D366;
    color: #fff !important;
    border: none;
  }

  .btn-whatsapp:hover {
    background-color: #128C7E;
    box-shadow: 0 5px 15px rgba(37, 211, 102, 0.4);
  }

  .category-badge {
    background: #d63384;
    color: #fff !important;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 12px;
  }

  .price {
    background: #d63384 !important;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(214, 51, 132, 0.3);
  }

  .ad-location-map {
    width: 100%;
    height: 300px;
    border-radius: 14px;
    box-shadow: 0 10px 25px rgba(118, 47, 78, 0.12);
    overflow: hidden;
  }

  .map-unavailable {
    padding: 16px;
    border-radius: 14px;
    background: #fff5fa;
    color: #7d4562;
    border: 1px solid #f2d5e3;
  }

  .btn-map-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #d63384 0%, #ff5fa2 100%);
    color: #fff !important;
    border: none;
    box-shadow: 0 10px 18px rgba(214, 51, 132, 0.22);
  }

  .btn-map-link:hover {
    color: #fff !important;
    transform: translateY(-1px);
    box-shadow: 0 14px 24px rgba(214, 51, 132, 0.28);
  }
</style>
