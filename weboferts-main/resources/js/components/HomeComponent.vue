<template>
  <div class="home-enhanced" :style="homeStyle">
    <div id="hero-area" class="hero-parallax" :style="heroStyle">
      <div class="overlay hero-overlay"></div>
      <div class="hero-glow hero-glow-a"></div>
      <div class="hero-glow hero-glow-b"></div>
      <div class="container">
        <div class="row">
          <div class="col-md-12 col-lg-12 col-xs-12 text-center">
            <div class="contents hero-contents">
              <span class="hero-badge">Descubre sabores unicos</span>
              <h1 class="head-title neon-title">
                Bienvenido a <span class="year">Plaza Comidas</span>
              </h1>
              <p class="hero-subtitle">
                Come todo lo que puedas encontrar, <br />
                ó Busca donde degustar la mejor comida de nuestro pais
              </p>
              <buscar :categorias="categorias"></buscar>
            </div>
          </div>
        </div>
      </div>
    </div>

    <homecategorias :categorias="categorias"></homecategorias>
    <homerecomendados></homerecomendados>
    <homedestacados></homedestacados>

    <section class="services section-padding">
      <div class="container">
        <div class="row">
          <div class="col-md-6 col-lg-4 col-xs-12">
            <div class="services-item wow fadeInRight" data-wow-delay="0.2s">
              <div class="icon">
                <i class="lni-book"></i>
              </div>
              <div class="services-content">
                <h3><a href="#">Fully Documented</a></h3>
                <p>
                  Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quo
                  aut magni perferendis repellat rerum assumenda facere.
                </p>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-4 col-xs-12">
            <div class="services-item wow fadeInRight" data-wow-delay="0.4s">
              <div class="icon">
                <i class="lni-leaf"></i>
              </div>
              <div class="services-content">
                <h3><a href="#">Clean & Modern Design</a></h3>
                <p>
                  Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quo
                  aut magni perferendis repellat rerum assumenda facere.
                </p>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-4 col-xs-12">
            <div class="services-item wow fadeInRight" data-wow-delay="0.6s">
              <div class="icon">
                <i class="lni-map"></i>
              </div>
              <div class="services-content">
                <h3><a href="#">Great Features</a></h3>
                <p>
                  Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quo
                  aut magni perferendis repellat rerum assumenda facere.
                </p>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-4 col-xs-12">
            <div class="services-item wow fadeInRight" data-wow-delay="0.8s">
              <div class="icon">
                <i class="lni-cog"></i>
              </div>
              <div class="services-content">
                <h3><a href="#">Completely Customizable</a></h3>
                <p>
                  Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quo
                  aut magni perferendis repellat rerum assumenda facere.
                </p>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-4 col-xs-12">
            <div class="services-item wow fadeInRight" data-wow-delay="1s">
              <div class="icon">
                <i class="lni-pointer-up"></i>
              </div>
              <div class="services-content">
                <h3><a href="#">User Friendly</a></h3>
                <p>
                  Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quo
                  aut magni perferendis repellat rerum assumenda facere.
                </p>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-4 col-xs-12">
            <div class="services-item wow fadeInRight" data-wow-delay="1.2s">
              <div class="icon">
                <i class="lni-layout"></i>
              </div>
              <div class="services-content">
                <h3><a href="#">Awesome Layout</a></h3>
                <p>
                  Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quo
                  aut magni perferendis repellat rerum assumenda facere.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="counter-section section-padding">
      <div class="container">
        <div class="row">
          <div class="col-md-3 col-sm-6 work-counter-widget text-center">
            <div class="counter">
              <div class="icon"><i class="lni-layers"></i></div>
              <h2 class="counterUp">12090</h2>
              <p>Regular Ads</p>
            </div>
          </div>

          <div class="col-md-3 col-sm-6 work-counter-widget text-center">
            <div class="counter">
              <div class="icon"><i class="lni-map"></i></div>
              <h2 class="counterUp">350</h2>
              <p>Locations</p>
            </div>
          </div>

          <div class="col-md-3 col-sm-6 work-counter-widget text-center">
            <div class="counter">
              <div class="icon"><i class="lni-user"></i></div>
              <h2 class="counterUp">23453</h2>
              <p>Reguler Members</p>
            </div>
          </div>

          <div class="col-md-3 col-sm-6 work-counter-widget text-center">
            <div class="counter">
              <div class="icon"><i class="lni-briefcase"></i></div>
              <h2 class="counterUp">250</h2>
              <p>Premium Ads</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="pricing-table" class="section-padding">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="mainHeading">
              <h2 class="section-title neon-title">Select A Package</h2>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 col-xs-12">
            <div class="table">
              <div class="icon">
                <i class="lni-gift"></i>
              </div>
              <div class="title">
                <h3>SILVER</h3>
              </div>
              <div class="pricing-header">
                <p class="price-value"><sup>$</sup>29<span>/ Mo</span></p>
              </div>
              <ul class="description">
                <li><strong>Free</strong> ad posting</li>
                <li><strong>No</strong> Featured ads availability</li>
                <li><strong>For 30</strong> days</li>
                <li><strong>100%</strong> Secure!</li>
              </ul>
              <button class="btn btn-common">Buy Now</button>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 col-xs-12">
            <div class="table" id="active-tb">
              <div class="icon">
                <i class="lni-leaf"></i>
              </div>
              <div class="title">
                <h3>STANDARD</h3>
              </div>
              <div class="pricing-header">
                <p class="price-value"><sup>$</sup>89<span>/ Mo</span></p>
              </div>
              <ul class="description">
                <li><strong>Free</strong> ad posting</li>
                <li><strong>6</strong> Featured ads availability</li>
                <li><strong>For 30</strong> days</li>
                <li><strong>100%</strong> Secure!</li>
              </ul>
              <button class="btn btn-common">Buy Now</button>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 col-xs-12">
            <div class="table">
              <div class="icon">
                <i class="lni-layers"></i>
              </div>
              <div class="title">
                <h3>PLANINIUM</h3>
              </div>
              <div class="pricing-header">
                <p class="price-value"><sup>$</sup>99<span>/ Mo</span></p>
              </div>
              <ul class="description">
                <li><strong>Free</strong> ad posting</li>
                <li><strong>20</strong> Featured ads availability</li>
                <li><strong>For 25</strong> days</li>
                <li><strong>100%</strong> Secure!</li>
              </ul>
              <button class="btn btn-common">Buy Now</button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="testimonial section-padding">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div id="testimonials" class="owl-carousel">
              <div class="item">
                <div class="testimonial-item">
                  <div class="img-thumb">
                    <img src="assets/img/testimonial/img1.png" alt="" />
                  </div>
                  <div class="content">
                    <h2><a href="#">John Doe</a></h2>
                    <p class="description">
                      Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                      Explicabo quidem, excepturi facere magnam illum, at
                      accusantium doloremque odio.
                    </p>
                    <h3>Developer at of <a href="#">xyz company</a></h3>
                  </div>
                </div>
              </div>
              <div class="item">
                <div class="testimonial-item">
                  <div class="img-thumb">
                    <img src="assets/img/testimonial/img2.png" alt="" />
                  </div>
                  <div class="content">
                    <h2><a href="#">Jessica</a></h2>
                    <p class="description">
                      Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                      Explicabo quidem, excepturi facere magnam illum, at
                      accusantium doloremque odio.
                    </p>
                    <h3>Developer at of <a href="#">xyz company</a></h3>
                  </div>
                </div>
              </div>
              <div class="item">
                <div class="testimonial-item">
                  <div class="img-thumb">
                    <img src="assets/img/testimonial/img3.png" alt="" />
                  </div>
                  <div class="content">
                    <h2><a href="#">Johnny Zeigler</a></h2>
                    <p class="description">
                      Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                      Explicabo quidem, excepturi facere magnam illum, at
                      accusantium doloremque odio.
                    </p>
                    <h3>Developer at of <a href="#">xyz company</a></h3>
                  </div>
                </div>
              </div>
              <div class="item">
                <div class="testimonial-item">
                  <div class="img-thumb">
                    <img src="assets/img/testimonial/img1.png" alt="" />
                  </div>
                  <div class="content">
                    <h2><a href="#">John Doe</a></h2>
                    <p class="description">
                      Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                      Explicabo quidem, excepturi facere magnam illum, at
                      accusantium doloremque odio.
                    </p>
                    <h3>Developer at of <a href="#">xyz company</a></h3>
                  </div>
                </div>
              </div>
              <div class="item">
                <div class="testimonial-item">
                  <div class="img-thumb">
                    <img src="assets/img/testimonial/img2.png" alt="" />
                  </div>
                  <div class="content">
                    <h2><a href="#">Jessica</a></h2>
                    <p class="description">
                      Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                      Explicabo quidem, excepturi facere magnam illum, at
                      accusantium doloremque odio.
                    </p>
                    <h3>Developer at of <a href="#">xyz company</a></h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="subscribes section-padding">
      <div class="container">
        <div class="row wrapper-sub">
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <p>
              Join our 10,000+ subscribers and get access to the latest
              templates, freebies, announcements and resources!
            </p>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <form>
              <div class="subscribe">
                <input
                  class="form-control"
                  name="EMAIL"
                  placeholder="Your email here"
                  required=""
                  type="email"
                />
                <button class="btn btn-common" type="submit">Subscribe</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

    <section class="cta section-padding">
      <div class="container">
        <div class="row">
          <div class="col-xs-12 col-md-6 col-lg-4">
            <div class="single-cta">
              <div class="cta-icon">
                <i class="lni-grid"></i>
              </div>
              <h4>Refreshing Design</h4>
              <p>
                Duis autem vel eum iriure dolor in hendrerit in vulputate velit
                esse molestie
              </p>
            </div>
          </div>
          <div class="col-xs-12 col-md-6 col-lg-4">
            <div class="single-cta">
              <div class="cta-icon">
                <i class="lni-brush"></i>
              </div>
              <h4>Easy to Customize</h4>
              <p>
                Duis autem vel eum iriure dolor in hendrerit in vulputate velit
                esse molestie
              </p>
            </div>
          </div>
          <div class="col-xs-12 col-md-6 col-lg-4">
            <div class="single-cta">
              <div class="cta-icon">
                <i class="lni-headphone-alt"></i>
              </div>
              <h4>24/7 Support</h4>
              <p>
                Duis autem vel eum iriure dolor in hendrerit in vulputate velit
                esse molestie
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="cta-final section-padding">
      <div class="container">
        <div class="cta-final-box text-center">
          <h2 class="cta-final-title">Haz que tu negocio destaque hoy</h2>
          <p class="cta-final-subtitle">
            Publica tus anuncios y conecta con miles de clientes hambrientos en minutos.
          </p>
          <div class="cta-final-actions">
            <a :href="registroUrl" class="btn btn-common cta-pulse-btn">Publicar Anuncio</a>
            <a :href="anunciosUrl" class="btn btn-border cta-secondary-btn">Explorar Ofertas</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Componente del carrito agregado -->
    <cart-component></cart-component>
  </div>
