import axios from 'axios'

const state = {
	status     : [],
	loading    : false,
	dialog     : false,
	editedIndex: -1,
	editedItem : {},
	alert      : {
		alert: false,
		messages: '',
		type: 'success'
	}
}

const mutations = {
	FETCH_COUPON_STATUS(state, payload) {
		state.status = payload.status
	},
	LOADING_COUPON_STATUS(state) {
		state.loading = !state.loading
	},
	DIALOG_COUPON_STATUS(state) {
		state.dialog = true
	},
	DIALOG_COUPON_CLOSE(state) {
		state.dialog      = false
		state.editedIndex = -1
	},
	EDIT_COUPON_STATUS(state, payload) {
		state.editedIndex = state.coupons.indexOf(payload)
		state.editedItem  = Object.assign({}, payload)
		state.dialog      = true
	},
	UPDATE_COUPON_STATUS(state, payload) {
		if(state.editedIndex > -1) {
			Object.assign(state.status[state.editedIndex], payload)
		} else {
			state.status.push((payload))
		}
	},
	REMOVE_COUPON_STATUS(state, payload) {
		var status = state.status
		status.splice(status.indexOf(payload), 1)
	},
	ALERT_COUPON_STATUS(state, payload) {
		state.alert = Object.assign(state.alert, payload)
		setTimeout(() => {
			state.alert = {
				alert: false,
				messages: '',
				type: 'success'
			}
		}, 5000)
	}
}

const actions = {
	fetchCouponStatus: ({commit}, payload) => new Promise((resolve, reject) => {
		commit('LOADING_COUPON_STATUS')
		axios.get('/api/Coupon/GetCouponStatus').then(response => {
			if(response.status === 200) {
				commit('FETCH_COUPON_STATUS', response.data)
			}
			resolve(response)
		}).catch(errors => {
			reject(errors)
		}).finally(() => {
			commit('LOADING_COUPON_STATUS')
		})
	}),
}

const getters = {
	
}

export default {
	state, mutations, actions, getters
}