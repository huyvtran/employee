<template>
	<v-card>
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
					<v-flex xs2 :key="0">
						<v-select
						:items="filterActive"
						item-text="name"
						item-value="value"
						v-model="search.active"
						label="Trạng thái kích hoạt"
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

					<v-flex xs2 :key="1">
						<v-select
						:items="filterBan"
						item-text="name"
						item-value="value"
						v-model="search.ban"
						label="Trạng thái khóa"
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
			:loading="loading"
			:headers="headers"
			:search="search.text"
			:items="filterData"
			hide-actions
			class="elevation-1"
			>
			<template slot="items" slot-scope="props">
				<td class="text-xs-center"> 
					<div> {{props.item.id}} </div>
				</td>
				<td>
					<v-avatar>
						<img
						:src="image(props.item.image)"
						alt="John"
						>
					</v-avatar>
				</td>
				<td>
					{{props.item.name}}
				</td>
				<td>
					<div><v-icon size="16" color="red">mail</v-icon>: <span class="font-weight-bold">{{props.item.email}}</span></div>
					<div><v-icon size="16" color="indigo">phone</v-icon>: <span class="font-weight-bold">{{props.item.phone | formatPhone}}</span></div>
				</td>
				<td class="font-italic">{{props.item.address}}</td>
				<td class="text-xs-center">
					<v-tooltip top>
						<v-icon slot="activator" :color="props.item.isActived ? 'green darken-3' : 'red'">{{props.item.isActived ? 'done' : 'close'}}</v-icon> 
						<span>{{props.item.isActived ? 'Đã kích hoạt' : 'Chưa kích hoạt'}}</span>
					</v-tooltip>
				</td>
				<td :class="{ 'green--text text--darken-3' : !props.item.isBanned, 'red--text font-weight-bold' : props.item.isBanned }">
					<v-tooltip top>
						<v-icon slot="activator" :color="props.item.isBanned ? 'red' : 'green darken-3'">{{props.item.isBanned ? 'lock' : 'lock_open'}}</v-icon> 
						<span>{{props.item.isBanned ? 'Bị khóa' : 'Hoạt động'}}</span>
					</v-tooltip>
				</td>

				<td>
					<v-menu bottom left offset-y>
						<v-btn slot="activator" icon class="mx-0">
							<v-icon>more_vert</v-icon>
						</v-btn>
						<v-list>
							<v-list-tile :to="{name: 'CustomerDetail', params: {userId: props.item.id}}" avatar>
								<v-list-tile-avatar>								
									<v-icon class="blue white--text" size="32">account_circle</v-icon>
								</v-list-tile-avatar>
								<v-list-tile-content>
									<v-list-tile-title>Xem thông tin chi tiết</v-list-tile-title>
								</v-list-tile-content>
							</v-list-tile>
							<v-list-tile @click="editItem(props.item)" avatar>
								<v-list-tile-avatar>
									<v-icon class="teal white--text" size="32">edit</v-icon>
								</v-list-tile-avatar>
								<v-list-tile-content>
									<v-list-tile-title>Chỉnh sửa thông tin</v-list-tile-title>
								</v-list-tile-content>
							</v-list-tile>
						</v-list>					
					</v-menu>
				</td>
			</template>
		</v-data-table>
		<v-tooltip left>
			<v-btn slot="activator" fixed bottom right icon  dark color="green darken-2" @click.native="$store.commit('DIALOG_STORE')"><v-icon>add</v-icon></v-btn>
			<span>Thêm mới</span>
		</v-tooltip>

	</v-card-text>
</v-card>
</template>

<script>
import index from '@/mixins/index'
import { mapState } from 'vuex'
export default {
	mixins: [ index ],
	data() {
		return {
			title: 'Danh sách khách hàng',
			headers: [
			{
				text: '#',
				align: 'center',
				value: 'id'
			},
			{ text: 'Hình đại diện', value:'email', align: 'center', sortable:false, width:'120px'},
			{ text: 'Họ tên', value: 'name', align: 'center'},
			{ text: 'Liên hệ', value: 'phone', sortable:false},
			{ text: 'Địa chỉ', value: 'address', align: 'center', sortable: false },
			{ text: 'Kích hoạt', value: 'city_name', sortable: false },
			{ text: 'Khóa', value: 'district_name', sortable: false },
			{ text: 'Tác vụ', sortable: false}
			],
			loading: false,
			search: {
				text: '',
				active: null,
				ban: null,
			},
			filter:false,
			filterActive: [{name: 'Tất cả', value: null}, {name: 'Đã kích hoạt', value: true}, {name: 'Chưa kích hoạt', value: false}],
			filterBan: [{name: 'Tất cả', value: null}, {name: 'Bị khóa', value: true}, {name: 'Hoạt động', value: false}],
		}
	},
	methods: {
		filterAction() {
			if(this.filter) {
				this.search = {
					text: '',
					active: null,
					ban: null,
				}
			}
			this.filter = !this.filter

		},
		filterByActive(list, value) {
			const search = value

			if(search == null) {
				return list
			}

			return list.filter(item => item.isActived === search)
		},
		filterByBan(list, value) {
			const search = value

			if(search == null) {
				return list
			}

			return list.filter(item => item.isBanned === search)
		},
	},
	computed: {
		...mapState({
			customers: state => Array.from(state.userStore.users),
			alert: state 	 => state.userStore.alert,

		}),
		filterData() {
			if(this.customers.length>0) {
				return this.filterByActive(this.filterByBan(this.customers, this.search.ban), this.search.active)
			}

		}
	},
	created() {
		setTimeout(() => {
			this.loading = !this.loading
			this.$store.dispatch('fetchCustomer').finally(() => {
				this.loading = !this.loading
			})
		},100)		
	}
}


</script>