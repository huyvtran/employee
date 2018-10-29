<template>
	<v-card>
		<!-- Breadcrumb Start -->
		<v-toolbar dense color="grey lighten-3" class="elevation-0" flat>
			<v-breadcrumbs>
				<v-icon slot="divider">chevron_right</v-icon>
				<v-breadcrumbs-item v-for="(item, index) in breadcrumbs" :to="{name: item.name}" :key="index">
					{{ item.text }}
				</v-breadcrumbs-item>
			</v-breadcrumbs>
		</v-toolbar> <!-- Breadcrumb End -->

		<v-container fluid grid-list-md>
			<v-data-iterator :items="filterData" :rows-per-page-items="rowsPerPageItems" content-tag="v-layout" row wrap :search="search.text">
				<v-toolbar slot="header" class="mb-2" color="indigo darken-5" dark flat >
					<v-toolbar-title>Danh sách mã khuyến mãi</v-toolbar-title>
					<v-spacer></v-spacer>
					<v-select
					:items="filterPublic"
					item-text="name"
					item-value="value"
					v-model="search.public"
					label="Loại mã"
					max-height="400">
					<template slot="item" slot-scope="data">							
						<v-list-tile-content>
							<v-list-tile-title>{{data.item.name}}</v-list-tile-title>
						</v-list-tile-content>
						<v-list-tile-action>
							{{data.item.storeCount}}
						</v-list-tile-action>
					</template></v-select>
					
					<v-spacer></v-spacer>

					<v-select
					:items="filterActive"
					item-text="name"
					item-value="value"
					v-model="search.active"
					label="Kích hoạt"
					max-height="400">
					<template slot="item" slot-scope="data">							
						<v-list-tile-content>
							<v-list-tile-title>{{data.item.name}}</v-list-tile-title>
						</v-list-tile-content>
						<v-list-tile-action>
							{{data.item.storeCount}}
						</v-list-tile-action>
					</template></v-select>
					
					<v-spacer></v-spacer>
					

						<v-text-field
						append-icon="search"
						label="Tìm kiếm theo (mã, tên,...)"
						v-model="search.text"
						solo-inverted
						hide-details
						></v-text-field>
				
					
				</v-toolbar>

				<v-flex	slot="item"	slot-scope="props" xs12 sm6	md4	lg3	>
					<v-card style="border-radius: 8px">
						<v-toolbar dense flat>
							<v-toolbar-title class="subheading font-weight-bold red--text ">{{ props.item.coupon }}</v-toolbar-title>
							<v-spacer></v-spacer>
							<span><v-icon size="20">{{props.item.public ? 'public' : 'person'}}</v-icon>{{ props.item.public ? 'Công khai' : 'Cá nhân'}}</span>
							<v-menu bottom left>
								<v-btn slot="activator" icon >
									<v-icon>more_vert</v-icon>
								</v-btn>

								<v-list dense>
									<v-list-tile @click="editItem(props.item)" avatar>
										<v-list-tile-avatar size="26">
											<v-icon class="blue white--text">edit</v-icon>
										</v-list-tile-avatar>
										<v-list-tile-title>Chỉnh sửa</v-list-tile-title>
									</v-list-tile>
									<v-list-tile avatar :to="{name: 'CouponDetail', params: {couponId: props.item.id}}" >
										<v-list-tile-avatar size="26">
											<v-icon class="green white--text">store</v-icon>
										</v-list-tile-avatar>
										<v-list-tile-title>Áp dụng cửa hàng</v-list-tile-title>
									</v-list-tile>
								</v-list>
							</v-menu>
						</v-toolbar>

						<v-divider></v-divider>

						<v-list dense>
							<v-list-tile>
								<v-list-tile-content>Id:</v-list-tile-content>
								<v-list-tile-content class="align-end font-weight-bold">{{ props.item.id }}</v-list-tile-content>
							</v-list-tile>

							<v-list-tile>
								<v-list-tile-content>Giới hạn:</v-list-tile-content>
								<v-list-tile-content class="align-end red--text">{{ props.item.couponUsed }}/{{ props.item.maxCoupons }}</v-list-tile-content>
							</v-list-tile>

							<v-list-tile>
								<v-list-tile-content>Tiêu đề:</v-list-tile-content>
								<v-list-tile-content class="align-end">{{ props.item.title }}</v-list-tile-content>
							</v-list-tile>

							<v-list-tile>
								<v-list-tile-content>Từ ngày:</v-list-tile-content>
								<v-list-tile-content class="align-end font-weight-bold">{{ props.item.startedAt.date | formatDateTime}}</v-list-tile-content>
							</v-list-tile>

							<v-list-tile>
								<v-list-tile-content>Đến ngày:</v-list-tile-content>
								<v-list-tile-content class="align-end font-weight-bold">{{ props.item.endedAt.date | formatDateTime}}</v-list-tile-content>
							</v-list-tile>

							<v-list-tile>
								<v-list-tile-content>Khuyến mãi (%):</v-list-tile-content>
								<v-list-tile-content class="align-end red--text font-weight-bold">{{ props.item.discountPercent }}%</v-list-tile-content>
							</v-list-tile>

							<v-list-tile>
								<v-list-tile-content>Khuyến mãi (VNĐ):</v-list-tile-content>
								<v-list-tile-content class="align-end red--text font-weight-bold">{{ props.item.discountPrice | formatPrice}}</v-list-tile-content>
							</v-list-tile>

							<v-list-tile>
								<v-list-tile-content>Giảm tối đa (VNĐ):</v-list-tile-content>
								<v-list-tile-content class="align-end red--text font-weight-bold">{{ props.item.maxPrice | formatPrice}}</v-list-tile-content>
							</v-list-tile>

							<v-list-tile>
								<v-list-tile-content>Giá trị hóa đơn (VNĐ):</v-list-tile-content>
								<v-list-tile-content class="align-end red--text font-weight-bold">{{ props.item.totalAmount | formatPrice}}</v-list-tile-content>
							</v-list-tile>

							<v-list-tile>
								<v-list-tile-content>Trạng thái:</v-list-tile-content>
								<v-list-tile-content class="align-end">{{ props.item.status.name }}</v-list-tile-content>
							</v-list-tile>

							<v-list-tile>
								<v-list-tile-content>Kích hoạt:</v-list-tile-content>
								<v-list-tile-content class="align-end" :class="{'red--text font-weight-bold font-italic': !props.item.actived, 'green--text font-weight-bold': props.item.actived}">{{ props.item.actived ? 'Kích hoạt' : 'Chưa kích hoạt' }}</v-list-tile-content>
							</v-list-tile>

							<v-list-tile>
								<v-list-tile-content class="align-center">{{ props.item.notes}}</v-list-tile-content>
							</v-list-tile>
						</v-list>
						<v-divider></v-divider>
						<v-toolbar flat dense>
							<v-btn flat  @click="listStoreOpen(props.item.stores)" block v-if="props.item.stores.length > 0">
								<span v-for="(store, index) in props.item.stores" >
									<v-avatar  v-if="index < 5" size="30">
										<img :src="image(store.avatar)">
									</v-avatar>
									<span v-if="index === 6" class="grey--text caption">(+{{ props.item.stores.length - 5 }} Khác)</span>
								</span>
							</v-btn>
							<div v-if="props.item.stores.length === 0" class="font-weight-bold red--text">
								Chưa có cửa hàng được áp dụng
							</div>
						</v-toolbar>
					</v-card>
				</v-flex>
			</v-data-iterator>
		</v-container>

		<v-tooltip left>
			<v-btn slot="activator" fixed bottom right icon  dark color="green darken-3" @click.native="$store.commit('DIALOG_OPEN')"><v-icon>add</v-icon></v-btn>
			<span>Thêm mới</span>
		</v-tooltip>

		<vue-dialog />
		<v-dialog v-model="dialog" max-width="500" scrollable>			
			<v-card class="border--radius">
				<v-toolbar dense flat>
					<v-toolbar-title>Danh sách các quán được áp dụng</v-toolbar-title>
					<v-spacer></v-spacer>
					<v-btn icon @click.native="listStoreClose">
						<v-icon>close</v-icon>
					</v-btn>
				</v-toolbar>
				<v-divider></v-divider>
				<v-card-text style="height: 450;">
					<v-list subheader>
						<template v-for="(store, index) in stores">
							<v-list-tile  :key="index" avatar>
								<v-list-tile-avatar>
									<img :src="image(store.avatar)">
								</v-list-tile-avatar>

								<v-list-tile-content>
									<v-list-tile-title v-html="store.name"></v-list-tile-title>
									<v-list-tile-sub-title>Chiết khấu: {{store.discount}}%</v-list-tile-sub-title>
								</v-list-tile-content>
							</v-list-tile>
							<v-divider
							inset
							></v-divider>
						</template>
					</v-list>
				</v-card-text>
			</v-card>
		</v-dialog>
	</v-card>
