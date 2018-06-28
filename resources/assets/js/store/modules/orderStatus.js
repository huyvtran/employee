import axios from 'axios'

const state = {
	orderStatus: {},
	loading: false,
	dialog: false,
	editedIndex: -1,
	editedItem: {},
	alert: {
		show: false,
		message: '',
		type: 'success'
	}
}

const mutations = {
	FETCH_ORDER_STATUS(state, payload) {
		state.orderStatus = payload.data
	},
	LOADING_ORDER_STATUS(state) {
		state.loading = !state.loading
	},
	DIALOG_ORDER_STATUS(state) {
		state.dialog = !state.dialog
	},
	DIALOG_ORDER_STATUS_CLOSE(state) {
		state.dialog = false
		state.editedIndex = -1
	},
	EDIT_ORDER_STATUS(state, payload) {
		state.editedIndex = state.orderStatus.indexOf(payload)
		state.editedItem = Object.assign({}, payload)
		state.dialog = true
	},
	UPDATE_ORDER_STATUS(state, payload) {
		if(state.editedIndex > -1) {
			Object.assign(state.orderStatus[state.editedIndex], payload.data)
		} else {
			state.orderStatus.push((payload.data))
		}
	},
	REMOVE_ORDER_STATUS(state, payload) {
		var orderStatus = state.orderStatus
		orderStatus.splice(orderStatus.indexOf(payload), 1)
	},
	ALERT_ORDER_STATUS(state, payload) {
		state.alert = Object.assign(state.alert, payload)
	},
	ALERT_ORDER_STATUS_CLOSE(state, payload) {
		state.alert = {
			show: false,
			message: '',
			type: 'success'
		}
	}

}

const actions = {
	fetchOrderStatus: ({commit}, payload) => new Promise((resolve, reject) => {
		axios.get('/api/GetOrderStatus').then(response => {
			if(response.status === 200) {
				commit('FETCH_ORDER_STATUS', response.data)
			}
			resolve(response)
		}).catch(errors => {
			reject(errors)
		})
	})
}

const getters = {

}

export default {
	state, mutations, actions, getters
}