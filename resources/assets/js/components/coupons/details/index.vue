<template>
	<v-container grid-list-md>
		<!-- Breadcrumb Start -->
		<v-toolbar dense color="grey lighten-3" class="elevation-0" flat>
			<v-breadcrumbs :items="breadcrumbs">
				<template slot="item" slot-scope="props">
					<v-breadcrumbs-item :to="props.item.href" exact :disabled="props.item.disabled">
						{{ props.item.text.toUpperCase() }}
					</v-breadcrumbs-item>
				</template>
			</v-breadcrumbs>
		</v-toolbar> <!-- Breadcrumb End -->
		<v-card color="transparent">
			<v-progress-linear v-if="loading" :indeterminate="true" class="ma-0"></v-progress-linear>
			<div v-else>
				<v-toolbar dense flat color="white">
					<v-toolbar-title class="red--text text--accent-3">
						{{coupon.coupon}}
					</v-toolbar-title>
					<v-spacer></v-spacer>
					<div>Khuyến mãi kết thúc sau: 
						<span class="body-2"><strong>{{time.dd}}</strong> Ngày <strong>{{time.hh}} : {{time.mm}} : {{time.ss}} </strong>
						</span>
					</div>
				</v-toolbar>
				<v-divider></v-divider>
				<v-card-text>
					<v-layout row wrap>
						<v-flex xs6>
							<v-card class="border--radius">
								<v-toolbar dense flat>
									<v-toolbar-title>Thông tin chi tiết</v-toolbar-title>
									<v-spacer></v-spacer>
									<span><v-icon size="20">{{coupon.public ? 'public' : 'person'}}</v-icon>{{ coupon.public ? 'Công khai' : 'Cá nhân'}}</span>						
								</v-toolbar>
								<v-list dense>
									<v-list-tile>
										<v-list-tile-content>Id:</v-list-tile-content>
										<v-list-tile-content class="align-end font-weight-bold">{{ coupon.id }}</v-list-tile-content>
									</v-list-tile>

									<v-list-tile>
										<v-list-tile-content>Giới hạn:</v-list-tile-content>
										<v-list-tile-content class="align-end red--text">{{ coupon.couponUsed }}/{{ coupon.maxCoupons }}</v-list-tile-content>
									</v-list-tile>

									<v-list-tile>
										<v-list-tile-content>Tiêu đề:</v-list-tile-content>
										<v-list-tile-content class="align-end">{{ coupon.title }}</v-list-tile-content>
									</v-list-tile>

									<v-list-tile>
										<v-list-tile-content>Từ ngày:</v-list-tile-content>
										<v-list-tile-content class="align-end font-weight-bold">{{ coupon.startedAt.date | formatDateTime}}</v-list-tile-content>
									</v-list-tile>

									<v-list-tile>
										<v-list-tile-content>Đến ngày:</v-list-tile-content>
										<v-list-tile-content class="align-end font-weight-bold">{{ coupon.endedAt.date | formatDateTime}}</v-list-tile-content>
									</v-list-tile>

									<v-list-tile>
										<v-list-tile-content>Khuyến mãi (%):</v-list-tile-content>
										<v-list-tile-content class="align-end red--text font-weight-bold">{{ coupon.discountPercent }}%</v-list-tile-content>
									</v-list-tile>

									<v-list-tile>
										<v-list-tile-content>Khuyến mãi (VNĐ):</v-list-tile-content>
										<v-list-tile-content class="align-end red--text font-weight-bold">{{ coupon.price | formatPrice}}</v-list-tile-content>
									</v-list-tile>

									<v-list-tile>
										<v-list-tile-content>Giảm tối đa (VNĐ):</v-list-tile-content>
										<v-list-tile-content class="align-end red--text font-weight-bold">{{ coupon.maxPrice | formatPrice}}</v-list-tile-content>
									</v-list-tile>

									<v-list-tile>
										<v-list-tile-content>Giá trị hóa đơn (VNĐ):</v-list-tile-content>
										<v-list-tile-content class="align-end red--text font-weight-bold">{{ coupon.totalAmount | formatPrice}}</v-list-tile-content>
									</v-list-tile>

									<v-list-tile>
										<v-list-tile-content>Trạng thái:</v-list-tile-content>
										<v-list-tile-content class="align-end font-weight-bold">{{ coupon.status.name }}</v-list-tile-content>
									</v-list-tile>

									<v-list-tile>
										<v-list-tile-content>Kích hoạt:</v-list-tile-content>
										<v-list-tile-content class="align-end" :class="{'red--text font-weight-bold font-italic': !coupon.actived, 'green--text font-weight-bold': coupon.actived}">{{ coupon.actived ? 'Kích hoạt' : 'Chưa kích hoạt' }}</v-list-tile-content>
									</v-list-tile>

									<v-list-tile>
										<v-list-tile-content class="align-center">{{ coupon.notes}}</v-list-tile-content>
									</v-list-tile>
								</v-list>
							</v-card>
						</v-flex>
						<v-flex xs6>							
							<v-card class="border--radius">
								<v-toolbar color="white" dense flat>
									<v-toolbar-title><span class="grey--text" v-if="coupon.stores.length > 0">({{coupon.stores.length}})</span> Cửa hàng được áp dụng tại {{city.name}}</v-toolbar-title>
									<v-spacer></v-spacer>
								</v-toolbar>
								<v-toolbar flat>
									<v-tooltip top>
										<v-btn slot="activator" icon small @click.native="refresh" :loading="progress"><v-icon>refresh</v-icon></v-btn>
										<span>Làm mới</span>
									</v-tooltip>						

									<v-combobox v-model="selected" :items="city.stores" label="Danh sách cửa hàng" multiple :item-text="name" single-line item-value="id" @change="changeAttribute">

										<v-list-tile slot="prepend-item" ripple @click="toggle" >
											<v-list-tile-action>
												<v-icon :color="selected.length > 0 ? 'primary' : ''">
													{{icon}}
												</v-icon>
											</v-list-tile-action>
											<v-list-tile-title>{{selectAllStore ? 'Bỏ chọn tất cả' : 'Chọn tất cả'}}</v-list-tile-title>
										</v-list-tile>
										<v-divider
										slot="prepend-item"
										class="mt-2"
										></v-divider>
										<template slot="selection" slot-scope="{ item, index }" >
											<v-chip v-if="index === 0">
												<v-avatar style="padding: 1px">
													<img
													:src="image(item.avatar)"
													>
												</v-avatar>
												<span>{{ item.name }}</span>
											</v-chip>
											<span
											v-if="index === 1"
											class="grey--text caption"
											>(+{{ selected.length - 1 }} khác)</span>
										</template> 
									</v-combobox>

									<v-btn color="success" small :disabled="disabled" @click.native="update(coupon)" :loading="progress">Áp dụng</v-btn>	

								</v-toolbar>
								<v-card color="grey lighten-3" flat>
									<v-tooltip top v-for="(store, i) in selected" :key="i">		
										<v-chip slot="activator"  color="white">
											<v-avatar style="padding: 1px">
												<img
												:src="image(store.avatar)"
												alt="John"
												>
											</v-avatar>
											<span class="text-truncate" style="max-width:100px">{{store.name}}</span>	
										</v-chip>
										<span>{{store.name}}</span>
									</v-tooltip>
								</v-card>						
							</v-card>							
						</v-flex>  
					</v-layout>					
				</v-card-text>
			</div>
		</v-card>
		<vue-dialog />
	</v-container>
