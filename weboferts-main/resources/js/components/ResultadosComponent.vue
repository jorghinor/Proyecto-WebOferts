<template>
<section class="featured-lis section-padding resultados-wrap">
  <div class="container resultados-container">
    <div class="row">
      <aside class="col-lg-3 col-md-4 mb-3 mb-md-0">
        <div class="filters-panel sticky-filters">
          <span class="filters-kicker">Búsqueda premium</span>
          <h5 class="filters-title">Afina lo que quieres ver</h5>
          <p class="filters-help">Refina resultados en tiempo real, con una presentación más clara y comercial.</p>

          <div v-if="hasFilters" class="search-glance">
            <span v-for="(item, idx) in activeFilters" :key="'aside-' + idx" class="soft-chip">{{ item }}</span>
          </div>

          <div class="filters-metrics">
            <div class="metric-pill">
              <strong>{{ visibleResults }}</strong>
              <span>visibles</span>
            </div>
            <div class="metric-pill accent">
              <strong>{{ totalResults }}</strong>
              <span>totales</span>
            </div>
          </div>

          <label class="filter-label">Tipo de producto</label>
          <select v-model="tipoSeleccionado" class="form-control filter-input">
            <option value="todos">Todos</option>
            <option v-for="tipo in tiposDisponibles" :key="tipo" :value="tipo">{{ tipo }}</option>
          </select>

          <label class="filter-label mt-3">Precio mínimo (Bs.)</label>
          <input v-model.number="precioMin" type="number" min="0" class="form-control filter-input" placeholder="0" />

          <label class="filter-label mt-3">Precio máximo (Bs.)</label>
          <input v-model.number="precioMax" type="number" min="0" class="form-control filter-input" placeholder="Sin límite" />

          <div class="filter-actions">
            <button class="btn btn-sm btn-gradient-soft" @click="resetLocalFilters">
              Reset filtros locales
            </button>
            <button v-if="hasFilters" class="btn btn-sm btn-outline-soft" @click="clearFilters">
              Limpiar búsqueda
            </button>
          </div>
        </div>
      </aside>

      <div class="col-lg-9 col-md-8 wow fadeIn" data-wow-delay="0.5s">
        <div class="resultados-hero">
          <div>
            <span class="hero-kicker">Resultados / búsqueda</span>
            <h1 class="section-title resultados-title">Resultados de búsqueda</h1>
            <p class="resultados-subtitle">
              {{ visibleResults }} visible{{ visibleResults === 1 ? '' : 's' }} de {{ totalResults }} anuncio{{ totalResults === 1 ? '' : 's' }} encontrado{{ totalResults === 1 ? '' : 's' }}.
            </p>
          </div>
          <div class="hero-badges">
            <span class="hero-badge">
              <i class="lni-layers"></i>
              {{ activeFilters.length }} filtro{{ activeFilters.length === 1 ? '' : 's' }} activo{{ activeFilters.length === 1 ? '' : 's' }}
            </span>
            <span class="hero-badge accent">
              <i class="lni-sort-alpha-asc"></i>
              {{ sortLabel }}
            </span>
          </div>
        </div>

        <div v-if="hasFilters" class="active-filters-bar mb-3">
          <span class="filters-label">Filtros activos:</span>
          <span v-for="(item, idx) in activeFilters" :key="idx" class="filter-chip">{{ item }}</span>
          <button class="btn btn-sm btn-outline-soft ml-2" @click="clearFilters">Limpiar filtros</button>
        </div>

        <div v-if="texto && texto.trim()" class="query-spotlight">
          <span class="query-pill">Buscando: <strong>{{ texto }}</strong></span>
        </div>

        <div class="results-tools mb-3">
          <div class="view-toggle">
            <button class="btn btn-sm result-toggle" :class="viewMode === 'masonry' ? 'active' : 'btn-outline-primary'" @click="viewMode = 'masonry'">Mosaico</button>
            <button class="btn btn-sm result-toggle ml-1" :class="viewMode === 'list' ? 'active' : 'btn-outline-primary'" @click="viewMode = 'list'">Lista</button>
          </div>
          <div class="sort-select-wrap">
            <select v-model="sortBy" class="form-control form-control-sm sort-select">
              <option value="recientes">Más recientes</option>
              <option value="precio_asc">Precio menor a mayor</option>
              <option value="precio_desc">Precio mayor a menor</option>
              <option value="titulo_asc">Título A-Z</option>
            </select>
          </div>
        </div>

        <div v-if="loading" :class="viewMode === 'masonry' ? 'masonry-grid' : 'list-grid'">
          <div v-for="n in 6" :key="'sk' + n" class="masonry-item">
            <div class="skeleton-card">
              <div class="skeleton skeleton-image"></div>
              <div class="skeleton-content">
                <div class="skeleton skeleton-line w70"></div>
                <div class="skeleton skeleton-line w90"></div>
                <div class="skeleton skeleton-line w55"></div>
                <div class="skeleton skeleton-line w40"></div>
              </div>
            </div>
          </div>
        </div>

        <div v-else :class="viewMode === 'masonry' ? 'masonry-grid' : 'list-grid'">
          <div v-for="anuncio in displayedAnuncios" :key="anuncio.id" class="masonry-item" data-aos="fade-up">
            <div class="featured-box premium-card" @mousemove="onCardMove($event)" @mouseleave="onCardLeave($event)">
              <figure class="featured-figure">
                <div
                  v-if="anuncio.paquete"
                  :class="['ribbon', 'ribbon-top-left', ribbonShapeClass(anuncio.paquete)]"
                  :style="{ '--ribbon-color': anuncio.paquete.color }"
                >
                  <span :data-label="ribbonText(anuncio.paquete)"></span>
                </div>
                <div class="featured-pill" :class="{ 'featured-pill--premium': anuncio.paquete && anuncio.paquete.label }">
                  {{ getPackageLabel(anuncio) }}
                </div>
                <div class="city-pill">
                  <i class="lni-map-marker"></i>
                  {{ anuncio.negocio && anuncio.negocio.ciudad ? anuncio.negocio.ciudad : 'Bolivia' }}
                </div>
                <div class="icon"><i class="lni-heart"></i></div>
                <a href="#">
                  <img
                    class="img-fluid cover"
                    :class="{ 'is-loaded': isImageLoaded(anuncio) }"
                    :src="getMainPhoto(anuncio)"
                    alt=""
                    loading="lazy"
                    @load="markImageLoaded(anuncio)"
                  />
                </a>
              </figure>

              <div class="feature-content">
                <div class="product">
                  <a href="#"><i class="lni-folder"></i> {{ anuncio.producto && anuncio.producto.tipoproducto ? anuncio.producto.tipoproducto : 'Selección destacada' }}</a>
                </div>

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

                <h4 class="product-title">
                  <a :href="getLink(anuncio.id)">{{ anuncio.titulo }}</a>
                </h4>

                <span class="product-badge">{{ anuncio.producto && anuncio.producto.nombre ? anuncio.producto.nombre : 'Producto' }}</span>

                <span class="adcategories">
                  <span v-for="cate in anuncio.categorias" :key="cate.id" class="result-tag">{{ cate.cname }}</span>
                </span>

                <div class="quick-trust">
                  <span><i class="lni-check-mark-circle"></i> Comercio visible</span>
                  <span><i class="lni-eye"></i> Contacto directo</span>
                </div>

                <ul class="address">
                  <li><a href="#"><i class="lni-alarm-clock"></i> Oferta vigente</a></li>
                  <li><a href="#"><i class="lni-user"></i> {{ anuncio.negocio && anuncio.negocio.nnegocio ? anuncio.negocio.nnegocio : 'Comercio aliado' }}</a></li>
                  <li><a href="#"><i class="lni-package"></i> {{ anuncio.producto && anuncio.producto.tipoproducto ? anuncio.producto.tipoproducto : 'General' }}</a></li>
                </ul>

                <div class="listing-bottom">
                  <div class="price-block">
                    <small>Desde</small>
                    <h3 class="price">{{ formatPrice(anuncio.producto && anuncio.producto.precio_regular ? anuncio.producto.precio_regular : 0) }} Bs.</h3>
                  </div>

                  <div class="card-footer-actions">
                    <a :href="getLink(anuncio.id)" class="btn btn-common btn-sm action-detail-btn">
                      <i class="lni-eye action-icon"></i> Ver detalle
                    </a>
                    <a :href="getWhatsAppLink(anuncio.negocio && anuncio.negocio.celular)" target="_blank" rel="noopener" class="btn btn-whatsapp btn-sm action-whatsapp-btn text-white">
                      <i class="lni-phone-handset action-icon"></i> WhatsApp
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div v-if="displayedAnuncios.length === 0" class="col-12">
            <div class="empty-state text-center mt-3">
              <div class="empty-icon"><i class="lni-search-alt"></i></div>
              <h4>No encontramos coincidencias con esos filtros</h4>
              <p>Prueba ampliar el rango de precio, cambiar el tipo o limpiar la búsqueda para descubrir más opciones.</p>
              <div class="empty-actions">
                <button class="btn btn-gradient-soft btn-sm" @click="resetLocalFilters">Reset filtros locales</button>
                <button v-if="hasFilters" class="btn btn-outline-soft btn-sm" @click="clearFilters">Limpiar búsqueda</button>
              </div>
            </div>
          </div>
        </div>

        <div class="pagination-bar" v-if="pagination && pagination.last_page > 1">
          <nav>
            <ul class="pagination justify-content-center">
              <li class="page-item">
                <button v-if="pagination.pre_page != null" class="btn page-link active" type="submit" @click="previo">
                  <span v-if="loadingPrevio" class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                  Previo
                </button>
              </li>

              <template v-for="pagina in (0, pagination.last_page)">
                <button v-if="pagina - 1 === pagination.actual_page" :key="pagina" class="btn mbtn page-link active" type="submit" disabled>{{ pagina }}</button>
                <button v-else :key="pagina" class="btn mbtn page-link" type="submit" @click="go(pagina - 1)">{{ pagina }}</button>
              </template>

              <li class="page-item">
                <button v-if="pagination.last_page > 0 && pagination.actual_page < lastPageIndex" class="btn page-link active" type="submit" @click="ultimo">
                  <span v-if="loadingNext" class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                  Último
                </button>
              </li>
              <li class="page-item">
                <button v-if="pagination.next_page != null" class="btn page-link active" type="submit" @click="siguiente">
                  <span v-if="loadingNext" class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                  Sig
                </button>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</section>
