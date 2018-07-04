<template>
	<v-app id="inspire">
		<v-toolbar color="blue" dark scroll-toolbar-off-screen app>
			<v-toolbar-title>Employee Dofuu</v-toolbar-title>
			<v-toolbar-items>
				<v-btn flat class="black--text" :to="{name: 'Dashboard'}">Dashboard</v-btn>
				<v-btn flat class="black--text" :to="{name: 'Order'}">Đơn đặt hàng</v-btn>
				<v-btn flat class="black--text" :to="{name: 'Store'}">Quản lý cửa hàng</v-btn>
				<v-menu open-on-hover bottom offset-y>
					<v-btn slot="activator" class="black--text" flat>Quản lý dịch vụ <v-icon right>arrow_drop_down</v-icon></v-btn>
					<v-list>
						<v-list-tile :to="{name: 'Service'}">
							<v-list-tile-title>Dịch vụ</v-list-tile-title>
						</v-list-tile>
						<v-list-tile :to="{name: 'Delivery'}">
							<v-list-tile-title>Nơi áp dụng</v-list-tile-title>
						</v-list-tile>
					</v-list>
				</v-menu>
			</v-toolbar-items>
			<v-spacer></v-spacer>
			<v-menu
			:close-on-content-click="false"
			v-model="notificationMenu"
			:min-width="432"
			offset-y
			allow-overflow
			>
			<v-badge slot="activator" color="red" overlap>
				<span slot="badge">{{unreadCount}}</span>
				<v-btn color="indigo" dark icon>
					<v-icon>
						shopping_cart
					</v-icon>
				</v-btn>
			</v-badge>

			<v-card>
				<v-tabs	v-model="tab"	color="primary"	dark fixed-tabs slider-color="primary" height="40">
					<v-tab>
						Recent
					</v-tab>
					<v-tab>
						Old
					</v-tab>
				</v-tabs>

				<v-divider></v-divider>
				<v-card-text style="height: 300px;" class="scroll-y" v-if="notifications.length>0">
					<v-list dense three-line>
						<template v-for="(notification, index) in notifications" v-if="tab==0">						
							<v-list-tile avatar  :class="{'blue-grey lighten-5': notification.read_at == null}"  @click="readNotification(notification)" v-if="notification.read_at == null">
								<v-list-tile-avatar>
									<img :src="image(notification.data.owner.image)">
								</v-list-tile-avatar>
								<v-list-tile-content>
									<v-list-tile-title>
										<strong>
											{{notification.data.name}}
										</strong>
									</v-list-tile-title>
									<v-list-tile-title>
										Đặt hàng với hóa đơn mã số {{notification.data.id}}
									</v-list-tile-title>
									<v-list-tile-sub-title>
										<v-icon small>access_time</v-icon>	{{ formatTime(notification.data.bookingDate) }}
									</v-list-tile-sub-title>
								</v-list-tile-content>
								<v-list-tile-action v-if="notification.read_at != null">
									<v-tooltip top>									
										<v-icon slot="activator" color="success">check</v-icon>
										<span>Seen</span>
									</v-tooltip>
									<v-list-tile-action-text></v-list-tile-action-text>
									<v-list-tile-action-text>{{ notification.read_at | formatDateTimeHumanize }}</v-list-tile-action-text>
								</v-list-tile-action>
							</v-list-tile>
							<v-divider :key="index" v-if="notification.read_at == null"></v-divider>
						</template>

						<template v-for="(notification, index) in notifications" v-if="tab==1">						
							<v-list-tile avatar  @click="readNotification(notification)" v-if="notification.read_at != null">
								<v-list-tile-avatar>
									<img :src="image(notification.data.owner.image)">
								</v-list-tile-avatar>
								<v-list-tile-content>
									<v-list-tile-title>
										<strong>
											{{notification.data.name}}
										</strong>
									</v-list-tile-title>
									<v-list-tile-title>
										Đặt hàng với hóa đơn mã số {{notification.data.id}}
									</v-list-tile-title>
									<v-list-tile-sub-title>
										<v-icon small>access_time</v-icon> {{ formatTime(notification.data.bookingDate) }}
									</v-list-tile-sub-title>
								</v-list-tile-content>
								<v-list-tile-action v-if="notification.read_at != null">
									<v-tooltip top>									
										<v-icon slot="activator" color="success">check</v-icon>
										<span>Seen</span>
									</v-tooltip>
									<v-list-tile-action-text></v-list-tile-action-text>
									<v-list-tile-action-text>{{ notification.read_at | formatDateTimeHumanize }}</v-list-tile-action-text>
								</v-list-tile-action>
							</v-list-tile>
							<v-divider v-if="notification.read_at != null"></v-divider>
						</template>
					</v-list>
				</v-card-text>
				<v-divider></v-divider>
				<v-list>
					<v-system-bar status color="primary" lights-out class="justify-center">
						<router-link :to="{name: 'Notification'}"> See All </router-link>
					</v-system-bar>
				</v-list>
			</v-card>
		</v-menu>
	</v-toolbar>
	<v-content>
		<router-view></router-view>
	</v-content>
</v-app>
</template>

<script>
import index from '@/mixins/index'
export default {
	mixins: [index],
	data() {
		return {
			notificationMenu:false,
			tab:0
		}
	},
	computed: {
		notifications() {
			return this.$store.getters.getNotification
		},
		unreadCount() {
			return this.$store.getters.unreadCount
		}
	},
	methods: {
		formatTime(date) {
			var start = moment(date, 'DD-MM-YYYY HH:mm')
			return start.startOf().locale('vi').fromNow()
		},
	}
}
</script>