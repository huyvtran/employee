<template>
	<v-layout row>
		<v-flex xs12 sm6 offset-sm3>
			<v-card>
				<v-list two-line>
					<template v-for="(data, i) in catalogues">
						<v-subheader>{{ data.name }}</v-subheader>
						<v-divider></v-divider>
						<v-list-tile v-for="(item,i) in data.products" :key="i" avatar @click="">
							<v-list-tile-avatar>
								<img :src="image(item.image)">
							</v-list-tile-avatar>
							<v-list-tile-content>
								<v-list-tile-title v-html="item.name"></v-list-tile-title>
								<v-list-tile-sub-title v-html="item._name"></v-list-tile-sub-title>
							</v-list-tile-content>
						</v-list-tile>
					</template>
				</v-list>
			</v-card>
		</v-flex>
	</v-layout>
	<!-- <v-content>
		<v-layout column>
			<v-list three-line>
				<template v-for="(data, i) in catalogues">
					<v-subheader>{{ data.name }}</v-subheader>

					<v-list-tile v-for="(item,i) in data.products" :key="i" avatar @click="">
						<v-list-tile-avatar>
							<img :src="image(item.image)">
						</v-list-tile-avatar>
						<v-list-tile-content>
							<v-list-tile-title v-html="item.name"></v-list-tile-title>
							<v-list-tile-sub-title v-html="item._name"></v-list-tile-sub-title>
						</v-list-tile-content>
					</v-list-tile>
				</template>
			</v-list> -->
	<!-- 		<v-flex xs4 v-for="(data, i) in catalogues" :key="i">
		<v-subheader>{{data.name}}</v-subheader> -->
				<!-- <v-expansion-panel popout focusable>
					<v-expansion-panel-content v-for="(item,i) in data.products" :key="i" expand-icon="md-menu-down">
						<v-list slot="header" dense three-line class="transparent">
							<v-list-tile avatar>
								<v-list-tile-avatar>
									<img :src="image(item.image)" alt="avatar">
								</v-list-tile-avatar>
								<v-list-tile-content>
									<v-list-tile-title><h3>{{ item.name }}</h3></v-list-tile-title>
									<v-list-tile-sub-title v-if="item._name != null">{{ item._name }}</v-list-tile-sub-title>
									<v-list-tile-sub-title v-if="item.description != null">Mô tả: {{ item.description }}</v-list-tile-sub-title>
								</v-list-tile-content>
								<v-list-tile-content>
									<v-list-tile-title>										
										Topping: <strong>{{item.haveTopping ? "Có" :  "Không"}}</strong>
									</v-list-tile-title>
									<v-list-tile-title>
										Size: <strong>{{item.haveSize ? "Có" :  "Không"}}</strong>
									</v-list-tile-title>
								</v-list-tile-content>
								<v-list-tile-action>
									<h3 v-if="!item.haveSize">{{item.price | formatPrice}}</h3>
									<h3 v-else v-for="(size, i) in item.sizes">{{size.name}}: {{size.price | formatPrice}}</h3>
									<v-list-tile-action-text>Đã được đặt <strong>{{item.count}}</strong> lần</v-list-tile-action-text>									
								</v-list-tile-action>
							</v-list-tile>
						</v-list>
						<v-layout row wrap>
							<v-flex xs4>
								<v-card>
									<v-card-media
									height="180"
									:src="image(item.image)"
									></v-card-media>
								</v-card>
							</v-flex>
							<v-flex xs8>
								<v-card color="transparent">
									<v-card-title primary-title>
										<v-layout row wrap>
											<v-flex xs12>
												<v-flex xs4 v-for="(size, i) in item.sizes" :key="i">
													{{size.name}}
												</v-flex>
											</v-flex>

											<v-flex xs12>
												<v-radio-group>
													<v-radio  v-for="(size, i) in item.sizes"  :label="`${size.name} (${size._name})`" :key="i">{{size.price}}</v-radio>
												</v-radio-group>
											</v-flex>
										</v-layout>				
										
									</v-card-title>
								</v-card>
							</v-flex>
						</v-layout>
					</v-expansion-panel-content>
				</v-expansion-panel> -->
				<!-- </v-flex> -->
	<!-- 	</v-layout>
	</v-content> -->
</template>

<script>
import index from '@/mixins/index'
import axios from 'axios'
export default {
	mixins: [index],
	data() {
		return {
			catalogues: []
		}
	},
	methods: {
		getMenu: function(id) {
			axios.get('/api/GetStore/'+id+'/Menu').then(response => {
				if(response.status == 200) {
					this.catalogues = response.data.data
				}
			})
		}
	},
	computed: {

	},
	created() {
		this.getMenu(this.$route.params.storeId)
	}
}
</script>