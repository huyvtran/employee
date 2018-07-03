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

		<v-card-title primary-title>
			<div>
				<div class="headline">{{title}}</div>
			</div>
			<v-spacer></v-spacer>
			<div>
				<v-icon>fas fa-lock</v-icon>
				<v-btn color="red accent-3" :outline="filter" dark small @click="filterAction" round><v-icon left>search</v-icon>{{filter ? 'Tắt bộ lọc' : 'Bật bộ lọc'}}</v-btn>
			</div>
		</v-card-title>		
		<v-card-text>
			
			<v-alert :color="alert.type" dismissible :value="alert.show" outline v-show="alert.index === 0 && $route.name == alert.name">
				{{alert.message}}
			</v-alert>

			<!-- Filter start-->
			<v-container v-show="filter" fluid grid-list-md transition="slide-y-transition">
				<v-layout row wrap  >
					<v-flex xs2 :key="1">
						<v-select
						:items="filterTypes"
						item-text="name"
						item-value="id"
						v-model="search.type"
						label="Loại cửa hàng"
						max-height="400">
						<template slot="item" slot-scope="data">							
							<v-list-tile-content>
								<v-list-tile-title>{{data.item.name}}</v-list-tile-title>
							</v-list-tile-content>
							<v-list-tile-action>
								{{data.item.storeCount}}
							</v-list-tile-action>
						</template></v-select>
					</v-flex>

					<v-flex xs1 :key="2">
						<v-select
						:items="filterShow"
						item-text="name"
						item-value="value"
						v-model="search.show"
						label="Ẩn/Hiện"
						></v-select>
					</v-flex>

					<v-flex xs2 :key="3">
						<v-select
						:items="filterVerify"
						item-text="name"
						item-value="value"
						v-model="search.verify"
						label="Xác thực"
						></v-select>
					</v-flex>

					<v-flex xs4 :key="4">
						<v-text-field
						append-icon="search"
						label="Tìm kiếm theo (mã, tên, địa chỉ, số điện thoại...)"
						single-line
						hide-details
						v-model="search.text"
						></v-text-field>
					</v-flex>
				</v-layout>
			</v-container>	<!-- Filter End -->	
			
			<!-- Table -->
			<v-data-table
			:headers="headers"
			:search="search.text"
			:items="filterData"
			hide-actions
			class="elevation-1"
			>
			<template slot="items" slot-scope="props">
				<td class="text-xs-center">
					<div>{{ props.item.id }}</div>
					<div><v-btn :color="props.expanded ? 'yellow' : 'grey lighten-2'" small @click="props.expanded = !props.expanded" icon>
						<v-icon v-if="!props.expanded">expand_more</v-icon>
						<v-icon v-if="props.expanded">expand_less</v-icon>
					</v-btn></div>
				</td>
				<td>
					<v-card width="120px">						
						<v-card-media
						:src="image(props.item.avatar)"
						alt="avatar"
						height="120px"
						>
						<v-layout >
							<v-flex xs12 align-end flexbox>								
								<v-spacer></v-spacer>
							</v-flex>
						</v-layout>
					</v-card-media>
					<v-card-actions>
						<v-tooltip top>
							<span class="black--text" slot="activator"><v-icon size="18" color="black">visibility</v-icon> {{props.item.views}}</span>
							<span>Lượt xem: {{props.item.views}}</span>
						</v-tooltip>
						<v-tooltip top>
							<span class="blue--text" slot="activator"><v-icon size="18" color="blue">swap_vert</v-icon> {{props.item.priority}}</span>
							<span>Mức ưu tiên: {{props.item.priority}}</span>
						</v-tooltip>
						<v-spacer></v-spacer>
						<v-tooltip top v-if="props.item.isVerified">
							<span class="black--text" slot="activator"><v-icon size="18" color="green darken-3">verified_user</v-icon></span>
							<span>Xác nhận đã hợp tác cùng Dofuu</span>
						</v-tooltip>	
					</v-card-actions>
				</v-card>
			</td>
			<td class="text-xs-center">
				<div>{{ props.item.type_name }}</div>
				<div><strong>{{ props.item.name }}</strong></div>

			</td>
			<td>
				<div>SĐT: <strong>{{ props.item.phone }}</strong></div>
				<div>Mail: <strong>{{props.item.user.email}}</strong></div>	
				<div>Địa chỉ: <strong><a @click.stop.prevent="showMap(props.item)">{{props.item.address}}</a></strong></div>				
			</td>
			<td :class="{'primary--text': props.item.isShowed}"><strong>{{ props.item.isShowed ? 'Hiện' : 'Ẩn'}}</strong></td>
			<td class="text-xs-center"><div :class="{'green--text text--darken-3': props.item.discount>10, 'red--text': props.item.discount>0, 'orange--text text--darken-4': props.item.discount>15}" class="font-weight-bold">{{ props.item.discount }}%</div></td>
			<td>{{ props.item.city_name }}</td>
			<td>{{ props.item.district_name }}</td>
			<td>
				<v-menu bottom left offset-y>
					<v-btn slot="activator" icon class="mx-0">
						<v-icon>more_vert</v-icon>
					</v-btn>
					<v-list>
						<v-list-tile :to="{name: 'About', params: {storeId: props.item.id}}" avatar>
							<v-list-tile-avatar>								
								<v-icon class="red white--text">store</v-icon>
							</v-list-tile-avatar>
							<v-list-tile-content>
								<v-list-tile-title>Xem cửa hàng</v-list-tile-title>
							</v-list-tile-content>
						</v-list-tile>
						<v-list-tile @click="editItem(props.item)" avatar>
							<v-list-tile-avatar>
								<v-icon class="teal white--text">edit</v-icon>
							</v-list-tile-avatar>
							<v-list-tile-content>
								<v-list-tile-title>Chỉnh sửa cửa hàng</v-list-tile-title>
							</v-list-tile-content>
						</v-list-tile>
					</v-list>					
				</v-menu>
			</td>
		</template>

		<template slot="expand" slot-scope="props">
			<v-container fluid grid-list-md class="grey lighten-3">
				<v-layout row wrap>
					<v-flex d-flex xs12 sm6 md6>
						<v-card>
							<v-toolbar card color="indigo" dark>
								<v-toolbar-title>
									Thông tin người sở hữu
								</v-toolbar-title>
							</v-toolbar>
							<v-card-text>
								<v-subheader>Thông tin chủ sở hữu</v-subheader>
								<v-container>
									<v-layout row wrap>
										<v-flex xs4>
											UID:
										</v-flex>
										<v-flex xs8>
											<strong>{{ props.item.user.id }}</strong>
										</v-flex>

										<v-flex xs4>
											Chủ sở hữu/Quản lý:
										</v-flex>
										<v-flex xs8>
											<strong>{{ props.item.user.name }}</strong>
										</v-flex>

										<v-flex xs4>
											Ngày sinh:
										</v-flex>
										<v-flex xs8>
											<strong>{{ props.item.user.birthday }}</strong>
										</v-flex>

										<v-flex xs4>
											Giới tính:
										</v-flex>
										<v-flex xs8>
											<strong>{{ gender(props.item.user.gender) }}</strong>
										</v-flex>											
									</v-layout>
								</v-container>

								<v-subheader>Liên hệ</v-subheader>	
								<v-container>
									<v-layout row wrap>
										<v-flex xs4>
											Email:
										</v-flex>
										<v-flex xs8>
											<strong>{{ props.item.user.email}}</strong>
										</v-flex>

										<v-flex xs4>
											Số điện thoại:
										</v-flex>
										<v-flex xs8>
											<strong>{{ props.item.user.phone }} </strong>
										</v-flex>

										<v-flex xs4>
											Address:
										</v-flex>
										<v-flex xs8>
											<a @click.stop.prevent="showMap(props.item)">			
												<strong>{{ props.item.user.address }}</strong>
											</a>
										</v-flex>
									</v-layout>
								</v-container>								
							</v-card-text>
						</v-card>
					</v-flex>
					<v-flex d-flex xs12 sm6 md6>
						<v-layout row wrap>
							<v-flex d-flex>
								<v-layout row wrap>
									<v-flex
									d-flex
									xs12
									>
									<v-card>
										<v-toolbar card color="red" dark>
											<v-toolbar-title>
												Cài đặt
											</v-toolbar-title>
										</v-toolbar>
										<v-card-text>

											<v-subheader>Cửa hàng</v-subheader>

											<v-container>
												<v-layout row wrap>
													<v-flex xs4>
														Mức độ ưu tiên: 
													</v-flex>
													<v-flex xs8>
														<strong>{{ props.item.priority}}</strong>
													</v-flex>

													<v-flex xs4>
														Hợp tác:
													</v-flex>
													<v-flex xs8 :class="{'green--text text--darken-3': props.item.isVerified}">
														<strong>{{ props.item.isVerified ? 'Xác thực' : 'Chưa xác thực'}}</strong>
													</v-flex>

													<v-flex xs4>
														Ẩn/Hiện:
													</v-flex>
													<v-flex xs8>
														<strong>															
															{{ props.item.isShowed ? 'Hiện' : 'Ẩn'}}
														</strong>
													</v-flex>													

													<v-flex xs4>
														Trạng thái:
													</v-flex>
													<v-flex xs8>
														<strong> 
															{{ props.item.status_name}}
														</strong>														
													</v-flex>
												</v-layout>
											</v-container>

											<v-subheader>Tài khoản</v-subheader>

											<v-container>
												<v-layout row wrap>	
													<v-flex xs4>
														Kích hoạt tài khoản:
													</v-flex>
													<v-flex xs8 :class="{'red--text': !props.item.user.isActived, 'green--text text--darken-3': props.item.user.isActived}">
														<strong>															
															{{ props.item.user.isActived ? 'Đã kích hoạt' : 'Chưa kích hoạt'}}
														</strong>
													</v-flex>

													<v-flex xs4>
														Khóa tài khoản: 
													</v-flex>
													<v-flex xs8 :class="{'red--text': props.item.user.isBanned, 'green--text text--darken-3': !props.item.user.isBanned}">
														<strong>
															{{ props.item.user.isBanned ? 'Đã bị cấm' : 'Hoạt động'}}
														</strong>
													</v-flex>
												</v-layout>
											</v-container>												
										</v-card-text>
									</v-card>
								</v-flex>
							</v-layout>
						</v-flex>
					</v-layout>
				</v-flex>
			</v-layout>
		</v-container>
	</template>