</template>

<script>
	import Dialog from './dialog'
	import { mapState } from 'vuex'
	import index from '@/mixins/index'
	export default {
		mixins: [index],
		components: {
			'vue-dialog': Dialog
		},
		data() {
			return {
				breadcrumbs: [
				{
					text    : 'Dashboard',
					name    : 'Dashboard',
					disabled: false
				},
				{
					text    : 'Coupon',
					disabled: true
				}
				],
				rowsPerPageItems: [25,50,100, {"text": "Tất cả", "value": -1} ],
				headers: [
				{
					text    : 'Id',
					align   : 'left',
					sortable: false,
				},
				{ 	
					text: 'Coupon', 
					align   : 'center'
				},
				{ 
					text: 'Hạn sử dụng', 
					sortable: false,
				},
				{ 
					text: 'Tiêu đề/ Ghi chú', 
					sortable: false, 
				},
				{ 
					text: 'Khuyến mãi', 
					value: 'discountPercent'
				},
				{ 
					text: 'Số lượng'
				},		
				{ 
					text: 'Trạng thái'
				},
				{ 
					text: 'Kích hoạt'
				},
				{ 
					text: 'Tác vụ', 
					sortable: false
				}
				],
				dialog: false,
				stores: [],
				search: {
					text: '',
					active: null,
					public: null
				},
				filterPublic: [{name: 'Tất cả', value: null}, {name: 'Công khai', value: true}, {name: 'Cá nhân', value: false}],
				filterActive: [{name: 'Tất cả', value: null}, {name: 'Đã kích hoạt', value: true}, {name: 'Chưa kích hoạt', value: false}],
			}
		},
		created() {
			this.$store.dispatch('fetchCoupon')
		},
		methods: {
			editItem(item) {
				this.$store.dispatch('editCoupon', item)
			},
			listStoreOpen: function(stores) {
				var vm    = this
				vm.stores = stores
				vm.dialog = true
			},
			listStoreClose: function() {
				var vm    = this 
				vm.stores = []
				vm.dialog = false
			},
			filterByPublic(list, value) {
				const search = value
				if(search == null) {
					return list
				}
				return list.filter(item => item.public === search)
			},
			filterByActived(list, value) {
				const search = value
				if(search == null) {
					return list
				}
				return list.filter(item => item.actived === search)
			},
		},
		computed: {
			...mapState({
				coupons: state => state.couponStore.coupons,
				alert  : state  => state.storeStore.alert
			}),
			filterData() {
				if(this.coupons.length>0) {
					return this.filterByPublic(this.filterByActived(this.coupons, this.search.active), this.search.public)
				}
			}
		}
	}
</script>
