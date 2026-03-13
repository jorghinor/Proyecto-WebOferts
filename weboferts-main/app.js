/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('categorias', require('./components/CategoriasComponent.vue').default);
Vue.component('registro', require('./components/RegistroComponent.vue').default);
Vue.component('negocios', require('./components/NegociosComponent.vue').default);
Vue.component('homecategorias', require('./components/HomeCategorias.vue').default);
Vue.component('loginform', require('./components/LoginForm.vue').default);

Vue.component('admincategorias', require('./components/admin/Categorias.vue').default);
//agregado
Vue.component('adminegocios', require('./components/admin/Negocios.vue').default);
//componente para anuncios
//Vue.component('anuncios', require('./components/AnunciosComponent.vue').default);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