</template>

<script>
import { getAnuncioCardImage } from "../utils/anuncioImage";

export default {
  name: "Resultados",
  data() {
    return {
      texto: "",
      ciudad: "",
      categoria: "",
      anuncios: [],
      page: 0,
      pages: 5,
      pagination: {
        cuenta: 1,
      },
      prevDisabled: true,
      nextDisabled: true,
      loadingNext: false,
      loadingPrevio: false,
      loading: false,
      precioMin: null,
      precioMax: null,
      tipoSeleccionado: "todos",
      sortBy: "recientes",
      viewMode: "masonry",
      imageLoaded: {},
    };
  },
  methods: {
    formatFilterValue(value) {
      if (!value) return "";
      const text = value.toString().trim();
      if (!text) return "";
      return text.charAt(0).toUpperCase() + text.slice(1);
    },
    resolveImagePath(path) {
      if (!path) {
        return window.location.origin + "/assets/img/logo.png";
      }

      if (path.startsWith("http://") || path.startsWith("https://")) {
        return path;
      }

      const normalized = path.startsWith("/") ? path : `/${path}`;
      return window.location.origin + normalized;
    },
    getBusinessLogo(image) {
      if (!image || image === "default") {
        return window.location.origin + "/assets/img/logo.png";
      }

      if (image.startsWith("http://") || image.startsWith("https://")) {
        return image;
      }

      const normalized = image.startsWith("/") ? image : `/${image}`;
      if (normalized.includes("/storage/images/") && !normalized.includes("/thumbnail/")) {
        return window.location.origin + normalized.substring(0, normalized.lastIndexOf("/")) + "/thumbnail/" + normalized.substring(normalized.lastIndexOf("/") + 1);
      }

      return this.resolveImagePath(normalized);
    },
    getMainPhoto(anuncio) {
      return getAnuncioCardImage(anuncio);
    },
    getPackageLabel(anuncio) {
      if (anuncio && anuncio.paquete && anuncio.paquete.label) {
        return anuncio.paquete.label;
      }
      return "Visible";
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
    formatPrice(value) {
      const amount = Number(value || 0);
      if (Number.isNaN(amount)) return "0";
      return amount.toLocaleString("es-BO");
    },
    getWhatsAppLink(phone) {
      const raw = (phone || "").toString();
      const digits = raw.replace(/[^0-9]/g, "");
      return digits ? `https://wa.me/${digits}` : "#";
    },
    onCardMove(event) {
      const card = event.currentTarget;
      if (!card) return;
      const rect = card.getBoundingClientRect();
      const x = event.clientX - rect.left;
      const y = event.clientY - rect.top;
      const mx = ((x / rect.width) - 0.5) * 10;
      const my = ((y / rect.height) - 0.5) * 10;
      card.style.setProperty("--mx", `${mx.toFixed(2)}px`);
      card.style.setProperty("--my", `${my.toFixed(2)}px`);
    },
    onCardLeave(event) {
      const card = event.currentTarget;
      if (!card) return;
      card.style.setProperty("--mx", "0px");
      card.style.setProperty("--my", "0px");
    },
    markImageLoaded(anuncio) {
      if (!anuncio || !anuncio.id) return;
      this.$set(this.imageLoaded, anuncio.id, true);
    },
    isImageLoaded(anuncio) {
      return !!(anuncio && anuncio.id && this.imageLoaded[anuncio.id]);
    },
    fetchAnuncios() {
      this.loading = true;
      this.anuncios = [];

      const queryString = window.location.search;
      const urlParams = new URLSearchParams(queryString);
      this.texto = urlParams.get("txt") || "";
      this.ciudad = urlParams.get("ciudad") || "";
      this.categoria = urlParams.get("cat") || "";

      const fData = {
        texto: this.texto,
        ciudad: this.ciudad,
        categoria: this.categoria,
        page: this.page,
        pages: this.pages,
        cuenta: this.pagination.cuenta,
      };

      axios({
        url: window.location.origin + "/buscar",
        data: fData,
        method: "POST",
      })
        .then((resp) => {
          this.anuncios = resp.data.result.anuncios || [];
          this.pagination = {
            first_page: resp.data.result.pagination.first_page,
            actual_page: resp.data.result.pagination.actual_page,
            next_page: resp.data.result.pagination.next_page,
            total: resp.data.result.pagination.total,
            pre_page: resp.data.result.pagination.pre_page,
            pages: resp.data.result.pagination.pages,
            last_page: resp.data.result.pagination.last_page,
            cuenta: resp.data.result.pagination.cuenta,
          };
          this.loadingNext = false;
          this.loadingPrevio = false;
          this.prevDisabled = this.pagination.pre_page === null;
          this.nextDisabled = this.pagination.next_page === null;
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.loading = false;
        });
    },
    siguiente() {
      this.loadingNext = true;
      this.page = Number(this.pagination.actual_page + 1);
      this.fetchAnuncios();
    },
    previo() {
      this.loadingPrevio = true;
      this.page = Number(this.pagination.actual_page - 1);
      if (this.page >= 0) {
        this.fetchAnuncios();
      }
    },
    ultimo() {
      this.loadingNext = true;
      this.page = Number(this.pagination.last_page > 0 ? this.pagination.last_page - 1 : 0);
      this.fetchAnuncios();
    },
    go(page) {
      this.loadingNext = true;
      this.page = page;
      this.fetchAnuncios();
    },
    clearFilters() {
      window.location.href = "/resultados";
    },
    resetLocalFilters() {
      this.precioMin = null;
      this.precioMax = null;
      this.tipoSeleccionado = "todos";
    },
    getLink(id) {
      return window.location.origin + "/anuncio/detalle/" + id;
    },
  },
  computed: {
    totalResults() {
      return this.pagination && this.pagination.total ? this.pagination.total : 0;
    },
    visibleResults() {
      return this.displayedAnuncios.length;
    },
    lastPageIndex() {
      return this.pagination && this.pagination.last_page ? this.pagination.last_page - 1 : 0;
    },
    hasFilters() {
      return !!((this.texto && this.texto.trim()) || (this.ciudad && this.ciudad.trim()) || (this.categoria && this.categoria.trim()));
    },
    activeFilters() {
      const items = [];
      if (this.texto && this.texto.trim()) items.push("Palabra: " + this.texto.trim());
      if (this.ciudad && this.ciudad.trim()) items.push("Ciudad: " + this.formatFilterValue(this.ciudad));
      if (this.categoria && this.categoria.trim()) items.push("Categoria: " + this.formatFilterValue(this.categoria));
      return items;
    },
    sortLabel() {
      if (this.sortBy === "precio_asc") return "Precio ascendente";
      if (this.sortBy === "precio_desc") return "Precio descendente";
      if (this.sortBy === "titulo_asc") return "Título A-Z";
      return "Más recientes";
    },
    tiposDisponibles() {
      const set = new Set();
      (this.anuncios || []).forEach((a) => {
        const t = a && a.producto && a.producto.tipoproducto ? a.producto.tipoproducto : "";
        if (t) set.add(t);
      });
      return Array.from(set);
    },
    filteredAnuncios() {
      return (this.anuncios || []).filter((anuncio) => {
        const precio = anuncio && anuncio.producto && anuncio.producto.precio_regular ? Number(anuncio.producto.precio_regular) : 0;
        const tipo = anuncio && anuncio.producto && anuncio.producto.tipoproducto ? anuncio.producto.tipoproducto : "";

        const minOk = this.precioMin === null || this.precioMin === "" || precio >= Number(this.precioMin);
        const maxOk = this.precioMax === null || this.precioMax === "" || precio <= Number(this.precioMax);
        const tipoOk = this.tipoSeleccionado === "todos" || tipo === this.tipoSeleccionado;
        return minOk && maxOk && tipoOk;
      });
    },
    displayedAnuncios() {
      const items = [...this.filteredAnuncios];
      if (this.sortBy === "precio_asc") {
        items.sort((a, b) => Number((a.producto && a.producto.precio_regular) || 0) - Number((b.producto && b.producto.precio_regular) || 0));
      } else if (this.sortBy === "precio_desc") {
        items.sort((a, b) => Number((b.producto && b.producto.precio_regular) || 0) - Number((a.producto && a.producto.precio_regular) || 0));
      } else if (this.sortBy === "titulo_asc") {
        items.sort((a, b) => String(a.titulo || "").localeCompare(String(b.titulo || "")));
      } else {
        items.sort((a, b) => Number(b.id || 0) - Number(a.id || 0));
      }
      return items;
    },
  },
  created() {
    this.fetchAnuncios();
  },
};
</script>

<style lang="scss" scoped>
.resultados-wrap {
  background:
    radial-gradient(circle at top left, rgba(255, 206, 231, 0.48), transparent 28%),
    radial-gradient(circle at top right, rgba(255, 210, 133, 0.22), transparent 30%),
    linear-gradient(180deg, #fff8fc 0%, #fffaf4 100%);
}

.resultados-container {
  position: relative;
  background: rgba(255, 255, 255, 0.8);
  border: 1px solid rgba(243, 217, 232, 0.9);
  border-radius: 24px;
  padding: 24px;
  box-shadow: 0 24px 48px rgba(118, 47, 78, 0.14);
  overflow: hidden;
}

.resultados-container::before {
  content: "";
  position: absolute;
  inset: 0;
  pointer-events: none;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.25), transparent 45%, rgba(255, 239, 246, 0.35) 100%);
}

