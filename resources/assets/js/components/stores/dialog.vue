<template>
	<v-dialog persistent v-model="dialog" scrollable fullscreen transition="scale-transition" origin="center center" :overlay="false">
		<v-card color="grey lighten-2">
			<v-progress-linear :indeterminate="true" class="my-0" v-if="loading"></v-progress-linear>
			<v-toolbar dark color="primary">
				<v-btn icon @click.native="close" dark>
					<v-icon>close</v-icon>
				</v-btn>
				<v-toolbar-title><span class="headline">{{ formTitle }}</span></v-toolbar-title>
				<v-spacer></v-spacer>
				<v-toolbar-items>
					<v-btn  :disabled="disabled" :loading="process" @click.native="save" flat dark>{{editedIndex > -1 ? 'Lưu thay đổi' : 'Hoàn thành'}}</v-btn>
				</v-toolbar-items>
			</v-toolbar>
			<v-card-text>
				<v-container fluid grid-list-md>
					<v-layout row wrap :class="{'justify-center': editedIndex > -1}">
						<v-flex d-flex xs12 sm6 md6 v-if="editedIndex===-1">
							<v-card>
								<v-card-title primary class="title">Thông tin chủ sở hữu/quản lý</v-card-title>
								<v-card-text>
									<form>										
										<v-subheader>
											Thông tin người dùng
										</v-subheader>
										<v-container>		
											<v-text-field
											prepend-icon="person" 
											label="Tên chủ sở hữu/Quản lý" 
											v-model="editedItem.user.name"
											v-validate="'required'"
											:error-messages="errors.collect('name')"
											data-vv-name="name"
											data-vv-scope="user"
											></v-text-field>

											<v-text-field
											prepend-icon="email" 
											label="Email" 
											v-model="editedItem.user.email"
											v-validate="'required|email'"
											:error-messages="errors.collect('email')"
											data-vv-name="email"
											data-vv-scope="user"></v-text-field>

											<v-text-field
											prepend-icon="lock" 
											label="Mật khẩu" 
											v-model="editedItem.user.password" 
											type="password"
											v-validate="'required|min:8|max:36'"
											:error-messages="errors.collect('password')"
											data-vv-name="password"
											data-vv-scope="user"></v-text-field>

											<v-text-field 
											prepend-icon="lock" 
											label="Xác nhận mật khẩu" 
											v-model="editedItem.user.confirm_password" 
											type="password"
											v-validate="{required:true, is:editedItem.user.password}"
											data-vv-name="confirmPassword"
											:error-messages="errors.collect('confirmPassword')"
											data-vv-scope="user"></v-text-field>

											<v-radio-group v-model="editedItem.user.gender" row v-validate="'required|numeric'"
											:error-messages="errors.collect('gender')"
											data-vv-name="gender" 
											data-vv-scope="user">
											<v-radio label="Nam" :value='1' color="primary" @change="changeAttribute"></v-radio>
											<v-radio label="Nữ" :value='0' color="primary" @change="changeAttribute"></v-radio>	</v-radio-group>

											<v-menu
											ref="menu"
											lazy
											:close-on-content-click="false"
											v-model="menu"
											transition="scale-transition"
											offset-y
											full-width
											:nudge-right="40"
											min-width="290px"
											>
											<v-text-field
											slot="activator"
											label="Ngày sinh"
											v-model="editedItem.user.birthday"
											prepend-icon="event"
											v-validate="'required'"
											:error-messages="errors.collect('birthday')"
											data-vv-name="birthday"
											data-vv-scope="user"
											readonly
											></v-text-field>
											<v-date-picker
											locale="vi-vn"
											ref="picker"
											v-model="editedItem.user.birthday"
											min="1950-01-01"
											:max="new Date().toISOString().substr(0, 10)"
											></v-date-picker>
										</v-menu>

									</v-container>

									<v-divider></v-divider>

									<v-subheader>Liên hệ</v-subheader>
									<v-container>
										<v-text-field 
										mask="(####) ### - ####"
										prepend-icon="phone"
										label="Số điện thoại" 
										v-model="editedItem.user.phone"
										v-validate="'required|numeric|min:10|max:11'"
										:error-messages="errors.collect('phone')"
										data-vv-name="phone"
										data-vv-scope="user"></v-text-field>

										<v-text-field
										prepend-icon="place" 
										label="Địa chỉ" 
										v-model="editedItem.user.address"
										v-validate="'required|max:255'"
										:error-messages="errors.collect('address')"
										data-vv-name="address"
										data-vv-scope="user"></v-text-field>
									</v-container>

									<v-divider></v-divider>

									<v-subheader>Cài đặt</v-subheader>

									<v-container>
										<v-switch label="Kích hoạt tài khoản" v-model="editedItem.user.isActived" color="primary" @change="changeAttribute"></v-switch>
									</v-container>
								</form>
							</v-card-text>
						</v-card>
					</v-flex>
					<v-flex d-flex xs12 sm6 md6 >
						<v-card>
							<v-card-title primary class="title">Thông tin cửa hàng</v-card-title>
							<v-card-text>
								<form>
									<v-subheader>Thông tin cửa hàng</v-subheader>
									<v-container>
										<v-select
										:items="types"
										v-model="editedItem.store.type_id"
										label="Loại cửa hàng"
										item-text="name"
										item-value="id"
										prepend-icon="category"
										v-validate="'required'"
										:error-messages="errors.collect('type')"
										data-vv-name="type"
										data-vv-scope="store"
										@change="changeAttribute"
										></v-select>

										<v-text-field
										prepend-icon="store" 
										label="Tên cửa hàng" 
										v-model="editedItem.store.name"
										v-validate="'required'"
										:error-messages="errors.collect('storeName')"
										data-vv-name="storeName"
										data-vv-scope="store"></v-text-field>

										<v-text-field
										mask="(####) ### - ####"
										prepend-icon="phone" 
										label="Số điện thoại cửa hàng" 
										v-model="editedItem.store.phone"v-validate="'required|numeric|min:10|max:11'"
										:error-messages="errors.collect('storePhone')"
										data-vv-name="storePhone"
										data-vv-scope="store"></v-text-field>

										<v-text-field
										prepend-icon="access_time" 
										label="Thời gian chuẩn bị" 
										v-model="editedItem.store.preparetime"
										suffix="phút"
										v-validate="'required|numeric|max:2'"
										:error-messages="errors.collect('prepareTime')"
										data-vv-name="prepareTime"
										data-vv-scope="store"></v-text-field>

										<v-select
										:items="cities"
										v-model="editedItem.store.city_id"
										label="Thành phố/Tỉnh"
										item-text="name"
										item-value="id"
										prepend-icon="map"
										@change="changeCity"
										v-validate="'required'"
										:error-messages="errors.collect('city')"
										data-vv-name="city"
										data-vv-scope="store"
										></v-select>

										<v-select
										v-if="editedItem.store.city_id != null"
										:items="districts"
										v-model="editedItem.store.district_id"
										label="Quận/Huyện"
										item-text="name"
										item-value="id"
										prepend-icon="streetview"
										v-validate="'required'"
										:error-messages="errors.collect('district')"
										data-vv-name="district"
										data-vv-scope="store"
										@change="changeAttribute"
										></v-select>

										<v-text-field
										id="auto-complete"
										ref="autocomplete"
										prepend-icon="place" 
										label="Địa chỉ cửa hàng" 
										v-model="editedItem.store.address"
										v-validate="'required|max:255'"
										:error-messages="errors.collect('storeAddress')"
										data-vv-name="storeAddress"
										data-vv-scope="store"></v-text-field>

										<GmapMap v-if="dialog" :center="{lat:editedItem.store.lat, lng:editedItem.store.lng}" :zoom="15" map-type-id="terrain" style="width: 100%; height: 300px">
											<GmapMarker	:position="{lat:editedItem.store.lat, lng:editedItem.store.lng}"
											:clickable="true" :draggable="true" @dragend="updateCenter"/>
										</GmapMap>
									</v-container>

									<v-divider></v-divider>

									<v-subheader>Cài đặt</v-subheader>

									<v-container>
										<v-flex xs12>
											<v-select
											:items="priorities"
											v-model="editedItem.store.priority"
											label="Mức độ ưu tiên"
											prepend-icon="swap_vert"
											v-validate="'required'"
											@change="changeAttribute"
											></v-select>
										</v-flex>		

										<v-flex xs12>
											<v-switch label="Ẩn/Hiện" v-model="editedItem.store.isShowed" color="primary" @change="changeAttribute"></v-switch>
										</v-flex>
										
										<v-flex xs12>
											<v-checkbox
											label="Xác nhận hợp tác"
											color="primary"
											v-model="editedItem.store.isVerified"
											@change="changeAttribute"
											></v-checkbox>
										</v-flex>	

										<v-flex xs12>
											<v-text-field
											prepend-icon="attach_money" 
											label="Chiết khấu hoa hồng" 
											v-model="editedItem.store.discount"
											v-validate="{required: editedItem.store.isVerified, numeric:true, max: 2}"
											:error-messages="errors.collect('discount')"
											data-vv-name="discount"
											data-vv-scope="store"
											suffix="%"></v-text-field>
										</v-flex>								

									</v-container>
								</form>
							</v-card-text>
						</v-card>
					</v-flex>
				</v-layout>
			</v-container>
		</v-card-text>
	</v-card>
	<v-snackbar auto-height top right :timeout="0" color="error" vertical :value="errors.items.length>0" >
		<span v-for="error in errors.items"> {{error.msg}}</span>
	</v-snackbar>
