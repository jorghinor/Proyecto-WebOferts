<template>
  <div class="location-picker">
    <div class="location-picker__meta">
      <small class="location-picker__hint">{{ helperText }}</small>
      <small v-if="coordsText" class="location-picker__coords">{{ coordsText }}</small>
    </div>
    <div ref="map" class="location-picker__map" :style="{ height }"></div>
  </div>
</template>

<script>
const DEFAULT_CENTER = [-17.3895, -66.1568];

export default {
  name: 'LocationPicker',
  props: {
    latitude: { type: [Number, String], default: null },
    longitud: { type: [Number, String], default: null },
    gmaps: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
    visible: { type: Boolean, default: true },
    height: { type: String, default: '320px' },
  },
  data() {
    return { map: null, marker: null };
  },
  computed: {
    coordsText() {
      const pos = this.currentPosition;
      return pos ? `Lat: ${pos[0].toFixed(6)} | Lng: ${pos[1].toFixed(6)}` : '';
    },
    helperText() {
      return this.disabled
        ? 'Activa edición para cambiar la ubicación.'
        : 'Haz clic en el mapa o arrastra el marcador para guardar coordenadas.';
    },
    currentPosition() {
      const lat = this.parseNumber(this.latitude);
      const lng = this.parseNumber(this.longitud);
      return lat === null || lng === null ? null : [lat, lng];
    },
  },
  mounted() {
    this.$nextTick(() => {
      this.initMap();
      setTimeout(() => this.invalidateMap(), 350);
    });
  },
  beforeDestroy() {
    if (this.map) this.map.remove();
  },
  watch: {
    latitude() { this.syncFromProps(); },
    longitud() { this.syncFromProps(); },
    gmaps() { this.syncFromProps(); },
    disabled() { this.applyMarkerState(); },
    visible(value) {
      if (!value) return;
      this.$nextTick(() => {
        if (!this.map) this.initMap();
        setTimeout(() => this.invalidateMap(), 150);
      });
    },
  },
  methods: {
    parseNumber(value) {
      const parsed = parseFloat(value);
      return Number.isFinite(parsed) ? parsed : null;
    },
    extractCoordsFromUrl(url) {
      if (!url) return null;
      let decoded = url;
      try {
        decoded = decodeURIComponent(url);
      } catch (error) {
        decoded = url;
      }

      const candidates = [url, decoded].filter((value, index, list) => value && list.indexOf(value) === index);
      for (const candidate of candidates) {
        const match = candidate.match(/@(-?[0-9]+(?:\.[0-9]+)?),(-?[0-9]+(?:\.[0-9]+)?)/)
          || candidate.match(/!3d(-?[0-9]+(?:\.[0-9]+)?)!4d(-?[0-9]+(?:\.[0-9]+)?)/)
          || candidate.match(/query=(-?[0-9]+(?:\.[0-9]+)?),(-?[0-9]+(?:\.[0-9]+)?)/)
          || candidate.match(/q=(-?[0-9]+(?:\.[0-9]+)?),(-?[0-9]+(?:\.[0-9]+)?)/);
        if (match) {
          return [parseFloat(match[1]), parseFloat(match[2])];
        }
      }
      return null;
    },
    initMap() {
      if (typeof L === 'undefined' || this.map || !this.$refs.map) return;
      const positionFromUrl = this.extractCoordsFromUrl(this.gmaps);
      const position = this.currentPosition || positionFromUrl || DEFAULT_CENTER;
      this.map = L.map(this.$refs.map).setView(position, this.currentPosition ? 16 : 13);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(this.map);
      this.map.on('click', (event) => {
        if (this.disabled) return;
        this.placeMarker([event.latlng.lat, event.latlng.lng], true);
      });
      if (this.currentPosition || positionFromUrl) {
        this.placeMarker(position, false);
        if (!this.currentPosition && positionFromUrl) {
          this.emitLocation(position[0], position[1]);
        }
      }
    },
    placeMarker(position, emitChange) {
      if (!this.map) return;
      if (!this.marker) {
        this.marker = L.marker(position, { draggable: !this.disabled }).addTo(this.map);
        this.marker.on('dragend', () => {
          const point = this.marker.getLatLng();
          this.emitLocation(point.lat, point.lng);
        });
      } else {
        this.marker.setLatLng(position);
      }
      this.map.panTo(position);
      this.applyMarkerState();
      if (emitChange) this.emitLocation(position[0], position[1]);
    },
    applyMarkerState() {
      if (!this.marker || !this.marker.dragging) return;
      this.disabled ? this.marker.dragging.disable() : this.marker.dragging.enable();
      this.invalidateMap();
    },
    syncFromProps() {
      const position = this.currentPosition || this.extractCoordsFromUrl(this.gmaps);
      if (!position || !this.map) return;
      this.placeMarker(position, false);
      if (!this.currentPosition && this.gmaps) {
        this.emitLocation(position[0], position[1]);
      }
    },
    emitLocation(lat, lng) {
      const latitude = Number(lat.toFixed(6));
      const longitud = Number(lng.toFixed(6));
      this.$emit('location-selected', {
        latitude,
        longitud,
        gmaps: `https://www.google.com/maps/search/?api=1&query=${latitude},${longitud}`,
      });
    },
    invalidateMap() {
      if (this.map) this.map.invalidateSize();
    },
  },
};
</script>

<style scoped>
.location-picker__meta { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 8px; color: #6f8192; font-size: 12px; }
.location-picker__coords { font-weight: 700; color: #355169; }
.location-picker__map { width: 100%; border-radius: 14px; overflow: hidden; border: 1px solid #dbe7f1; box-shadow: 0 10px 22px rgba(42, 73, 106, 0.1); }
</style>

