<template>
  <v-footer app padless class="client-footer-override">
    <v-card
      flat
      tile
      width="100%"
      class="text-center"
      color="transparent"
    >
      <v-card-text class="topnav">
        <v-tooltip bottom v-for="link in links" :key="link.title">
          <template v-slot:activator="{ on, attrs }">
            <a class="mx-4 sin" :href="link.route" icon v-bind="attrs" v-on="on">
              <v-icon color="#5b3a4c" size="24px">{{link.icon}}</v-icon>
            </a>
          </template>
          <span>{{link.title}}</span>
        </v-tooltip>

      </v-card-text>

      <v-divider style="border-color: rgba(136, 14, 79, 0.1);"></v-divider>

      <v-card-text class="py-2" style="color: #6a5460;">
        {{ new Date().getFullYear() }} — <strong>devdesing</strong>
      </v-card-text>
    </v-card>
  </v-footer>
</template>

<script>
export default {
  name: "FooterClient",
  data() {
    return {
      links: [
        {
          title: "Dashboard",
          icon: "mdi-home",
          route: "/user",
        },
        {
          title: "Mi Negocio",
          icon: "mdi-star",
          route: "/user/negocio",
        },
        {
          title: "Productos",
          icon: "mdi-package-variant",
          route: "/user/productos",
        },
        {
          title: "Anuncios",
          icon: "mdi-buffer",
          route: "/user/anuncios",
        },
      ]
    }
  },
};
</script>

<style scoped>
  .v-footer.client-footer-override {
    background: linear-gradient(90deg, #ffd9ef 0%, #ffc8e7 48%, #ffd8c9 100%) !important;
    box-shadow: 0 -8px 16px rgba(146, 92, 120, 0.16) !important;
    border-top: 1px solid #f1bdd8 !important;
  }

  .sin {
    text-decoration: none;
  }

  .topnav a.active {
    border-bottom: 3px solid #ad1457;
  }

  .v-icon:hover {
    transform: scale(1.1);
    transition: transform 0.2s;
  }
</style>
