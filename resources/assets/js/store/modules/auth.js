import axios from 'axios'

const state = {
	isAuth: !!localStorage.getItem('jwt_emp'),
	currentUser: null,
}

const mutations = {
	SET_AUTH_USER(state, payload) {
		state.currentUser = Object.assign({}, payload.data)
		state.isAuth   = true
	},
	CLEAR_AUTH_USER(state) {
		state.isAuth      = false
		state.currentUser = null
		window.localStorage.removeItem('jwt_emp')
	}
}

const actions = {
	getAuth: ({commit}) => new Promise((resolve, reject) => {
		var vm = this
		const data = []
		axios.post('/api/dfe/', data).then(response => {
			if(response.status === 200) {
				commit('SET_AUTH_USER', response.data)
			}
			resolve(response)
		}).catch(error => {
			reject(error)
		})
	}),
	logout: ({commit}) => new Promise((resolve, reject) => {
		commit('CLEAR_AUTH_USER')
	})
}

const getters = {

}

export default {
	state, mutations, actions, getters
}