<template>
  <div>
    <!-- Botón flotante del carrito -->
    <div class="cart-floating-btn" @click="drawer = !drawer">
      <v-badge
        :content="cartCount"
        :value="cartCount"
        color="red"
        overlap
        offset-x="20"
        offset-y="20"
      >
        <v-btn fab large color="primary" class="white--text elevation-4">
          <v-icon>mdi-cart</v-icon>
        </v-btn>
      </v-badge>
    </div>

    <!-- Drawer (Panel lateral) del carrito -->
    <v-navigation-drawer
      v-model="drawer"
      fixed
      right
      temporary
      width="350"
      class="cart-drawer"
    >
      <div class="drawer-layout">
        <!-- Cabecera -->
        <div class="drawer-header">
          <v-list-item class="px-2 py-2 primary white--text">
            <v-list-item-content>
              <v-list-item-title class="text-h6">
                Tu Pedido
              </v-list-item-title>
              <v-list-item-subtitle class="white--text opacity-80">
                {{ cartCount }} items
              </v-list-item-subtitle>
            </v-list-item-content>
            <v-btn icon dark @click="drawer = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-list-item>
          <v-divider></v-divider>
        </div>

        <!-- Cuerpo (Lista de productos con scroll) -->
        <div class="drawer-body">
          <v-list two-line v-if="cart.length > 0">
            <template v-for="(item, index) in cart">
              <v-list-item :key="item.id">
                <v-list-item-avatar rounded>
                  <v-img :src="item.foto || '/assets/img/logo.png'"></v-img>
                </v-list-item-avatar>

                <v-list-item-content>
                  <v-list-item-title class="font-weight-bold">{{ item.titulo }}</v-list-item-title>
                  <v-list-item-subtitle class="caption">
                    {{ item.negocio_nombre }}
                  </v-list-item-subtitle>
                  <v-list-item-subtitle class="primary--text font-weight-bold">
                    {{ item.precio }} Bs.
                  </v-list-item-subtitle>
                </v-list-item-content>

                <v-list-item-action class="d-flex flex-column align-center">
                  <div class="d-flex align-center quantity-controls">
                    <v-btn x-small icon color="grey" @click="decrement(item)">
                      <v-icon>mdi-minus</v-icon>
                    </v-btn>
                    <span class="mx-2 font-weight-bold">{{ item.cantidad }}</span>
                    <v-btn x-small icon color="primary" @click="increment(item)">
                      <v-icon>mdi-plus</v-icon>
                    </v-btn>
                  </div>
                  <v-btn x-small icon color="red" class="mt-2" @click="remove(item)">
                    <v-icon>mdi-delete-outline</v-icon>
                  </v-btn>
                </v-list-item-action>
              </v-list-item>
              <v-divider :key="'div-'+index"></v-divider>
            </template>
          </v-list>

          <div v-else class="empty-cart text-center pa-10">
            <v-icon size="64" color="grey lighten-2">mdi-cart-off</v-icon>
            <p class="grey--text mt-3">Tu carrito está vacío</p>
            <v-btn text color="primary" @click="drawer = false">Seguir explorando</v-btn>
          </div>
        </div>

        <!-- Footer (Fijo al final) -->
        <div class="drawer-footer" v-if="cart.length > 0">
          <div class="d-flex justify-content-between mb-2">
            <span class="grey--text">Subtotal:</span>
            <span class="font-weight-bold">{{ cartTotal }} Bs.</span>
          </div>
          <div class="d-flex justify-content-between mb-4 title">
            <span>Total:</span>
            <span class="primary--text font-weight-black">{{ cartTotal }} Bs.</span>
          </div>
          <v-btn block color="green" large rounded @click="showCheckoutForm = true" class="white--text">
            Realizar Pedido
            <v-icon right>mdi-check-circle</v-icon>
          </v-btn>
        </div>
      </div>
    </v-navigation-drawer>

    <!-- Modal de Datos del Cliente y Pago -->
    <v-dialog v-model="showCheckoutForm" max-width="500px">
      <v-card>
        <v-card-title class="headline primary white--text">Finalizar Compra</v-card-title>
        <v-card-text class="pt-4">
          <v-form ref="form" v-model="valid">
            <h3 class="subtitle-1 mb-2 font-weight-bold">1. Tus Datos</h3>
            <v-text-field
              v-model="cliente.nombre"
              label="Nombre Completo"
              :rules="[v => !!v || 'El nombre es requerido']"
              required
              outlined
              dense
            ></v-text-field>

            <v-text-field
              v-model="cliente.email"
              label="Correo Electrónico"
              :rules="[
                v => !!v || 'El correo es requerido',
                v => /.+@.+\..+/.test(v) || 'Correo inválido'
              ]"
              required
              outlined
              dense
              type="email"
            ></v-text-field>

            <v-text-field
              v-model="cliente.telefono"
              label="Teléfono / Celular"
              :rules="[v => !!v || 'El teléfono es requerido']"
              required
              outlined
              dense
              type="number"
            ></v-text-field>

            <v-divider class="my-4"></v-divider>

            <h3 class="subtitle-1 mb-2 font-weight-bold">2. Método de Pago</h3>
            <v-radio-group v-model="metodoPago" row>
              <v-radio label="Efectivo / QR" value="efectivo"></v-radio>
              <v-radio label="Tarjeta (Stripe)" value="tarjeta"></v-radio>
            </v-radio-group>

            <!-- Formulario de Tarjeta (Solo si selecciona Tarjeta) -->
            <div v-show="metodoPago === 'tarjeta'" class="stripe-container pa-3 mb-3 rounded border">
              <div id="card-element">
                <!-- Stripe Element se montará aquí -->
              </div>
              <div id="card-errors" role="alert" class="red--text caption mt-2"></div>
            </div>

          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="grey darken-1" text @click="showCheckoutForm = false">Cancelar</v-btn>
          <v-btn color="green darken-1" class="white--text" :loading="processing" @click="processCheckout">
            Pagar {{ cartTotal }} Bs.
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
export default {
  name: "CartComponent",
  props: ['stripeKey'],
  data() {
    return {
      drawer: false,
      cart: [],
      showCheckoutForm: false,
      valid: true,
      processing: false,
      metodoPago: 'efectivo',
      cliente: {
        nombre: '',
        email: '',
        telefono: ''
      },
      stripe: null,
      card: null
    };
  },
  computed: {
    cartCount() {
      return this.cart.reduce((acc, item) => acc + item.cantidad, 0);
    },
    cartTotal() {
      return this.cart.reduce((acc, item) => acc + (item.precio * item.cantidad), 0).toFixed(2);
    }
  },
  watch: {
    showCheckoutForm(val) {
      if (val && this.metodoPago === 'tarjeta') {
        this.$nextTick(() => {
          this.initStripe();
        });
      }
    },
    metodoPago(val) {
      if (val === 'tarjeta' && this.showCheckoutForm) {
        this.$nextTick(() => {
          this.initStripe();
        });
      }
    }
  },
  methods: {
    loadCart() {
      const savedCart = localStorage.getItem('weboferts_cart');
      if (savedCart) {
        this.cart = JSON.parse(savedCart);
      }
    },
    saveCart() {
      localStorage.setItem('weboferts_cart', JSON.stringify(this.cart));
    },
    addToCart(product) {
      const existingItem = this.cart.find(item => item.id === product.id);
      if (existingItem) {
        existingItem.cantidad++;
      } else {
        this.cart.push({
          id: product.id,
          titulo: product.titulo,
          precio: product.precio,
          foto: product.foto,
          negocio_nombre: product.negocio_nombre,
          negocio_celular: product.negocio_celular,
          cantidad: 1
        });
      }
      this.saveCart();
      this.drawer = true;
    },
    increment(item) {
      item.cantidad++;
      this.saveCart();
    },
    decrement(item) {
      if (item.cantidad > 1) {
        item.cantidad--;
      } else {
        this.remove(item);
      }
      this.saveCart();
    },
    remove(item) {
      this.cart = this.cart.filter(i => i.id !== item.id);
      this.saveCart();
    },
    initStripe() {
      if (this.stripe) return;

      if (typeof Stripe === 'undefined') {
        console.error("Stripe.js no está cargado.");
        return;
      }

      const stripeKey = window.AppConfig ? window.AppConfig.stripeKey : null;

      if (!stripeKey) {
        console.error("Falta la clave pública de Stripe en AppConfig.");
        return;
      }

      this.stripe = Stripe(stripeKey);
      const elements = this.stripe.elements();

      this.card = elements.create('card', {
        style: {
          base: {
            color: '#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
              color: '#aab7c4'
            }
          },
          invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
          }
        }
      });

      this.card.mount('#card-element');

      this.card.on('change', ({error}) => {
        const displayError = document.getElementById('card-errors');
        if (error) {
          displayError.textContent = error.message;
        } else {
          displayError.textContent = '';
        }
      });
    },
    async processCheckout() {
      if (!this.$refs.form.validate()) return;
      this.processing = true;

      let stripeToken = null;

      if (this.metodoPago === 'tarjeta') {
        const {token, error} = await this.stripe.createToken(this.card);
        if (error) {
          const errorElement = document.getElementById('card-errors');
          errorElement.textContent = error.message;
          this.processing = false;
          return;
        }
        stripeToken = token.id;
      }

      axios.post('/checkout/process', {
        cart: this.cart,
        cliente: this.cliente,
        metodo_pago: this.metodoPago,
        stripeToken: stripeToken
      })
      .then(response => {
        if (response.data.status === 'success') {
          this.cart = [];
          this.saveCart();
          this.showCheckoutForm = false;
          this.drawer = false;
          window.location.href = response.data.redirect_url;
        } else {
          alert('Error: ' + response.data.message);
        }
      })
      .catch(error => {
        console.error(error);
        if (error.response && error.response.data && error.response.data.message) {
             alert('Error en el pago: ' + error.response.data.message);
        } else {
             alert('Ocurrió un error al procesar tu pedido.');
        }
      })
      .finally(() => {
        this.processing = false;
      });
    }
  },
  mounted() {
    this.loadCart();
    this.$root.$on('add-to-cart', this.addToCart);
  },
  beforeDestroy() {
    this.$root.$off('add-to-cart', this.addToCart);
  }
};
</script>

<style scoped>
.cart-floating-btn {
  position: fixed;
  bottom: 30px;
  right: 30px;
  z-index: 9999;
}
.cart-drawer {
  z-index: 10000 !important;
}
.drawer-layout {
  display: flex;
  flex-direction: column;
  height: 100%;
}
.drawer-header {
  flex-shrink: 0;
}
.drawer-body {
  flex-grow: 1;
  overflow-y: auto;
  min-height: 0;
}
.drawer-footer {
  flex-shrink: 0;
  background: #f8f9fa;
  border-top: 1px solid #e9ecef;
  padding: 16px;
}
.stripe-container {
  border: 1px solid #e0e0e0;
  background-color: #fcfcfc;
}
</style>