.resultados-hero {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  padding: 18px 20px;
  margin-bottom: 18px;
  border-radius: 22px;
  background: linear-gradient(135deg, rgba(255, 244, 250, 0.96) 0%, rgba(255, 250, 243, 0.98) 100%);
  border: 1px solid rgba(247, 214, 230, 0.9);
  box-shadow: 0 18px 36px rgba(133, 47, 88, 0.1);
}

.hero-kicker,
.filters-kicker {
  display: inline-flex;
  align-items: center;
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #8a2f63;
  background: linear-gradient(135deg, rgba(255, 229, 241, 0.95) 0%, rgba(255, 241, 213, 0.95) 100%);
  box-shadow: 0 10px 18px rgba(201, 82, 132, 0.12);
}

.hero-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  justify-content: flex-end;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 14px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.82);
  border: 1px solid rgba(241, 210, 227, 0.95);
  color: #6f4b5d;
  font-weight: 700;
  box-shadow: 0 12px 22px rgba(118, 47, 78, 0.08);
}

.hero-badge.accent {
  background: linear-gradient(135deg, rgba(255, 241, 214, 0.95) 0%, rgba(255, 224, 241, 0.95) 100%);
  color: #8a4f00;
}

.resultados-title {
  color: #ff4f9d;
  margin-top: 10px;
  margin-bottom: 6px;
  text-shadow: 0 0 8px rgba(255, 79, 157, 0.35), 0 3px 8px rgba(70, 13, 38, 0.2);
}

