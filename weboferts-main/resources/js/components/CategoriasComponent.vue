<template>
  <div class="main-container section-padding">
    <div class="container">
      <div class="row">
        <div class="col-lg-3 col-md-12 col-xs-12 page-sidebar">
          <aside>
            <div class="widget_search">
              <form role="search" id="search-form">
                <input
                  type="search"
                  class="form-control"
                  autocomplete="off"
                  name="s"
                  placeholder="Search..."
                  id="search-input"
                  value=""
                />
                <button type="submit" id="search-submit" class="search-btn">
                  <i class="lni-search"></i>
                </button>
              </form>
            </div>

            <div class="widget categories">
              <h4 class="widget-title">Categorias</h4>
              <ul class="categories-list">
                <li v-for="cate in categorias" :key="cate.id">
                  <a href="javascript:;" @click="setNegocios(cate.id)">
                    <span class="cat-name">
                      <span class="cat-badge" :style="getCategoryIconStyle(cate)">
                        <v-icon x-small color="white">{{ getIconName(cate) }}</v-icon>
                      </span>
                      {{ cate.cname }}
                    </span>
                    <span class="category-counter">({{ cate.cuantos }})</span>
                  </a>
                </li>
              </ul>
            </div>
            <div class="widget">
              <h4 class="widget-title">Advertisement</h4>
              <div class="add-box">
                <img
                  class="img-fluid"
                  src="http://127.0.0.1:8000/assets/img/img1.jpg"
                  alt=""
                />
              </div>
            </div>
          </aside>
        </div>
        <div class="col-lg-9 col-md-12 col-xs-12 page-content">
          <div class="product-filter">
            <div class="short-name">
              <span>Showing (1 - 12 products of 7371 products)</span>
            </div>
            <div class="Show-item">
              <span>Show Items</span>
              <form class="woocommerce-ordering" method="post">
                <label>
                  <select name="order" class="orderby">
                    <option selected="selected" value="menu-order">
                      49 items
                    </option>
                    <option value="popularity">popularity</option>
                    <option value="popularity">Average ration</option>
                    <option value="popularity">newness</option>
                    <option value="popularity">price</option>
                  </select>
                </label>
              </form>
            </div>
            <ul class="nav nav-tabs">
              <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#grid-view"
                  ><i class="lni-grid"></i
                ></a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#list-view"
                  ><i class="lni-list"></i
                ></a>
              </li>
            </ul>
          </div>

          <div class="adds-wrapper">
            <div class="tab-content">
              <div id="grid-view" class="tab-pane fade active show">
                <div class="row">
                  <div
                    v-for="negocio in negocios"
                    class="col-xs-12 col-sm-12 col-md-6 col-lg-6"
                    :key="negocio.id"
                    data-aos="fade-right"
                  >
                    <div class="featured-box">
                      <figure>
                        <div class="icon">
                          <i class="lni-heart"></i>
                        </div>
                        <a href="#"
                          ><img
                            v-if="negocio.logo!=='' || negocio.logo!==null"
                            class="img-fluid"
                            :src="negocio.logo"
                            :alt="negocio.nnegocio"
                        /></a>
                      </figure>
                      <div class="feature-content">
                        <div class="product">
                          <a
                            v-for="cate in negocio.categorias"
                            :key="cate.id"
                            href="#"
                          >
                            #{{ cate.cname }}</a
                          >
                        </div>
                        <h4>
                          <a href="ads-details.html">{{ negocio.nnegocio }}</a>
                        </h4>
                        <span>Last Updated: 3 hours ago</span>
                        <ul class="address">
                          <li>
                            <a href="#"
                              ><i class="lni-map-marker"></i> Avenue C, US</a
                            >
                          </li>
                          <li>
                            <a href="#"
                              ><i class="lni-alarm-clock"></i> Feb 18, 2018</a
                            >
                          </li>
                          <li>
                            <a href="#"
                              ><i class="lni-user"></i> Maria Barlow</a
                            >
                          </li>
                          <li>
                            <a href="#"><i class="lni-package"></i> Used</a>
                          </li>
                        </ul>
                        <div class="listing-bottom">
                          <h3 class="price float-left">$200.00</h3>
                          <a
                            href="account-myads.html"
                            class="btn-verified float-right"
                            ><i class="lni-check-box"></i> Verified Ad</a
                          >
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

