import Vue from 'vue'
import Router from 'vue-router'
import Vuex from 'vuex'

import store from '@/store/store'
import Index from '@/components/index'
import Login from '@/components/credentials/login'
import Dashboard from '@/components/dashboard/index'
import Order from '@/components/orders/index'
import OrderDetail from '@/components/orders/details/index'
import Store from '@/components/stores/index'
import StoreDetail from '@/components/stores/storeDetails/index'
import Coupon from '@/components/coupons/index'
import CouponDetail from '@/components/coupons/details/index'
import User from '@/components/users/index'
import Customer from '@/components/users/customers/index'
import CustomerDetail from '@/components/users/customers/details/index'
import Shipper from '@/components/users/shippers/index'
import About from '@/components/stores/storeDetails/about/index'
import Menu from '@/components/stores/storeDetails/menu/index'
import General from '@/components/stores/storeDetails/menu/general/index'
import Catalogue from '@/components/stores/storeDetails/menu/catalogue/index'
import Size from '@/components/stores/storeDetails/menu/size/index'
import Topping from '@/components/stores/storeDetails/menu/topping/index'
import Product from '@/components/stores/storeDetails/menu/product/index'
import Service from '@/components/services/index'
import Delivery from '@/components/services/deliveries/index'
import Notification from '@/components/notification/index'

import auth from '@/utils/auth.js'


Vue.use(Router)
Vue.use(Vuex)
Vue.use(auth)

const router = new Router({
	mode: 'history',
	routes: [
	{ 	
		path: '/', 
		component:Index,
		redirect: {
			name: 'Dashboard'
		},
		children: [
		{ path: '/dashboard', component: Dashboard, name: 'Dashboard', meta:{auth:true}},
		{ path: '/orders', component: Order, name: 'Order', meta:{auth:true}},
		{ path: '/orders/:orderId/details', component: OrderDetail, name: 'OrderDetail', meta: {auth:true}},
		{ path: '/stores', component: Store, name: 'Store', meta:{auth:true}},
		{
			path: '/stores/store-details/:storeId',
			component: StoreDetail,
			redirect: {name: 'About'},
			children: [
			{ path: 'about', component: About, name: 'About', meta: {auth:true}},
			{ path: 'menu', component: Menu, name: 'Menu', redirect: {name: 'General'},children: [
			{ path: 'general', component: General, name: 'General'},
			{ path: 'catalogues', component: Catalogue, name: 'Catalogue'},
			{ path: 'sizes', component: Size, name: 'Size'},
			{ path: 'toppings', component: Topping, name: 'Topping'},
			{ path: 'products', component: Product, name: 'Product'},
			], meta: {auth:true}},

			]
		},
		{
			path: '/coupons',
			component: Coupon,
			name: 'Coupon',
			meta: {auth:true},
		},
		{
			path: '/coupons/details/:couponId',
			component: CouponDetail,
			name: 'CouponDetail',
			meta: {auth: true}
		},
		{ 
			path: '/users', 
			component: User, 
			name: 'User', 
			meta: {auth:true},
			children: [
				{ path: 'customers', component: Customer, name: 'Customer'},
				{ path: 'shippers', component: Shipper, name: 'Shipper'}
			]

		},
		{
			path: '/users/customers/:userId/details',
			component: CustomerDetail,
			name: 'CustomerDetail',
			meta: {auth:true}
		},
		{ path: '/notifications', component: Notification, name: 'Notification', meta: {auth:true}},
		{ path: '/services', component: Service, name: 'Service', meta:{auth:true}},
		{ path: '/deliveries', component: Delivery, name: 'Delivery', meta:{auth:true}},
		]
	},
	{ path: '/login', component: Login, name: 'Login'},
	]
})

router.beforeEach(function(to, from, next) {
	if(to.meta.auth) {
		const authUser = JSON.parse(window.localStorage.getItem('jwt_emp'))
		if(authUser && authUser.access_token) {
			next()
		} else {
			next({name: 'Login'})
		}
	}
	next() 
})

export default router