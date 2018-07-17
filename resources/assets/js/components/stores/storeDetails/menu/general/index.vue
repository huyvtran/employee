<template>
	<v-content>
		<v-layout column>
			<v-layout row wrap  > <!-- Filter Start -->	

				<v-flex xs4 :key="0">
					<v-select
					:items="filterCatalogue"
					item-text="name"
					item-value="id"
					v-model="search.catalogue"
					label="Danh mục"
					></v-select>
				</v-flex>

				<v-flex xs8 :key="2">
					<v-text-field
					v-model="search.text"
					append-icon="search"
					label="Tìm kiếm món"
					hide-details
					></v-text-field>
				</v-flex>

			</v-layout> <!-- Filter End -->	
			<v-flex xs4 v-for="(data, i) in filterData" :key="i">
				<v-subheader>{{data.name}}</v-subheader>
				
				<v-expansion-panel popout focusable>

					<v-expansion-panel-content v-for="(item,i) in data.products" :key="i" mb-3 >
						<v-list slot="header" dense two-line class="transparent">
							<v-list-tile avatar>
								<v-list-tile-avatar size="40" class="pr-2">
									<img :src="image(item.image)" alt="avatar">
								</v-list-tile-avatar>
								
								<v-list-tile-content>
									<v-list-tile-title><h3>{{ item.name }}</h3></v-list-tile-title>
									<v-list-tile-sub-title v-if="item._name != null">{{ item._name }}</v-list-tile-sub-title>
									<v-list-tile-sub-title v-if="item.description != null">Mô tả: {{ item.description }}</v-list-tile-sub-title>
								</v-list-tile-content>

								<v-list-tile-content>					
									<h5>Topping: <strong>{{item.haveTopping ? "Có" :  "Không"}}</strong></h5>
									<h5>Size: <strong>{{item.haveSize ? "Có" :  "Không"}}</strong></h5>
									<h5>Đã được đặt <strong>{{item.count}}</strong> lần</h5>
								</v-list-tile-content>

								<v-list-tile-action>
									<h5 v-if="!item.haveSize">{{item.price | formatPrice}}</h5>
									<h5 v-else v-for="(size, i) in item.sizes">{{size.name}}: {{size.price | formatPrice}}</h5>									
								</v-list-tile-action>
							</v-list-tile>
						</v-list>
						
						<v-card v-if="item.haveTopping">
							<v-card-text class="grey lighten-3">
								<v-layout row wrap>
									<v-flex xs3 v-for="(topping, i) in toppings" :key="i">
										<div>{{topping.name}}</div> 
										<div class="subheader" v-if="topping._name.length>0">{{topping._name}}</div> 
										<h4>{{topping.price | formatPrice}}</h4> 
									</v-flex>
								</v-layout>
							</v-card-text>
						</v-card>
					</v-expansion-panel-content>
				</v-expansion-panel>
			</v-flex>
		</v-layout>
	</v-content>
</template>

<script>
import {mapState} from 'vuex'
import index from '@/mixins/index'
import axios from 'axios'
export default {
	mixins: [index],
	data() {
		return {
			menu: [],
			search: {
				text: '',
				catalogue: null,
			},
			filterCatalogue: [{name: 'Tất cả', id: null}]
		}
	},
	methods: {
		getMenu: function(id) {
			axios.get('/api/GetStore/'+id+'/Menu').then(response => {
				if(response.status == 200) {
					this.menu = response.data.data
				}
			})
		},
		filterByCatalogue(list, id) {
			const search = id

			if(search == null) {
				return list
			}

			return list.filter(item => item.id === search)

		},
	},
	computed: {
		...mapState({
			toppings: state   => state.toppingStore.toppings,
			catalogues: state => Array.from(state.catalogueStore.catalogues)
		}),
		filterData() {
			if(this.menu.length>0) {
				return this.filterByCatalogue(this.menu, this.search.catalogue)
			}
		}
	},
	watch: {
		'catalogues': function(val) {
			if(val.length > 0) {		
				val.forEach(item => {
					this.filterCatalogue.push({name: item.name, id: item.id})
				})
			}
		}
	},
	created() {
		this.getMenu(this.$route.params.storeId)
		this.$store.dispatch('getTopping', this.$route.params.storeId)
		this.$store.dispatch('getCatalogue', this.$route.params.storeId)
	}
}
</script>