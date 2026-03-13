<template>
<v-dialog v-model="dialog" persistent fullscreen>
      <template v-slot:activator="{ on, attrs }">
        <v-btn class="success black--text" dark v-bind="attrs" v-on="on">
          Imagen
        </v-btn>
      </template>
      <v-card>
        <clipperimagedialog ></clipperimagedialog>
      </v-card>
</v-dialog> 
</template>

<script>

export default {
  components: {
    VueCropper,
  },
  data() {
    return {
      dialog: false
    }
  },
}