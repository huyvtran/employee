import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex)

import alertStore from './modules/alert'
import authStore from './modules/auth'
import cityStore from './modules/city'
import districtStore from './modules/district'
import typeStore from './modules/type'
import storeStore from './modules/store'
import userStore from './modules/user'
import activityStore from './modules/activity'
import catalogueStore from './modules/catalogue'
import toppingStore from './modules/topping'
import sizeStore from './modules/size'
import productStore from './modules/product'
import orderStatusStore from './modules/orderStatus'
import notificationStore from './modules/notification.js'
import couponStatusStore from './modules/couponStatus'
import couponStore from './modules/coupon.js'
import dialogStore from './modules/dialog'

const debug = process.env.NODE_ENV !== 'production';
Vue.config.debug = debug;

export default new Vuex.Store({
	modules: {
		alertStore,
		authStore,
		cityStore,
		districtStore,
		typeStore,
		storeStore,
		userStore,
		activityStore,
		catalogueStore,
		toppingStore,
		sizeStore,
		productStore,
		orderStatusStore,
		notificationStore,
		couponStatusStore,
		couponStore,
		dialogStore
	},
	strict: debug
});