<template>
	<div>
		<v-dialog :value="dialog" persistent scrollable max-width="500px">
			<v-card>
				<v-progress-linear :indeterminate="true" class="my-0" v-if="loading"></v-progress-linear>
				<v-card-title>
					<span class="headline">{{formTitle}}</span>
				</v-card-title>
				<v-divider></v-divider>

				<v-card-text>
					<form v-if="!loading">
						<v-container grid-list-md>
							<v-layout wrap>							

								<v-flex xs12>
									<v-select
									:items="catalogues"
									v-model="editedItem.catalogue_id"
									item-text="name"
									item-value="id"
									label="Danh mục"
									prepend-icon="category"
									persistent-hint 
									hint="Chọn danh mục cho món"
									v-validate="'required'"
									data-vv-name="catalogue"
									:error-messages="errors.collect('catalogue')"
									@change="changeAttribute('catalogue')"
									autocomplete
									required></v-select>
								</v-flex>

								<v-flex xs12>
									<v-text-field v-model="editedItem.name" label="Tên món" prepend-icon="title" persistent-hint hint="Tên món bắt buộc" required v-validate="'required|max:254'" :error-messages="errors.collect('name')" data-vv-name="name"></v-text-field>
								</v-flex>

								<v-flex xs12>
									<v-text-field v-model="editedItem._name" label="Tên món tiếng Anh" prepend-icon="language" persistent-hint hint="Tên món tiếng Anh có thể để trống nếu không biết" v-validate="'max:254'" :error-messages="errors.collect('_name')" data-vv-name="_name"></v-text-field>
								</v-flex>							

								<v-flex xs12>
									<v-layout row wrap>
										<v-flex xs12>
											<v-radio-group v-model="editedItem.haveSize" row label="Size">
												<v-radio color="primary" label="Có" :value="true" @change="changeAttribute('size')"></v-radio>
												<v-radio color="primary" label="Không" :value="false" @change="changeAttribute('size')"></v-radio>
											</v-radio-group>									
										</v-flex>

										<v-flex xs12>
											<v-text-field v-model.number="editedItem.price" label="Giá" prepend-icon="money" persistent-hint :disabled="editedItem.haveSize" hint="Giá giành cho món không có size" suffix="vnđ" v-validate="{required: !editedItem.haveSize, numeric:true, min:1, max:8}" :error-messages="errors.collect('price')" data-vv-name="price" :required="!editedItem.haveSize"></v-text-field>
										</v-flex>

										<v-flex xs4  v-for="size in editedItem.sizes" :key="size.id">
											<v-text-field prepend-icon="money" :disabled="!editedItem.haveSize" :label="`Giá size ${size.name}`" v-model.number="size.price" suffix="vnđ" :hint="`Giá giành cho món có ${size.name}`" persistent-hint :required="editedItem.haveSize" v-validate="{required:true, numeric:true, min:1, max:8}" :error-messages="errors.collect('size._name')" :data-vv-name="size._name"></v-text-field> 
										</v-flex>
									</v-layout>
								</v-flex>

								<v-flex xs12>
									<v-radio-group v-model="editedItem.haveTopping" row mandatory label="Topping">
										<v-radio color="primary" label="Có" :value="true" @change="changeAttribute('topping')" ></v-radio>
										<v-radio color="primary" label="Không" :value="false" @change="changeAttribute('topping')"></v-radio>
									</v-radio-group>									
								</v-flex>

								<v-flex xs12>
									<v-text-field 
									label="Mô tả" 
									prepend-icon="description"
									v-model="editedItem.description"
									:error-messages="errors.collect('description')"
									v-validate="'max:254'"
									data-vv-name="description"
									required
									></v-text-field>
								</v-flex>

								<v-flex xs12>
									<v-select
									:items="priorities"
									v-model="editedItem.priority"
									label="Mức độ ưu tiên"
									prepend-icon="swap_vert"
									persistent-hint 
									hint="Mức độ ưu tiên cao nhất sẽ hiển thị đầu tiên"
									@change="changeAttribute('priority')"
									required></v-select>
								</v-flex>

								<v-flex xs12>
									<v-select
									label="Trạng thái"
									:items="status"
									prepend-icon="lens"
									v-model="editedItem.status_id"
									@change="changeAttribute('status')"
									:error-messages="errors.collect('status')"
									autocomplete
									v-validate="'required|numeric'"
									data-vv-name="status"
									item-value="id"
									item-text="name"
									required
									></v-select>
								</v-flex>
							</v-layout>
						</v-container>
						<small class="red--text">*trường bắt buộc</small>
					</form>
				</v-card-text>
				<v-divider></v-divider>
				<v-card-actions>
					<v-btn color="red" flat @click.native="close">Hủy</v-btn>
					<v-spacer></v-spacer>
					<v-btn :color=" index>-1 ? 'blue darken-1' : 'green darken-1'" :disabled="disabled" :loading="process" class="white--text" @click.native="save">{{index > -1 ? 'Lưu thay đổi' : 'Thêm'}} </v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>
		<v-snackbar auto-height top right :timeout="0" color="error" vertical :value="errors.items.length>0 || alert.show && alert.index == 1 && alert.name == $route.name " >
			<span v-for="error in errors.items" v-if="!alert.show"> {{error.msg}}</span>
			<span v-if="alert.show">{{alert.message}}</span>
		</v-snackbar>
	</div>
