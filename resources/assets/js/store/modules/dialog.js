import axios from 'axios'

const state = {
	dialog: false,
	editedIndex: -1,
	editedItem: {}
}

const mutations = {
	DIALOG_OPEN(state) {
		state.dialog = true
	},
	DIALOG_CLOSE(state) {
		state.dialog = false
		state.editedIndex = -1
	}
}

const actions = {
	
}

const getters = {
	
}

export default {
	state, mutations, actions, getters
}