.resultados-subtitle {
  color: #6f5a66;
  margin: 0;
}

.filters-panel {
  position: relative;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.96) 0%, rgba(255, 247, 251, 0.98) 100%);
  border: 1px solid rgba(237, 211, 228, 0.96);
  border-radius: 18px;
  padding: 18px;
  box-shadow: 0 18px 34px rgba(92, 34, 61, 0.12);
  overflow: hidden;
}

.filters-panel::after {
  content: "";
  position: absolute;
  inset: auto -20px -30px auto;
  width: 140px;
  height: 140px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(255, 195, 226, 0.38) 0%, transparent 70%);
  pointer-events: none;
}

.sticky-filters {
  position: sticky;
  top: 90px;
}

.filters-title {
  margin-top: 10px;
  margin-bottom: 4px;
  color: #7d2d58;
  font-weight: 700;
}

.filters-help {
  margin-bottom: 14px;
  font-size: 12px;
  color: #7a6a72;
}

.search-glance {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 14px;
}

.filters-metrics {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
  margin-bottom: 16px;
}

.metric-pill {
  display: flex;
  flex-direction: column;
  padding: 12px 14px;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.88);
  border: 1px solid rgba(243, 216, 229, 0.95);
  box-shadow: 0 12px 20px rgba(126, 42, 76, 0.07);
}

