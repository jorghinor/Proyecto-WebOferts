<template>
  <v-card height="100%" class="mis-anuncio-card">
    <v-img
      v-for="foto in localAnuncio.fotos"
      :key="foto.id"
      :src="foto.url"
      class="mis-anuncio-media white--text align-end"
      gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
    >
      <v-card-title class="mis-anuncio-title" v-text="localAnuncio.titulo"></v-card-title>
      <v-card-subtitle
        class="caption white--text mis-anuncio-code"
        v-text="localAnuncio.codigo"
      ></v-card-subtitle>
    </v-img>
    <v-divider></v-divider>
    <div v-if="localAnuncio.descripcion.length > 75" class="px-3 pt-3 pb-1 font-weight-thin mis-anuncio-description">
      {{ localAnuncio.descripcion.substring(0, 75) }}&nbsp;[...]
    </div>
    <div v-else class="px-3 pt-3 pb-1 font-weight-thin mis-anuncio-description">
      {{ localAnuncio.descripcion }}
    </div>

    <v-card-actions class="mis-anuncio-actions">
        <v-menu
            v-model="menu"
            :close-on-content-click="false"
            :nudge-width="200"
            offset-x
            >
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    v-bind="attrs"
                    v-on="on"
                    :class="`mis-anuncio-status ${generarEstado(localAnuncio.estadoanuncio)}`"
                    small
                    rounded
                    >{{ localAnuncio.estadoanuncio }}
                </v-btn>
            </template>
            <v-card>
                <v-list>
                    <v-list-item>
                        <v-list-item-avatar>
                        <img
                            :src="negocio.logo"
                        >
                        </v-list-item-avatar>

                        <v-list-item-content>
                        <v-list-item-title>{{localAnuncio.titulo}}</v-list-item-title>
                        <v-list-item-subtitle>{{localAnuncio.codigo}}</v-list-item-subtitle>
                        </v-list-item-content>

                        <v-list-item-action>
                            <v-icon
                                v-if="localAnuncio.estadoanuncio=='inactivo'"
                                color="danger">
                                mdi-lock-alert
                            </v-icon>
                            <v-icon
                                v-if="localAnuncio.estadoanuncio=='activo'"
                                color="success">
                                mdi-lock-open
                            </v-icon>
                        </v-list-item-action>
                    </v-list-item>
                </v-list>

                <v-divider></v-divider>

                <v-card-text v-if="localAnuncio.estadoanuncio=='inactivo'">
                    <h4>Anuncio en espera de aprobación</h4>
                    <v-btn class="primary py-1" block>Re-enviar Solicitud</v-btn>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    class="sin"
                    text
                    @click="menu = false"
                >
                    Cerrar
                </v-btn>
                </v-card-actions>
            </v-card>
        </v-menu>
      <v-spacer></v-spacer>
      <vistaanuncio
        :anuncio="localAnuncio"
        :negocio="negocio">
      </vistaanuncio>
      <editar-anuncio-client :anuncio="localAnuncio" :negocio="negocio" @anuncio-actualizado="onAnuncioActualizado"></editar-anuncio-client>
    </v-card-actions>
  </v-card>
</template>

<script>
import EditarAnuncioClient from './EditarAnuncioClient.vue';

export default {
    name: 'CardClient',
    components: {
        EditarAnuncioClient,
    },
    data() {
        return {
            fav: true,
            menu: false,
            localAnuncio: this.anuncio, // Create a local copy to modify
        }
    },
    props: {
        anuncio: Object,
        negocio: Object
    },
    methods: {
        generarEstado(estado) {
            if(estado=='activo'){
                return 'sin success grey--text'
            } else {
                return 'sin danger white--text'
            }
        },
        onAnuncioActualizado(anuncioActualizado) {
            // Update the local data with the new data from the event
            this.localAnuncio = { ...anuncioActualizado };
            // Optionally, emit another event to notify the parent (AnuncioClient.vue)
            this.$emit('update-list', this.localAnuncio);
        }
    },
}
</script>

<style lang="scss" scoped>
.mis-anuncio-card {
  position: relative;
  overflow: hidden;
  border: 1px solid rgba(220, 160, 190, 0.34) !important;
  background: linear-gradient(180deg, #fffefe 0%, #fff7fb 100%) !important;
  transition: transform 0.32s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.32s ease, filter 0.32s ease, border-color 0.32s ease;
  box-shadow: 0 18px 32px rgba(45, 20, 34, 0.2), 0 6px 14px rgba(160, 91, 127, 0.14) !important;
  will-change: transform, box-shadow;
  animation: anuncioCardRise 0.48s ease both;
}

.mis-anuncio-card::before {
  content: "";
  position: absolute;
  top: -28%;
  left: -38%;
  width: 52%;
  height: 180%;
  background: linear-gradient(115deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.22) 48%, rgba(255, 255, 255, 0) 100%);
  transform: translateX(-150%) rotate(14deg);
  transition: transform 0.8s ease;
  pointer-events: none;
  z-index: 2;
}