</template>

<script>
export default {
  name: "Home",
  data() {
      return {
          categorias: [],
          parallaxOffset: 0,
          colorShift: 0,
          prefersReducedMotion: false,
          isMobileView: false,
          rafPending: false,
      }
  },
  computed: {
    homeStyle() {
      const h1 = (330 + this.colorShift) % 360;
      const h2 = (18 + Math.floor(this.colorShift * 0.6)) % 360;
      const h3 = (345 + Math.floor(this.colorShift * 0.4)) % 360;
      return {
        "--scroll-shift": `${this.parallaxOffset}px`,
        background: `
          radial-gradient(circle at 12% 14%, hsla(${h1}, 100%, 78%, .30) 0, hsla(${h1}, 100%, 78%, 0) 44%),
          radial-gradient(circle at 86% 10%, hsla(${h2}, 100%, 76%, .32) 0, hsla(${h2}, 100%, 76%, 0) 46%),
          radial-gradient(circle at 70% 80%, hsla(${h3}, 100%, 75%, .25) 0, hsla(${h3}, 100%, 75%, 0) 44%),
          linear-gradient(180deg, #fff9fc 0%, #fff4f8 36%, #fff9f1 100%)
        `,
      };
    },
    heroStyle() {
      return {
        transform: `translate3d(0, ${this.parallaxOffset}px, 0)`,
      };
    },
    registroUrl() {
      return window.location.origin + "/registro";
    },
    anunciosUrl() {
      return window.location.origin + "/anuncios";
    },
  },
  methods: {
    onScrollParallax() {
      if (this.prefersReducedMotion || this.isMobileView) {
        this.parallaxOffset = 0;
        this.colorShift = 0;
        return;
      }
      if (this.rafPending) return;
      this.rafPending = true;
      requestAnimationFrame(() => {
        const y = window.scrollY || window.pageYOffset || 0;
        this.parallaxOffset = Math.min(y * 0.12, 36);
        this.colorShift = Math.min(Math.floor(y * 0.08), 38);
        this.rafPending = false;
      });
    },
    setMotionContext() {
      this.prefersReducedMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
      this.isMobileView = window.innerWidth < 768;
    },
  },
  mounted() {
    this.setMotionContext();
    window.addEventListener("resize", this.setMotionContext, { passive: true });
    window.addEventListener("scroll", this.onScrollParallax, { passive: true });
    this.onScrollParallax();
  },
  beforeDestroy() {
    window.removeEventListener("scroll", this.onScrollParallax);
    window.removeEventListener("resize", this.setMotionContext);
  },
  created () {
    axios.get( window.location.origin+"/home/categorias").then((res) => {
      this.categorias = res.data;
    });
  },
};
</script>

