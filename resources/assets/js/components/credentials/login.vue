<template>
	<v-app id="inspire">
		<v-content>
			<v-container fluid fill-height>
				<v-layout align-center justify-center>
					<v-flex xs12 sm8 md4 >
						<v-card class="elevation-12">
							<v-toolbar dark color="blue" dense class="elevation-0">
								<v-toolbar-title>Login form</v-toolbar-title>
								<v-spacer></v-spacer>
							</v-toolbar>
							<v-card-text>
								<v-form>
									<v-text-field
									prepend-icon="mail"									
									label="E-mail"
									v-model="email"
									v-validate="'required|email'"
									:error-messages="errors.collect('email')"
									data-vv-name="email"
									required
									></v-text-field>
									<v-text-field 
									prepend-icon="lock"
									label="Password"
									v-model="password" 
									type="password"
									v-validate="'required'"
									:error-messages="errors.collect('password')"
									data-vv-name="password"
									required
									></v-text-field>
								</v-form>
							</v-card-text>
							<v-card-actions>
								<v-btn color="blue white--text" block :loading="loading" @click="login">Login</v-btn>
							</v-card-actions>
						</v-card>
					</v-flex>
				</v-layout>
			</v-container>
		</v-content>
	</v-app>
</template>

<script>
import axios from 'axios'
import {post} from '@/services/api'
export default {
	data() {
		return {
			email: null,
			password: null,
			loading:false,
			locale: 'vi'
		}
	},
	methods: {
		login: async function() {
			
			var vm = this
			var data = {email:this.email, password: this.password}
			var authUser = {}
			vm.loading = await !vm.loading
			
			await axios.post('/api/login', data).then(async (response) => {
				
				if(response.status === 200) {
					
					authUser.access_token = response.data.access_token
					authUser.expires_in = response.data.expires_in + Date.now()
					await vm.$auth.setToken(authUser)

					vm.$store.dispatch('getAuth').then(response => {
						if(response.status === 200) {
							vm.$router.push({name: 'Dashboard'})
						}
					})
				}

			})
			
			vm.loading = !vm.loading
		}
	},
	created() {

	}
}
</script>