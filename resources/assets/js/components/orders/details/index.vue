<template>
	<v-container grid-list-lg class="wrap-print">

		<v-card v-if="order != null">
			<v-breadcrumbs>
				<v-icon slot="divider">chevron_right</v-icon>
				<v-breadcrumbs-item
				v-for="item in breadcrumbs"
				:key="item.text"
				:disabled="item.disabled"
				:to="{name: item.action}"
				>{{ item.text }}</v-breadcrumbs-item>
			</v-breadcrumbs>
			<v-tabs
			:value="`item-${tab}`"
			slider-color="primary"
			centered
			color="grey lighten-3"
			>
			<v-tab :href="`#item-0`" :value="0" @click.prevent="changeTab(0)">
				Thông tin chi tiết
			</v-tab>
			<v-tab :href="`#item-1`" :value="1" @click.prevent="changeTab(1)">
				Bản đồ
			</v-tab>
			<v-tab :href="`#item-2`" :value="2" @click.prevent="changeTab(2)">
				Xử lý
			</v-tab>
			<v-tab :href="`#item-3`" :value="2" @click.prevent="changeTab(3)">
				Đơn hàng thực tế
			</v-tab>
		</v-tabs>
		<v-card-text>
			<v-layout row wrap v-show="tab == 0">
				<v-flex xs12 md6 d-flex>
					<v-card>
						<v-layout row wrap class="grid-list-md">
							<v-flex xs5 class="text-xs-right">
								Nơi đặt:
							</v-flex>

							<v-flex xs5 offset-xs1>
								<div class="primary--text">
									<strong>
										{{order.store.name}}
									</strong>
								</div>
								<div>
									{{order.store.address}}
								</div>
								<h4>{{order.store.phone | formatPhone}}</h4>
							</v-flex>

							<v-flex xs5  class="text-xs-right">
								Người nhận:
							</v-flex>
							<v-flex xs5 offset-xs1>
								<strong>
									{{order.name}}
								</strong>
							</v-flex>

							<v-flex xs5  class="text-xs-right">
								Địa chỉ giao:
							</v-flex>
							<v-flex xs5 offset-xs1>
								<h4>{{order.address}}</h4>
							</v-flex>

							<v-flex xs5  class="text-xs-right">
								Số điện thoại:
							</v-flex>
							<v-flex xs5 offset-xs1>
								<h4>{{order.phone | formatPhone}}</h4>
							</v-flex>

							<v-flex xs5  class="text-xs-right">
								Ngày đặt:
							</v-flex>
							<v-flex xs5 offset-xs1>
								{{order.bookingDate}}
							</v-flex>

							<v-flex xs5  class="text-xs-right">
								Ngày nhận:
							</v-flex>
							<v-flex xs5 offset-xs1>
								{{order.receiveDate | formatDate}} {{order.receiveTime}}
							</v-flex>

							<v-flex xs5  class="text-xs-right">
								Phí vận chuyển:
							</v-flex>
							<v-flex xs5 offset-xs1>
								{{order.deliveryPrice | formatPrice}}
							</v-flex>

							<v-flex xs5  class="text-xs-right">
								Tổng tiền:
							</v-flex>
							<v-flex xs5 offset-xs1>
								<strong>{{order.total | formatPrice}}</strong>
							</v-flex>

							<v-flex xs5  class="text-xs-right">
								Ghi chú:
							</v-flex>
							<v-flex xs5 offset-xs1>
								<strong>{{order.memo}}</strong>
							</v-flex>
						</v-layout>
					</v-card>
				</v-flex>

				<v-flex xs12 md6 d-flex>
					<v-data-table
					:headers="headers"
					:items="order.items"
					hide-actions
					class="elevation-1">
					<template slot="headerCell" slot-scope="props">
						<span class="red--text text--accent-3">
							{{ props.header.text }}
						</span>
					</template>
					<template slot="items" slot-scope="props">
						<td>
							{{props.item.name}}
							<h4 v-if="props.item.pivot.toppings.length > 0">toppings thêm: <span v-for="topping in props.item.pivot.toppings">{{topping.name}} ({{topping.price | formatPrice}}). </span></h4>
							<div v-if="props.item.pivot.memo != null">Ghi chú: {{props.item.pivot.memo}}</div>
						</td>
						<td class="text-xs-center">
							{{props.item.pivot.quantity}}
						</td>
						<td>
							{{props.item.pivot.price | formatPrice}}
						</td>
						<td>
							{{props.item.pivot.total | formatPrice}}
						</td>
					</template>
					<template slot="footer">
						<tr>
							<td colspan="3">Tạm tính</td>
							<td colspan="3"><strong>{{order.subTotal | formatPrice}}</strong></td>
						</tr>
						<tr>
							<td colspan="3">Phí vận chuyển:</td>
							<td><strong>{{order.deliveryPrice | formatPrice}}</strong></td>
						</tr>
						<tr>
							<td colspan="3">Giảm giá:</td>
							<td><strong>{{-order.discountTotal | formatPrice}}</strong></td>
						</tr>
						<tr>
							<td colspan="3">Tổng:</td>
							<td class="red--text"><strong>{{order.total | formatPrice}}</strong></td>
						</tr>
					</template></v-data-table>
				</v-flex>
				<v-flex xs12>
					<v-card>
						<v-card-text>
							<v-layout row wrap>
								<v-flex xs5  class="text-xs-right">
									CSKH:
								</v-flex>
								<v-flex xs5 offset-xs1>
									<strong>{{order.employee.name}}</strong>
								</v-flex>

								<v-flex xs5  class="text-xs-right">
									Shipper:
								</v-flex>
								<v-flex v-if="order.shipper != null"  xs5 offset-xs1>
									<strong>{{order.shipper.name}}</strong>
								</v-flex>
							</v-layout>
						</v-card-text>
					</v-card>
				</v-flex>
			</v-layout>

			<div v-show="tab == 1">
				<v-layout row wrap v-if="matrix.show">
					<v-flex xs3>Từ:</v-flex>
					<v-flex xs9>{{order.store.address}}</v-flex>
					<v-flex xs3>
						Đến:
					</v-flex>
					<v-flex xs9>
						{{order.address}}
					</v-flex>
					<v-flex xs3>
						Khoảng cách:
					</v-flex>
					<v-flex xs9>
						<span class="primary--text">
							<strong>
								{{matrix.distance}}
							</strong>
						</span>
					</v-flex>
					<v-flex xs3>
						Phí vận chuyển:
					</v-flex>
					<v-flex xs9>
						<span class="red--text"><strong>
							{{order.deliveryPrice | formatPrice}}
						</strong></span>
					</v-flex>
				</v-layout>
				<div id="map"></div>
			</div>

			<div v-show="tab == 2">
				<v-layout row wrap v-if="order.statusId == orderStatus('Hủy')">
					<v-flex xs5 class="text-xs-right">
						Trạng thái:
					</v-flex>
					<v-flex xs5 offset-xs1 class="red--text">
						<strong>
							{{order.statusName}}
						</strong>
					</v-flex>

					<v-flex xs5 class="text-xs-right">
						Thông điệp:
					</v-flex>
					<v-flex xs5 offset-xs1>
						<div>Rất tiếc đơn hàng của quý khách đã bị hủy.</div>
						<div><strong>Cám ơn bạn đã sử dụng hệ thống.</strong></div>
					</v-flex>

					<v-flex xs5 class="text-xs-right">
						Lý do:
					</v-flex>
					<v-flex xs5 offset-xs1>
						<div v-for="(text, index) in order.orderNotes" :key="index">
							{{text}}
						</div>
					</v-flex>

				</v-layout>
				<v-card>
					<v-stepper v-model="order.statusId" :value="order.statusId" v-if="order.statusId != orderStatus('Hủy')">
						<v-stepper-header>
							<v-stepper-step step="1" :complete="order.statusId > orderStatus('Chờ xử lý') && order.statusId != orderStatus('Hủy')">Chờ xử lý</v-stepper-step>
							<v-divider></v-divider>
							<v-stepper-step step="2" :complete="order.statusId > orderStatus('Đang xử lý') && order.statusId != orderStatus('Hủy')">Đang xử lý</v-stepper-step>
							<v-divider></v-divider >
							<v-stepper-step step="3" :complete="order.statusId > orderStatus('Xác nhận') && order.statusId != orderStatus('Hủy')">Xác nhận</v-stepper-step>
							<v-divider></v-divider>
							<v-stepper-step step="4" :complete="order.statusId > orderStatus('Đang giao') && order.statusId != orderStatus('Hủy')">Đang giao</v-stepper-step>
							<v-divider></v-divider>
							<v-stepper-step step="5" :complete="order.statusId == orderStatus('Thành công') && order.statusId != orderStatus('Hủy')">Thành công</v-stepper-step>
						</v-stepper-header>
						<v-stepper-items>
							<v-stepper-content step="1">
								<v-layout row wrap>
									<v-flex xs4>
										Trạng thái:
									</v-flex>
									<v-flex xs8 class="yellow--text text--accent-4">
										<strong>
											{{order.statusName}}
										</strong>
									</v-flex>
									<v-flex xs4>
										Thông điệp:
									</v-flex>
									<v-flex xs8>
										<div>Đơn đặt hàng của quý khách đang chờ nhân viên tiếp nhận.</div>
										<div><strong>Xin vui lòng chờ trong giây lát.</strong></div>
										<div><strong>Cám ơn.</strong></div>
									</v-flex>
								</v-layout>
								<v-spacer></v-spacer>
								<v-btn color="primary" @click.native="changeStatus(order.statusStep)">Tiếp tục</v-btn>
							</v-stepper-content>
							<v-stepper-content step="2">
								<v-layout row wrap>
									<v-flex xs4>
										Trạng thái:
									</v-flex>
									<v-flex xs8 class="yellow--text text--accent-4">
										<strong>
											{{order.statusName}}
										</strong>
									</v-flex>
									<v-flex xs4>
										Thông điệp:
									</v-flex>
									<v-flex xs8>
										<div>
											Đơn hàng <span><strong> {{order.id}} </strong></span> đã được nhân viên<span v-if="order.employee != null"><strong> {{order.employee.name}} </strong></span>tiếp nhận và xử lý.
										</div>
										<div>
											<strong>Xin vui lòng chờ trong giây lát.
											</strong>
										</div>
										<div><strong>Cám ơn.</strong></div>
									</v-flex>
								</v-layout>
								<v-btn color="primary" @click.native="changeStatus(order.statusStep)">Tiếp tục</v-btn>
							</v-stepper-content>
							<v-stepper-content step="3">
								<v-layout row wrap>
									<v-flex xs4>
										Trạng thái:
									</v-flex>
									<v-flex xs8 class="success--text">
										<strong>
											{{order.statusName}}
										</strong>
									</v-flex>
									<v-flex xs4>
										Thông điệp:
									</v-flex>
									<v-flex xs8>
										<div>
											Đơn hàng {{order.id}} đã được xác nhận thành công.
										</div>
										<div>
											<strong>
												Xin vui lòng liên hệ cửa hàng để chuẩn bị.
											</strong>
										</div>
										<div>
											<strong>
												Cám ơn!
											</strong>
										</div>
									</v-flex>
									<v-flex xs4>
										Shipper:
									</v-flex>
									<v-flex xs6>
										<v-select
										:items="shippers"
										v-model="shipper"
										item-text="name"
										item-value="id"
										return-object
										label="Select"
										single-line
										></v-select>
									</v-flex>
									<v-flex xs2>
										<v-btn @click.stop="chooseShipper()">
											Chọn
										</v-btn>
									</v-flex>
								</v-layout>
								<v-btn color="primary" @click.native="changeStatus(order.statusStep)" :disabled="order.shipper == null">Tiếp tục</v-btn>
							</v-stepper-content>
							<v-stepper-content step="4">
								<v-layout row wrap>
									<v-flex xs4>
										Trạng thái:
									</v-flex>
									<v-flex xs8>
										{{order.statusName}}
									</v-flex>
									<v-flex xs4>
										Thông điệp:
									</v-flex>
									<v-flex xs8>
										<div>
											Đơn hàng của quý khách đã được nhân viên <span  v-if="order.shipper != null"><strong> {{order.shipper.name}} </strong></span> giao đến.
										</div>
										<div>
											<strong>
												Xin vui lòng chờ đợi ít phút.
											</strong>
										</div>
										<div>
											<strong>
												Cám ơn.
											</strong>
										</div>
									</v-flex>
									<v-btn color="primary" @click.native="changeStatus(order.statusStep)">Tiếp tục</v-btn>									
								</v-layout>								
							</v-stepper-content>
							<v-stepper-content step="5">
								<v-layout row wrap>
									<v-flex xs4>
										Trạng thái:
									</v-flex>
									<v-flex xs8 class="success--text">
										<strong>{{order.statusName}}</strong>
									</v-flex>
									<v-flex xs4>
										Thông điệp:
									</v-flex>
									<v-flex xs8>
										<div>Đơn hàng của quý khách đã được giao thành công.</div>
										<div><strong>Cám ơn quý khách đã đặt hàng từ hệ thống.</strong>
										</div>
										<div><strong>Chúc bạn dùng ngon miệng.</strong></div>
									</v-flex>
								</v-layout>
							</v-stepper-content>
						</v-stepper-items>
					</v-stepper>
				</v-card>
			</div>

			<v-layout row wrap v-show="tab == 3" v-if="order.actualOrder != null">
				<v-flex xs12 md6 d-flex>
					<v-card>
						<v-layout row wrap class="grid-list-md">
							<v-flex xs5 class="text-xs-right">
								Nơi đặt:
							</v-flex>

							<v-flex xs5 offset-xs1>
								<div class="primary--text">
									<strong>
										{{order.store.name}}
									</strong>
								</div>
								<div>
									{{order.store.address}}
								</div>
							</v-flex>

							<v-flex xs5 class="text-xs-right">
								Số điện thoại cửa hàng:
							</v-flex>

							<v-flex xs5 offset-xs1>
								<h4>{{order.store.phone | formatPhone}}</h4>
							</v-flex>

							<v-flex xs5  class="text-xs-right">
								Người nhận:
							</v-flex>
							<v-flex xs5 offset-xs1>
								<strong>
									{{order.name}}
								</strong>
							</v-flex>

							<v-flex xs5  class="text-xs-right">
								Địa chỉ giao:
							</v-flex>
							<v-flex xs5 offset-xs1>
								<h4>{{order.address}}</h4>
							</v-flex>

							<v-flex xs5  class="text-xs-right">
								Số điện thoại:
							</v-flex>
							<v-flex xs5 offset-xs1>
								<h4>{{order.phone | formatPhone}}</h4>
							</v-flex>

							<v-flex xs5  class="text-xs-right">
								Ngày đặt:
							</v-flex>
							<v-flex xs5 offset-xs1>
								{{order.bookingDate}}
							</v-flex>

							<v-flex xs5  class="text-xs-right">
								Ngày nhận:
							</v-flex>
							<v-flex xs5 offset-xs1>
								{{order.receiveDate | formatDate}} {{order.receiveTime}}
							</v-flex>

							<v-flex xs5  class="text-xs-right">
								Ghi chú:
							</v-flex>
							<v-flex xs5 offset-xs1>
								<strong>{{order.memo}}</strong>
							</v-flex>
						</v-layout>
					</v-card>
				</v-flex>

				<v-flex xs12 md6 d-flex>
					<v-data-table
					:headers="headers"
					:items="order.actualOrder.items"
					hide-actions
					class="elevation-1">
					<template slot="headerCell" slot-scope="props">
						<span class="red--text text--accent-3">
							{{ props.header.text }}
						</span>
					</template>
					<template slot="items" slot-scope="props">
						<td>
							{{props.item.name}}
							<h4 v-if="props.item.pivot.toppings.length > 0">toppings thêm: <span v-for="topping in props.item.pivot.toppings">{{topping.name}} ({{topping.price | formatPrice}}). </span></h4>
							<div v-if="props.item.pivot.memo != null">Ghi chú: {{props.item.pivot.memo}}</div>
						</td>
						<td class="text-xs-center">
							{{props.item.pivot.quantity}}
						</td>
						<td>
							{{props.item.pivot.price | formatPrice}}
						</td>
						<td>
							{{props.item.pivot.total | formatPrice}}
						</td>
					</template>
					<template slot="footer">
						<tr>
							<td colspan="3">Tạm tính</td>
							<td colspan="3"><strong>{{order.actualOrder.subTotal | formatPrice}}</strong></td>
						</tr>
						<tr>
							<td colspan="2">Chiết khấu:</td>
							<td>{{order.actualOrder.discountPercent}} %</td>
							<td><strong>{{-order.actualOrder.discountTotal | formatPrice}}</strong></td>
						</tr>
						<tr>
							<td colspan="3">Tổng:</td>
							<td class="red--text"><strong>{{order.actualOrder.total | formatPrice}}</strong></td>
						</tr>
					</template></v-data-table>
				</v-flex>
				<v-flex xs12>
					<v-card>
						<v-card-text>
							<v-layout row wrap>
								<v-flex xs5  class="text-xs-right">
									CSKH:
								</v-flex>
								<v-flex xs5 offset-xs1>
									<strong>{{order.employee.name}}</strong>
								</v-flex>

								<v-flex xs5  class="text-xs-right">
									Shipper:
								</v-flex>
								<v-flex v-if="order.shipper != null"  xs5 offset-xs1>
									<strong>{{order.shipper.name}}</strong>
								</v-flex>
							</v-layout>
						</v-card-text>
					</v-card>
				</v-flex>
			</v-layout>
		</v-card-text>
		<v-card-actions>
			<v-btn color="error" v-show="order.statusId != statusId('Thành công') && order.statusId != statusId('Hủy')" @click="cancelDialog = !cancelDialog" small>Hủy</v-btn>
		</v-card-actions>

		<v-dialog v-model="cancelDialog" :value="cancelDialog" max-width="500">
			<v-card>
				<v-card-title class="headline">Hủy đơn đặt hàng?</v-card-title>
				<v-divider></v-divider>
				<v-card-text>
					<v-checkbox v-for="(item, index) in reasons" :label="item.note" v-model="notes" :value="item.note" :key="index"></v-checkbox>
				</v-card-text>
				<v-divider></v-divider>
				<v-card-actions>
					<v-spacer></v-spacer>
					<v-btn @click.native="cancelDialog = false" round small>Hủy</v-btn>
					<v-btn color="red accent-3" dark @click.native="cancelOrder" round small>Chấp nhận</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>
	</v-card>