.metric-pill strong {
  font-size: 22px;
  line-height: 1;
  color: #913364;
}

.metric-pill span {
  margin-top: 4px;
  font-size: 12px;
  color: #806977;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.metric-pill.accent strong {
  color: #a26100;
}

.filter-label {
  font-size: 12px;
  color: #684a58;
  margin-bottom: 4px;
  display: block;
  font-weight: 700;
}

.filter-input {
  border-radius: 12px;
  border-color: #e8cddf;
  min-height: 44px;
  box-shadow: inset 0 1px 2px rgba(132, 52, 83, 0.04);
}

.filter-input:focus,
.sort-select:focus {
  border-color: #f090bf;
  box-shadow: 0 0 0 0.18rem rgba(255, 79, 157, 0.12);
}

.filter-actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-top: 16px;
}

.active-filters-bar {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  padding: 12px 14px;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.78);
  border: 1px solid rgba(240, 215, 228, 0.9);
  box-shadow: 0 14px 24px rgba(116, 44, 75, 0.08);
}

.filters-label {
  font-weight: 600;
  color: #6a5060;
}

.soft-chip,
.filter-chip,
.result-tag {
  display: inline-flex;
  align-items: center;
  padding: 6px 11px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
  box-shadow: 0 8px 16px rgba(103, 31, 67, 0.08);
}

.soft-chip {
  background: linear-gradient(135deg, rgba(255, 234, 244, 0.96) 0%, rgba(255, 245, 223, 0.96) 100%);
  color: #8a2f63;
}