<style lang="scss" scoped>
.home-enhanced {
  min-height: 100vh;
  transition: background var(--wf-speed-normal) linear;
  color: var(--wf-text-main);
}

#hero-area.hero-parallax {
  position: relative;
  overflow: hidden;
  transition: transform var(--wf-speed-fast) ease-out;
  min-height: 56vh;
  padding: 88px 0 54px !important;
}

.hero-overlay {
  background: linear-gradient(105deg, rgba(255, 58, 120, 0.45) 0%, rgba(255, 120, 75, 0.34) 65%, rgba(255, 177, 113, 0.3) 100%);
}

.hero-glow {
  position: absolute;
  border-radius: 999px;
  filter: blur(2px);
  pointer-events: none;
}

.hero-glow-a {
  width: 360px;
  height: 360px;
  top: -110px;
  left: -120px;
  background: radial-gradient(circle, rgba(255, 207, 226, 0.5) 0%, rgba(255, 207, 226, 0) 66%);
  animation: floatOrb 8s ease-in-out infinite;
}

.hero-glow-b {
  width: 320px;
  height: 320px;
  right: -90px;
  bottom: -100px;
  background: radial-gradient(circle, rgba(255, 210, 170, 0.45) 0%, rgba(255, 210, 170, 0) 68%);
  animation: floatOrb 10s ease-in-out infinite reverse;
}

