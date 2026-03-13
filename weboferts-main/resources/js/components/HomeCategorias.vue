<template>
  <section class="categories-icon section-padding bg-drack">
    <div class="container">
      <div class="row mb-3">
        <div class="col-12 text-center">
          <h2 class="top-cats-title">Top Categorias</h2>
          <p class="top-cats-subtitle">Categorias con mas negocios activos</p>
        </div>
      </div>
      <div class="row">
        <div v-for="cate in categorias" :key="cate.id" class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
          <a :href="'/anuncios/categoria/'+cate.url" class="wf-focusable cat-link">
            <div class="icon-box" :style="getCategoryCardStyle(cate)">
              <div class="icon text-center">
                <div class="top-cat-icon-wrap" :style="getCategoryIconStyle(cate)">
                  <v-icon x-large color="white" class="top-cat-icon">{{ getIconName(cate) }}</v-icon>
                </div>
              </div>
              <h4>{{cate.cname}}</h4>
              <span class="ncat">{{ displayCount(cate.id) }}</span>
            </div>
          </a>
        </div>

      </div>
    </div>
  </section>
</template>

<script>
import { getCategoryTheme } from '../utils/categoryTheme';
export default {
  name: "homecategorias",
  data() {
    return {
      counters: {},
    };
  },
  props: {
    categorias: Array
  },
  watch: {
    categorias: {
      immediate: true,
      handler() {
        this.startCounters();
      },
    },
  },
  methods: {
    displayCount(id) {
      return this.counters[id] || 0;
    },
    startCounters() {
      if (!Array.isArray(this.categorias)) return;
      this.categorias.forEach((cat, index) => {
        const target = Number(cat.cuantos || 0);
        this.animateCounter(cat.id, target, 700 + (index * 70));
      });
    },
    animateCounter(id, target, duration) {
      if (target <= 0) {
        this.$set(this.counters, id, 0);
        return;
      }
      const start = 0;
      const startedAt = performance.now();
      const step = (now) => {
        const elapsed = now - startedAt;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        const value = Math.round(start + (target - start) * eased);
        this.$set(this.counters, id, value);
        if (progress < 1) requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
    },
    getCategoryTheme(category) {
      return getCategoryTheme(category.icon || category.url || category.cname);
    },
    getIconName(category) {
      return this.getCategoryTheme(category).icon;
    },
    getCategoryCardStyle(category) {
      const theme = this.getCategoryTheme(category);
      return {
        background: `linear-gradient(180deg, rgba(255,255,255,0.14) 0%, rgba(255,255,255,0.05) 100%), ${theme.gradient}`,
        borderColor: theme.accent,
        boxShadow: `0 16px 32px ${theme.shadow}`,
      };
    },
    getCategoryIconStyle(category) {
      const theme = this.getCategoryTheme(category);
      return {
        background: `linear-gradient(180deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.08) 100%), ${theme.gradient}`,
        boxShadow: `0 12px 24px ${theme.shadow}`,
      };
    }
  }
};
</script>

<style lang="scss" scoped>
.categories-icon {
  position: relative;
  z-index: 2;
  margin-top: -32px;
  padding-top: 18px;
  padding-bottom: 36px;
}
.top-cats-title {
  color: #ff57a9;
  font-weight: 900;
  letter-spacing: .4px;
  text-shadow: 0 0 7px rgba(255,87,169,.5), 0 3px 10px rgba(45,12,29,.35);
}
.top-cats-subtitle {
  color: var(--wf-text-muted);
  margin-top: -2px;
}

.cat-link {
  display: block;
  text-decoration: none;
}

.icon-box {
  border: 2px solid #ffffff;
  border-radius: var(--wf-radius-md);
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.18) 0%, rgba(255, 255, 255, 0.08) 100%);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
  transition: transform var(--wf-speed-normal) ease, box-shadow var(--wf-speed-normal) ease, border-color var(--wf-speed-normal) ease;
  padding: 15px;
  text-align: center;
}

.top-cat-icon-wrap {
  width: 78px;
  height: 78px;
  margin: 0 auto 10px;
  border-radius: 24px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,0.35);
}

.icon-box:hover {
  transform: scale(1.045);
  border-color: #ffffff;
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
}

/* Efecto de crecimiento en el icono al pasar el mouse sobre la tarjeta */
.icon-box:hover .top-cat-icon {
  transform: scale(1.2);
}

.cat-link:focus-visible .icon-box {
  outline: 3px solid rgba(255, 102, 219, 0.45);
  outline-offset: 3px;
  transform: translateY(-2px);
}
.top-cat-icon {
  filter: drop-shadow(0 0 9px rgba(255, 255, 255, .38));
  transition: transform 0.3s ease; /* Transición suave para el icono */
}

.icon-box h4,
.icon-box .ncat {
  text-shadow: 0 2px 10px rgba(12, 4, 10, 0.45);
}

.icon-box h4 {
  color: #ffffff;
  font-weight: 800;
}

.icon-box .ncat {
  display: inline-block;
  margin-top: 6px;
  font-size: 18px;
  font-weight: 800;
  color: #fff;
  padding: 2px 10px;
  border-radius: 999px;
  background: rgba(255,255,255,0.14);
}

@media (max-width: 767px) {
  .categories-icon {
    margin-top: -14px;
    padding-top: 10px;
    padding-bottom: 24px;
  }
  .icon-box:hover {
    transform: scale(1.02);
  }
}
</style>
