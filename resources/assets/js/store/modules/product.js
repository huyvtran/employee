import axios from 'axios'

const state = {
	products: [],
	editedItem: null,
	editedIndex: -1,
	productDialog: false,
	status: []
}

const mutations = {
	FETCH_PRODUCT (state, payload) {
		state.products = payload.products
	},
	EDIT_PRODUCT(state, payload) {
		state.editedIndex   = state.products.indexOf(payload)
		state.editedItem    = Object.assign({}, payload)
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
			Object.assign(state.products[state.editedIndex], payload.product)	
		} else {
			state.products.unshift(payload.product)
		}
	},
	DESTROY_PRODUCT(state, payload) {
		var products = state.products
		products.splice(products.indexOf(payload), 1)
	},
	FETCH_PRODUCT_STATUS (state, payload) {
		state.status = payload.data
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
	}),
	getProductStatus: ({commit}) => new Promise((resolve, reject) => {
		axios.get('/api/GetProductStatus').then(response => {
			if(response.status === 200) {
				commit('FETCH_PRODUCT_STATUS', response.data)
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