.hero-contents {
  position: relative;
  z-index: 2;
  animation: fadeUp 800ms ease-out both;
  padding: 76px 0 36px !important;
}

.hero-badge {
  display: inline-block;
  margin-bottom: 12px;
  padding: 7px 14px;
  border-radius: 999px;
  background: linear-gradient(90deg, rgba(255, 126, 169, 0.28) 0%, rgba(255, 177, 118, 0.24) 100%);
  border: 1px solid rgba(255, 255, 255, 0.45);
  color: #fff6fb;
  font-size: 12px;
  letter-spacing: 0.6px;
  text-transform: uppercase;
  box-shadow: 0 8px 18px rgba(102, 32, 68, 0.24);
  text-shadow: 0 1px 4px rgba(30, 8, 20, 0.3);
}

.head-title {
  text-shadow: 0 8px 20px rgba(38, 11, 26, 0.3), 0 2px 8px rgba(38, 11, 26, 0.22);
  animation: pulseShadow 3.4s ease-in-out infinite;
}

.neon-title {
  color: #ff4fa1 !important;
  text-shadow:
    0 0 6px rgba(255, 77, 161, 0.85),
    0 0 12px rgba(255, 77, 161, 0.65),
    0 3px 10px rgba(60, 8, 33, 0.45) !important;
}

