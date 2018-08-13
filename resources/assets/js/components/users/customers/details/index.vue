<template>
	<v-container>
		<!-- Breadcrumb Start -->
		<v-toolbar dense color="transparent" class="elevation-0" flat>
			<v-breadcrumbs>
				<v-icon slot="divider">chevron_right</v-icon>
				<v-breadcrumbs-item v-for="(item, index) in breadcrumbs" :to="{name: item.name}" :disabled="item.disabled" :key="index">
					{{ item.text }}
				</v-breadcrumbs-item>
			</v-breadcrumbs>
		</v-toolbar> <!-- Breadcrumb End -->
		
		<v-dialog v-model="loading" hide-overlay persistent width="300">
			<v-card	color="blue" dark>
				<v-card-text>
					Xin Quý khách vui lòng chờ trong giây lát...
					<v-progress-linear
					indeterminate
					color="white"
					class="mb-0"
					></v-progress-linear>
				</v-card-text>
			</v-card>
		</v-dialog>

		<v-card  v-if="!loading && user != null">
			<v-toolbar dense color="transparent" class="elevation-0" flat>
				<v-toolbar-title>ID: {{user.id}}</v-toolbar-title>
				<v-spacer></v-spacer>
				<v-tooltip top>
					<span slot="activator"><v-icon color="red accent-3">scatter_plot</v-icon> <span class="font-weight-bold">{{user.points}}</span></span>
					<span>Điểm df dùng đổi thưởng</span>
				</v-tooltip>
			</v-toolbar>

			<v-divider></v-divider>

			<v-card-text justify-center>
				<v-layout row wrap class="justify-center">
					<v-avatar  size="150" color="grey" style="border">
						<img :src="image(user.image)" alt="avatar" style="{padding:5pix}">
					</v-avatar>
				</v-layout>

				<v-list>
					<v-list-tile @click="">
						<v-list-tile-action>
							<v-icon color="primary">person</v-icon>							
						</v-list-tile-action>

						<v-list-tile-content>
							<v-list-tile-title>{{user.name}}</v-list-tile-title>
						</v-list-tile-content>
					</v-list-tile>

					<v-divider inset></v-divider>

					<v-list-tile @click="">
						<v-list-tile-action>
							<v-icon color="purple">cake</v-icon>							
						</v-list-tile-action>

						<v-list-tile-content>
							<v-list-tile-title>{{user.birthday | formatDate}}</v-list-tile-title>
						</v-list-tile-content>
					</v-list-tile>

					<v-divider inset></v-divider>

					<v-list-tile @click="">
						<v-list-tile-action>
							<v-icon color="pink">wc</v-icon>							
						</v-list-tile-action>

						<v-list-tile-content>
							<v-list-tile-title>{{user.gender ? 'Nam' : 'Nữ'}}</v-list-tile-title>
						</v-list-tile-content>
					</v-list-tile>

					<v-divider inset></v-divider>

					<v-list-tile @click="">
						<v-list-tile-action>
							<v-icon color="red">mail</v-icon>
						</v-list-tile-action>

						<v-list-tile-content>
							<v-list-tile-title>{{user.email}}</v-list-tile-title>
						</v-list-tile-content>
					</v-list-tile>

					<v-divider inset></v-divider>

					<v-list-tile @click="">
						<v-list-tile-action>
							<v-icon color="green darken-3">phone</v-icon>
						</v-list-tile-action>

						<v-list-tile-content>
							<v-list-tile-title>{{user.phone | formatPhone}}</v-list-tile-title>
						</v-list-tile-content>
					</v-list-tile>

					<v-divider inset></v-divider>

					<v-list-tile @click="">
						<v-list-tile-action>
							<v-icon color="indigo">location_on</v-icon>
						</v-list-tile-action>

						<v-list-tile-content>
							<v-list-tile-title>{{user.address==null ? 'Chưa có' : user.address}}</v-list-tile-title>
						</v-list-tile-content>
					</v-list-tile>

					<v-list-tile @click="">
						<v-list-tile-action>
							<v-icon color="green">done</v-icon>
						</v-list-tile-action>

						<v-list-tile-content>
							<v-list-tile-title>{{user.isActived ? 'Đã kích hoạt' : 'Chưa kích hoạt'}}</v-list-tile-title>
						</v-list-tile-content>
					</v-list-tile>

					<v-list-tile @click="">
						<v-list-tile-action>
							<v-icon color="red">block</v-icon>
						</v-list-tile-action>

						<v-list-tile-content>
							<v-list-tile-title>{{user.isBanned ? 'Bị cấm' : 'Đang hoạt động'}}</v-list-tile-title>
						</v-list-tile-content>
					</v-list-tile>
				</v-list>			
			</v-card-text>
		</v-card>
	</v-container>
</template>

<script>
import index from '@/mixins/index'
import axios from 'axios'
import { mapState } from 'vuex'
export default {
	mixins: [ index ],
	data() {
		return {
			title: 'Danh sách khách hàng',
			loading: false,
			breadcrumbs: [
			{
				text: 'Dashboard',
				name: 'Dashboard',
				disabled: false
			},
			{
				text: 'Customers',
				name: 'Customer',
				disabled: false
			},
			{
				text: 'Details',
				disabled: true
			}
			],
			user: null
		}
	},
	methods: {
		getCustomerDetails: function(userId) {
			return new Promise((resolve, reject) => {
				axios.get('/api/GetUser/'+userId+'/Customer').then(response => {
					if(response.status === 200) {
						this.user = response.data.data						
					}
					resolve(response)
				}).catch(error => {
					reject(error)
				})
			})
		}
	},
	computed: {

	},
	created() {
		setTimeout(() => {
			this.loading = !this.loading
			this.getCustomerDetails(this.$route.params.userId).finally(() => {
				this.loading = !this.loading
			})
		},100)		
	}
}


</script>