.filter-chip {
  background: linear-gradient(90deg, #ff7eb8 0%, #9b7bff 100%);
  color: #fff;
}

.query-spotlight {
  margin-bottom: 16px;
}

.query-pill {
  display: inline-flex;
  align-items: center;
  padding: 10px 14px;
  border-radius: 999px;
  background: linear-gradient(135deg, rgba(255, 244, 249, 0.96) 0%, rgba(255, 247, 233, 0.96) 100%);
  border: 1px solid rgba(242, 212, 228, 0.95);
  box-shadow: 0 12px 22px rgba(116, 44, 75, 0.08);
  color: #7b3056;
}

.results-tools {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  flex-wrap: wrap;
  padding: 14px 16px;
  border-radius: 18px;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.92) 0%, rgba(255, 246, 250, 0.94) 100%);
  border: 1px solid rgba(241, 214, 228, 0.9);
  box-shadow: 0 14px 26px rgba(116, 44, 75, 0.08);
}

.view-toggle {
  display: flex;
  align-items: center;
  gap: 6px;
}

.result-toggle {
  border-radius: 999px;
  padding: 8px 14px;
  font-weight: 700;
}

.result-toggle.active,
.btn-gradient-soft,
.action-detail-btn {
  background: linear-gradient(135deg, #ff5ca8 0%, #d765ff 100%);
  border-color: transparent;
  color: #fff;
  box-shadow: 0 12px 22px rgba(214, 51, 132, 0.22);
}

.btn-outline-soft {
  border-radius: 999px;
  border: 1px solid rgba(214, 130, 176, 0.55);
  color: #8a2f63;
  background: rgba(255, 255, 255, 0.82);
}

.btn-gradient-soft:hover,
.action-detail-btn:hover,
.result-toggle.active:hover {
  color: #fff;
  transform: translateY(-1px);
  box-shadow: 0 14px 24px rgba(214, 51, 132, 0.28);
}

.btn-outline-soft:hover {
  color: #8a2f63;
  background: rgba(255, 240, 247, 0.96);
}

.sort-select-wrap {
  min-width: 220px;
}

.sort-select {
  border-radius: 12px;
  border-color: #e7ccdd;
  min-height: 40px;
}

.masonry-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 14px;
  align-items: stretch;
}

.masonry-item {
  break-inside: avoid;
  margin-bottom: 0;
  display: flex;
}

.list-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 14px;
}

.list-grid .masonry-item {
  break-inside: auto;
  margin-bottom: 0;
}

.list-grid .premium-card {
  display: grid;
  grid-template-columns: 280px 1fr;
  height: 100%;
}

.list-grid .featured-figure {
  height: 100%;
}

.list-grid .cover {
  height: 100%;
  min-height: 220px;
}

.premium-card {
  --mx: 0px;
  --my: 0px;
  position: relative;
  display: flex;
  flex-direction: column;
  isolation: isolate;
  border-radius: 16px;
  border: 1px solid rgba(240, 216, 229, 0.95);
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 245, 250, 0.98) 100%);
  box-shadow: 0 18px 30px rgba(117, 42, 75, 0.1);
  transition: transform 200ms ease, box-shadow 200ms ease, border-color 200ms ease;
  animation: cardIn 320ms ease both;
  overflow: hidden;
}

.premium-card::after {
  content: "";
  position: absolute;
  inset: -18px;
  border-radius: 28px;
  background:
    radial-gradient(circle at top left, rgba(255, 200, 121, 0.14), transparent 34%),
    radial-gradient(circle at top right, rgba(215, 101, 255, 0.12), transparent 38%);
  filter: blur(16px);
  z-index: -1;
}

.premium-card:hover {
  transform: translateY(-7px);
  border-color: #f1b6d4;
  box-shadow: 0 24px 42px rgba(88, 28, 54, 0.18);
}

.featured-figure {
  overflow: hidden;
  position: relative;
}

.featured-figure::after {
  content: "";
  position: absolute;
  inset: 0;
  pointer-events: none;
  background: linear-gradient(180deg, rgba(51, 18, 42, 0.03) 0%, rgba(51, 18, 42, 0.08) 54%, rgba(51, 18, 42, 0.32) 100%);
}

.featured-pill,
.city-pill {
  position: absolute;
  z-index: 3;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 12px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  box-shadow: 0 10px 22px rgba(61, 19, 90, 0.28);
}

