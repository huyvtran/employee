<template>
	<v-card>
		<v-tabs fixed-tabs>
			<v-tab :to="{name: 'General'}">
				Tổng thể
			</v-tab>
			<v-tab :to="{name: 'Catalogue'}">
				Danh mục
			</v-tab>
			<!-- <v-tab :to="{name: 'Size'}">
				Size
			</v-tab> -->
			<v-tab :to="{name: 'Topping'}">
				Topping
			</v-tab>
			<v-tab :to="{name: 'Product'}">
				Món
			</v-tab>
		</v-tabs>
		<v-card-text>
			<router-view></router-view>
		</v-card-text>
	</v-card>
</template>

<script>

</script>