</v-data-table>
<v-tooltip left>
	<v-btn slot="activator" fixed bottom right icon  dark color="green darken-2" @click.native="$store.commit('DIALOG_STORE')"><v-icon>add</v-icon></v-btn>
	<span>Thêm mới</span>
</v-tooltip>

</v-card-text>
<v-dialog v-model="mapDialog" max-width="800" v-if="mapDialog">
	<v-card>
		<gmap-map :center="location" :zoom="15" map-type-id="terrain" style="height: 500px">
			<GmapMarker	:position="location" :clickable="true" :icon="typeIcon(store.type_name)">
				<gmap-info-window :position="location" :opened="true">
					<v-card>
						<v-card-title primary-title>
							<div>
								<a class="body-2 font-weight-bold" :href="`https://www.dofuu.com/${store.city_slug}/${store.slug}`" target="_blank">{{store.name}}</a>
								<div class="grey--text">{{store.type_name}}</div>
							</div>
						</v-card-title>
						<v-list dense>
							<v-list-tile @click="">
								<v-list-tile-action>
									<v-icon color="indigo">phone</v-icon>
								</v-list-tile-action>
								<v-list-tile-content>
									<v-list-tile-title><h4>{{store.phone}}</h4></v-list-tile-title>
									<v-list-tile-sub-title><h4>Mobile</h4></v-list-tile-sub-title>
								</v-list-tile-content>
							</v-list-tile>

							<v-divider inset></v-divider>

							<v-list-tile @click="">
								<v-list-tile-action>
									<v-icon color="indigo">place</v-icon>
								</v-list-tile-action>
								<v-list-tile-content>
									<v-list-tile-title><h4>{{store.address}}</h4></v-list-tile-title>
									<v-list-tile-sub-title><h4>Address</h4></v-list-tile-sub-title>
								</v-list-tile-content>
							</v-list-tile>
						</v-list>
					</v-card>	
				</gmap-info-window>
			</GmapMarker>
		</gmap-map>
	</v-card>
