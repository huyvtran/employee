<template>
	<v-container fluid grid-list-md>
		<v-tabs fixed-tabs>
			<v-tab :to="{name: 'Customer'}">
				Khách hàng
			</v-tab>
			<v-tab :to="{name: 'Shipper'}">
				Shipper
			</v-tab>
		</v-tabs>

		<router-view></router-view>
	</v-container>
</template>

<script>
export default {

}

</script>