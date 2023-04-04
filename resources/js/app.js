import Vue from 'vue'
import store from '~/store'
import router from '~/router'
import i18n from '~/plugins/i18n'
import App from '~/components/App'

import '~/plugins'
import '~/components'

// import plugin
import VueToastr from 'vue-toastr'
// use plugin
Vue.use(VueToastr, {
  /* OverWrite Plugin Options if you need */
})

Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
  i18n,
  store,
  router,
  ...App
})