</v-dialog>
</template>

<script>
import UploadImage from '@/components/template/uploadImage'
import {mapState} from 'vuex'
import { Validator } from 'vee-validate';
import {getLocation} from '@/utils/index.js'
export default {
	components: {
		'vue-image': UploadImage
	},
	data() {
		return {
			editedItem: {
				user: {
					name: '',
					email: '',
					password:'',
					confirm_password:'',
					gender: 1,
					birthday: null,
					phone:'',
					address: '',
					lat:'',
					lng:'',
					role_id: 4,
					isActived: false,
				},
				store: {
					type_id: null,
					name: '',
					phone: '',
					preparetime: '',
					city_id: null,
					district_id: null,
					address: '',
					lat: 10.0452,
					lng: 105.7469,
					discount:0,
					priority:0,
					isShowed: false,
					isVerified: false,
				}
			},
			defaultItem: {
				user: {
					name: '',
					email: '',
					password:'',
					confirm_password:'',
					gender: 1,
					birthday: null,
					phone:'',
					address: '',
					lat:'',
					lng:'',
					role_id: 4,
					isActived: false,	
				},
				store: {
					type_id: null,
					name: '',
					phone: '',
					preparetime: '',
					city_id: null,
					district_id: null,
					address: '',
					lat: 10.0452,
					lng: 105.7469,
					discount:0,	
					priority:0,
					isShowed: false,
					isVerified: false,		
				}
			},
			process:false,
			loading: false,
			priorities: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
			disabled: true,
			menu:false,
			flag_store: false,
			imageUrl:null
		}
	},
	methods: {
		//AUTOCOMPLETE PLACE
		autoComplete() {
			var vm           = this
			var input        = document.getElementById('auto-complete')
			var autocomplete = new google.maps.places.Autocomplete(input)
			autocomplete.addListener('place_changed', function() {
				var place = autocomplete.getPlace()			
				if(!place.geometry) {
					var geocoder = new google.maps.Geocoder();
					geocoder.geocode({address: input.value}, function(results, status) {
						if(status === 'OK') {
							vm.editedItem.store.address = results[0].formatted_address
							vm.editedItem.store.lat     = results[0].geometry.location.lat()
							vm.editedItem.store.lng     = results[0].geometry.location.lng()
						}
					})
					return
				}
				vm.setPlace(place)
			})
		},
		//SET PLACE
		setPlace(place) {
			this.place = place
			if(this.place) {
				this.editedItem.store.address = this.place.formatted_address
				this.editedItem.store.lat     = this.place.geometry.location.lat()
				this.editedItem.store.lng     = this.place.geometry.location.lng()
			}
		},
		//Close Dialog
		close: async function() {
			this.editedItem = await this.defaultItem
			this.$validator.reset()
			this.disabled = await true
			this.$store.commit('DIALOG_STORE_CLOSE')

		},
		//Accept Update Store
		save: async function(request) {
			var vm = this
			vm.process = await true
			if (vm.editedIndex > -1) {
				//Accept Edit Store
				vm.$validator.validateAll('store').then(async function(result){
					if(result) {

						vm.$store.dispatch('updateStore', vm.editedItem.store).then(response => {
							if(response.status == 200) {
								vm.close()
							}
						}).catch(errors => {
							if(errors.response.status == 422) {

							}
						})	

					}
				}).finally(() => {
					vm.process = false
				})
			} else {
				//Accept Add Store
				vm.$validator.validateAll('user').then(async (result) => {
					if(result) {			

						vm.$validator.validateAll('store').then(async(resultStore) => {
							
							if(resultStore) {
								
								await getLocation(vm.editedItem.user.address).then(response => {
									vm.editedItem.user.address = response[0].formatted_address
									vm.editedItem.user.lat     = response[0].geometry.location.lat()
									vm.editedItem.user.lng     = response[0].geometry.location.lng()	
								})

								vm.$store.dispatch('addStore', vm.editedItem).then(response => {
									if(response.status == 201) {
										
										vm.close()

										vm.$store.dispatch('alert', {name: vm.$route.name, index:0, alert: {message: response.data.message, type: 'success'}})
									}
								}).catch(errors => {
									if(errors.response.status == 422) {
										vm.$store.dispatch('alert', {name: vm.$route.name, index:1, alert: {message: response.data.message, type: 'success'}})
									}
								})
							}
						})	
					}
				}).finally(() => {
					vm.process = false
				})				
			}
			
		},
		//Change city get district
		changeCity: function(id) {
			this.districts = this.$store.getters.getDistrictByCityId(id)
		},
		//Get image 
		getImage(value) {
			this.disabled = false
			this.editedItem.store.avatar = value
		},
		updateCenter(center) {
			this.editedItem.store.lat = center.latLng.lat()
			this.editedItem.store.lng = center.latLng.lng()
		},
		//CHANGE ATTRIBUTE TO DISABLE FALSE
		changeAttribute: function(attr) {
			this.disabled = false
		},
	},
	computed: {
		...mapState({
			dialog: state      => state.storeStore.dialog,
			editedIndex: state => state.storeStore.editedIndex,
			item: state        => state.storeStore.editedItem,
			alert: state       => state.storeStore.alert,
			roles: state       => Array.from(state.roleStore.roles),
			cities: state      => Array.from(state.cityStore.cities),
			types: state       => Array.from(state.typeStore.types),
			activities: state  => Array.from(state.activityStore.activities)
		}),
		formTitle () {
			return this.editedIndex === -1 ? 'Thêm mới' : 'Chỉnh sửa'
		}
	},
	watch: {
		menu (val) {
			val && this.$nextTick(() => (this.$refs.picker.activePicker = 'YEAR'))
		},
		item (val) {
			Object.assign(this.editedItem.store, val)	
		},
		dialog: async function(val, oldVal) {
			this.loading = await true
			if(val) {				
				await this.autoComplete()
			}
			this.loading = false
		},
		'editedItem.store.avatar': function(val, oldVal) {
			if(this.editedIndex >-1 && oldVal != null && val != null) {
				this.disabled = false
			} else if(this.editedIndex == -1 && val != null) {
				this.disabled = false
			}
		}, 
		'editedItem.store.name': function(val, oldVal) {
			if(this.editedIndex > -1 && oldVal != '') {
				this.disabled = false
			} else if(this.editedIndex == -1 && val != '') {
				this.disabled = false
			}
		},
		'editedItem.store.phone': function(val, oldVal) {
			if(this.editedIndex > -1 && oldVal != '') {
				this.disabled = false
			} else if(this.editedIndex == -1 && val != '') {
				this.disabled = false
			}
		}, 
		'editedItem.store.preparetime': function(val, oldVal) {
			if(this.editedIndex > -1 && oldVal != '') {
				this.disabled = false
			} else if(this.editedIndex == -1 && val != '') {
				this.disabled = false
			}
		}, 
		'editedItem.store.city_id': function(val, oldVal) {
			this.districts = this.$store.getters.getDistrictByCityId(val)
			if(this.editedIndex > -1 && oldVal != null) {
				this.disabled = false
			} else if(this.editedIndex == -1 && val != null) {
				this.disabled = false
			}
		},
		'editedItem.store.district_id': function(val, oldVal) {
			if(this.editedIndex > -1 && oldVal != null) {
				this.disabled = false
			} else if(this.editedIndex == -1 && val != null) {
				this.disabled = false
			}
		}, 
		'editedItem.store.priority': function(val, oldVal) {
			if(val) {
				this.disabled = false
			}
		},
		'editedItem.store.address': function(val, oldVal) {
			if(this.editedIndex > -1 && oldVal != '') {
				this.disabled = false
			} else if(this.editedIndex == -1 && val != '') {
				this.disabled = false
			}
		}, 
		'editedItem.user.name': function(val, oldVal) {
			if(this.editedIndex > -1 && oldVal != '') {
				this.disabled = false
			} else if(this.editedIndex == -1 && val != '') {
				this.disabled = false
			}
		},
		'editedItem.user.email': function(val, oldVal) {
			if(this.editedIndex > -1 && oldVal != '') {
				this.disabled = false
			} else if(this.editedIndex == -1 && val != '') {
				this.disabled = false
			}
		}, 	
		'editedItem.user.password': function(val, oldVal) {
			if(this.editedIndex > -1 && oldVal != '') {
				this.disabled = false
			} else if(this.editedIndex == -1 && val != '') {
				this.disabled = false
			}
		}, 
		'editedItem.user.confirm_password': function(val, oldVal) {
			if(this.editedIndex > -1 && oldVal != '') {
				this.disabled = false
			} else if(this.editedIndex == -1 && val != '') {
				this.disabled = false
			}
		}, 
		'editedItem.user.gender': function(val, oldVal) {
			if(this.editedIndex > -1 && oldVal != 1) {
				this.disabled = false
			} else if(this.editedIndex == -1 && val != 1) {
				this.disabled = false
			}
		},
		'editedItem.user.birthday': function(val, oldVal) {
			if(this.editedIndex > -1 && oldVal != null) {
				this.disabled = false
			} else if(this.editedIndex == -1 && val != null) {
				this.disabled = false
			}
		},
		'editedItem.user.phone': function(val, oldVal) {
			if(this.editedIndex > -1 && oldVal != '') {
				this.disabled = false
			} else if(this.editedIndex == -1 && val != '') {
				this.disabled = false
			}
		},
		'editedItem.user.address': function(val, oldVal) {
			if(this.editedIndex > -1 && oldVal != '') {
				this.disabled = false
			} else if(this.editedIndex == -1 && val != '') {
				this.disabled = false
			}
		},

	},
	created: function() {
		this.$store.dispatch('fetchType').then(response => {
			if(response.status == 200) {
				this.$store.dispatch('fetchCity')
				this.$store.dispatch('fetchDistrict')
			}
		})
	}
}
</script>