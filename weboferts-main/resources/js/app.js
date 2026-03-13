/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.Vue = require('vue');
import vuetify from './vuetify';

// Import Material Design Icons CSS
import '@mdi/font/css/materialdesignicons.css';

// SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal;

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
});
window.Toast = Toast;


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
Vue.component('homerecomendados', require('./components/RecomendadosComponent.vue').default);
Vue.component('homedestacados', require('./components/DestacadosComponent.vue').default);
Vue.component('buscar', require('./components/BuscarComponent.vue').default);
Vue.component('home', require('./components/HomeComponent.vue').default);
Vue.component('resultados', require('./components/ResultadosComponent.vue').default);
Vue.component('detalleanuncio', require('./components/DetalleAnuncio.vue').default);
Vue.component('location-picker', require('./components/shared/LocationPicker.vue').default);
/* Vue.component('loginform', require('./components/LoginForm.vue').default); */

Vue.component('dashboardadmin', require('./components/admin/DashboardAdmin.vue').default);
Vue.component('navbar', require('./components/admin/Navbar.vue').default);

Vue.component('admincategorias', require('./components/admin/Categorias.vue').default);
Vue.component('adminusuarios', require('./components/admin/Usuarios.vue').default);
Vue.component('nuevacategoria', require('./components/admin/NuevaCategoria.vue').default);
Vue.component('editarcategoria', require('./components/admin/EditarCategoria.vue').default);
Vue.component('adminanuncios', require('./components/admin/Anuncios.vue').default);
Vue.component('menucard', require('./components/admin/MenuCard.vue').default);
/*PARA NEGOCIOS*/
Vue.component('adminegocios', require('./components/admin/Negocios.vue').default);
Vue.component('nuevonegocio', require('./components/admin/NuevoNegocio.vue').default);
Vue.component('editarnegocio', require('./components/admin/EditarNegocio.vue').default);
Vue.component('cropperimage', require('./components/admin/CropperImage.vue').default);
Vue.component('clipperimage', require('./components/ClipperImage.vue').default);
Vue.component('clipperimage2', require('./components/ClipperImage2.vue').default);
/*PARA USUARIOS*/
Vue.component('adminusuarios', require('./components/admin/Usuarios.vue').default);
/*PARA ANUNCIOS*/
Vue.component('anuncios', require('./components/AnunciosComponent.vue').default);

// New component for ads by category
Vue.component('anuncios-por-categoria-component', require('./components/AnunciosPorCategoriaComponent.vue').default);

/* Dashboar cliente */
Vue.component('dashboardcliente', require('./components/client/DashboardCliente.vue').default);
Vue.component('navbarclient', require('./components/client/NavbarClient.vue').default);
Vue.component('negocioclient', require('./components/client/NegocioClient.vue').default);
Vue.component('footerclient', require('./components/client/FooterClient.vue').default);
Vue.component('nuevoanuncio', require('./components/client/NuevoAnuncio.vue').default);
Vue.component('vistaanuncio', require('./components/client/VistaAnuncio.vue').default);
Vue.component('cardclient', require('./components/client/CardClient.vue').default);
//Vue.component('imagenanuncio', require('./components/client/ImagenAnuncio.vue').default);
/*PAQUETE DEL CLIENTE*/
Vue.component('anuncioclient', require('./components/client/AnuncioClient.vue').default);
Vue.component('clipperimagedialog', require('./components/ClipperImageDialog.vue').default);
/*COMPRAR PAQUETE*/
Vue.component('comprarpaquete', require('./components/client/ComprarPaquete.vue').default);

//Vue.component('paquetes', require('./components/PaquetesComponent.vue').default);
Vue.component('adminpaquetes', require('./components/admin/Paquetes.vue').default);
Vue.component('nuevopaquete', require('./components/admin/NuevoPaquete.vue').default);
Vue.component('editarpaquete', require('./components/admin/EditarPaquete.vue').default);
Vue.component('detallepaquete', require('./components/admin/DetallePaquete.vue').default);
Vue.component('colorpicker', require('./components/admin/ColorPicker.vue').default);
/*PARA PRODUCTOS PANEL CLIENTES*/
//Vue.component('productoClient', require('./components/client/ProductoClient.vue').default);
Vue.component('clientproductos', require('./components/client/ProductoClient.vue').default);
Vue.component('nuevoproducto', require('./components/client/NuevoProducto.vue').default);
Vue.component('editarproducto', require('./components/client/EditarProducto.vue').default);
Vue.component('detalleproducto', require('./components/client/DetalleProducto.vue').default);
/*PARA PRODUCTOS PANEL ADMIN*/
Vue.component('adminproductos', require('./components/admin/Productos.vue').default);
//Vue.component('nuevoproductoadmin', require('./components/admin/NuevoProductoAdmin.vue').default);

// Carrito de Compras
Vue.component('cart-component', require('./components/client/CartComponent.vue').default);

// Componente de Reseñas
Vue.component('reviews-component', require('./components/ReviewsComponent.vue').default);

// Componentes Admin
Vue.component('adminpedidos', require('./components/admin/AdminPedidos.vue').default);
Vue.component('adminreviews', require('./components/admin/AdminReviews.vue').default);
Vue.component('admininventory', require('./components/admin/AdminInventory.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    vuetify
});
