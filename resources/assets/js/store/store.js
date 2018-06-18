import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex)
Vue.config.debug = true;

import alertStore from './modules/alert'
import authStore from './modules/auth'
import cityStore from './modules/city'
import districtStore from './modules/district'
import typeStore from './modules/type'
import storeStore from './modules/store'
import activityStore from './modules/activity'
import catalogueStore from './modules/catalogue'
import toppingStore from './modules/topping'
import sizeStore from './modules/size'
import productStore from './modules/product'

const debug = process.env.NODE_ENV !== 'production';

export default new Vuex.Store({
	modules: {
		alertStore,
		authStore,
		cityStore,
		districtStore,
		typeStore,
		storeStore,
		activityStore,
		catalogueStore,
		toppingStore,
		sizeStore,
		productStore,
	},
	strict: debug
});