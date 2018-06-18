import axios from 'axios'

const state = {
	catalogues: [],
	editedItem: null,
	editedIndex: -1,
	catalogueDialog: false,
}

const mutations = {
	//CATALOGUE
	FETCH_CATALOGUE (state, payload) {
		state.catalogues = payload.data
	},
	EDIT_CATALOGUE(state, payload) {
		state.editedIndex     = state.catalogues.indexOf(payload)
		state.editedItem      = Object.assign({}, payload)
		state.catalogueDialog = true
	},
	SHOW_CATALOGUE_DIALOG(state) {
		state.catalogueDialog = true
	},
	CLOSE_CATALOGUE_DIALOG(state) {
		state.editedIndex     = -1
		state.editedItem      = null,
		state.catalogueDialog = false
	},
	UPDATE_CATALOGUE(state, payload) {
		if(state.editedIndex > -1) {
			Object.assign(state.catalogues[state.editedIndex], payload.data)	
		} else {
			state.catalogues.unshift(payload.data)
		}
	}
}

const actions = {
	getCatalogue: ({commit}, id) => new Promise((resolve, reject) => {
		axios.get('/api/GetStore/'+id+'/Menu/Catalogue').then(response => {
			if(response.status === 200) {
				commit('FETCH_CATALOGUE', response.data)
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