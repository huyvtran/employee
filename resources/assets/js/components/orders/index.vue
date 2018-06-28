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
			
		<!-- Filter start-->
		<v-container v-show="filter" fluid grid-list-md transition="slide-y-transition">
			<v-layout row wrap  >
				<v-flex xs3>
					<v-select
					auto
					prepend-icon="lens"
					:items="statusFilters"
					v-model="editedItem.statusId"
					item-value="id"
					item-text="name"
					label="Trạng thái"
					></v-select>
				</v-flex>

				<v-flex xs3>						
					<v-menu
					ref="fromDialog"
					:close-on-content-click="false"
					v-model="fromModal"
					:nudge-right="40"
					lazy
					transition="scale-transition"
					offset-y
					full-width
					max-width="290px"
					min-width="290px">
					<v-text-field
					slot="activator"
					label="Từ ngày"
					v-model="fromDateString"
					prepend-icon="event"
					readonly></v-text-field>
					<v-date-picker locale="vn-vi" :show-current="false" v-model="editedItem.fromDate" no-title @input="$refs.fromDialog.save(editedItem.fromDate)"></v-date-picker></v-menu>
				</v-flex>

				<v-flex xs3>
					<v-menu
					ref="toDialog"
					:close-on-content-click="false"
					v-model="toModal"
					:nudge-right="40"
					lazy
					transition="scale-transition"
					offset-y
					full-width
					max-width="290px"
					min-width="290px"
					>

					<v-text-field
					slot="activator"
					label="Đến ngày"
					v-model="toDateString"
					prepend-icon="event"
					readonly
					></v-text-field>
					<v-date-picker locale="vn-vi" :show-current="false" v-model="editedItem.toDate" no-title @input="$refs.toDialog.save(editedItem.toDate)"></v-date-picker></v-menu>
				</v-flex>

				<v-flex xs1>
					<v-btn small color="primary" outline @click="fetchOrders" :loading="loading">
						<v-icon>search</v-icon>
					</v-btn>		
				</v-flex>
			</v-layout>
		</v-container>	<!-- Filter End -->	

		<!-- Table -->
		<v-data-table :headers="headers" :items="orders" class="elevation-1" :loading="loading" hide-actions >
			<template slot="headerCell" slot-scope="props">
				<span class="red--text text--accent-3">
					{{ props.header.text }}
				</span>
			</template>

			<template slot="items" slot-scope="props">

				<td>{{ props.index + 1 }}</td>

				<td><strong>{{ props.item.id }}</strong></td>

				<td>
					<div><strong>{{ props.item.store.name }}</strong></div>
					<div>{{ props.item.store.address }}</div>
				</td>

				<td>
					<div>Ngày đặt: {{ props.item.bookingDate }}</div>
					<div><strong>Ngày giao: {{ props.item.receiveDate | formatDate }} {{ props.item.receiveTime }}</strong></div>
				</td>

				<td>
					<div v-if="props.item.employee != null">{{ props.item.employee.name }}</div>
					<div v-if="props.item.shipper != null">{{ props.item.shipper.name }}</div>
				</td>

				<td>
					<div><strong>{{ props.item.total | formatPrice}}</strong></div>
					<div class="primary--text"><strong>{{ props.item.payment.paymentMethod}}</strong></div>
				</td>

				<td class="text-md-left">
					<div>
						<strong :class="{'success--text': props.item.statusId == statusId('Thành công') || props.item.statusId == statusId('Xác nhận'), 'error--text': props.item.statusId == statusId('Hủy'), 'yellow--text text--accent-4': props.item.statusId == statusId('Chờ xử lý') || props.item.statusId == statusId('Đang xử lý')}">
							{{props.item.statusName}}
						</strong>
					</div>

					<div class="success--text" v-if="props.item.statusId == statusId('Thành công')"><strong>{{props.item.statusName}}</strong></div>
					<div v-else-if="props.item.statusId != orderStatus('Thành công')">
					</div>
				</td>

				<td>
					<v-tooltip top>									
						<v-btn slot="activator" icon small :to="{name: 'OrderDetail', params: {orderId: props.item.id}}">		
							<v-icon color="primary">visibility</v-icon>
						</v-btn>
						<span>Xem chi tiết</span>
					</v-tooltip>
				</td>

			</template>

			<template slot="pageText" slot-scope="{ pageStart, pageStop }">
				Từ {{ pageStart }} đến {{ pageStop }}
			</template>
		</v-data-table>	

	</v-card-text>
