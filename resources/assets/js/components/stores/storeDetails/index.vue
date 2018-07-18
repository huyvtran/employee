<template>
	<v-container fluid grid-list-md>
		<v-tabs fixed-tabs>
			<v-tab :to="{name: 'About', params: {storeId: $route.params.storeId}}">
				Giới thiệu
			</v-tab>
			<v-tab :to="{name: 'Menu', params: {storeId: $route.params.storeId}}">
				Thực đơn
			</v-tab>
			<v-tab>
				Nhận xét (comming soon)
			</v-tab>
			<v-tab>
				Không gian (comming soon)	
			</v-tab>
		</v-tabs>
		<v-card-text v-if="store != null">
			<v-layout row wrap>
				<!-- Navigation start -->
				<v-flex xs3 >

					<v-card>
						<v-system-bar status color="red darken-3" height="35px">
							<v-btn icon :to="{name: 'Store'}" exact small>
								<v-icon color="white">chevron_left</v-icon>
							</v-btn>
							<v-spacer></v-spacer>
							<v-btn icon>
								<v-icon color="white">more_vert</v-icon>
							</v-btn>	
						</v-system-bar>

						<v-card-media :src="image(store.avatar)" height="250px">
							<v-layout column class="media">
								<v-card-title>
									<v-spacer></v-spacer>
									<v-tooltip top v-if="store.isVerified">
										<v-btn slot="activator" dark icon>
											<v-icon  color="green darken-3">verified_user</v-icon>
										</v-btn>
										<span>Chứng nhận hợp tác cùng Dofuu</span>
									</v-tooltip>									
								</v-card-title>
							</v-layout>							
						</v-card-media>

						<v-toolbar dense flat color="red darken-3" dark class="elevation-0">
							<v-subheader class="text-xs-center"><h3>{{store.name}}</h3></v-subheader>
						</v-toolbar>

						<v-list three-line>

							<v-list-tile>
								<v-list-tile-action>
									<v-icon color="indigo">phone</v-icon>
								</v-list-tile-action>
								<v-list-tile-content>
									<v-list-tile-title><h4>Mobile</h4></v-list-tile-title>
									<v-list-tile-sub-title><h4>{{store.phone | formatPhone}}</h4></v-list-tile-sub-title>
								</v-list-tile-content>
							</v-list-tile>

							<v-divider inset></v-divider>

							<v-list-tile>
								<v-list-tile-action>
									<v-icon color="indigo">mail</v-icon>
								</v-list-tile-action>
								<v-list-tile-content>
									<v-list-tile-title><h4>E-mail</h4></v-list-tile-title>
									<v-list-tile-sub-title><h4>{{store.user.email}}</h4></v-list-tile-sub-title>
								</v-list-tile-content>
							</v-list-tile>

							<v-divider inset></v-divider>

							<v-list-tile >
								<v-list-tile-action>
									<v-icon color="indigo">place</v-icon>
								</v-list-tile-action>
								<v-list-tile-content>
									<v-list-tile-title><h4>Address</h4></v-list-tile-title>
									<v-list-tile-sub-title><h4>{{store.address}}</h4></v-list-tile-sub-title>
								</v-list-tile-content>
							</v-list-tile>
						</v-list>
					</v-card>
				</v-flex> <!-- Navigation End -->
				<v-flex xs9>
					<router-view></router-view>
				</v-flex>
			</v-layout>
		</v-card-text>
	</v-container>
</template>

<script>
import {mapState} from 'vuex'
import index from '@/mixins/index'
import getLocation from '@/mixins/getLocation'
import ActivityDialog from '@/components/template/activityDialog'
import axios from 'axios'
// import Dialog from './dialog'
export default {
	mixins: [index, getLocation],
	data() {
		return {

		}
	},
	components: {
		'vue-activity-dialog': ActivityDialog
	},
	methods: {

		statusIcon: function(value) {
			var status = new String(value).toLowerCase()
			switch(status) {
				case 'mở cửa':
				return {url:apiDomain+'/storage/image/pin-green-icon.png'}
				break
				case 'đóng cửa':
				return {url:apiDomain+'/storage/image/pin-red-icon.png'}
				break
			}
		},
	},
	computed: {
		...mapState({
			store: state => state.storeStore.store,
			alert: state => state.activityStore.alert
		})
	},
	created() {
		setTimeout(() => {
			this.$store.dispatch('showStore', this.$route.params.storeId)
		}, 100)
	}
}
</script>