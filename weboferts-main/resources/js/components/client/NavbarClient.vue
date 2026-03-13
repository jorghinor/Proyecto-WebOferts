<template>
  <v-app-bar app clipped-right flat height="72">
    <v-menu offset-y>
      <template v-slot:activator="{ on, attrs }">
        <v-app-bar-nav-icon v-bind="attrs" v-on="on"></v-app-bar-nav-icon>
      </template>
      <v-list>
        <v-list-item
          v-for="link in links"
          :key="link.title"
          :href="link.route"
          link
        >
          <v-list-item-icon>
            <v-icon>{{ link.icon }}</v-icon>
          </v-list-item-icon>
          <v-list-item-title>{{ link.title }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>

    <v-toolbar-title>{{ pageTitle }}</v-toolbar-title>

    <v-spacer></v-spacer>

    <v-btn icon>
      <v-icon>mdi-heart</v-icon>
    </v-btn>

    <v-btn icon>
      <v-icon>mdi-magnify</v-icon>
    </v-btn>

    <v-menu bottom left>
      <template v-slot:activator="{ on, attrs }">
        <v-btn icon v-bind="attrs" v-on="on">
          <v-icon>mdi-dots-vertical</v-icon>
        </v-btn>
      </template>

      <v-list>
        <v-list-item>
          <v-list-item-title>
            <div>
              <a
                class="primary v-btn v-btn--is-elevated v-btn--has-bg v-btn--rounded theme--light v-size--small"
                :href="logout"
                onclick="event.preventDefault();
                          document.getElementById('logout-form').submit();"
              >
                Logout
              </a>
              <form
                id="logout-form"
                :action="logout"
                method="POST"
                style="display: none"
              >
                <input type="text" name="_token" :value="csrf" />
              </form>
            </div>
          </v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-app-bar>
</template>

<script>
export default {
  name: "NavbarClient",
  props: {
    pageTitle: String,
  },
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
      ],
      csrf: document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content"),
      logout: window.location.origin + "/logout",
    };
  },
  methods: {
    micuenta() {
      window.location.href = "/cuenta";
    },
    salir() {},
  },

};
</script>

<style lang="scss" scoped>
</style>
