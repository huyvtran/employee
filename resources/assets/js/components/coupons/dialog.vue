<template>
	<v-dialog v-model="dialog" persistent fullscreen hide-overlay transition="dialog-bottom-transition">
		<v-card>
			<v-toolbar dark color="blue" flat dense>
				<v-btn icon dark @click="close">
					<v-icon>close</v-icon>
				</v-btn>
				<v-toolbar-title>{{ formTitle }}</v-toolbar-title>
				<v-spacer></v-spacer>
				<v-toolbar-items>
					<v-btn dark flat @click.native="save" :disabled="disabled" :loading="progress">Hoàn thành</v-btn>
				</v-toolbar-items>
			</v-toolbar>
			
			<v-alert :type="alert.type" dismissible v-model="alert.alert">
				{{alert.messages}}
			</v-alert>

			<v-card-text>
				<v-form>
					<v-layout row wrap>
						<v-flex xs12>
							<v-text-field 
							prepend-icon="text_format"
							label="Tiêu đề" 
							v-model="editedItem.title"
							:error-messages="errors.collect('title')"
							v-validate="'required|max:254'"
							data-vv-name="title"
							box
							></v-text-field>		
						</v-flex>

						<v-flex xs12>
							<v-text-field 
							prepend-icon="redeem"
							label="Mã khuyến mãi" 
							v-model.trim="editedItem.coupon"
							@input="forceUppercase"
							:error-messages="errors.collect('coupon')"
							v-validate="'required|max:15'"
							data-vv-name="coupon"
							box
							></v-text-field>	
						</v-flex>

						<v-flex xs6>
							<v-menu
							ref="start"
							lazy
							:close-on-content-click="false"
							v-model="start"
							transition="scale-transition"
							offset-y
							full-width
							:nudge-right="40"
							min-width="290px"
							:return-value.sync="editedItem.startedAt"
							>
							<v-text-field
							slot="activator"
							label="Thời gian bắt đầu"
							v-model="editedItem.startedAt"
							prepend-icon="event"
							readonly
							:error-messages="errors.collect('startedAt')"
							v-validate="'required'"
							data-vv-name="startedAt"
							box
							></v-text-field>
							<v-date-picker v-model.trim="editedItem.startedAt" no-title scrollable locale="vi-vn">
								<v-spacer></v-spacer>
								<v-btn flat color="primary" @click="start = false">Cancel</v-btn>
								<v-btn flat color="primary" @click="$refs.start.save(editedItem.startedAt)">OK</v-btn>
							</v-date-picker></v-menu>
						</v-flex>

						<v-flex xs6 class="pl-2">
							<v-menu
							ref="end"
							lazy
							:close-on-content-click="false"
							v-model="end"
							transition="scale-transition"
							offset-y
							full-width
							:nudge-right="40"
							min-width="290px"
							:return-value.sync="editedItem.endedAt"
							>

							<v-text-field
							slot="activator"
							label="Thời gian kết thúc"
							v-model="editedItem.endedAt"
							prepend-icon="event"
							readonly
							:error-messages="errors.collect('endedAt')"
							v-validate="'required'"
							data-vv-name="endedAt"
							box
							></v-text-field>

							<v-date-picker v-model.trim="editedItem.endedAt" no-title scrollable locale="vi-vn">
								<v-spacer></v-spacer>
								<v-btn flat color="primary" @click="end = false">Cancel</v-btn>
								<v-btn flat color="primary" @click="$refs.end.save(editedItem.endedAt)">OK</v-btn>
							</v-date-picker></v-menu>
						</v-flex>
						<v-flex xs12>				
							<v-radio-group v-model="priceOptions" row hide-details @change="changeAttribute('price')">
								<v-flex xs6 >				
									<v-layout align-center>
										<v-radio  :value="false" class="mr-0"></v-radio>
										<v-text-field 
										:disabled="priceOptions"
										label="Giảm giá theo phần trăm" 
										v-model.trim="editedItem.discountPercent"
										:error-messages="errors.collect('percent')"
										v-validate="'required|numeric'"
										data-vv-name="percent"
										suffix="%"
										box
										></v-text-field>	
									</v-layout>			
								</v-flex>

								<v-flex xs6 class="pl-2">
									<v-layout align-center>
										<v-radio :value="true" class="mr-0"></v-radio>
										<v-text-field 
										:disabled="!priceOptions"
										label="Giảm giá theo tiền" 
										v-model.trim="editedItem.discountPrice"
										:error-messages="errors.collect('price')"
										v-validate="'required|numeric'"
										data-vv-name="price"
										suffix="VNĐ"
										box
										></v-text-field>	
									</v-layout>
								</v-flex>
							</v-radio-group>
						</v-flex>

						<v-flex xs6>
							<v-text-field 
							prepend-icon="attach_money"
							label="Giá trị giảm tối đa" 
							v-model.trim="editedItem.maxPrice"
							:error-messages="errors.collect('maxPrice')"
							v-validate="'required|numeric'"
							data-vv-name="maxPrice"
							hint="Số tiền tối đa có thể giảm"
							persistent-hint
							suffix="VNĐ"
							box
							></v-text-field>	
						</v-flex>

						<v-flex xs6 class="pl-2">
							<v-text-field
							prepend-icon="receipt"
							label="Giá trị tổng hóa đơn" 
							v-model.trim="editedItem.totalAmount"
							:error-messages="errors.collect('totalAmount')"
							v-validate="'required|numeric'"
							data-vv-name="totalAmount"
							hint="Điều kiện áp dụng khi hóa đơn đạt giá trị"
							persistent-hint
							suffix="VNĐ"
							box
							></v-text-field>	
						</v-flex>
						<v-flex xs12>
							<v-text-field 
							prepend-icon="subject"
							label="Ghi chú" 
							v-model="editedItem.notes"
							:error-messages="errors.collect('notes')"
							v-validate="'required'"
							data-vv-name="notes"
							box
							></v-text-field>	
						</v-flex>						

						<v-flex xs12>
							<v-text-field 
							prepend-icon="vertical_align_top"
							label="Số lần sử dụng" 
							v-model.trim="editedItem.maxCoupons"
							:disabled="!editedItem.public"
							:error-messages="errors.collect('quantity')"
							v-validate="'required|numeric'"
							data-vv-name="quantity"
							box
							></v-text-field>	
						</v-flex>						

						<v-flex xs12>
							<v-select
							prepend-icon="lens"
							label="Trạng thái"
							chips
							:items="status"
							v-model="editedItem.statusId"
							:error-messages="errors.collect('status')"
							v-validate="'required'"
							data-vv-name="status"
							item-text="coupon_status_name"
							item-value="id"
							box
							@change="changeAttribute('status')"
							>
							<template slot="selection" slot-scope="data">
								<v-chip outline :color="data.item.color" small>
									<v-icon :color="data.item.color">lens</v-icon> {{data.item.name}}
								</v-chip>
							</template>
							<template slot="item" slot-scope="data">
								<template >
									<v-list-tile>
										<v-icon :color="data.item.color">lens</v-icon>
									</v-list-tile>
									<v-list-tile-content>
										<v-list-tile-title v-html="data.item.name"></v-list-tile-title>
									</v-list-tile-content>
								</template>
							</template>
						</v-select>
					</v-flex>

					<v-flex xs6>
						<v-switch
						:label="editedItem.public ? 'Công khai' : 'Cá nhân'"
						v-model="editedItem.public"
						color="primary"
						@change="changeAttribute('public')"></v-switch>
					</v-flex>

					<v-flex xs6>
						<v-switch
						:label="editedItem.actived ? 'Kích hoạt' : 'Chưa kích hoạt'"
						v-model="editedItem.actived"
						color="red"
						@change="changeAttribute('active')"></v-switch>
					</v-flex>

				</v-layout>
			</v-form>
		</v-card-text>
	</v-card>
