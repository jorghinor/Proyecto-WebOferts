<template>
  <v-app id="inspire">
    <navbar></navbar>

    <v-main>
      <v-row class="px-4 pt-3 pb-1 admin-filters-row" align="center">
        <v-col cols="12" md="4" class="py-1">
          <v-select
            v-model="selectedPeriod"
            :items="periodOptions"
            item-text="text"
            item-value="value"
            label="Periodo"
            dense
            outlined
            hide-details
            @change="onPeriodChange"
          ></v-select>
        </v-col>
        <v-col v-if="selectedPeriod === 'custom'" cols="12" sm="6" md="3" class="py-1">
          <v-text-field
            v-model="dateFrom"
            type="date"
            label="Desde"
            dense
            outlined
            hide-details
          ></v-text-field>
        </v-col>
        <v-col v-if="selectedPeriod === 'custom'" cols="12" sm="6" md="3" class="py-1">
          <v-text-field
            v-model="dateTo"
            type="date"
            label="Hasta"
            dense
            outlined
            hide-details
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="2" class="py-1 d-flex justify-end admin-filters-actions">
          <v-btn
            v-if="selectedPeriod === 'custom'"
            color="primary"
            rounded
            small
            class="mr-2"
            @click="applyCustomRange"
          >
            Aplicar
          </v-btn>
          <v-btn color="primary" rounded small @click="exportDashboardPdf">
            Exportar PDF
          </v-btn>
        </v-col>
      </v-row>

      <div id="admin-stats-panels" class="admin-stats-wrap">
        <!-- NUEVAS TARJETAS DE VENTAS -->
        <div class="admin-stats-grid mb-4" style="grid-template-columns: repeat(2, 1fr);">
          <div class="admin-stat-card" style="background: linear-gradient(180deg, #e8f5e9 0%, #c8e6c9 100%); border-color: #a5d6a7;">
            <div class="admin-stat-label" style="color: #2e7d32;">Ventas Totales</div>
            <div class="admin-stat-value" style="color: #1b5e20;">{{ stats.ventas_totales || 0 }} Bs.</div>
          </div>
          <div class="admin-stat-card" style="background: linear-gradient(180deg, #fff3e0 0%, #ffe0b2 100%); border-color: #ffcc80;">
            <div class="admin-stat-label" style="color: #ef6c00;">Pedidos Realizados</div>
            <div class="admin-stat-value" style="color: #e65100;">{{ stats.cantidad_pedidos || 0 }}</div>
          </div>
        </div>

        <div class="admin-stats-grid">
          <div class="admin-stat-card">
            <div class="admin-stat-label">{{ negociosLabel }}</div>
            <div class="admin-stat-value">{{ stats.negocios }}</div>
          </div>
          <div class="admin-stat-card">
            <div class="admin-stat-label">{{ anunciosLabel }}</div>
            <div class="admin-stat-value">{{ stats.anuncios }}</div>
          </div>
          <div class="admin-stat-card">
            <div class="admin-stat-label">{{ productosLabel }}</div>
            <div class="admin-stat-value">{{ stats.productos }}</div>
          </div>
        </div>

        <div class="admin-chart-grid">
          <div class="admin-pie-box">
            <div class="admin-chart-title">Distribucion de metricas (Torta)</div>
            <div class="admin-pie-chart" v-html="pieSvg"></div>
            <div class="admin-pie-legend">
              <div class="admin-legend-item"><span class="admin-dot pie-n"></span> {{ negociosLabel }} {{ nPct }}%</div>
              <div class="admin-legend-item"><span class="admin-dot pie-a"></span> {{ anunciosLabel }} {{ aPct }}%</div>
              <div class="admin-legend-item"><span class="admin-dot pie-p"></span> {{ productosLabel }} {{ pPct }}%</div>
            </div>
          </div>

          <div class="admin-bars-box" v-html="comparativoMetricas3DHtml"></div>
        </div>

        <!-- NUEVA GRÁFICA DE VENTAS DIARIAS -->
        <div class="admin-chart-grid mt-4" style="grid-template-columns: 1fr;">
           <div class="admin-bars-box" v-html="ventasDiariasBarsHtml"></div>
        </div>

        <div class="admin-tops-grid">
          <!-- NUEVA TABLA DE PRODUCTOS VENDIDOS -->
          <div class="admin-top-card" style="border-color: #81c784;">
            <div class="admin-top-title" style="color: #2e7d32;">Top Ventas (Carrito)</div>
            <ol class="admin-top-list" v-if="stats.top_productos_vendidos_real && stats.top_productos_vendidos_real.length">
              <li v-for="item in stats.top_productos_vendidos_real" :key="`real-${item.producto}`">
                <strong>{{ item.producto }}</strong> · {{ item.cantidad_vendida }} un. · Bs {{ item.total_generado }}
              </li>
            </ol>
            <ol class="admin-top-list" v-else><li>Sin ventas registradas</li></ol>
          </div>

          <div class="admin-top-card">
            <div class="admin-top-title">Top Negocios Rentables</div>
            <ol class="admin-top-list" v-if="stats.top_negocios_rentables && stats.top_negocios_rentables.length">
              <li v-for="item in stats.top_negocios_rentables" :key="`neg-${item.id}`">
                <strong>{{ item.nnegocio }}</strong> · Inversion Bs {{ item.inversion_total }} · {{ item.anuncios_activos }} anuncios
              </li>
            </ol>
            <ol class="admin-top-list" v-else><li>Sin datos disponibles</li></ol>
          </div>

          <div class="admin-top-card">
            <div class="admin-top-title">Top Productos Promocionados</div>
            <ol class="admin-top-list" v-if="stats.top_productos_promocionados && stats.top_productos_promocionados.length">
              <li v-for="item in stats.top_productos_promocionados" :key="`pro-${item.id}`">
                <strong>{{ item.producto }}</strong> ({{ item.negocio }}) · {{ item.anuncios_activos }} anuncios activos
              </li>
            </ol>
            <ol class="admin-top-list" v-else><li>Sin datos disponibles</li></ol>
          </div>
        </div>

        <div class="admin-mini-charts-grid">
          <div class="admin-mini-chart-card" v-html="topProductosVendidosBarsHtml"></div>
          <div class="admin-mini-chart-card" v-html="topAnunciosRentablesBarsHtml"></div>
        </div>
      </div>

      <v-row class="mt-2">
        <v-col>
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
        </v-col>
      </v-row>
    </v-main>
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
  name: "DashboardAdmin",
  data: () => ({
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
    stats: {
      negocios: 0,
      anuncios: 0,
      productos: 0,
      total: 0,
      ventas_totales: 0,
      cantidad_pedidos: 0,
      top_productos_vendidos_real: [],
      ventas_diarias: [],
      labels: {
        negocios: "Negocios rentables",
        anuncios: "Anuncios destacados",
        productos: "Productos promocionados",
      },
      top_negocios_rentables: [],
      top_productos_promocionados: [],
      top_anuncios_destacados: [],
      top_productos_vendidos: [],
      top_anuncios_rentables: [],
    },
    isCompact3D: false,
    selectedPeriod: "all",
    dateFrom: "",
    dateTo: "",
    periodOptions: [
      { text: "Todo", value: "all" },
      { text: "Hoy", value: "today" },
      { text: "Ultimos 7 dias", value: "week" },
      { text: "Este mes", value: "month" },
      { text: "Personalizado", value: "custom" },
    ],
  }),
  computed: {
    negociosLabel() {
      return (this.stats.labels && this.stats.labels.negocios) || "Negocios";
    },
    anunciosLabel() {
      return (this.stats.labels && this.stats.labels.anuncios) || "Anuncios";
    },
    productosLabel() {
      return (this.stats.labels && this.stats.labels.productos) || "Productos";
    },
    totalSafe() {
      return this.stats.total > 0 ? this.stats.total : 1;
    },
    nPct() {
      return Math.round((this.stats.negocios / this.totalSafe) * 100);
    },
    aPct() {
      return Math.round((this.stats.anuncios / this.totalSafe) * 100);
    },
    pPct() {
      return Math.max(0, 100 - this.nPct - this.aPct);
    },
    pieSvg() {
      const slices = [
        { value: this.nPct, color: "#56b9f8", tooltip: `${this.negociosLabel}: ${this.stats.negocios} (${this.nPct}%)` },
        { value: this.aPct, color: "#ff7bb7", tooltip: `${this.anunciosLabel}: ${this.stats.anuncios} (${this.aPct}%)` },
        { value: this.pPct, color: "#8dd9a5", tooltip: `${this.productosLabel}: ${this.stats.productos} (${this.pPct}%)` },
      ];
      return this.buildPieSvg(slices);
    },
    comparativoMetricas3DHtml() {
      return this.build3DBarChartSvg(
        [
          { nombre: this.negociosLabel, valor: this.stats.negocios },
          { nombre: this.anunciosLabel, valor: this.stats.anuncios },
          { nombre: this.productosLabel, valor: this.stats.productos },
        ],
        {
          title: "Comparativo de metricas (Barras 3D con ejes X-Y-Z)",
          labelKey: "nombre",
          valueKey: "valor",
          frontColor: "#69b2ff",
          sideColor: "#4f95df",
          topColor: "#a9d4ff",
          compact: this.isCompact3D,
          tooltipBuilder: (item) => `${item.nombre}: ${item.valor}`,
        }
      );
    },
    ventasDiariasBarsHtml() {
      return this.build3DBarChartSvg(this.stats.ventas_diarias, {
        title: "Ventas Diarias (Bs) - Últimos 7 días",
        labelKey: "fecha",
        valueKey: "total_venta",
        frontColor: "#66bb6a",
        sideColor: "#43a047",
        topColor: "#a5d6a7",
        compact: this.isCompact3D,
        tooltipBuilder: (item) => `${item.fecha}: ${item.total_venta} Bs`,
      });
    },
    topProductosVendidosBarsHtml() {
      return this.build3DBarChartSvg(this.stats.top_productos_vendidos, {
        title: "Top 5 Productos Mas Vendidos (Barras 3D)",
        labelKey: "producto",
        subLabelKey: "negocio",
        valueKey: "ventas_estimadas",
        frontColor: "#ffb257",
        sideColor: "#e78c2c",
        topColor: "#ffd39a",
        compact: this.isCompact3D,
        showSubLabel: false,
        tooltipBuilder: (item) =>
          `${item.producto} (${item.negocio}) - Ventas estimadas: ${item.ventas_estimadas} - Inversion Bs ${item.inversion_total}`,
      });
    },
    topAnunciosRentablesBarsHtml() {
      return this.build3DBarChartSvg(this.stats.top_anuncios_rentables, {
        title: "Top 5 Anuncios Mas Rentables (Barras 3D)",
        labelKey: "titulo",
        subLabelKey: "negocio",
        valueKey: "rentabilidad_total",
        frontColor: "#7e99ff",
        sideColor: "#617fef",
        topColor: "#b5c4ff",
        compact: this.isCompact3D,
        showSubLabel: false,
        tooltipBuilder: (item) => `${item.titulo} (${item.negocio}) - Rentabilidad Bs ${item.rentabilidad_total}`,
      });
    },
    periodLabel() {
      if (this.selectedPeriod === "all") return "Todo";
      if (this.selectedPeriod === "today") return "Hoy";
      if (this.selectedPeriod === "week") return "Ultimos 7 dias";
      if (this.selectedPeriod === "month") return "Este mes";
      if (this.selectedPeriod === "custom") {
        if (this.dateFrom && this.dateTo) return `${this.dateFrom} a ${this.dateTo}`;
        return "Personalizado";
      }
      return "Sin filtro";
    },
  },
  methods: {
    updateViewportMode() {
      this.isCompact3D = window.innerWidth <= 760;
    },
    formatDateISO(date) {
      const y = date.getFullYear();
      const m = String(date.getMonth() + 1).padStart(2, "0");
      const d = String(date.getDate()).padStart(2, "0");
      return `${y}-${m}-${d}`;
    },
    setPeriodRange(period) {
      const today = new Date();
      if (period === "all") {
        this.dateFrom = "";
        this.dateTo = "";
        return;
      }
      if (period === "today") {
        const d = this.formatDateISO(today);
        this.dateFrom = d;
        this.dateTo = d;
        return;
      }
      if (period === "week") {
        const from = new Date(today);
        from.setDate(from.getDate() - 6);
        this.dateFrom = this.formatDateISO(from);
        this.dateTo = this.formatDateISO(today);
        return;
      }
      if (period === "month") {
        const from = new Date(today.getFullYear(), today.getMonth(), 1);
        this.dateFrom = this.formatDateISO(from);
        this.dateTo = this.formatDateISO(today);
      }
    },
    getFilterParams() {
      if (this.selectedPeriod === "custom") {
        if (this.dateFrom && this.dateTo) {
          return { date_from: this.dateFrom, date_to: this.dateTo };
        }
        return {};
      }
      return { date_from: this.dateFrom, date_to: this.dateTo };
    },
    onPeriodChange() {
      if (this.selectedPeriod !== "custom") {
        this.setPeriodRange(this.selectedPeriod);
        this.fetchDashboardStats();
      }
    },
    applyCustomRange() {
      if (!this.dateFrom || !this.dateTo) {
        alert("Selecciona ambas fechas para el rango personalizado.");
        return;
      }
      if (this.dateFrom > this.dateTo) {
        alert("La fecha 'Desde' no puede ser mayor que 'Hasta'.");
        return;
      }
      this.fetchDashboardStats();
    },
    safeText(value) {
      if (value === null || value === undefined) return "";
      return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/\"/g, "&quot;")
        .replace(/'/g, "&#39;");
    },
    polarToCartesian(centerX, centerY, radius, angleInDegrees) {
      const angleInRadians = ((angleInDegrees - 90) * Math.PI) / 180.0;
      return {
        x: centerX + radius * Math.cos(angleInRadians),
        y: centerY + radius * Math.sin(angleInRadians),
      };
    },
    describeArc(x, y, radius, startAngle, endAngle) {
      const start = this.polarToCartesian(x, y, radius, endAngle);
      const end = this.polarToCartesian(x, y, radius, startAngle);
      const largeArcFlag = endAngle - startAngle <= 180 ? "0" : "1";
      return `M ${x} ${y} L ${start.x} ${start.y} A ${radius} ${radius} 0 ${largeArcFlag} 0 ${end.x} ${end.y} Z`;
    },
    buildPieSvg(slices) {
      let startAngle = 0;
      const paths = slices
        .map((slice) => {
          const angle = (slice.value / 100) * 360;
          const endAngle = startAngle + angle;
          const d = this.describeArc(50, 50, 46, startAngle, endAngle);
          startAngle = endAngle;
          return `<path class="admin-pie-slice" d="${d}" fill="${slice.color}" data-tooltip="${slice.tooltip}"></path>`;
        })
        .join("");
      return `<svg class="admin-pie-svg" viewBox="0 0 100 100" aria-label="Grafica de torta">${paths}<circle cx="50" cy="50" r="22" fill="rgba(255,255,255,.72)"></circle></svg>`;
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
        width: compact ? 500 : 560,
        height: compact ? 245 : 255,
        originX: compact ? 60 : 72,
        originY: compact ? 198 : 212,
        xLen: compact ? 370 : 415,
        yLen: compact ? 138 : 160,
        zLen: compact ? 95 : 120,
        zSkewX: 0.72,
        zSkewY: 0.45,
        barWidth: compact ? 30 : 38,
        barDepth: compact ? 20 : 26,
        slot: compact ? 64 : 74,
        startX: compact ? 20 : 24,
        scaleY: (compact ? 118 : 140) / maxVal,
      };

      const O = this.project3DPoint(0, 0, 0, cfg);
      const X = this.project3DPoint(cfg.xLen, 0, 0, cfg);
      const Y = this.project3DPoint(0, cfg.yLen, 0, cfg);
      const Z = this.project3DPoint(0, 0, cfg.zLen, cfg);
      const yTextOffset = compact ? 18 : 24;
      const tickY1 = compact ? 14 : 18;
      const tickY2 = compact ? 24 : 30;
      const tickY3 = compact ? 34 : 42;
      const showSubLabel = !!config.showSubLabel;

      const yTicks = 5;
      const yGrid = Array.from({ length: yTicks + 1 })
        .map((_, i) => {
          const yVal = (cfg.yLen / yTicks) * i;
          const p1 = this.project3DPoint(0, yVal, 0, cfg);
          const p2 = this.project3DPoint(cfg.xLen, yVal, 0, cfg);
          const val = Math.round((maxVal / yTicks) * i);
          return `
            <line class="admin-grid-line" x1="${p1.x}" y1="${p1.y}" x2="${p2.x}" y2="${p2.y}"></line>
            <text class="admin-axis-text" x="${p1.x - yTextOffset}" y="${p1.y + 3}">${val}</text>
          `;
        })
        .join("");

      const zTicks = 5;
      const zScale = cfg.zLen / zTicks;
      const zMarks = Array.from({ length: zTicks + 1 })
        .map((_, i) => {
          const p = this.project3DPoint(0, 0, zScale * i, cfg);
          return `<text class="admin-axis-text" x="${p.x - 10}" y="${p.y + 13}">${i}</text>`;
        })
        .join("");

      const bars = top5
        .map((item, idx) => {
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
          const shortLabelMax = compact ? 7 : 10;
          const shortLabel = label.length > shortLabelMax ? `${label.substring(0, shortLabelMax)}...` : label;
          const sublabel = config.subLabelKey ? this.safeText(item[config.subLabelKey] || "") : "";
          const shortSubMax = compact ? 7 : 10;
          const shortSub = sublabel.length > shortSubMax ? `${sublabel.substring(0, shortSubMax)}...` : sublabel;
          const tooltip = this.safeText(config.tooltipBuilder(item));

          const xTick = this.project3DPoint(x0 + bw / 2, 0, 0, cfg);
          const vText = this.project3DPoint(x0 + bw / 2, h + 10, d * 0.4, cfg);

          return `
            <g class="admin-3d-bar" data-tooltip="${tooltip}">
              <polygon class="admin-face-side" fill="${config.sideColor}" points="${this.pointsToString([B, C, G, F])}"></polygon>
              <polygon class="admin-face-top" fill="${config.topColor}" points="${this.pointsToString([D, C, G, H])}"></polygon>
              <polygon class="admin-face-front" fill="${config.frontColor}" points="${this.pointsToString([A, B, C, D])}"></polygon>
              <text class="admin-value-text" x="${vText.x}" y="${vText.y}" text-anchor="middle">${value}</text>
            </g>
            <text class="admin-axis-text" x="${xTick.x}" y="${cfg.originY + tickY1}" text-anchor="middle">${idx + 1}</text>
            <text class="admin-axis-text" x="${xTick.x}" y="${cfg.originY + tickY2}" text-anchor="middle">${shortLabel}</text>
            ${showSubLabel && shortSub ? `<text class="admin-axis-text" x="${xTick.x}" y="${cfg.originY + tickY3}" text-anchor="middle">${shortSub}</text>` : ""}
          `;
        })
        .join("");

      return `
        <div class="admin-chart-title">${config.title}</div>
        <div class="admin-plot3d-wrap">
          <svg class="admin-plot3d" viewBox="0 0 ${cfg.width} ${cfg.height}" preserveAspectRatio="xMidYMid meet" aria-label="${config.title}">
            ${yGrid}
            <line class="admin-axis-line" x1="${O.x}" y1="${O.y}" x2="${X.x}" y2="${X.y}"></line>
            <line class="admin-axis-line" x1="${O.x}" y1="${O.y}" x2="${Y.x}" y2="${Y.y}"></line>
            <line class="admin-axis-line" x1="${O.x}" y1="${O.y}" x2="${Z.x}" y2="${Z.y}"></line>
            <text class="admin-axis-name" x="${X.x + 8}" y="${X.y + 2}">X</text>
            <text class="admin-axis-name" x="${Y.x - 8}" y="${Y.y - 6}">Y</text>
            <text class="admin-axis-name" x="${Z.x - 12}" y="${Z.y - 8}">Z</text>
            ${zMarks}
            ${bars}
          </svg>
        </div>
      `;
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
    fetchDashboardStats() {
      axios
        .get(window.location.origin + "/admin/dashboard/stats", {
          params: this.getFilterParams(),
        })
        .then((res) => {
          if (res.data && res.data.success && res.data.data) {
            this.stats = res.data.data;
            this.$nextTick(() => this.attachTooltipHandlers());
          }
        })
        .catch((err) => {
          console.log("dashboard stats error", err);
        });
    },
    buildPrintHtml() {
      const mount = this.$el && this.$el.querySelector("#admin-stats-panels");
      if (!mount) return "";
      const styleNode = document.getElementById("dashboard-admin-static-styles");
      const logoUrl = `${window.location.origin}/assets/img/logo.png`;
      const now = new Date();
      const fecha = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}-${String(
        now.getDate()
      ).padStart(2, "0")} ${String(now.getHours()).padStart(2, "0")}:${String(now.getMinutes()).padStart(2, "0")}`;

      return `
        <!doctype html>
        <html>
          <head>
            <meta charset="utf-8" />
            <title>Reporte Dashboard WebOfertas</title>
            <style>
              body { font-family: Arial, sans-serif; margin: 6mm; color: #22384b; font-size: 12px; }
              .report-header { margin-bottom: 5px; border-bottom: 1px solid #d5e3ee; padding-bottom: 4px; display: flex; align-items: center; gap: 12px; }
              .report-logo-wrap { width: 40px; height: 40px; border-radius: 10px; background: #f4f9fd; border: 1px solid #d8e6f2; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; }
              .report-logo { width: 36px; height: 36px; object-fit: contain; }
              .report-head-text { min-width: 0; }
              .report-title { font-size: 16px; font-weight: 800; margin: 0 0 4px; }
              .report-sub { font-size: 11px; color: #48627a; margin: 0; }
              ${styleNode ? styleNode.textContent : ""}
              #admin-stats-panels { break-inside: auto; page-break-inside: auto; }
              .admin-chart-tooltip { display: none !important; }
              @media print {
                body { margin: 6mm; font-size: 12px; }
                .admin-stats-wrap { margin: 0 !important; }
                #admin-stats-panels { display: block; }

                /* Compact Grids */
                .admin-stats-grid, .admin-chart-grid, .admin-tops-grid, .admin-mini-charts-grid {
                    display: block !important;
                    gap: 4px !important;
                    margin-bottom: 4px !important;
                    margin-top: 4px !important;
                }

                /* Force 2 columns for mini charts in print to save vertical space */
                .admin-mini-charts-grid {
                    display: grid !important;
                    grid-template-columns: 1fr 1fr !important;
                }

                /* Cards */
                .admin-stat-card, .admin-pie-box, .admin-bars-box, .admin-top-card, .admin-mini-chart-card {
                    padding: 4px 8px !important;
                    margin-bottom: 4px !important;
                    border-radius: 4px !important;
                    box-shadow: none !important;
                    border: 1px solid #ccc !important;
                    break-inside: avoid;
                }

                /* Text reductions */
                .admin-stat-label { font-size: 10px !important; }
                .admin-stat-value { font-size: 16px !important; margin-top: 2px !important; }
                .admin-chart-title { font-size: 12px !important; margin-bottom: 2px !important; }
                .admin-top-title { font-size: 12px !important; margin-bottom: 2px !important; }
                .admin-top-list li { font-size: 10px !important; margin-bottom: 1px !important; line-height: 1.1 !important; }

                /* Charts */
                .admin-pie-chart { width: 80px !important; height: 80px !important; margin: 2px auto !important; }
                .admin-plot3d { height: 130px !important; }
                .admin-axis-text, .admin-value-text { font-size: 14px !important; font-weight: bold !important; }
                .admin-axis-name { font-size: 16px !important; font-weight: bold !important; }
              }
            </style>
          </head>
          <body>
            <div class="report-header">
              <div class="report-logo-wrap">
                <img class="report-logo" src="${logoUrl}" alt="Logo PlazaComidas" onerror="this.style.display='none';" />
              </div>
              <div class="report-head-text">
                <h1 class="report-title">Reporte de Graficas - Admin WebOfertas</h1>
                <p class="report-sub">Generado: ${fecha}</p>
                <p class="report-sub">Periodo: ${this.periodLabel}</p>
              </div>
            </div>
            <div id="admin-stats-panels">${mount.innerHTML}</div>
          </body>
        </html>
      `;
    },
    exportDashboardPdf() {
      const html = this.buildPrintHtml();
      if (!html) return;
      const printWindow = window.open("", "_blank");
      if (!printWindow) {
        alert("No se pudo abrir la ventana de impresion. Habilita popups para este sitio.");
        return;
      }
      printWindow.document.open();
      printWindow.document.write(html);
      printWindow.document.close();
      printWindow.focus();
      setTimeout(() => {
        printWindow.print();
      }, 350);
    },
  },
  mounted() {
    this.updateViewportMode();
    this.setPeriodRange(this.selectedPeriod);
    this._onResizeDashboard = () => {
      const previous = this.isCompact3D;
      this.updateViewportMode();
      if (previous !== this.isCompact3D) {
        this.$nextTick(() => this.attachTooltipHandlers());
      }
    };
    window.addEventListener("resize", this._onResizeDashboard);
    this.fetchDashboardStats();
    this.$nextTick(() => this.attachTooltipHandlers());
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

<style id="dashboard-admin-static-styles">
.admin-stats-wrap { margin: 10px 16px 24px; }
.admin-filters-row { margin-bottom: 4px; }
.admin-filters-actions { gap: 8px; }
.admin-stats-grid { display: grid; grid-template-columns: repeat(3, minmax(160px, 1fr)); gap: 12px; margin-bottom: 14px; }
.admin-stat-card { background: linear-gradient(180deg, #fff 0%, #f6fbff 100%); border: 1px solid #d8e6f2; border-radius: 12px; padding: 10px 12px; box-shadow: 0 8px 18px rgba(40, 76, 107, .12); }
.admin-stat-label { font-size: 12px; color: #587085; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; }
.admin-stat-value { font-size: 26px; color: #254056; font-weight: 800; line-height: 1.1; margin-top: 6px; }
.admin-chart-grid { display: grid; grid-template-columns: minmax(220px, 280px) 1fr; gap: 18px; align-items: center; }
.admin-tops-grid { display: grid; grid-template-columns: repeat(3, minmax(220px, 1fr)); gap: 12px; margin-top: 14px; }
.admin-top-card { background: linear-gradient(180deg, #fff 0%, #f8fcff 100%); border: 1px solid #d8e6f2; border-radius: 12px; padding: 10px 12px; box-shadow: 0 8px 16px rgba(42, 80, 112, .1); }
.admin-top-title { font-size: 12px; font-weight: 800; color: #35566f; margin-bottom: 7px; text-transform: uppercase; }
.admin-top-list { margin: 0; padding-left: 16px; }
.admin-top-list li { font-size: 12px; color: #4a6880; margin-bottom: 5px; line-height: 1.3; }
.admin-pie-box, .admin-bars-box { background: linear-gradient(180deg, #fff 0%, #f7fcff 100%); border: 1px solid #d8e6f2; border-radius: 14px; box-shadow: 0 10px 20px rgba(42, 80, 112, .12); }
.admin-pie-box { padding: 14px; }
.admin-bars-box { padding: 14px 16px 18px; }
.admin-chart-title { font-size: 13px; font-weight: 800; color: #2d4f67; margin-bottom: 10px; }
.admin-pie-chart { width: 170px; height: 170px; margin: 4px auto 8px; filter: drop-shadow(0 10px 18px rgba(41, 72, 95, .2)); }
.admin-pie-svg { width: 100%; height: 100%; transform: perspective(700px) rotateX(14deg); }
.admin-pie-slice { transition: transform 180ms ease, filter 180ms ease; transform-origin: 50% 50%; cursor: pointer; }
.admin-pie-slice:hover { transform: scale(1.04); filter: brightness(1.08); }
.admin-pie-legend { display: grid; gap: 5px; font-size: 12px; color: #436179; }
.admin-legend-item { display: flex; align-items: center; gap: 7px; }
.admin-dot { width: 10px; height: 10px; border-radius: 50%; }
.pie-n { background:#56b9f8; }
.pie-a { background:#ff7bb7; }
.pie-p { background:#8dd9a5; }
.admin-bar-value { font-size: 13px; font-weight: 800; color: #2f4f64; margin-top: 8px; }
.admin-mini-charts-grid { display: grid; grid-template-columns: repeat(2, minmax(280px, 1fr)); gap: 12px; margin-top: 14px; }
.admin-mini-chart-card { background: linear-gradient(180deg, #fff 0%, #f7fcff 100%); border: 1px solid #d8e6f2; border-radius: 14px; padding: 12px 12px 14px; box-shadow: 0 10px 18px rgba(42, 80, 112, .1); }
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
@media (max-width: 900px) {
  .admin-chart-grid { grid-template-columns: 1fr; }
  .admin-pie-chart { width: 150px; height: 150px; }
  .admin-tops-grid { grid-template-columns: 1fr; }
  .admin-mini-charts-grid { grid-template-columns: 1fr; }
  .admin-plot3d { height: 230px; }
}
@media (max-width: 640px) {
  .admin-stats-wrap { margin: 8px 10px 16px; }
  .admin-filters-actions { justify-content: flex-start !important; }
  .admin-stats-grid { grid-template-columns: 1fr; }
  .admin-stat-value { font-size: 22px; }
  .admin-top-list li { font-size: 11px; }
  .admin-chart-title { font-size: 12px; }
  .admin-plot3d { height: 210px; }
}
</style>