.mis-anuncio-card::after {
  content: "";
  position: absolute;
  right: -24px;
  bottom: -38px;
  width: 150px;
  height: 150px;
  background: radial-gradient(circle, rgba(255, 111, 179, 0.2) 0%, rgba(255, 111, 179, 0) 72%);
  opacity: 0;
  transform: translateY(10px);
  transition: opacity 0.3s ease, transform 0.3s ease;
  pointer-events: none;
}

.mis-anuncio-card .v-divider {
  border-color: rgba(233, 195, 215, 0.72) !important;
}

.mis-anuncio-card:hover {
  transform: translateY(-10px) scale(1.035);
  box-shadow: 0 28px 52px rgba(28, 11, 20, 0.34), 0 10px 22px rgba(180, 82, 136, 0.18) !important;
  filter: saturate(1.06);
  border-color: rgba(236, 130, 184, 0.62) !important;
}

.mis-anuncio-card:hover::before {
  transform: translateX(255%) rotate(14deg);
}

.mis-anuncio-card:hover::after {
  opacity: 1;
  transform: translateY(-10px);
}

.mis-anuncio-card:hover ::v-deep .mis-anuncio-media {
  box-shadow: inset 0 -28px 42px rgba(20, 8, 15, 0.16);
}

.mis-anuncio-title,
.mis-anuncio-code,
.mis-anuncio-description,
.mis-anuncio-status {
  position: relative;
  z-index: 3;
}

.mis-anuncio-title {
  transition: transform 0.28s ease, text-shadow 0.28s ease;
  text-shadow: 0 2px 10px rgba(20, 8, 15, 0.18);
}

.mis-anuncio-code {
  letter-spacing: 0.8px;
  opacity: 0.96;
  transform: translateY(0);
  transition: opacity 0.28s ease, transform 0.28s ease;
}

.mis-anuncio-description {
  color: #5f4b56;
  line-height: 1.55;
  transition: color 0.28s ease, transform 0.28s ease;
}

.mis-anuncio-actions {
  position: relative;
  z-index: 3;
  padding: 10px 12px 12px;
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.82) 0%, rgba(255, 244, 249, 0.96) 100%);
  border-top: 1px solid rgba(236, 198, 217, 0.72);
  backdrop-filter: blur(6px);
}

.mis-anuncio-status {
  transition: transform 0.24s ease, box-shadow 0.24s ease, filter 0.24s ease;
  box-shadow: 0 8px 18px rgba(56, 23, 39, 0.2) !important;
}

.mis-anuncio-card:hover .mis-anuncio-title {
  transform: translateY(-2px);
  text-shadow: 0 8px 18px rgba(20, 8, 15, 0.28);
}

.mis-anuncio-card:hover .mis-anuncio-description {
  color: #4d3944;
  transform: translateY(-1px);
}

.mis-anuncio-card:hover .mis-anuncio-code {
  opacity: 1;
  transform: translateY(-1px);
}

.mis-anuncio-card:hover .mis-anuncio-status {
  transform: scale(1.06);
  box-shadow: 0 14px 26px rgba(46, 16, 31, 0.28) !important;
  filter: saturate(1.05);
}

.mis-anuncio-card:hover ::v-deep .mis-anuncio-media .v-image__image {
  transform: scale(1.09);
  filter: saturate(1.14) contrast(1.04) brightness(1.03);
}

::v-deep .mis-anuncio-media .v-image__image {
  transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1), filter 0.55s ease;
}

::v-deep .mis-anuncio-actions .v-btn--icon {
  transition: transform 0.22s ease, box-shadow 0.22s ease, background-color 0.22s ease;
  border-radius: 999px;
}

::v-deep .mis-anuncio-actions .v-btn--icon .v-icon {
  transition: transform 0.22s ease, color 0.22s ease;
}

.mis-anuncio-card:hover ::v-deep .mis-anuncio-actions .v-btn--icon {
  background: rgba(255, 255, 255, 0.76);
  box-shadow: 0 10px 18px rgba(40, 16, 28, 0.14);
}

.mis-anuncio-card:hover ::v-deep .mis-anuncio-actions .v-btn--icon:hover {
  transform: translateY(-2px) scale(1.1);
}

.mis-anuncio-card:hover ::v-deep .mis-anuncio-actions .v-btn--icon:hover .v-icon {
  transform: scale(1.08);
  color: #c44786;
}

@keyframes anuncioCardRise {
  from {
    opacity: 0;
    transform: translateY(10px) scale(0.985);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}
</style>