</template>

<script>
	import axios from 'axios'
	import index from '@/mixins/index'
	import moment from 'moment'
	import Dialog from '../dialog'
	export default {
		mixins: [index],
		components: {
			'vue-dialog': Dialog
		},
		data() {
			return {
				coupon  : null,
				city    : null,
				selected: [],
				disabled: true,
				loading : false,
				progress: false,
				time: {
					dd:0,
					hh:0,
					mm:0,
					ss:0
				},
				breadcrumbs: [
				{
					text    : 'Quản lý khuyến mãi',
					href    : '/coupons',
					disabled: false,
				},
				{
					text    : 'Chi tiết',
					name    : '',
					disabled: true,
				}
				],
			}
		},
		methods: {
			//SHOW INFORMATION COUPON
			showCoupon: function(id) {
				var vm         = this
				const couponId = id
				if(!vm.loading) {
					vm.loading = true
					setTimeout(() => {
						axios.get('/api/Coupon/'+couponId+'/ShowCoupon', { withCredentials: true }).then(response => {
							if(response.status === 200) {
								vm.coupon = Object.assign({}, response.data.coupon)
								if( vm.coupon.stores.length>0 ) {
									vm.coupon.stores.forEach(item => {
										vm.selected.push(item)
									})
								}
							}
						}).finally(() => {
							vm.loading = false	
						})
					}, 100)					
				}
			},
			//GET CITY WITH STORE HAS VERIFIED
			getCityWithStore: function() {
				var vm = this
				axios.get('/api/City/GetCityWithStore', {withCredentials: true}).then(response => {
					if(response.status === 200) {
						this.city = response.data.city
					}
				})

			},
			// EDIT INFORMATION
			editItem(item) {
				this.$store.dispatch('editCoupon', item)
			},
			//UPDATE STORES HAS COUPON		
			update: function(item) {
				var vm         = this
				const couponId = item.id
				const params   = { secret: item.token }
				const data     = { stores: vm.selected }

				if(!vm.progress) {
					vm.progress = true 
					setTimeout(() => {
						axios.post('/api/Coupon/'+couponId+'/UpdateStore', data, { params, withCredentials: true }).then(response => {
							if(response.status === 200) {
								vm.coupon = Object.assign({}, response.data.coupon)
							}
						}).finally(() => {
							vm.progress = false
							vm.disabled = true
						})
					}, 100)
				}
			},
			//RUN TIME EXPIRES COUPON
			start: function (date) {
				if(!!this.coupon) {
					date = this.coupon.endedAt
					if(moment(date.date).isAfter(moment.tz(moment(), 'Asia/Ho_Chi_Minh'))) {
						setTimeout(() => {
							let timeNow   = new Date().getTime()

							let endedTime = new Date(date.date).getTime()				

							let distance  = Math.floor(endedTime - timeNow)/1000

							let day       = Math.floor(distance / (60 * 60 * 24));

							let hour      = Math.floor((distance % (60 * 60 * 24)) / (60 * 60));

							let minutes   = Math.floor((distance % (60 * 60)) / 60);

							var seconds   = Math.floor(distance % (60));

							this.time     = {dd: day, hh: hour, mm: minutes, ss: seconds}
						}, 1000)
					}
				}
			},			
			//SELECT ALL STORE
			toggle: function() {
				this.$nextTick(() => {
					if (this.selectAllStore) {
						this.selected = []
					} else {
						this.selected = this.city.stores.slice()
					}
				})
			},
			//CUSTOM NAME FOR LIST
			name: function(item) {
				return item.name+` (${item.discount}%)`
			},
			//UNBLOCK BUTTON
			changeAttribute: function() {
				if(this.disabled) {
					this.disabled = false
				}
			},
			//REFRESH COUPON
			refresh: async function() {
				var vm = this 
				if(!vm.progress) {
					vm.progress = true 
					await setTimeout(async () => {
						vm.selected = []
						if( vm.coupon.stores.length>0 ) {
							await vm.coupon.stores.forEach(item => {
								vm.selected.push(item)
							})
							vm.disabled = true
						}
					}, 200)
					vm.progress = false
				}				
			}
		},
		computed: {
			selectAllStore: function() {
				return this.selected.length === this.city.stores.length
			},
			selectSomeStore: function() {
				return this.selected.length > 0 && !this.selectAllStore
			},
			icon: function() {
				if (this.selectAllStore) {
					return 'check_box'
				}
				else if (this.selectSomeStore) {
					return 'indeterminate_check_box'
				}
				return 'check_box_outline_blank'
			},
		},
		created() {
			this.showCoupon(this.$route.params.couponId)
			this.getCityWithStore()
		},
		mounted() {
			this.start()
		},
		beforeUpdate() {
			this.start()
		}
	}
</script>