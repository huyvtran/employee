<template>
	<div>
		<v-card-title primary-title>
			<v-btn color="green darken-3" class="white--text" @click.native="$store.commit('SHOW_CATALOGUE_DIALOG')" small round>
				Thêm mới
			</v-btn>

			<v-spacer></v-spacer>

			<v-layout row wrap  > <!-- Filter Start -->	

				<v-flex xs4 :key="0">
					<v-select
					:items="filterShow"
					item-text="name"
					item-value="value"
					v-model="search.show"
					label="Ẩn/Hiện"
					></v-select>
				</v-flex>

				<v-flex xs8 :key="1">
					<v-text-field
					v-model="search.text"
					append-icon="search"
					label="Tìm kiếm danh mục"
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
			<td class="text-xs-center">{{props.item.id}}</td>
			<td>
				<div><h4>{{ props.item.name }} <span class="body-1" v-if="props.item._name != null">({{ props.item._name }})</span></h4> </div>				
			</td>			
			<td class="text-xs-center">{{ props.item.priority}}</td>
			<td :class="{'blue--text': props.item.isShowed, 'red--text': !props.item.isShowed}">{{ props.item.isShowed ? 'Hiện' : 'Ẩn'}}</td>
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
								<v-list-tile-title>Chỉnh sửa danh mục</v-list-tile-title>
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
				text: 'Tên danh mục',
				align: 'left',
				value: 'name',
			},
			{ text: 'Mức ưu tiên', value: 'priority', align: 'center' },
			{ text: 'Ẩn/Hiện', value: '_name', sortable: false },
			{ text: 'Tác vụ', sortable: false, align: 'center' },
			],
			search: {
				text: '',
				show: null,
			},
			loading: false,
			filterShow: [{name: 'Tất cả', value: null}, {name: 'Hiện', value: true}, {name: 'Ẩn', value: false}],
		}
	},
	methods: {
		getCatalogue: function(id) {
			return new Promise(async (resolve, reject) => {
				var vm = this
				vm.loading = await true
				await this.$store.dispatch('getCatalogue', id)
				vm.loading = false
			})
		},
		editItem: function(item) {
			this.$store.commit('EDIT_CATALOGUE', item)
		},
		filterByShow(list, value) {
			const search = value

			if(search == null) {
				return list
			}

			return list.filter(item => item.isShowed === search)
		},
	},
	computed: {
		...mapState({
			items: state => state.catalogueStore.catalogues
		}),
		filterData() {
			if(this.items.length>0) {
				return this.filterByShow(this.items, this.search.show)
			}
		}
	},
	created() {
		this.getCatalogue(this.$route.params.storeId)
	}
}
</script>