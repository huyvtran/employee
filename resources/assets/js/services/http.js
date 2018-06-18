import Vue from 'vue'
import axios from 'axios'
import NProgress from 'nprogress'
import Auth from '@/utils/auth'
Vue.use(Auth)
export const http = {
	init() {
		axios.interceptors.request.use(config => {
			config.headers.common           	= {'Accept' : 'application/json', 'X-Requested-With' : 'XMLHttpRequest'}
			config.headers.Authorization        = `Bearer `+ Vue.auth.getToken()
			return config
		})

		axios.interceptors.response.use(response => {
			NProgress.done()
			const token = response.headers['Authorization'] || response.data['token']
			token && window.localStorage.setItem('jwt_emp', token)
			return response
		},function (error) {

			NProgress.done()
			if(error.response.status === 401) {
				window.localStorage.removeItem('jwt_emp');
			}
			return Promise.reject(error);
		})
	}
}