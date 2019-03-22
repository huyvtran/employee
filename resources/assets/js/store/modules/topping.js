import axios from 'axios'
const state = {
	toppings: [],
	editedItem: null,
	editedIndex: -1,
	toppingDialog: false,
}

const mutations = {
	FETCH_TOPPING (state, payload) {
		state.toppings = payload.toppings
	},
	EDIT_TOPPING(state, payload) {
		state.editedIndex   = state.toppings.indexOf(payload)
		state.editedItem    = Object.assign({}, payload)
		state.toppingDialog = true
	},
	DELETE_TOPPING(state, payload) {
		var toppings = state.toppings
		toppings.splice(toppings.indexOf(payload), 1)
	},
	SHOW_TOPPING_DIALOG(state) {
		state.toppingDialog = true
	},
	CLOSE_TOPPING_DIALOG(state) {
		state.editedIndex   = -1
		state.editedItem    = null,
		state.toppingDialog = false
	},
	UPDATE_TOPPING(state, payload) {
		if(state.editedIndex > -1) {
			Object.assign(state.toppings[state.editedIndex], payload.data)	
		} else {
			state.toppings.unshift(payload.data)
		}
	}
}

const actions = {
	getTopping: ({commit}, id) => new Promise((resolve, reject) => {
		axios.get('/api/GetStore/'+id+'/Menu/Topping').then(response => {
			if(response.status === 200) {
				commit('FETCH_TOPPING', response.data)
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