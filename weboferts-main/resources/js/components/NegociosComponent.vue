<template>
  <div class="inner-box">
    <div class="dashboard-box">
      <h2 v-if="categoria!=null" class="dashbord-title">{{categoria.cname}}</h2>
    </div>
    <div class="dashboard-wrapper">
      <nav class="nav-table">
        <ul>
          <li class="active"><a href="#">All Ads (42)</a></li>
          <li><a href="#">Published (88)</a></li>
          <li><a href="#">Featured (12)</a></li>
          <li><a href="#">Sold (02)</a></li>
          <li><a href="#">Active (42)</a></li>
          <li><a href="#">Expired (01)</a></li>
        </ul>
      </nav>
      <table class="table table-responsive dashboardtable tablemyads">
        <thead>
          <tr>
            <th>Img</th>
            <th>Negocio</th>
            <th>Celular</th>
            <th>Telefonos</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(negocio, index) in negocios" :key="negocio.id" data-category="active">
            <td>
              <div class="custom-control custom-checkbox">
                  {{index+1}}
              </div>
            </td>
            <td class="photo">
              <img
                class="img-fluid"
                :src="getBusinessImage(negocio.logo)"
                :alt="negocio.nnegocio"
              />
            </td>
            <td data-title="Title">
              <h3>{{negocio.nnegocio}}</h3>
              <span>{{negocio.dir}}</span>
              <small class="d-block text-muted">{{negocio.ciudad}}</small>
              <small v-if="negocio.web" class="d-block text-muted">{{negocio.web}}</small>
            </td>
            <td data-title="Celular">
              <span class="adstatus adstatusactive">{{negocio.celular}}</span>
            </td>
            <td data-title="telefonos">
              <span class="adcategories">{{negocio.telefonos}}</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: "negocios",
  data() {
    return {
      negocios: [],
      categoria: null
    };
  },

  created() {
    /* let param = this.$route.query.page */
    let param = window.location.href.split('/').pop();
    axios.get(window.location.origin+'/categorias/home/negocios/'+param).then((res) => {
      this.categoria = res.data.categoria
      this.negocios = res.data.negocios;
    });
  },
  methods: {
    resolveImagePath(image) {
      if (image.startsWith('http://') || image.startsWith('https://')) {
        return image;
      }

      const normalized = image.startsWith('/') ? image : `/${image}`;
      return window.location.origin + normalized;
    },
    getBusinessImage(image) {
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
  }
};
</script>

<style lang="scss" scoped>
</style>
