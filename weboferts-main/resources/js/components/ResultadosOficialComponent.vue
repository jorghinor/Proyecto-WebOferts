<template>
  <section class="services section-padding">
    <div class="page-content">
      <div class="inner-box">
        <div class="dashboard-box">
          <h2 class="dashbord-title">Resultados</h2>
        </div>
        <div class="dashboard-wrapper">
          <nav class="nav-table">
            <ul>
              <li class="active"><a href="#">{{texto}}</a></li>
            </ul>
          </nav>
          <table class="table table-responsive dashboardtable tablemyads">
            <thead>
              <tr>
                <!--<th>##</th>-->
                <th>Anuncio</th>
                <th>Titulo</th>
                <th>Categorias</th>
                <th>Precio</th>
                <th>Contacto</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="anuncio in anuncios"
                :key ="anuncio.id"
              > 
                <!--<td class="py-4 px-2 font-weight-light">{{ anuncio.cuenta }}.-</td>-->             
                <td class="photo">
                  <img v-for="foto in anuncio.fotos"
                    :key="foto.id"
                    class="img-fluid"
                    :src="foto.url"
                    alt=""
                  />
                </td>
                <td data-title="Title">
                  <h3>{{anuncio.titulo}}</h3>
                  <span>{{anuncio.producto.nombre}}</span>
                </td>
                <td data-title="Category">
                  <span class="adcategories">
                      <span 
                        v-for="cate in anuncio.categorias"
                        :key="cate.id"
                        class="badge badge-info">{{cate.cname}}</span>
                  </span>
                </td>
                <td data-title="Price">
                  <h3>{{anuncio.producto.precio_regular}} Bs.</h3>
                </td>
                <td data-title="Action">
                    <span class="adstatus adstatusactive">
                      {{anuncio.negocio.celular}}
                    </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
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
                    v-if="pagina - 1 === pagination.actual_page"
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
                    @click="go(pagina - 1)"
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
  </section>
</template>

<script>
export default {
  name: "Resultados", 
  data() {
    return {
      texto: "",
      ciudad: "",
      categoria: "",
      anuncios: [],
      page: 0,
      pages: 4,
      pagination: {
        cuenta: 1,
      },
      prevDisabled: true,
      nextDisabled: true,
      loadingNext: false,
      loadingPrevio: false,
      fotos: [],
    };
  },
  //created() {
  methods: {
    fetchAnuncios() {  
    this.anuncios = null;  
    this.fotos = null;
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    this.texto = urlParams.get("txt");
    this.ciudad = urlParams.get("ciudad");
    this.categoria = urlParams.get("cat");

    let fData = {
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
        //this.anuncios = resp.data.result;
        this.anuncios = resp.data.result.anuncios;
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
      /*.catch((err) => {
        if (err.response.status == 422) {
          console.log(err.response);
        }
      });*/
       .catch((error) => {
        console.log(error)
      });
  },
   siguiente() {
      this.loadingNext = true;
      this.page = Number(this.pagination.actual_page + 1);
      this.cuenta = Number(this.pagination.cuenta);
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
      this.page = Number(this.pagination.last_page);
      this.fetchAnuncios();
    },
    go(page) {
      this.loadingNext = true;
      this.page = page;
      this.cuenta = Number(this.pagination.cuenta);
      this.fetchAnuncios();
    },
  },  
    created() {
    this.fetchAnuncios();
  },
};
</script>

<style lang="scss" scoped>
</style>