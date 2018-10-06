<template>
	<div>
		<v-card-title primary-title>
			<v-btn color="green darken-3" class="white--text" @click.native="$store.commit('SHOW_PRODUCT_DIALOG')" small round>
				Thêm mới
			</v-btn>

			<v-spacer></v-spacer>

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

				<v-flex xs2 :key="1">
					<v-select
					:items="filterTopping"
					item-text="name"
					item-value="value"
					v-model="search.topping"
					label="Topping"
					></v-select>
				</v-flex>

				<v-flex xs2 :key="2">
					<v-select
					:items="filterSize"
					item-text="name"
					item-value="value"
					v-model="search.size"
					label="Size"
					></v-select>
				</v-flex>

				<v-flex xs4 :key="3">
					<v-text-field
					v-model="search.text"
					append-icon="search"
					label="Tìm kiếm món"
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
				<v-hover>
					<v-card  slot-scope="{ hover }" width="120px" style="border-radius: 50%">		
						<v-img	:aspect-ratio="16/9" :src="image(props.item.image)" height="120">
							<v-expand-transition>
								<div v-if="hover || $vuetify.breakpoint.smAndDown" class="d-flex transition-fast-in-fast-out black lighten-2 v-card--reveal white--text" style=" cursor: pointer;"  @click.prevent="updatingAvatar(props.item)" :style="cameraOverlay">
									<v-layout column justify-center align-center>
										<v-flex>
											<v-icon color="white">camera_alt</v-icon>
										</v-flex>
										<v-flex >
											<div>Đổi ảnh</div>
										</v-flex>
									</v-layout>
								</div>
							</v-expand-transition>		
						</v-img>
					</v-card>
				</v-hover>
			</td>
			<td>
				<h4>{{ props.item.name}}</h4>
				<h4 class="grey--text" v-if="props.item._name != null">{{props.item._name}}</h4>
				<div>Đã bán <strong class="red--text">{{props.item.count}}</strong> lần</div>
				<div v-if="props.item.description != null">Mô tả: {{props.item.description}}</div>
			</td>
			<td>
				<h5 v-if="!props.item.haveSize">{{ props.item.price | formatPrice}}</h5>
				<h5 v-else v-for="(size, i) in props.item.sizes" :key="i">
					{{size.name}}: {{size.price | formatPrice}}
				</h5>
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
								<v-list-tile-title>Chỉnh sửa món</v-list-tile-title>
							</v-list-tile-content>
						</v-list-tile>

						<v-list-tile @click="removeItem(props.item)" avatar>
							<v-list-tile-avatar>
								<v-icon class="red white--text">delete</v-icon>
							</v-list-tile-avatar>
							<v-list-tile-content>
								<v-list-tile-title>Xóa món</v-list-tile-title>
							</v-list-tile-content>
						</v-list-tile>
					</v-list>					
				</v-menu>
			</td>
		</template>
	</v-data-table>
	<vue-dialog></vue-dialog>
	<vue-image ref="avatar"></vue-image>
</div>
</template>

<script>
	import Dialog from './dialog'
	import axios from 'axios'
	import {mapState} from 'vuex'
	import index from '@/mixins/index'
	import ImageDialog from '@/components/commons/ImageDialog'
	export default {
		mixins: [index],
		components: {
			'vue-dialog': Dialog,
			'vue-image': ImageDialog
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
					size: null,
					catalogue: null,
				},
				loading: false,
				filterTopping: [{name: 'Tất cả', value: null}, {name: 'Có', value: true}, {name: 'Không', value: false}],
				filterSize: [{name: 'Tất cả', value: null}, {name: 'Có', value: true}, {name: 'Không', value: false}],
				filterCatalogue: [{name: 'Tất cả', id: null}]
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
			editItem: async function(item) {
				await this.$store.commit('EDIT_PRODUCT', item)
				this.$store.commit('SHOW_PRODUCT_DIALOG')
			},
			removeItem: function(item) {
				var vm = this
				vm.$swal({
					title: 'Bạn có chắc xóa?',
					text: "Bạn sẽ không thể khôi phục lại!",
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Đồng ý!',
					cancelButtonText: 'Không, Hủy',
					confirmButtonClass: 'btn error',
					cancelButtonClass: 'btn',
					buttonsStyling: false,
					reverseButtons: true
				}).then((result) => {
					if (result.value) {
						vm.$swal(
							'Deleted!',
							item.name+' đã được xóa.',
							'success'
							).then(() => {
								axios.post('/api/GetStore/'+vm.$route.params.storeId+'/Menu/Product/Delete', item).then(response => {
									if(response.status == 204) {
										vm.$store.commit('DESTROY_PRODUCT', item)
									}
								})
							})
						} else{
							vm.$swal(
								'Cancelled',
								'',
								'error'
								)
						}
					})


			},
			updatingAvatar(product) {
				var vm     = this
				const size = { width: 350, height: 350 }
				this.$store.commit('EDIT_PRODUCT', product)
				this.$refs.avatar.open('Thay đổi ảnh '+product.name, size).then(response => {
					if(response.status) {
						vm.updateAvatar(response.avatar, product)
					}
				})
			},
			updateAvatar(avatar, product) {
				var vm          = this
				const storeId   = vm.$route.params.storeId
				const productId = product.id
				const data      = { storeId: storeId, avatar: avatar }
				axios.post('/api/Menu/Product/'+productId+'/UpdateAvatar', data, { withCredentials: true }).then(response => {
					if(response.status === 200 ){
						vm.$store.commit('UPDATE_PRODUCT', response.data)
					}
				}).finally(() => {

				})
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
			filterByCatalogue(list, id) {
				const search = id

				if(search == null) {
					return list
				}

				return list.filter(item => item.catalogue_id === search)

			},
		},
		computed: {
			...mapState({
				items: state      => state.productStore.products,
				catalogues: state => Array.from(state.catalogueStore.catalogues)
			}),
			filterData() {
				if(this.items.length>0) {
					return this.filterByCatalogue(this.filterBySize(this.filterByTopping(this.items, this.search.topping), this.search.size), this.search.catalogue)
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
			this.getProduct(this.$route.params.storeId)
			this.$store.dispatch('getCatalogue', this.$route.params.storeId)
		}
	}
</script>