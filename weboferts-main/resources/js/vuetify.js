import Vue from 'vue'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
Vue.use(Vuetify)

export default new Vuetify({
    theme: {
        themes: {
            light: {
                primary: '#8080ff',
                secondary: '#696969',
                success: '#1affa3',
                accent: '#8c9eff',
                error: '#b71c1c',
                danger: '#ff1a75',
            },
        },
        icons: {
            iconfont: ['mdi'],
        },
    },
})