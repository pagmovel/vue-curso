import Vue from 'vue'
import App from './App.vue'

import VueValidator from 'vue-validator'
import VueResouce from 'vue-resource'

import VueRouter from 'vue-router'
import Routes from './routes.js'

Vue.use(VueValidator)
Vue.use(VueResouce)

Vue.use(VueRouter)
const router = new VueRouter({
	linkActiveClass: 'active',
})
router.map(Routes)
router.start(App, 'App')

/*new Vue({
  el: 'body',
  components: { App }
})*/