</v-container>
</template>

<script>
	import axios from 'axios'
	import { initMap, calculateDirection } from '@/utils'
	import {mapState} from 'vuex'
	export default {
		data() {
			return {
				breadcrumbs: [
				{
					text: 'Dashboard',
					action: 'Dashboard',
					disabled: false
				},
				{
					text: 'Danh sách đơn đặt hàng',
					action: 'Order',
					disabled: false
				},
				{
					text: 'Chi tiết đơn đặt hàng',
					action: '',
					disabled: true
				}
				],
				loading: false,
				order: null,
				tab: 0,
				headers: [
				{ text: 'Món', sortable: false, },
				{ text: 'Số lượng', sortable: false, },
				{ text: 'Giá', sortable: false, },
				{ text: 'Tổng', sortable:false}
				],
				cancelDialog: false,
				reasons: [
				{note: 'Nhập sai số điện thoại'},
				{note: 'Nhập sai thông tin liên hệ'},
				{note: 'Đơn hàng chậm trễ'},
				],
				matrix: {
					show:false,
					start: '',
					end: '',
					duration: '0 phút',
					distance: '0 Km'
				},
				shippers: [],
				shipper: null,
				cancelDialog: false,
				reasons: [
				{note: 'Không liên hệ được'},
				{note: 'Thông tin không đúng'},
				{note: 'Cửa hàng không hoạt động'},
				{note: 'Món đặt đã hết'},
				{note: 'Xảy ra sự cố không mong muốn'},
				],
				notes: []
			}
		},
		methods: {
			chooseShipper: async function() {
				const data = {orderId: this.order.id, shipperId: this.shipper.id, step: this.order.statusStep, statusId: this.order.statusId, confirm: true}
				this.loading = await true
				await axios.post('/api/GetOrder/'+this.$route.params.orderId+'/Details/ChooseShipper', data, {withCredentials: true}).then(response => {
					if(response.status == 200 && response.data.type == 'success') {
						this.order = response.data.data
					}
				})
				this.loading = false
			},
			getShippers: async function() {
				const data = []
				this.loading = await true
				this.axios.post('/api/GetUser/Shippers', data, {withCredentials:true}).then(response => {
					if(response.status == 200) {
						this.shippers = response.data.data
					}
				})
			},
			getOrderDetails: async function(id) {
				const data = {orderId: id}
				this.loading = await true
				await axios.post('/api/GetOrder/'+id+'/Details', data, {withCredentials: true}).then(response => {
					if(response.status == 200) {
						this.order = response.data.data
					}
				})
				this.loading = false
			},
			changeTab(index) {
				this.tab = index
			},

		//DIRECTION GOOGLE MAP
		calculateRoute() {
			var vm        = this
			var start     = vm.order.store
			var end       = vm.order
			var distance  = '0 Km'
			var duration  = '0 phút'
			var map       = initMap(start)

			calculateDirection(map, start, end).then(response => {
				var leg  = response.routes[0].legs[0];
				distance = leg.distance.text
				duration = leg.duration.text
				vm.matrix.distance = distance
				vm.matrix.duration = duration
				vm.matrix.show     = true
			})
		},
		//STATUS ORDER
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
		changeStatus: function(step) {
			var vm = this
			const data = {orderId: vm.order.id ,step: ++step, confirm:true}
			vm.$swal({
				title: 'Bạn có đồng ý chuyển bước?',
				text: "Bạn sẽ không thể quay trở lại bước trước!",
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Đồng ý',
				cancelButtonText: 'Không',
				confirmButtonClass: 'v-btn primary',
				cancelButtonClass: 'v-btn',
				buttonsStyling: false,
				reverseButtons: true
			}).then((result) => {
				if (result.value) {
					vm.$swal(
						'Đổi trạng thái!',
						this.order.statusName+' trạng thái đã được thay đổi.',
						'success'
						).then(() => {
							axios.post('/api/GetOrder/'+this.$route.params.orderId+'/Details/ChangeStatus', data).then(response => {
								if(response.status == 200) {
									vm.order = response.data.data
								}
							})
						})
					} else{
						vm.$swal(
							'Cancelled',
							'',
							'error'
							)
					}
				})
		},
		cancelOrder: async function() {
			var vm = this
			const data = {orderId: vm.order.id, confirm: true, orderNotes: this.notes}
			vm.$swal({
				title: 'Bạn có đồng ý hủy đơn đặt hàng?',
				text: "Bạn sẽ không thể khôi phục lại đơn đặt hàng đã hủy!",
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Đồng ý!',
				cancelButtonText: 'Không',
				confirmButtonClass: 'btn primary',
				cancelButtonClass: 'btn',
				buttonsStyling: false,
				reverseButtons: true
			}).then((result) => {
				if (result.value) {
					vm.$swal(
						'Hủy đơn đặt hàng!',
						vm.order.statusName+' đã được chuyển sang hủy.',
						'success'
						).then(() => {
							axios.post('/api/GetOrder/'+this.$route.params.orderId+'/Details/CancelOrder', data).then(response => {
								if(response.status == 200) {
									vm.order        = response.data.data
									vm.cancelDialog = false
								}
							})
						})
					} else{
						vm.$swal('Cancelled', '', 'error')
					}
				})
		}
	},
	computed: {
		...mapState({
			status: state => state.orderStatusStore.orderStatus
		})
	},
	watch: {
		'tab': async function(val, oldVal) {
			if(val == 1) {
				if(!this.matrix.show) {
					setTimeout(async () => {
						await this.calculateRoute()
					}, 500)
				}
			}
		},
		'cancelDialog': function(val, oldVal) {
			if(!val) {
				this.notes = []
			}
		}
	},
	mounted() {
		this.$store.dispatch('fetchOrderStatus').then(response => {
			if(response.status === 200) {
				this.getOrderDetails(this.$route.params.orderId)
				this.getShippers()
			}
		})
	}
}
</script>
<style>
#map {
	width: 100%;
	height: 250px;
}
@media print {
	.application {
		display: block !important;
	}
	.application--wrap {
		display: block !important;
	}
	.content--wrap {
		display: block !important;
	}
	.v-toolbar, .v-breadcrumbs, .v-tabs {
		display: none !important;
	}
	.wrap-print {
		padding-top: 0px !important;
		margin-top: 0px !important;
	}
	.button-hidden {

	}
}
</style>