.hero-subtitle {
  text-shadow: 0 4px 12px rgba(35, 10, 22, 0.26);
  font-size: 18px;
  line-height: 1.65;
  animation: fadeUp 1100ms ease-out both;
}

.services,
.counter-section,
#pricing-table,
.testimonial,
.subscribes,
.cta {
  position: relative;
}

.services::before,
.counter-section::before,
#pricing-table::before,
.testimonial::before,
.subscribes::before,
.cta::before {
  content: "";
  position: absolute;
  inset: 0;
  pointer-events: none;
  transition: transform 160ms ease-out;
}

.services::before {
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.35) 0%, rgba(255, 244, 250, 0.35) 100%);
  transform: translate3d(0, calc(var(--scroll-shift, 0px) * -0.35), 0);
}

.counter-section::before {
  background: linear-gradient(180deg, rgba(255, 224, 236, 0.26) 0%, rgba(255, 244, 228, 0.26) 100%);
  transform: translate3d(0, calc(var(--scroll-shift, 0px) * -0.22), 0);
}

#pricing-table::before {
  background: linear-gradient(180deg, rgba(255, 246, 252, 0.28) 0%, rgba(255, 250, 242, 0.34) 100%);
  transform: translate3d(0, calc(var(--scroll-shift, 0px) * -0.28), 0);
}

.testimonial::before {
  background: linear-gradient(180deg, rgba(255, 241, 248, 0.24) 0%, rgba(255, 247, 237, 0.24) 100%);
  transform: translate3d(0, calc(var(--scroll-shift, 0px) * -0.18), 0);
}

.subscribes::before {
  background: linear-gradient(180deg, rgba(255, 236, 245, 0.22) 0%, rgba(255, 245, 233, 0.22) 100%);
  transform: translate3d(0, calc(var(--scroll-shift, 0px) * -0.14), 0);
}

.cta::before {
  background: linear-gradient(180deg, rgba(255, 239, 246, 0.22) 0%, rgba(255, 244, 235, 0.22) 100%);
  transform: translate3d(0, calc(var(--scroll-shift, 0px) * -0.16), 0);
}

.services-item,
.table,
.testimonial-item,
.single-cta {
  border-radius: var(--wf-radius-md);
  background: rgba(255, 255, 255, 0.86);
  border: 1px solid var(--wf-border-soft);
  box-shadow: var(--wf-shadow-soft);
  transition: transform var(--wf-speed-normal) ease, box-shadow var(--wf-speed-normal) ease, border-color var(--wf-speed-normal) ease;
}

.services-item:hover,
.table:hover,
.testimonial-item:hover,
.single-cta:hover {
  transform: translateY(-6px);
  box-shadow: var(--wf-shadow-pop);
  border-color: var(--wf-border-strong);
}

.section-title,
.mainHeading h2,
.single-cta h4,
.services-content h3 a {
  text-shadow: 0 2px 8px rgba(112, 30, 62, 0.18), 0 0 7px rgba(255, 92, 168, 0.32);
}

.work-counter-widget .counter {
  border-radius: var(--wf-radius-md);
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.78) 0%, rgba(255, 238, 246, 0.66) 100%);
  border: 1px solid rgba(255, 182, 207, 0.28);
  box-shadow: 0 10px 22px rgba(135, 46, 84, 0.12);
  transition: transform var(--wf-speed-normal) ease;
}

