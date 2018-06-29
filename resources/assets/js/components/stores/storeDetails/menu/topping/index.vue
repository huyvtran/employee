<template>
	<div>
		<v-card-title primary-title>
			<v-btn color="green darken-3" class="white--text" @click.native="$store.commit('SHOW_TOPPING_DIALOG')" small round>
				Thêm mới
			</v-btn>

			<v-spacer></v-spacer>

			<v-text-field
			v-model="search.text"
			append-icon="search"
			label="Tìm kiếm topping"
			single-line
			hide-details
			></v-text-field>


		</v-card-title>

		<v-data-table
		:headers="headers"
		:items="items"
		hide-actions
		:search="search.text"
		:loading="loading"
		class="elevation-1"
		>
		<v-progress-linear slot="progress" color="blue" indeterminate></v-progress-linear>
		<template slot="items" slot-scope="props">
			<td class="text-xs-center">{{props.item.id}}</td>
			<td>
				<div><h4>{{ props.item.name }} <span class="body-1" v-if="props.item._name.length > 0 ">({{ props.item._name }})</span></h4> </div>
			</td>			
			<td class="text-xs-center">{{ props.item.price | formatPrice}}</td>
			
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
								<v-list-tile-title>Chỉnh sửa topping</v-list-tile-title>
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
export default {
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
			{
				text: 'Topping',
				align: 'left',
				value: 'name',
			},
			{ text: 'Giá', value: 0, align: 'center' },
			{ text: 'Tác vụ', sortable: false, align: 'center' },
			],
			search: {
				text: ''
			},
			loading: false,
		}
	},
	methods: {
		getTopping: function(id) {
			return new Promise(async (resolve, reject) => {
				var vm = this
				vm.loading = await true
				await this.$store.dispatch('getTopping', id)
				vm.loading = false
			})
		},
		editItem: function(item) {
			this.$store.commit('EDIT_TOPPING', item)
		}
	},
	computed: {
		...mapState({
			items: state => state.toppingStore.toppings
		})
	},
	created() {
		this.getTopping(this.$route.params.storeId)
	}
}
</script>