<template>
  <v-app id="inspire">
    <navbarclient :pageTitle="title"></navbarclient>

    <v-main class="grey lighten-3">
      <v-container>
        <v-card class="elevation-2">
          <v-toolbar height="80px" flat>
            <v-toolbar-title>Resumen de mi Negocio</v-toolbar-title>
          </v-toolbar>
          <v-divider></v-divider>

          <v-card-text>
            <v-row>
              <!-- Tarjetas de Resumen -->
              <v-col cols="12" sm="6" md="3">
                <v-card class="elevation-2 text-center pa-4">
                  <v-icon large color="blue">mdi-bullhorn</v-icon>
                  <div class="headline font-weight-bold mt-2">{{ stats.anuncios_activos }}</div>
                  <div class="caption">Anuncios Activos</div>
                </v-card>
              </v-col>
              <v-col cols="12" sm="6" md="3">
                <v-card class="elevation-2 text-center pa-4">
                  <v-icon large color="green">mdi-package-variant-closed</v-icon>
                  <div class="headline font-weight-bold mt-2">{{ stats.total_productos }}</div>
                  <div class="caption">Productos Totales</div>
                </v-card>
              </v-col>
              <v-col cols="12" sm="6" md="3">
                <v-card class="elevation-2 text-center pa-4">
                  <v-icon large color="amber">mdi-star</v-icon>
                  <div class="headline font-weight-bold mt-2">{{ stats.calificacion_promedio || 'N/A' }}</div>
                  <div class="caption">Calificación Promedio</div>
                </v-card>
              </v-col>
              <v-col cols="12" sm="6" md="3">
                <v-card class="elevation-2 text-center pa-4">
                  <v-icon large color="purple">mdi-eye</v-icon>
                  <div class="headline font-weight-bold mt-2">{{ totalVistas }}</div>
                  <div class="caption">Vistas Totales (Sim)</div>
                </v-card>
              </v-col>

              <!-- Gráfica y Últimas Reseñas -->
              <v-col cols="12" md="7">
                <div class="admin-bars-box" v-html="anunciosMasVistosBarsHtml"></div>
              </v-col>

              <v-col cols="12" md="5">
                <v-card class="elevation-2 pa-4">
                  <h3 class="subtitle-1 font-weight-bold mb-3">Últimas Reseñas</h3>
                  <v-list dense v-if="stats.ultimas_resenas && stats.ultimas_resenas.length">
                    <v-list-item v-for="review in stats.ultimas_resenas" :key="review.id">
                      <v-list-item-content>
                        <v-list-item-title class="font-weight-bold">{{ review.user_name }}</v-list-item-title>
                        <v-list-item-subtitle>{{ review.comment }}</v-list-item-subtitle>
                      </v-list-item-content>
                      <v-list-item-action>
                        <v-rating :value="review.rating" color="amber" dense readonly size="14"></v-rating>
                      </v-list-item-action>
                    </v-list-item>
                  </v-list>
                  <div v-else class="text-center grey--text py-4">
                    Aún no tienes reseñas.
                  </div>
                </v-card>
              </v-col>

              <!-- Gráfica Antigua -->
              <v-col cols="12" class="mt-5">
                 <v-card class="elevation-2 pa-4">
                    <h3 class="subtitle-1 font-weight-bold mb-3">Gráfica Antigua</h3>
                    <v-sparkline
                      :value="value"
                      :gradient="gradient"
                      :smooth="radius || false"
                      :padding="padding"
                      :line-width="width"
                      :stroke-linecap="lineCap"
                      :gradient-direction="gradientDirection"
                      :fill="fill"
                      :type="type"
                      :auto-line-width="autoLineWidth"
                      auto-draw
                    ></v-sparkline>
                 </v-card>
              </v-col>

            </v-row>
          </v-card-text>
        </v-card>
      </v-container>
    </v-main>
    <footerclient></footerclient>
  </v-app>
</template>

<script>
const gradients = [
  ["#222"],
  ["#42b3f4"],
  ["red", "orange", "yellow"],
  ["purple", "violet"],
  ["#00c6ff", "#F0F", "#FF0"],
  ["#f72047", "#ffd200", "#1feaea"],
];