</v-card>
</template>

<script>

import axios from 'axios'
import {mapState} from 'vuex'
import moment from 'moment'
import index from '@/mixins/index'

function formatDate(str) {
	if(str != null) {
		return str.substring(8, 10)+'/'+str.substring(5, 7)+'/'+str.substring(0, 4)
	}
	return null
}

export default {
	mixins: [index],
	data() {
		return {
			title: "Danh sách đơn đặt hàng",
			breadcrumbs: [
			{
				text: 'Dashboard',
				name: 'Dashboard',
				disabled: false
			},
			{
				text: 'Đơn đặt hàng',
				disabled: true
			}
			],
			headers: [
			{
				text: 'No.',
				align: 'left',
				sortable: false,
				value: 'name'
			},
			{ text: 'Mã đặt hàng', sortable: false, },
			{ text: 'Nơi đặt', sortable: false },
			{ text: 'Ngày đặt', sortable: false },
			{ text: 'CSKH/ Người giao', sortable: false },
			{ text: 'Tổng tiền', sortable: false },
			{ text: 'Trạng thái', sortable: false },
			{ text: 'Chi tiết', sortable:false }
			],
			editedItem: {
				fromDate: new Date(moment().subtract(7, 'days')).toISOString().substr(0, 10),
				toDate: new Date().toISOString().substr(0, 10),
				statusId: 0
			},
			statusFilters: [],
			orders: [],
			order: null,
			fromModal:false,
			fromDateString: formatDate(new Date(moment().subtract(7, 'days')).toISOString().substr(0, 10)),
			toModal:false,
			toDateString: formatDate(new Date().toISOString().substr(0, 10)),
			loading:false,
			dialog: false,
			filter:false,
			cancelData: {}, 
			notes: []
		}
	},
	methods: {
		filterAction() {
			if(this.filter) {
				// this.search = {
				// 	text: '',
				// 	type: -1,
				// 	show: null,
				// 	verify: null
				// }
			}
			this.filter = !this.filter

		},
		//GET ORDER LIST BY FILTER
		fetchOrders: async function() {
			var data     = this.editedItem
			this.loading = await !this.loading
			await axios.post('/api/GetOrderByFilter', data, {withCredentials:true}).then(response => {
				if(response.status === 200) {
					this.orders = response.data.data
				}
			})
			setTimeout(() => {
				this.loading = !this.loading
			}, 500)
		},
		//CHOOSE STATUS ORDER
		orderStatus: function(status) {
			const _s = new String(status).toLowerCase();
			switch(_s) {
				case 'chờ xử lý': 
				return 1
				break;
				case 'đang xử lý':
				return 2
				break;
				case 'xác nhận':
				return 3 
				break;
				case 'đang giao':
				return 4
				break;
				case 'thành công':
				return 5
				break;
				case 'hủy': 
				return 6
				break;
			}
		},
		statusId: function(status) {
			const _s = new String(status).toLowerCase();
			var status = this.status.find(item => {
				return item.name.toLowerCase() === _s
			})
			return status.id
		},
	},
	computed: {
		...mapState({
			status: state => state.orderStatusStore.orderStatus
		})
	},
	watch: {
		'editedItem.fromDate': function(val, oldVal) {
			this.fromDateString = formatDate(val)
		},
		'editedItem.toDate': function(val, oldVal) {
			this.toDateString = formatDate(val)
		},
		'status': function(val) {
			if(val) {
				var array = [{id:0, name: 'Tất cả'}]
				val.forEach(item => {
					array.push({id:item.id, name: item.name})
				})
				this.statusFilters = array
			}
		}
	},
	mounted() {
		this.$store.dispatch('fetchOrderStatus').then(response => {
			if(response.status == 200) {
				this.fetchOrders()
			}
		})

	}
}
</script>