<!--           <div>
            <pre>{{ pagination }}</pre>
          </div> -->

          <div class="pagination-bar">
            <nav>
              <ul class="pagination justify-content-center">
                <li class="page-item">
                  <button
                    v-if="pagination.pre_page != null"
                    class="btn page-link active"
                    type="submit"
                    @click="previo"
                  >
                    <span
                      v-if="loadingPrevio"
                      class="spinner-grow spinner-grow-sm"
                      role="status"
                      aria-hidden="true"
                    ></span>
                    Previo
                  </button>
                </li>

                <template v-for="pagina in (0, pagination.last_page)">
                  <button
                    v-if="pagina-1 === pagination.actual_page"
                    :key="pagina"
                    class="btn mbtn page-link active"
                    type="submit"
                    disabled
                  >
                    {{ pagina }}
                  </button>
                  <button
                    v-else
                    :key="pagina"
                    class="btn mbtn page-link"
                    type="submit"
                    @click="go(pagina-1)"
                  >
                    {{ pagina }}
                  </button>
                </template>


                <li class="page-item">
                  <button
                    v-if="pagination.last_page != pagination.actual_page"
                    class="btn page-link active"
                    type="submit"
                    @click="ultimo"
                  >
                    <span
                      v-if="loadingNext"
                      class="spinner-grow spinner-grow-sm"
                      role="status"
                      aria-hidden="true"
                    ></span>
                    &Uacute;ltimo
                  </button>
                </li>
                <li class="page-item">
                  <button
                    v-if="pagination.next_page != null"
                    class="btn page-link active"
                    type="submit"
                    @click="siguiente"
                  >
                    <span
                      v-if="loadingNext"
                      class="spinner-grow spinner-grow-sm"
                      role="status"
                      aria-hidden="true"
                    ></span>
                    Sig
                  </button>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { getCategoryTheme } from '../utils/categoryTheme';
export default {
  name: "categorias",
  data() {
    return {
      categorias: [],
      negocios: null,
      page: 0,
      pages: 50,
      pagination: {
        cuenta: 1,
      },
      prevDisabled: true,
      nextDisabled: true,
      loadingNext: false,
      loadingPrevio: false,
    };
  },
  methods: {
    getCategoryTheme(category) {
      return getCategoryTheme(category.icon || category.url || category.cname);
    },
    getIconName(category) {
      return this.getCategoryTheme(category).icon;
    },
    getCategoryIconStyle(category) {
      const theme = this.getCategoryTheme(category);
      return {
        background: theme.gradient,
        boxShadow: `0 10px 18px ${theme.shadow}`,
      };
    },
    fetchNegocios() {
      //pruebas
      this.negocios = null;
      //this.total = 0;
      let formData = {
        page: this.page,
        pages: this.pages,
        cuenta: this.pagination.cuenta,
      };
      axios({
        url: window.location.origin + "/catsnego",
        data: formData,
        method: "POST",
      })
        .then((resp) => {
          this.negocios = resp.data.result.negocios;
          this.categorias = resp.data.result.categorias;
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
          this.prevDisabled = false;
          if (this.pagination.pre_page != null) {
            this.prevDisabled = true;
          }
          this.nextDisabled = false;
          if (this.pagination.next_page != null) {
            this.nextDisabled = true;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    siguiente() {
      this.loadingNext = true;
      this.page = Number(this.pagination.actual_page + 1);
      this.cuenta = Number(this.pagination.cuenta);
      this.fetchNegocios();
    },
    previo() {
      this.loadingPrevio = true;
      this.page = Number(this.pagination.actual_page - 1);
      if (this.page >= 0) {
        this.fetchNegocios();
      }
    },
    ultimo() {
      this.loadingNext = true;
      this.page = Number(this.pagination.last_page);
      this.fetchNegocios();
    },
    go(page) {
      this.loadingNext = true;
      this.page = page
      this.cuenta = Number(this.pagination.cuenta);
      this.fetchNegocios();
    },
  },
  created() {
    this.fetchNegocios();
  },
};
</script>

<style scoped>
.mbtn {
  padding: 7px 7px;
}

.categories-list li a {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}

.cat-name {
  display: inline-flex;
  align-items: center;
  gap: 10px;
}

.cat-badge {
  width: 32px;
  height: 32px;
  border-radius: 12px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(255,255,255,0.28);
}
</style>
