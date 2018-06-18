import axios from 'axios'

const state = {
	products: [],
	editedItem: null,
	editedIndex: -1,
	productDialog: false,
}

const mutations = {
	FETCH_PRODUCT (state, payload) {
		state.products = payload.data
	},
	EDIT_PRODUCT(state, payload) {
		state.editedIndex   = state.products.indexOf(payload)
		state.editedItem    = Object.assign({}, payload)
		state.productDialog = true
	},
	SHOW_PRODUCT_DIALOG(state) {
		state.productDialog = true
	},
	CLOSE_PRODUCT_DIALOG(state) {
		state.editedIndex   = -1
		state.editedItem    = null,
		state.productDialog = false
	},
	UPDATE_PRODUCT(state, payload) {
		if(state.editedIndex > -1) {
			Object.assign(state.products[state.editedIndex], payload.data)	
		} else {
			state.products.unshift(payload.data)
		}
	}
}

const actions = {
	getProduct: ({commit}, id) => new Promise((resolve, reject) => {
		axios.get('/api/GetStore/'+id+'/Menu/Product').then(response => {
			if(response.status === 200) {
				commit('FETCH_PRODUCT', response.data)
			}
			resolve(response)
		})
	})
}

const getters = {
	
}

export default {
	state, mutations, actions, getters
}