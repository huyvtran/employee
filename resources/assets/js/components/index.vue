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
		</v-toolbar>
		<v-content>
			<router-view></router-view>
		</v-content>
	</v-app>
</template>

<script>
export default {

}
</script>