import axios from 'axios'
const state = {
	sizes: [],
	editedItem: null,
	editedIndex: -1,
	sizeDialog: false,
}

const mutations = {
	FETCH_SIZE (state, payload) {
		state.sizes = payload.data
	},
	EDIT_SIZE(state, payload) {
		state.editedIndex = state.sizes.indexOf(payload)
		state.editedItem  = Object.assign({}, payload)
		state.sizeDialog  = true
	},
	SHOW_SIZE_DIALOG(state) {
		state.sizeDialog  = true
	},
	CLOSE_SIZE_DIALOG(state) {
		state.editedIndex = -1
		state.editedItem  = null,
		state.sizeDialog  = true
	},
	UPDATE_SIZE(state, payload) {
		if(state.editedIndex > -1) {
			Object.assign(state.sizes[state.editedIndex], payload.data)	
		} else {
			state.sizes.unshift(payload.data)
		}
	}
}

const actions = {
	getSize: ({commit}, id) => new Promise((resolve, reject) => {
		axios.get('/api/GetStore/'+id+'/Menu/Size').then(response => {
			if(response.status === 200) {
				commit('FETCH_SIZE', response.data)
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