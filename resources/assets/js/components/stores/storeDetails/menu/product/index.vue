<template>
	<div>
		<v-card-title primary-title>
			<v-btn color="green darken-3" class="white--text" @click.native="$store.commit('SHOW_PRODUCT_DIALOG')" small round>
				Thêm mới
			</v-btn>

			<v-spacer></v-spacer>

			<v-layout row wrap  > <!-- Filter Start -->	

				<v-flex xs2 :key="0">
					<v-select
					:items="filterTopping"
					item-text="name"
					item-value="value"
					v-model="search.topping"
					label="Topping"
					></v-select>
				</v-flex>

				<v-flex xs2 :key="1">
					<v-select
					:items="filterSize"
					item-text="name"
					item-value="value"
					v-model="search.size"
					label="Size"
					></v-select>
				</v-flex>

				<v-flex xs8 :key="2">
					<v-text-field
					v-model="search.text"
					append-icon="search"
					label="Tìm kiếm danh mục"
					single-line
					hide-details
					></v-text-field>
				</v-flex>

			</v-layout> <!-- Filter End -->	

		</v-card-title>

		<v-data-table
		:headers="headers"
		:items="filterData"
		hide-actions
		:search="search.text"
		:loading="loading"
		class="elevation-1"
		>
		<v-progress-linear slot="progress" color="blue" indeterminate></v-progress-linear>
		<template slot="items" slot-scope="props">
			<td class="text-xs-center">
				<div>{{ props.item.id }}</div>
			</td>
			<td>
				<v-avatar size="120" color="grey" class="my-2">
					<img :src="image(props.item.image)" alt="alt">
				</v-avatar>
			</td>
			<td>
				<h4>{{ props.item.name}}</h4>
				<h4 class="grey--text" v-if="props.item._name != null">{{props.item._name}}</h4>
				<div>Đã bán <strong class="red--text">{{props.item.count}}</strong> lần</div>
			</td>
			<td>
				<h4 v-if="!props.item.haveSize">{{ props.item.price | formatPrice}}</h4>
				<h4 v-else v-for="size in props.item.sizes">
					{{size.name}}: {{size.price | formatPrice}}
				</h4>
			</td>		
			<td>
				<div>Trạng thái: <strong>{{ props.item.status}}</strong></div>
				<div>Size: <span :class="{'red--text': !props.item.haveSize, 'blue--text': props.item.haveSize}">{{ props.item.haveSize ? 'Có' : 'Không'}}</span></div>
				<div>Topping: <span :class="{'red--text': !props.item.haveTopping, 'blue--text': props.item.haveTopping}">{{ props.item.haveTopping ? 'Có' : 'Không'}}</span></div>
			</td>		
			<td class="text-xs-center">{{ props.item.priority}}</td>
			<td class="text-xs-center">
				<v-menu bottom left offset-y>
					<v-btn slot="activator" icon class="mx-0">
						<v-icon>more_vert</v-icon>
					</v-btn>
					<v-list>
						<v-list-tile @click="editItem(props.item)" avatar>
							<v-list-tile-avatar>
								<v-icon class="teal white--text">edit</v-icon>
							</v-list-tile-avatar>
							<v-list-tile-content>
								<v-list-tile-title>Chỉnh sửa danh mục</v-list-tile-title>
							</v-list-tile-content>
						</v-list-tile>
					</v-list>					
				</v-menu>
			</td>
		</template>
	</v-data-table>
	<vue-dialog></vue-dialog>
</div>
</template>

<script>
import Dialog from './dialog'
import axios from 'axios'
import {mapState} from 'vuex'
import index from '@/mixins/index'
export default {
	mixins: [index],
	components: {
		'vue-dialog': Dialog
	},
	data() {
		return {
			headers: [
			{
				text: '#',
				align: 'center',
				value: 'id',
			},
			{ text: 'Hình ảnh', align: 'center', value: '_name', sortable: false},
			{ text: 'Tên món', align: 'left', value: 'name'},
			{ text: 'Giá', align: 'left', value: 'price', width: '150'},
			{ text: 'Tùy chọn', align: 'left', sortable: false, width: '150'},
			{ text: 'Ưu tiên', value: 'priority', align: 'center' },
			{ text: '', sortable: false, align: 'center' },
			],
			search: {
				text: '',
				topping: null,
				size: null
			},
			loading: false,
			filterTopping: [{name: 'Tất cả', value: null}, {name: 'Có', value: true}, {name: 'Không', value: false}],
			filterSize: [{name: 'Tất cả', value: null}, {name: 'Có', value: true}, {name: 'Không', value: false}],
		}
	},
	methods: {
		getProduct: function(id) {
			return new Promise(async (resolve, reject) => {
				var vm = this
				vm.loading = await true
				await this.$store.dispatch('getProduct', id)
				vm.loading = false
			})
		},
		editItem: function(item) {
			this.$store.commit('EDIT_PRODUCT', item)
		},
		filterByTopping(list, value) {

			const search = value

			if(search == null) {
				return list
			}

			return list.filter(item => item.haveTopping === search)
		},
		filterBySize(list, value) {
			
			const search = value

			if(search == null) {
				return list
			}

			return list.filter(item => item.haveSize === search)
		},
	},
	computed: {
		...mapState({
			items: state => state.productStore.products
		}),
		filterData() {
			if(this.items.length>0) {
				return this.filterBySize(this.filterByTopping(this.items, this.search.topping), this.search.size)
			}
		}
	},
	created() {
		this.getProduct(this.$route.params.storeId)
	}
}
</script>