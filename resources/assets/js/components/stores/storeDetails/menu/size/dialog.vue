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
									<v-text-field v-model="editedItem.name" label="Kích thước" prepend-icon="title" persistent-hint hint="Xác định kích thước sản phẩm" required v-validate="'required|max:20'" :error-messages="errors.collect('name')" data-vv-name="name"></v-text-field>
								</v-flex>

								<v-flex xs12>
									<v-text-field v-model="editedItem._name" label="Kích thước tiếng Anh" prepend-icon="language" persistent-hint hint="Kích thước tiếng Anh có thể để trống" v-validate="'max:20'" :error-messages="errors.collect('_name')" data-vv-name="_name"></v-text-field>
								</v-flex>

								<v-flex xs12>
									<v-text-field v-model="editedItem.price" label="Giá" prepend-icon="money" persistent-hint hint="Kích thước tiếng Anh có thể để trống" suffix="vnđ" v-validate="'max:20'" :error-messages="errors.collect('_name')" data-vv-name="_name"></v-text-field>
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
				'priority': 0,
				'isShowed': true
			},
			default: {
				'name': '',
				'_name': null,
				'priority': 0,
				'isShowed': true
			},
			disabled: true,
			process: false,
			priorities: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
		}
	},
	methods: {
		close: async function() {
			this.disabled   = await true
			await this.$store.commit('CLOSE_CATALOGUE_DIALOG')
			this.editedItem = await Object.assign({}, this.default)
			this.$validator.reset()
		},
		save: async function() {
			var vm     = this
			vm.process = await true
			var data   = vm.editedItem
			if(vm.index > -1) {
				// ACCEPT EDIT
				axios.post('/api/GetStore/'+vm.$route.params.storeId+'/Menu/Catalogue/Edit', data).then(response => {
					if(response.status === 200) {
						vm.$store.commit('UPDATE_CATALOGUE', response.data)
						vm.close()
					}
				})
			}else {
				//ACCEPT SAVE
				axios.post('/api/GetStore/'+vm.$route.params.storeId+'/Menu/Catalogue/Add', data).then(response => {
					if(response.status === 201) {
						vm.$store.commit('UPDATE_CATALOGUE', response.data)
						vm.close()
					}
				})
			}
			vm.process = false 
		},
		changePriority: function() {
			this.disabled = false			
		},
		changeShow: function() {
			this.disabled = false
		}
	},
	computed: {
		...mapState({
			dialog: state => state.sizeStore.sizeDialog,
			item: state   => state.sizeStore.editedItem,
			index: state  => state.sizeStore.editedIndex
		}),
		formTitle: function() {
			return this.index > -1 ? 'Chỉnh sửa kích thước' : 'Thêm kích thước' 
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
	},
	created() {
		const dictionary = {
			vi: {
				attributes: {
					name: 'Kích thước',
					_name: 'Kích thước tiếng Anh'
				}
			}
		};
		Validator.localize(dictionary);
	}
}
</script>