.featured-pill {
  top: 12px;
  right: 12px;
  background: linear-gradient(135deg, #ff79b6 0%, #d765ff 100%);
  color: #fff;
}

.featured-pill--premium {
  background: linear-gradient(135deg, #ffbe61 0%, #ff8f66 34%, #d765ff 100%);
}

.city-pill {
  left: 12px;
  bottom: 12px;
  background: rgba(53, 17, 56, 0.84);
  color: #ffeab2;
  backdrop-filter: blur(6px);
}

.cover {
  width: 100%;
  height: 220px;
  object-fit: cover;
  opacity: 0.65;
  filter: blur(5px);
  transform: translate3d(var(--mx), var(--my), 0) scale(1.04);
  transition: opacity 260ms ease, filter 260ms ease, transform 260ms ease;
}

.cover.is-loaded {
  opacity: 1;
  filter: blur(0);
  transform: translate3d(var(--mx), var(--my), 0) scale(1.01);
}

.premium-card:hover .cover.is-loaded {
  transform: translate3d(calc(var(--mx) * 1.2), calc(var(--my) * 1.2), 0) scale(1.08);
  filter: saturate(1.08) brightness(1.03);
}

.feature-content {
  position: relative;
  z-index: 1;
  padding: 16px 16px 14px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.product a {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  background: linear-gradient(135deg, rgba(255, 236, 246, 0.96) 0%, rgba(255, 246, 218, 0.96) 100%);
  color: #8a2f63;
}

.business-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 12px 0 10px;
}

.business-logo-shell {
  width: 52px;
  height: 52px;
  min-width: 52px;
  position: relative;
  cursor: pointer;
  border-radius: 50%;
  padding: 4px;
  background: linear-gradient(135deg, #fff8ef 0%, #ffe0ac 32%, #ffd6ec 70%, #ffc0e0 100%);
  box-shadow: 0 12px 26px rgba(167, 52, 113, 0.2);
  border: 1px solid rgba(255, 245, 220, 0.95);
  transition: transform 220ms ease, box-shadow 220ms ease;
}

.business-logo-mini {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
  background: #fff;
  transition: transform 220ms ease, filter 220ms ease;
}

.business-logo-tooltip {
  position: absolute;
  left: 50%;
  bottom: calc(100% + 10px);
  transform: translateX(-50%) translateY(8px) scale(0.96);
  min-width: 160px;
  padding: 10px 12px;
  border-radius: 12px;
  background: rgba(58, 18, 54, 0.96);
  color: #fff;
  box-shadow: 0 14px 28px rgba(39, 10, 35, 0.26);
  opacity: 0;
  pointer-events: none;
  transition: opacity 200ms ease, transform 200ms ease;
  z-index: 6;
  text-align: center;
}

.business-logo-tooltip strong,
.business-logo-tooltip small {
  display: block;
}

.business-logo-tooltip small {
  margin-top: 4px;
  color: #ffdbe9;
  font-size: 11px;
}

.business-logo-tooltip::after {
  content: "";
  position: absolute;
  left: 50%;
  top: 100%;
  transform: translateX(-50%);
  border-width: 6px;
  border-style: solid;
  border-color: rgba(58, 18, 54, 0.96) transparent transparent transparent;
}

.business-logo-shell:hover .business-logo-tooltip,
.business-logo-shell:focus-within .business-logo-tooltip {
  opacity: 1;
  transform: translateX(-50%) translateY(0) scale(1);
}

.premium-card:hover .business-logo-shell {
  transform: translateY(-3px) scale(1.08) rotate(4deg);
  box-shadow: 0 16px 28px rgba(167, 52, 113, 0.28);
}

.premium-card:hover .business-logo-mini {
  filter: saturate(1.08) brightness(1.03);
}

.business-copy {
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.business-name {
  margin: 0;
  font-weight: 800;
  color: #663247;
}

.business-location {
  color: #91627b;
  font-size: 12px;
}

.product-title {
  min-height: 58px;
  margin-bottom: 10px;
}

.product-title a {
  display: -webkit-box;
  overflow: hidden;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
  color: #4a1833;
  transition: color 180ms ease, text-shadow 180ms ease;
}

.premium-card:hover .product-title a {
  color: #b83d1c;
  text-shadow: 0 0 8px rgba(255, 181, 90, 0.36);
}

.product-badge {
  display: inline-flex;
  align-self: flex-start;
  padding: 6px 12px;
  margin-bottom: 10px;
  border-radius: 999px;
  background: linear-gradient(135deg, rgba(255, 248, 231, 0.96) 0%, rgba(255, 226, 182, 0.96) 100%);
  color: #7a194f;
  font-size: 13px;
  font-weight: 800;
  box-shadow: 0 10px 18px rgba(255, 190, 97, 0.14);
}

.adcategories {
  display: flex;
  flex-wrap: wrap;
  align-content: flex-start;
  gap: 8px;
  min-height: 58px;
  max-height: 58px;
  overflow: hidden;
  margin-bottom: 10px;
}

.result-tag {
  background: linear-gradient(135deg, rgba(255, 231, 243, 0.98) 0%, rgba(233, 236, 255, 0.98) 100%);
  color: #764d89;
}

.quick-trust {
  margin-top: 4px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 11px;
  color: #6f5a66;
  gap: 8px;
}

.quick-trust span {
  display: inline-flex;
  align-items: center;
  padding: 5px 10px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.78);
  border: 1px solid rgba(255, 216, 157, 0.48);
  box-shadow: 0 8px 16px rgba(128, 51, 95, 0.08);
}

.quick-trust i,
.address i {
  margin-right: 4px;
  color: #ffaf2d;
}

.address {
  margin-top: 12px;
  margin-bottom: 0;
}

.address li {
  margin-bottom: 8px;
}

.address li a {
  color: #7a6171;
}

.listing-bottom {
  margin-top: auto;
  padding-top: 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  border-top: 1px solid rgba(114, 43, 73, 0.08);
}

.price-block {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.price-block small {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #8e7483;
}

.price {
  margin: 0;
  color: #b83d1c;
  font-weight: 800;
}

.card-footer-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.action-detail-btn,
.action-whatsapp-btn {
  position: relative;
  overflow: hidden;
  border-radius: 999px;
  transition: transform 180ms ease, box-shadow 180ms ease, filter 180ms ease;
}

.action-detail-btn::after,
.action-whatsapp-btn::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,0.16) 45%, transparent 100%);
  transform: translateX(-120%);
  transition: transform 360ms ease;
  pointer-events: none;
}

.btn-whatsapp,
.action-whatsapp-btn {
  background-color: #25D366;
  box-shadow: 0 10px 18px rgba(37, 211, 102, 0.24);
}

.action-detail-btn:hover,
.action-whatsapp-btn:hover {
  transform: translateY(-2px) scale(1.03);
  filter: brightness(1.03);
}

.action-detail-btn:hover::after,
.action-whatsapp-btn:hover::after {
  transform: translateX(120%);
}

.action-whatsapp-btn:hover {
  box-shadow: 0 14px 24px rgba(37, 211, 102, 0.32);
}

.action-icon {
  transition: transform 180ms ease;
}

.action-detail-btn:hover .action-icon {
  transform: translateX(2px) scale(1.08);
}

.action-whatsapp-btn:hover .action-icon {
  transform: scale(1.1) rotate(-6deg);
}

.skeleton-card {
  border-radius: 16px;
  border: 1px solid #f0d8e5;
  background: #fff;
  overflow: hidden;
}

.skeleton {
  animation: pulse 1.2s ease-in-out infinite;
  background: linear-gradient(90deg, #f8edf3 0%, #f4e0eb 50%, #f8edf3 100%);
  background-size: 180% 100%;
}

.skeleton-image {
  height: 200px;
}

.skeleton-content {
  padding: 12px;
}

.skeleton-line {
  height: 12px;
  border-radius: 8px;
  margin-bottom: 10px;
}

.w40 { width: 40%; }
.w55 { width: 55%; }
.w70 { width: 70%; }
.w90 { width: 90%; }

.empty-state {
  padding: 34px 18px;
  border-radius: 20px;
  background: linear-gradient(135deg, rgba(255, 251, 253, 0.98) 0%, rgba(255, 246, 237, 0.98) 100%);
  border: 1px solid rgba(240, 216, 229, 0.95);
  box-shadow: 0 18px 30px rgba(116, 44, 75, 0.08);
}

.empty-icon {
  width: 74px;
  height: 74px;
  margin: 0 auto 14px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  font-size: 30px;
  color: #fff;
  background: linear-gradient(135deg, #ff6aac 0%, #d765ff 100%);
  box-shadow: 0 16px 26px rgba(214, 51, 132, 0.22);
}

.empty-state h4 {
  color: #6d3151;
  margin-bottom: 8px;
}

.empty-state p {
  color: #7b6572;
  max-width: 520px;
  margin: 0 auto;
}

.empty-actions {
  margin-top: 16px;
  display: flex;
  justify-content: center;
  gap: 10px;
  flex-wrap: wrap;
}

.pagination-bar {
  margin-top: 20px;
  padding: 14px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.82);
  border: 1px solid rgba(240, 215, 228, 0.92);
  box-shadow: 0 14px 24px rgba(116, 44, 75, 0.08);
}

.page-link {
  border-radius: 999px !important;
  margin: 0 4px;
  border: none;
  box-shadow: 0 8px 16px rgba(119, 43, 76, 0.08);
}

@keyframes pulse {
  0% { background-position: 0% 50%; }
  100% { background-position: 180% 50%; }
}

@keyframes cardIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 991px) {
  .masonry-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .resultados-hero { flex-direction: column; }
  .hero-badges { justify-content: flex-start; }
}

@media (max-width: 767px) {
  .resultados-container {
    padding: 14px;
    border-radius: 14px;
  }

  .resultados-hero {
    padding: 16px;
    border-radius: 16px;
  }

  .sticky-filters {
    position: static;
  }

  .masonry-grid { grid-template-columns: 1fr; }
  .cover { height: 180px; }

  .list-grid .premium-card {
    display: block;
  }

  .list-grid .cover {
    min-height: 180px;
    height: 180px;
  }

  .quick-trust,
  .listing-bottom {
    flex-direction: column;
    align-items: flex-start;
  }

  .card-footer-actions,
  .empty-actions {
    width: 100%;
  }

  .card-footer-actions .btn,
  .empty-actions .btn,
  .filter-actions .btn {
    width: 100%;
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