export default {
  name: "DashboardCliente",
  data() {
    return {
      title: "Dashboard",
      stats: {},
      // Datos para gráfica antigua
      width: 2,
      radius: 10,
      padding: 8,
      lineCap: "round",
      gradient: gradients[5],
      value: [0, 2, 5, 9, 5, 10, 3, 5, 0, 0, 1, 8, 2, 9, 0],
      gradientDirection: "top",
      gradients,
      fill: false,
      type: "trend",
      autoLineWidth: false,
      isCompact3D: false,
    };
  },
  computed: {
    totalVistas() {
      if (!this.stats.anuncios_mas_vistos) return 0;
      return this.stats.anuncios_mas_vistos.reduce((acc, item) => acc + item.vistas, 0);
    },
    anunciosMasVistosBarsHtml() {
      return this.build3DBarChartSvg(this.stats.anuncios_mas_vistos, {
        title: "Top 5 Anuncios Más Vistos",
        labelKey: "titulo",
        valueKey: "vistas",
        frontColor: "#8e44ad",
        sideColor: "#7d3c98",
        topColor: "#c39bd3",
        compact: this.isCompact3D,
        tooltipBuilder: (item) => `${item.titulo}: ${item.vistas} vistas`,
      });
    }
  },
  methods: {
    updateViewportMode() {
      this.isCompact3D = window.innerWidth <= 760;
    },
    safeText(value) {
      if (value === null || value === undefined) return "";
      return String(value).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\"/g, "&quot;").replace(/'/g, "&#39;");
    },
    project3DPoint(x, y, z, cfg) {
      return {
        x: cfg.originX + x - z * cfg.zSkewX,
        y: cfg.originY - y - z * cfg.zSkewY,
      };
    },
    pointsToString(points) {
      return points.map((p) => `${p.x.toFixed(2)},${p.y.toFixed(2)}`).join(" ");
    },
    build3DBarChartSvg(items, config) {
      if (!items || !items.length) {
        return `<div class="admin-chart-title">${config.title}</div><div class="admin-top-list"><li>Sin datos disponibles</li></div>`;
      }

      const top5 = items.slice(0, 5);
      const maxVal = Math.max(...top5.map((it) => Number(it[config.valueKey]) || 0), 1);
      const compact = !!config.compact;
      const cfg = {
        width: compact ? 500 : 560, height: compact ? 245 : 255,
        originX: compact ? 60 : 72, originY: compact ? 198 : 212,
        xLen: compact ? 370 : 415, yLen: compact ? 138 : 160,
        zLen: compact ? 95 : 120, zSkewX: 0.72, zSkewY: 0.45,
        barWidth: compact ? 30 : 38, barDepth: compact ? 20 : 26,
        slot: compact ? 64 : 74, startX: compact ? 20 : 24,
        scaleY: (compact ? 118 : 140) / maxVal,
      };

      const O = this.project3DPoint(0, 0, 0, cfg);
      const X = this.project3DPoint(cfg.xLen, 0, 0, cfg);
      const Y = this.project3DPoint(0, cfg.yLen, 0, cfg);
      const Z = this.project3DPoint(0, 0, cfg.zLen, cfg);
      const yTextOffset = compact ? 18 : 24;
      const tickY1 = compact ? 14 : 18;
      const tickY2 = compact ? 24 : 30;
      const showSubLabel = !!config.showSubLabel;

      const yTicks = 5;
      const yGrid = Array.from({ length: yTicks + 1 }).map((_, i) => {
        const yVal = (cfg.yLen / yTicks) * i;
        const p1 = this.project3DPoint(0, yVal, 0, cfg);
        const p2 = this.project3DPoint(cfg.xLen, yVal, 0, cfg);
        const val = Math.round((maxVal / yTicks) * i);
        return `<line class="admin-grid-line" x1="${p1.x}" y1="${p1.y}" x2="${p2.x}" y2="${p2.y}"></line><text class="admin-axis-text" x="${p1.x - yTextOffset}" y="${p1.y + 3}">${val}</text>`;
      }).join("");

      const bars = top5.map((item, idx) => {
        const value = Number(item[config.valueKey]) || 0;
        const h = value * cfg.scaleY;
        const x0 = cfg.startX + idx * cfg.slot;
        const bw = cfg.barWidth;
        const d = cfg.barDepth;

        const A = this.project3DPoint(x0, 0, 0, cfg);
        const B = this.project3DPoint(x0 + bw, 0, 0, cfg);
        const C = this.project3DPoint(x0 + bw, h, 0, cfg);
        const D = this.project3DPoint(x0, h, 0, cfg);
        const F = this.project3DPoint(x0 + bw, 0, d, cfg);
        const G = this.project3DPoint(x0 + bw, h, d, cfg);
        const H = this.project3DPoint(x0, h, d, cfg);

        const label = this.safeText(item[config.labelKey] || `Item ${idx + 1}`);
        const shortLabel = label.length > 10 ? `${label.substring(0, 10)}...` : label;
        const tooltip = this.safeText(config.tooltipBuilder(item));

        const xTick = this.project3DPoint(x0 + bw / 2, 0, 0, cfg);
        const vText = this.project3DPoint(x0 + bw / 2, h + 10, d * 0.4, cfg);

        return `<g class="admin-3d-bar" data-tooltip="${tooltip}"><polygon class="admin-face-side" fill="${config.sideColor}" points="${this.pointsToString([B, C, G, F])}"></polygon><polygon class="admin-face-top" fill="${config.topColor}" points="${this.pointsToString([D, C, G, H])}"></polygon><polygon class="admin-face-front" fill="${config.frontColor}" points="${this.pointsToString([A, B, C, D])}"></polygon><text class="admin-value-text" x="${vText.x}" y="${vText.y}" text-anchor="middle">${value}</text></g><text class="admin-axis-text" x="${xTick.x}" y="${cfg.originY + tickY1}" text-anchor="middle">${idx + 1}</text><text class="admin-axis-text" x="${xTick.x}" y="${cfg.originY + tickY2}" text-anchor="middle">${shortLabel}</text>`;
      }).join("");

      return `<div class="admin-chart-title">${config.title}</div><div class="admin-plot3d-wrap"><svg class="admin-plot3d" viewBox="0 0 ${cfg.width} ${cfg.height}" preserveAspectRatio="xMidYMid meet" aria-label="${config.title}">${yGrid}<line class="admin-axis-line" x1="${O.x}" y1="${O.y}" x2="${X.x}" y2="${X.y}"></line><line class="admin-axis-line" x1="${O.x}" y1="${O.y}" x2="${Y.x}" y2="${Y.y}"></line>${bars}</svg></div>`;
    },
    attachTooltipHandlers() {
      let tooltip = document.getElementById("admin-chart-tooltip");
      if (!tooltip) {
        tooltip = document.createElement("div");
        tooltip.id = "admin-chart-tooltip";
        tooltip.className = "admin-chart-tooltip";
        document.body.appendChild(tooltip);
      }
      const elements = this.$el.querySelectorAll("[data-tooltip]");
      elements.forEach((el) => {
        if (el.dataset.boundTooltip === "1") return;
        el.dataset.boundTooltip = "1";
        el.addEventListener("mouseenter", () => {
          tooltip.textContent = el.dataset.tooltip || "";
          tooltip.classList.add("visible");
        });
        el.addEventListener("mousemove", (e) => {
          tooltip.style.left = `${e.clientX + 12}px`;
          tooltip.style.top = `${e.clientY + 14}px`;
        });
        el.addEventListener("mouseleave", () => {
          tooltip.classList.remove("visible");
        });
      });
    },
  },
  created() {
    axios.get('/user/dashboard/stats')
      .then(response => {
        this.stats = response.data;
        this.$nextTick(() => this.attachTooltipHandlers());
      })
      .catch(error => {
        console.error("Error cargando estadísticas del cliente:", error);
      });
  },
  mounted() {
    this.updateViewportMode();
    this._onResizeDashboard = () => {
      const previous = this.isCompact3D;
      this.updateViewportMode();
      if (previous !== this.isCompact3D) {
        this.$nextTick(() => this.attachTooltipHandlers());
      }
    };
    window.addEventListener("resize", this._onResizeDashboard);
  },
  updated() {
    this.$nextTick(() => this.attachTooltipHandlers());
  },
  beforeDestroy() {
    if (this._onResizeDashboard) {
      window.removeEventListener("resize", this._onResizeDashboard);
    }
  },
};
</script>