</v-dialog>
</template>

<script>
	import {ErrorBag, Validator} from 'vee-validate'
	import axios from 'axios'
	import {mapState} from 'vuex'
	import vietnam from 'vee-validate/dist/locale/vi';
	export default {
		data: function() {
			return {
				editedItem: {
					title          : '',
					coupon         : '',
					notes          : '',
					discountPercent: 0,
					discountPrice  : 0,
					maxPrice       : 0,
					totalAmount    : 0,
					maxCoupons     : 1,
					startedAt      : null,
					endedAt        : null,
					actived        : false,
					public         : false,
					statusId       : 1
				},
				defaultItem: {
					title          : '',
					coupon         : '',
					notes          : '',
					discountPercent: 0,
					discountPrice  : 0,
					maxPrice       : 0,
					totalAmount    : 0,
					maxCoupons     : 1,
					startedAt      : null,
					endedAt        : null,
					actived        : false,
					public         : false,
					statusId       : 1
				},
				progress    : false,
				priceOptions: false,
				disabled    : true,
				start       : false,
				end         : false,
				locale      : 'vi'
			}
		},
		methods: {
		//Close Dialog
		close: function() {
			this.editedItem = Object.assign({}, this.defaultItem)	
			this.$store.dispatch('defaultCoupon')
			
			setTimeout(() => {
				this.disabled   = true
				this.errors.clear()		
				
				console.log(this.disabled)
			},300)
		},
		//Action Coupon
		save: function(request) {
			var vm = this
			vm.$validator.validateAll().then(async function(result){
				if(result) {		
					if (vm.editedIndex > -1) {
						//Accept Update Coupon					
						vm.updateCoupon(vm.editedItem).then(response => {
							if(response.status === 200) {
								vm.close()
							}
						})						
					} else {
						//Accept Add Coupon						
						vm.addCoupon().then(response => {
							if(response.status === 201) {
								vm.close()
							}
						})			
					}
				}
			})			
		},
		addCoupon: function() {
			var vm       = this
			const data   = Object.assign({}, vm.editedItem)
			if(!vm.progress) {
				vm.progress = true
				return new Promise((resolve, reject) => {
					setTimeout(() => {
						axios.post('/api/Coupon/AddCoupon', data, {withCredentials: true}).then(response => {
							if(response.status === 201) {
								vm.$store.dispatch('updateCoupon', response.data)
								resolve(response)
							}
						}).catch(error => {
							reject(error)
						}).finally(() => {
							vm.progress = false
						})
					}, 100)					
				}) 
			}
		},
		updateCoupon: function(payload) {			
			var vm       = this 
			const id     = payload.id
			const data   = Object.assign({}, payload)
			const params = {secret: payload.token}
			if(!vm.progress) {
				vm.progress = true
				return new Promise((resolve, reject) => {
					setTimeout(() => {
						axios.post('/api/Coupon/'+ id +'/UpdateCoupon', data, {params, withCredentials: true}).then(response => {
							if(response.status === 200) {
								vm.$store.dispatch('updateCoupon', response.data)
								resolve(response)
							}
						}).catch(error => {reject(error)}).finally(() => {vm.progress = false})
					}, 100)					
				})
			}
		},
		forceUppercase(e) {
			this.editedItem.coupon = e.toUpperCase()
		},
		changeAttribute(value) {
			const _v = new String(value).toLowerCase();
			switch(_v) {
				case 'price': 
				if(!this.priceOptions) {
					this.editedItem.discountPrice   = 0
				} else {
					this.editedItem.discountPercent = 0
				}
				break;
				default: 
				this.disabled = false
			}
		},
		disabledButton: function(val, oldVal, type) {
			switch(type) {
				case 'text': 
				if(this.editedIndex > -1 && val != oldVal && (oldVal != '' || oldVal != null)) {
					this.disabled = false
				} else if(this.editedIndex == -1 && (val != '' || val != null)) {
					this.disabled = false
				}
				break;
				case 'number': 
				if(this.editedIndex > -1 && val != oldVal && oldVal != 0) {
					this.disabled = false
				} else if(this.editedIndex == -1 && val != 0) {
					this.disabled = false
				}
				break;
			}
		} 
	},
	computed: {
		...mapState({
			status     : state => Array.from(state.couponStatusStore.status),
			dialog     : state => state.dialogStore.dialog,
			editedIndex: state => state.couponStore.editedIndex,
			item       : state => state.couponStore.editedItem,
			alert      : state => state.alertStore.alert
		}),
		formTitle () {
			return this.editedIndex === -1 ? 'Thêm mới' : 'Chỉnh sửa'
		}
	},
	watch: {
		item (val) {
			if(val) {
				this.editedItem           = Object.assign(this.editedItem, val)
				this.editedItem.startedAt = val.startedAt.date.slice(0,10)
				this.editedItem.endedAt   = val.endedAt.date.slice(0,10)
				this.disabled             = true
			}			
		},
		'editedItem.title': function(val, oldVal) {
			this.disabledButton(val, oldVal, 'text')
		},
		'editedItem.coupon': function(val, oldVal) {
			this.disabledButton(val, oldVal, 'text')
		},
		'editedItem.notes': function(val, oldVal) {
			this.disabledButton(val, oldVal, 'text')
		},
		'editedItem.discountPercent': function(val, oldVal) {
			this.disabledButton(val, oldVal, 'number')
		},
		'editedItem.maxCoupons': function(val, oldVal) {
			this.disabledButton(val, oldVal, 'number')
		},
		'editedItem.startedAt': function(val, oldVal) {
			this.disabledButton(val, oldVal, 'text')
		},
		'editedItem.endedAt': function(val, oldVal) {
			this.disabledButton(val, oldVal, 'text')
		},
		'editedItem.public': function(val, oldVal) {
			if(!val) {
				this.editedItem.maxCoupons = 1
			} else {
				this.editedItem.maxCoupons = 999
			}
		},
		'editedItem.discountPrice': function(val, oldVal) {
			if(this.editedIndex > -1 && val > 0) {
				this.priceOptions = true
			}
		},
		'editedItem.maxPrice': function(val, oldVal) {
			this.disabledButton(val, oldVal, 'number')
		},
		'editedItem.totalAmount': function(val, oldVal) {
			this.disabledButton(val, oldVal, 'number')
		},
	},
	created() {
		this.$validator.localize(this.locale, {
			messages  :vietnam.messages,
			attributes: {
				title    : 'Tiêu đề',
				coupon   : 'Mã khuyến mãi',
				notes    : 'Ghi chú',
				percent  : 'Phần trăm',
				price    : 'Giá trị',
				quantity : 'Số lần sử dụng',
				startedAt: 'Ngày bắt đầu',
				endedAt  : 'Ngày kết thúc'
			}
		})
		this.$validator.localize(this.locale)
	},
	mounted() {
		this.$store.dispatch('fetchCouponStatus')
	}
}
</script>

<style>

</style>