</v-dialog>
<vue-dialog></vue-dialog>
</v-card>
</template>

<script>
import {mapState} from 'vuex'
import index from '@/mixins/index'
import getLocation from '@/mixins/getLocation'
import Dialog from './dialog'
export default {
	mixins: [index, getLocation],
	data() {
		return {
			title: 'Danh sách cửa hàng',
			breadcrumbs: [
			{
				text: 'Dashboard',
				name: 'Dashboard',
				disabled: false
			},
			{
				text: 'Store',
				disabled: true
			}
			],
			headers: [
			{
				text: '#',
				align: 'center',
				value: 'id'
			},
			{ text: 'Hình đại diện', value:'user.email', align: 'center', sortable:false, width:'120px'},
			{ text: 'Cửa hàng', value: 'name', align: 'center'},
			{ text: 'Liên hệ', value: 'phone', sortable:false},
			{ text: 'Ẩn/Hiện', value: 'address', sortable:false },
			{ text: 'Chiết khấu', value: 'discount', align: 'center' },
			{ text: 'Thành phố', value: 'city_name' },
			{ text: 'Quận', value: 'district_name' },
			{ text: 'Tác vụ', sortable: false}
			],
			loading: false,
			search: {
				text: '',
				type: -1,
				show: null,
				verify: null
			},
			filter:false,
			filterTypes: [{name: 'Tất cả', id: -1, countStore: 0}],
			filterShow: [{name: 'Tất cả', value: null}, {name: 'Hiện', value: true}, {name: 'Ẩn', value: false}],
			filterVerify: [{name: 'Tất cả', value: null}, {name: 'Đã xác thực', value: true}, {name: 'Chưa xác thực', value: false}],
			mapDialog: false,
			store:null
		}
	},
	components: {
		'vue-dialog': Dialog,
	},
	methods: {
		//Show google map
		showMap(request) {
			this.mapDialog = true
			this.location  = {
				lat: request.lat,
				lng: request.lng
			}
			this.store 	   = request
		},
		//Icon google map
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
		//Convert value int to string for gender
		gender: function(value) {
			const gender = parseInt(value)
			if(parseInt(gender)==1) {
				return 'Nam'
			} else {
				return 'Nữ'
			}
		},
		//Edit Store
		editItem (item) {
			this.$store.dispatch('editStore', item)
		},
		filterAction() {
			if(this.filter) {
				this.search = {
					text: '',
					type: -1,
					show: null,
					verify: null
				}
			}
			this.filter = !this.filter

		},
		filterByType(list, id) {
			const search = id

			if(id == -1) {
				return list
			}

			return list.filter(item => item.type_id === search)
		},
		filterByShow(list, value) {
			const search = value

			if(search == null) {
				return list
			}

			return list.filter(item => item.isShowed === search)
		},
		filterByVerify(list, value) {
			const search = value

			if(search == null) {
				return list
			}

			return list.filter(item => item.isVerified === search)
		}
	},
	computed: {
		...mapState({
			items: state  => Array.from(state.storeStore.stores),
			alert: state  => state.storeStore.alert,
			dialog: state => state.storeStore.dialog,
			cities: state => Array.from(state.cityStore.cities),
			types: state  => Array.from(state.typeStore.types),
		}),
		formTitle() {
			return this.editedIndex === -1 ? 'New User' : 'Edit User'
		},
		filterData() {
			if(this.items.length>0) {
				return this.filterByVerify(this.filterByShow(this.filterByType(this.items, this.search.type), this.search.show), this.search.verify)
			}

		}
	},
	watch: {
		types: function(val, oldVal) {
			var count = 0
			
			if(this.filterTypes.length==1) {
				val.forEach(type => {
					count = count + type.storeCount
					this.filterTypes.push(type)
				})
				this.filterTypes.find(type => {
					if(type.id === -1) {
						type.storeCount = count
					}
				})
			}
		}
	},
	created() {
		setTimeout(() => {
			this.$store.dispatch('fetchStore')
			this.$store.dispatch('fetchType').then(async(response) => {
				if(response.status == 200) {
					await this.$store.dispatch('fetchCity')
					await this.$store.dispatch('fetchDistrict')
				}
			})
		},100)		
	}
}
</script>