<style scoped>
/* Estilos del Dashboard de Admin para consistencia */
.admin-bars-box { background: linear-gradient(180deg, #fff 0%, #f7fcff 100%); border: 1px solid #d8e6f2; border-radius: 14px; box-shadow: 0 10px 20px rgba(42, 80, 112, .12); padding: 14px 16px 18px; }
.admin-chart-title { font-size: 13px; font-weight: 800; color: #2d4f67; margin-bottom: 10px; }
.admin-plot3d-wrap { margin-top: 6px; }
.admin-plot3d { width: 100%; height: 260px; }
.admin-axis-line { stroke: #5f7b93; stroke-width: 1.6; }
.admin-grid-line { stroke: rgba(74, 107, 132, .24); stroke-width: 1; stroke-dasharray: 3 3; }
.admin-axis-text { font-size: 10px; fill: #4b667d; font-weight: 700; }
.admin-axis-name { font-size: 11px; fill: #2f4e64; font-weight: 800; }
.admin-3d-bar { cursor: pointer; transition: transform 150ms ease; transform-origin: center; }
.admin-3d-bar:hover .admin-face-front { filter: brightness(1.08); }
.admin-3d-bar:hover .admin-face-side { filter: brightness(1.08); }
.admin-3d-bar:hover .admin-face-top { filter: brightness(1.08); }
.admin-face-front { fill-opacity: .96; }
.admin-face-side { fill-opacity: .88; }
.admin-face-top { fill-opacity: .92; }
.admin-value-text { font-size: 10px; fill: #28475d; font-weight: 800; }
.admin-chart-tooltip { position: fixed; z-index: 9999; pointer-events: none; background: rgba(33, 49, 62, .94); color: #fff; border-radius: 8px; padding: 6px 8px; font-size: 12px; font-weight: 700; box-shadow: 0 8px 16px rgba(15, 26, 34, .35); opacity: 0; transform: translateY(4px); transition: opacity 120ms ease, transform 120ms ease; max-width: 220px; }
.admin-chart-tooltip.visible { opacity: 1; transform: translateY(0); }
</style>
