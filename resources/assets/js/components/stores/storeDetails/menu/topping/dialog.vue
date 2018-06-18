<template>
	<div>
		<v-dialog :value="dialog" persistent scrollable max-width="500px">
			<v-card>
				<v-card-title>
					<span class="headline">{{formTitle}}</span>
				</v-card-title>
				<v-divider></v-divider>
				<v-card-text>
					<form>
						<v-container grid-list-md>
							<v-layout wrap>							
								<v-flex xs12>
									<v-text-field v-model="editedItem.name" label="Tên topping" prepend-icon="title" persistent-hint hint="Tên topping là bắt buộc" required v-validate="'required|max:20'" :error-messages="errors.collect('name')" data-vv-name="name"></v-text-field>
								</v-flex>

								<v-flex xs12>
									<v-text-field v-model="editedItem._name" label="Tên topping tiếng Anh" prepend-icon="language" persistent-hint hint="Tên topping tiếng Anh có thể để trống" v-validate="'max:20'" :error-messages="errors.collect('_name')" data-vv-name="_name"></v-text-field>
								</v-flex>

								<v-flex xs12>
									<v-text-field v-model="editedItem.price" label="Giá" prepend-icon="money" persistent-hint hint="Giá của topping" suffix="vnđ" v-validate="'required|max:10|numeric'" :error-messages="errors.collect('price')" data-vv-name="price" required></v-text-field>
								</v-flex>

							</v-layout>
						</v-container>
						<small class="red--text">*trường bắt buộc</small>
					</form>
				</v-card-text>
				<v-divider class="mt-5"></v-divider>
				<v-card-actions>
					<v-btn color="red" flat @click.native="close">Hủy</v-btn>
					<v-spacer></v-spacer>
					<v-btn :color=" index>-1 ? 'blue darken-1' : 'green darken-1'" :disabled="disabled" :loading="process" class="white--text" @click.native="save">{{index > -1 ? 'Lưu thay đổi' : 'Thêm'}} </v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>
		<v-snackbar auto-height top right :timeout="0" color="error" vertical :value="errors.items.length>0" >
			<span v-for="error in errors.items"> {{error.msg}}</span>
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
				'price': 0
			},
			default: {
				'name': '',
				'_name': null,
				'price': 0
			},
			disabled: true,
			process: false,
		}
	},
	methods: {
		close: async function() {
			this.disabled   = await true
			await this.$store.commit('CLOSE_TOPPING_DIALOG')
			this.editedItem = await Object.assign({}, this.default)
			this.$validator.reset()
		},
		save: async function() {
			var vm     = this
			vm.process = await true
			var data   = vm.editedItem
			// var data   = Object.assign({}, {'name': vm.editedItem.name, '_name': vm.editedItem._name, 'price': parseFloat(vm.editedItem.price)})
			if(vm.index > -1) {
				// ACCEPT EDIT
				await axios.post('/api/GetStore/'+vm.$route.params.storeId+'/Menu/Topping/Edit', data).then(response => {
					if(response.status === 200) {
						vm.$store.commit('UPDATE_TOPPING', response.data)
						vm.close()
					}
				})
			} else {
				//ACCEPT SAVE
				await axios.post('/api/GetStore/'+vm.$route.params.storeId+'/Menu/Topping/Add', data).then(response => {
					if(response.status === 201) {
						vm.$store.commit('UPDATE_TOPPING', response.data)
						vm.close()
					}
				})
			}
			vm.process = false 
		},
	},
	computed: {
		...mapState({
			dialog: state => state.toppingStore.toppingDialog,
			item: state   => state.toppingStore.editedItem,
			index: state  => state.toppingStore.editedIndex
		}),
		formTitle: function() {
			return this.index > -1 ? 'Chỉnh sửa topping' : 'Thêm topping' 
		}
	},
	watch: {
		'item': function(val) {
			Object.assign(this.editedItem, val)	
		},
		'editedItem.name': function(val, oldVal) {
			
			if(this.index > -1) {
				if(oldVal.length > 0) {
					if(val.length > 0) {						
						this.disabled = false
					} else {
						this.disabled = true
					}
				} else {
					this.disabled = true
				}
			} else if(this.index === -1) {
				if(val.length > 0) {
					this.disabled = false
				} else {
					this.disabled = true
				}
			}

		},
		'editedItem._name': function(val, oldVal) {
			//not required
			if(this.index > -1) {
				if(oldVal != null) {						
					this.disabled = false
				}
			}
		},
		'editedItem.price': function(val, oldVal) {
			if(this.index > -1 && oldVal != 0) {
				this.disabled = false
			} else if(this.index == -1 && val != 0) {
				this.disabled = false
			}
		},
	},
	created() {
		const dictionary = {
			vi: {
				attributes: {
					name: 'Topping',
					_name: 'Topping tiếng Anh',
					price: 'Giá'
				}
			}
		};
		Validator.localize(dictionary);
	}
}
</script>