export default function (Vue) {
	Vue.auth = {
		setToken(token) {
			localStorage.setItem('jwt_emp', JSON.stringify(token))
		},
		getToken() {
			var auth = JSON.parse(localStorage.getItem('jwt_emp'))
			if(!auth) {
				return null
			}
			if(Date.now()>parseInt(auth.expires_in)) {
				this.destroyToken();
				return null;
			}
			else{
				return auth.access_token 
			}
		},
		destroyToken() {
			localStorage.removeItem('jwt_emp')
		},
		isAuthenticated() {
			if(this.getToken()) {
				return true
			} else {
				return false
			}
		}
	}
	Object.defineProperties(Vue.prototype, {
		$auth: {
			get() {
				return Vue.auth
			}
		}
	})
}
