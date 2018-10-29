import axios from 'axios'

const state = {
	coupons    : [],
	loading    : false,
	editedIndex: -1,
	editedItem : null
}

const mutations = {
	DEFAULT_COUPON() {
		state.editedIndex = -1
		state.editedItem  = null
	},
	FETCH_COUPON(state, payload) {
		state.coupons = payload.coupons
	},
	LOADING_COUPON(state) {
		state.loading = !state.loading
	},
	EDIT_COUPON(state, payload) {
		state.editedIndex = state.coupons.indexOf(payload)
		state.editedItem  = Object.assign({}, payload)
	},
	UPDATE_COUPON(state, payload) {
		if(state.editedIndex > -1) {
			Object.assign(state.coupons[state.editedIndex], payload)
		} else {
			state.coupons.push((payload))
		}
	},
	REMOVE_COUPON(state, payload) {
		var coupons = state.coupons
		coupons.splice(coupons.indexOf(payload), 1)
	},
}

const actions = {
	fetchCoupon: ({commit}, payload) => new Promise((resolve, reject) => {
		commit('LOADING_COUPON')
		axios.get('/api/Coupon/GetCoupon').then(response => {
			if(response.status === 200) {
				commit('FETCH_COUPON', response.data)
			}
			resolve(response)
		}).catch(errors => {
			reject(errors)
		}).finally(() => {
			commit('LOADING_COUPON')
		})
	}),
	editCoupon: ({commit}, payload) => new Promise((resolve, reject) => {
		commit('EDIT_COUPON', payload)
		commit('DIALOG_OPEN')
	}),
	updateCoupon: ({commit}, payload) => new Promise((resolve, reject) => {
		commit('UPDATE_COUPON', payload.coupon)
	}),
	defaultCoupon: ({commit}) => new Promise((resolve, reject) => {
		commit('DEFAULT_COUPON')
		commit('DIALOG_CLOSE')
	}),
}

const getters = {
	
}

export default {
	state, mutations, actions, getters
}