<template>
  <div class="search-bar">
    <div class="search-inner">
      <form class="search-form" @submit.prevent="onSubmit">
        <div class="form-group inputwithicon">
          <i class="lni-tag"></i>
          <input
            type="text"
            name="customword"
            v-model="texto"
            class="form-control"
            placeholder="Palabra Clave"
            autocomplete="off"
          />
        </div>
        <div class="form-group inputwithicon">
          <i class="lni-map-marker"></i>
          <div class="select">
            <select v-model="ciudad">
              <option value="">Ciudad</option>
              <option value="cochabamba">Cochabamba</option>
              <option value="la paz">La Paz</option>
              <option value="santa cruz">Santa Cruz</option>
              <option value="sucre">Sucre</option>
              <option value="tarija">Tarija</option>
              <option value="oruro">Oruro</option>
              <option value="potosi">Potosi</option>
              <option value="beni">Beni</option>
              <option value="pando">Pando</option>
            </select>
          </div>
        </div>
        <div class="form-group inputwithicon">
          <i class="lni-menu"></i>
          <div class="select">
            <select v-model="categoria">
              <option value="">Categorias</option>
              <option
                v-for="categoria in categorias"
                :key="categoria.id"
                :value="categoria.cname"
              >
                {{ categoria.cname }}
              </option>
            </select>
          </div>
        </div>
        <button class="btn btn-common" type="submit">
          <i class="lni-search"></i> Buscar Ahora
        </button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: "Buscar",
  props: {
    categorias: Array,
  },
  data() {
    return {
      texto: "",
      ciudad: "",
      categoria: "",
    };
  },
  methods: {
    onSubmit() {
      const txt = (this.texto || "").trim();
      const ciudad = (this.ciudad || "").trim();
      const cat = (this.categoria || "").trim();
      if (!txt && !ciudad && !cat) {
        return;
      }
      window.location.href =
        "/resultados?txt=" +
        encodeURIComponent(txt) +
        "&ciudad=" +
        encodeURIComponent(ciudad) +
        "&cat=" +
        encodeURIComponent(cat);
    },
  },
};
</script>

<style scoped>
.search-bar {
  margin-top: 40px;
}
.search-bar .search-form {
  width: 100%;
  float: left;
}
.search-bar .search-inner {
  background: #fff;
  overflow: hidden;
  border-radius: 50px;
  margin: 0;
  border: 0;
  padding: 10px;
  width: 100%;
  float: left;
  position: relative;
  background: rgba(128, 128, 128, 0.5);
}
.search-bar .form-group:first-child {
  border: 0;
}
.search-bar .form-group {
  margin: 5px;
  float: left;
  width: 27.33%;
  padding: 0 0 0 52px;
  background: #fff;
  border-radius: 50px;
}
.search-bar .form-group .form-control {
  position: relative;
  z-index: 1;
  border: 0;
  height: 50px;
  border-radius: 4px;
  font-size: 15px;
  line-height: 30px;
  padding: 15px 15px 15px 0;
}
.search-bar .form-group .form-control,
.search-bar .form-group .select select {
  border: 0;
  height: 50px;
  border-radius: 50px;
  font-size: 15px;
  line-height: 30px;
  padding: 10px 15px 15px 0;
  background: #fff;
}
.search-bar .form-group .form-control,
.search-bar .form-group .select select:focus {
  outline: none;
  box-shadow: none;
}
.search-bar .select {
  color: #666;
  float: left;
  width: 100%;
  position: relative;
  text-transform: uppercase;
}
.search-bar .select:after {
  top: 7px;
  right: 15px;
  z-index: 2;
  color: #666;
  display: block;
  content: "\e93a";
  position: absolute;
  text-align: center;
  font-size: inherit;
  line-height: 40px;
  font-family: lineicons;
}
.search-bar .select select {
  z-index: 1;
  width: 100%;
  position: relative;
  appearance: none;
  -moz-appearance: none;
  -webkit-appearance: none;
}
.search-bar .select select option {
  color: #666;
}
.search-bar .inputwithicon {
  position: relative;
}
.search-bar .inputwithicon i {
  top: 0;
  left: 0;
  z-index: 2;
  color: #00bcd4;
  position: absolute;
  left: 20px;
  font-size: 20px;
  line-height: 50px;
}
.search-bar .btn-common {
  top: 15px;
  right: 15px;
  padding: 0;
  z-index: 2;
  width: 157px;
  border-radius: 50px;
  border: none;
  cursor: pointer;
  line-height: 50px;
  text-align: center;
  position: absolute;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  transition: all 0.4s ease-in-out;
  -moz-transition: all 0.4s ease-in-out;
  -webkit-transition: all 0.4s ease-in-out;
  -o-transition: all 0.4s ease-in-out;
}
.search-bar .btn-common i {
  font-size: 20px;
  vertical-align: middle;
}

@media (max-width: 991px) {
  .search-bar .form-group {
    width: 100%;
    margin-bottom: 10px;
  }
  .search-bar .btn-common {
    position: static;
    width: 100%;
    margin-top: 10px;
  }
}
</style>