.work-counter-widget .counter:hover {
  transform: translateY(-4px);
}

.subscribes .wrapper-sub,
.cta .row {
  position: relative;
  z-index: 1;
}
.cta-final {
  position: relative;
}
.cta-final-box {
  border-radius: 20px;
  padding: 34px 20px;
  background: linear-gradient(105deg, rgba(255, 116, 170, 0.24) 0%, rgba(255, 172, 114, 0.24) 100%);
  border: 1px solid rgba(255, 149, 197, 0.45);
  box-shadow: 0 18px 34px rgba(92, 30, 66, 0.18);
}
.cta-final-title {
  color: #ff4a9f;
  font-weight: 900;
  text-shadow: 0 0 9px rgba(255, 74, 159, 0.4), 0 4px 10px rgba(46, 11, 30, 0.3);
}
.cta-final-subtitle {
  color: var(--wf-text-muted);
  max-width: 760px;
  margin: 6px auto 18px;
}
.cta-final-actions {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 10px;
}
.cta-pulse-btn {
  border-radius: 999px;
  padding: 10px 22px;
  animation: ctaPulse 1.8s ease-in-out infinite;
  box-shadow: 0 0 0 0 rgba(255, 79, 158, 0.6);
}
.cta-secondary-btn {
  border-radius: 999px;
  padding: 10px 22px;
  border-color: #ff7fbe;
  color: #b13f7e;
}
.cta-secondary-btn:hover {
  border-color: #ff6cb4;
  color: #9f2f6f;
  background: rgba(255,255,255,.7);
}

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translate3d(0, 18px, 0);
  }
  to {
    opacity: 1;
    transform: translate3d(0, 0, 0);
  }
}

@keyframes floatOrb {
  0%,
  100% {
    transform: translate3d(0, 0, 0);
  }
  50% {
    transform: translate3d(0, 12px, 0);
  }
}

@keyframes pulseShadow {
  0%,
  100% {
    text-shadow: 0 8px 20px rgba(38, 11, 26, 0.3), 0 2px 8px rgba(38, 11, 26, 0.22);
  }
  50% {
    text-shadow: 0 11px 30px rgba(38, 11, 26, 0.38), 0 3px 10px rgba(38, 11, 26, 0.24);
  }
}
@keyframes ctaPulse {
  0% {
    box-shadow: 0 0 0 0 rgba(255, 79, 158, 0.55);
    transform: translateY(0);
  }
  70% {
    box-shadow: 0 0 0 14px rgba(255, 79, 158, 0);
    transform: translateY(-1px);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(255, 79, 158, 0);
    transform: translateY(0);
  }
}

@media (max-width: 991px) {
  #hero-area.hero-parallax {
    min-height: 48vh;
    padding: 72px 0 42px !important;
  }
  .hero-contents {
    padding: 62px 0 22px !important;
  }
  .hero-subtitle {
    font-size: 16px;
  }
  .hero-glow-a,
  .hero-glow-b {
    opacity: 0.65;
  }
}

@media (max-width: 767px) {
  #hero-area.hero-parallax {
    min-height: 42vh;
    padding: 64px 0 30px !important;
  }
  .hero-contents {
    padding: 52px 0 14px !important;
  }
  .hero-badge {
    font-size: 11px;
    padding: 6px 12px;
  }
  .head-title {
    font-size: 34px;
    line-height: 1.2;
  }
  .hero-subtitle {
    font-size: 15px;
    line-height: 1.55;
  }
}

@media (max-width: 767px), (prefers-reduced-motion: reduce) {
  #hero-area.hero-parallax,
  .services::before,
  .counter-section::before,
  #pricing-table::before,
  .testimonial::before,
  .subscribes::before,
  .cta::before {
    transform: none !important;
  }
  .hero-glow-a,
  .hero-glow-b,
  .cta-pulse-btn,
  .head-title {
    animation: none !important;
  }
}
</style>
