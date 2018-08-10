import axios from 'axios'
const state = {
	users: {},
	user: null,
	loading: false,
	dialog: false,
	editedIndex: -1,
	editedItem: {},
	alert: {
		type:Array,
		alert: false,
		messages: '',
		type: 'success'
	}
}

const mutations = {
	FETCH_USER (state, payload) {
		state.users = payload.data
	},
	
	LOADING_USER(state) {
		state.loading = !state.loading
	},
	
	DIALOG_USER(state) {
		state.dialog = !state.dialog
	},

	DIALOG_USER_CLOSE(state) {
		state.dialog = false
		state.editedIndex = -1
	},
	
	SHOW_USER(state, payload) {
		state.user = Object.assign({}, payload.data)
	},

	EDIT_USER(state, payload) {
		state.editedIndex = state.users.indexOf(payload)
		state.editedItem = Object.assign({}, payload)
		state.dialog = true
	},
	
	UPDATE_USER(state, payload) {
		if(state.editedIndex > -1) {
			Object.assign(state.users[state.editedIndex], payload.data)	
		} else {
			state.users.unshift(payload.data)
		}

	},

	ALERT_USER(state, payload) {
		state.alert = Object.assign(state.alert, payload)
	}
}

const actions = {
	fetchCustomer: ({commit}) => new Promise((resolve, reject) => {
		var vm = this
		axios.get('/api/GetUser/Customers').then(response => {
			if(response.status == 200) {
				commit('FETCH_USER', response.data)
			}
			resolve(response)
		}).catch(errors => {
			reject(errors)
		})
	}),
	fetchShipper: ({commit}) => new Promise((resolve, reject) => {
		var vm = this
		const data = []
		axios.post('/api/GetUser/Shippers', data, {withCredentials:true}).then(response => {
			if(response.status == 200) {
				commit('FETCH_USER', response.data)
			}
			resolve(response)
		}).catch(errors => {
			reject(errors)
		})
	})
	// addStore: ({commit}, payload) => new Promise((resolve, reject) => {
	// 	var vm = this
	// 	axios.post('/api/AddStore', payload).then(response => {
	// 		if(response.status == 201) {
	// 			commit('UPDATE_STORE', response.data)
	// 		}
	// 		resolve(response)
	// 	}).catch(errors => {
	// 		reject(errors)
	// 	})
	// }),
	// showStore: ({commit}, id) => new Promise((resolve, reject) => {
	// 	var vm = this 
	// 	axios.get('/api/GetStore/'+ id).then(response => {
	// 		if(response.status == 200) {
	// 			commit('SHOW_STORE', response.data)
	// 		}
	// 		resolve(response)
	// 	}).catch(errors => {
	// 		reject(errors)
	// 	})
	// }),
	// editStore: ({commit}, payload) => new Promise((resolve, reject) => {
	// 	commit('EDIT_STORE', payload)
	// }),
	// updateStore: ({commit}, payload) => new Promise((resolve, reject) => {
	// 	axios.put('/api/UpdateStore/'+payload.id, payload).then(response => {
	// 		if(response.status == 200) {
	// 			commit('UPDATE_STORE', response.data)
	// 		} 
	// 		resolve(response)
	// 	}).catch(errors => {
	// 		reject(errors)
	// 	})
	// }),
}

const getters = {

}

export default {
	state, mutations, actions, getters
}