</template>

<script>
import { Validator } from 'vee-validate'
import axios from 'axios'
import {mapState} from 'vuex'
export default {
	data() {
		return {
			editedItem: {
				'name': '',
				'_name': null,
				'price': 0,
				'haveSize': false,
				'haveTopping': false,
				'sizes': [],
				'priority': 0,
				'status_id': 1,
				'description': null,
				'catalogue_id': null,
				'image': null
			},
			default: {
				'name': '',
				'_name': null,
				'price': 0,
				'haveSize': false,
				'haveTopping': false,
				'sizes': [],
				'priority': 0,
				'status_id': 1,
				'description': null,
				'catalogue_id': null,
				'image': null
			},
			loading: false,
			disabled: true,
			process: false,
			priorities: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
		}
	},
	methods: {
		//CLOSE DIALOG
		close: async function() {
			this.disabled   = await true
			this.editedItem = await Object.assign({}, this.default)	
			await this.$store.commit('CLOSE_PRODUCT_DIALOG')
			this.$validator.reset()
		},
		save: async function() {
			var vm     = this
			vm.process = await true
			var data   = vm.editedItem
			if(vm.index > -1) {
				
				if(vm.editedItem.haveSize) {
					data.price = 0
				}
				
				// ACCEPT EDIT
				vm.$validator.validateAll().then(async function(result){
					if(result) {
						await axios.post('/api/GetStore/'+vm.$route.params.storeId+'/Menu/Product/Edit', data).then(async (response) => {
							if(response.status === 200) {
								await vm.$store.commit('UPDATE_PRODUCT', response.data)
								await vm.close()
							}
						}).catch(error => {
							if(error.response.status === 422) {
								vm.$store.dispatch('alert', {name: vm.$route.name, index:1, show:true, message: error.response.data.message, type: 'error', close:true})
							}							
						})
					}					
				})
			} else {

				// ACCEPT SAVE
				vm.$validator.validateAll().then(async function(result){
					if(result) {
						await axios.post('/api/GetStore/'+vm.$route.params.storeId+'/Menu/Product/Add', data).then(async (response) => {
							if(response.status === 201) {
								await vm.$store.commit('UPDATE_PRODUCT', response.data)
								await vm.close()
							}
						}).catch(error => {
							if(error.response.status === 422) {
								vm.$store.dispatch('alert', {name: vm.$route.name, index:1, show:true, message: error.response.data.message, type: 'error', close:true})
							}							
						})
					}					
				})
			}
			vm.process = false 
		},
		//CHANGE ATTRIBUTE TO DISABLE FALSE
		changeAttribute: function(attr) {
			var value = new String(attr).toLowerCase()
			switch(value) {
				case 'size':
				return this.disabled = false
				break
				case 'topping':
				return this.disabled = false
				break
				case 'status':
				return this.disabled = false
				break
				case 'catalogue':
				return this.disabled = false
				break
				case 'priority':
				return this.disabled = false
				break
			}
		},
	},
	computed: {
		...mapState({
			dialog: state     => state.productStore.productDialog,
			item: state       => state.productStore.editedItem,
			index: state      => state.productStore.editedIndex,
			status: state     => state.productStore.status,
			catalogues: state => state.catalogueStore.catalogues,
			sizes: state      => state.sizeStore.sizes,
			alert: state 	  => state.alertStore.alert
		}),
		formTitle: function() {
			return this.index > -1 ? 'Chỉnh sửa món' : 'Thêm món' 
		}
	},
	watch: {
		'item': function(val) {
			if(val != null) {
				this.editedItem = Object.assign(this.editedItem, val)
				if(!val.haveSize) {
					var sizes = []
					this.sizes.forEach(item => {
						sizes.push({id: item.id, 'name': item.name, '_name': item._name, 'price': 0})
					})
					this.editedItem.sizes = sizes
				}
			}
		},
		'dialog': async function(val) {
			if(val) {
				this.loading = await true
				await this.$store.dispatch('getCatalogue', this.$route.params.storeId)
				
				if(this.index === -1) {
					await this.sizes.forEach(item => {
						this.editedItem.sizes.push({id: item.id, 'name': item.name, '_name': item._name, 'price': 0})
					})
				} 

				this.loading = false
			}
		},
		'sizes': function(val) {
			if(val.length > 0) {

			}
		},
		'editedItem.name': function(val, oldVal) {
			if(this.editedIndex > -1 && oldVal != '') {
				this.disabled = false
			} else if(this.editedIndex == -1 && val != '') {
				this.disabled = false
			}
		},
		'editedItem._name': function(val, oldVal) {
			//not required
			if(this.index > -1) {
				if(oldVal != null) {
					if(val.length==0) {
						this.editedItem._name = null
					}						
					this.disabled = false
				}
			}
		},
	},
	created() {
		this.$store.dispatch('getSize', this.$route.params.storeId)
		this.$store.dispatch('getProductStatus')

		const dictionary = {
			vi: {
				attributes: {
					catalogue: 'Danh mục',
					name: 'Tên món',
					_name: 'Tên món tiếng Anh',
					price: 'Giá',
					description: 'Mô tả',
					status: 'Trạng thái'
				}
			}
		};
		Validator.localize(dictionary);
	}
}
</script>