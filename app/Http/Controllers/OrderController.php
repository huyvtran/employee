<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\RegularOrder;
use App\Models\OrderStatus;
use App\Models\ActualOrder;
use App\Http\Resources\OrderResource;
use DateTime;

class OrderController extends Controller
{
	public function __construct() {
		$this->middleware('auth:api')->except('calculatePoint');
		$this->orderCancelled = OrderStatus::where('order_status_name', '=', 'Hủy')->first();
		$this->orderCompleted = OrderStatus::where('order_status_name', '=', 'Thành công')->first();
	}

	//CHOOSE SHIPPER FOR ORDER
	public function chooseShipper(Request $request, $id) {
		$orderId   = $request->orderId;
		$shipperId = $request->shipperId;
		$step      = $request->step;
		$statusId  = $request->statusId;
		if($request->filled('orderId', 'shipperId', 'step', 'statusId', 'confirm')) {
			if($request->confirm) {
				$order = RegularOrder::where('id', '=', $orderId)->where('status_id', '=', $statusId)->first();
				if($order->status->number_order == $request->step){
					$order->shipper_id = $shipperId;
					$order->save();
					$res   = [
						'type'    => 'success',
						'message' => 'Get order details successfully.',
						'data'    => new OrderResource($order->load('products', 'user', 'store', 'employee', 'shipper', 'actualOrder'))
					];	
					return response($res, 200);
				}
			}
		}
		$res = [
			'type'    => 'error',
			'message' => 'Choose shipper error.',
			'data'    => []
		];
		return response($res, 200);
	}
	// CANCEL ORDER
	public function cancelOrder(Request $request, $id) {
		$orderId = $request->orderId;
		
		if($request->confirm && $request->filled('orderId')) {
			$order = RegularOrder::where('id', '=', $orderId)->where('status_id', '!=', $this->orderCancelled->id)->first();
			
			if($order->employee->id == auth('api')->user()->id) {
				$order->status_id = $this->orderCancelled->id;
				$order->note      = serialize($request->orderNotes);
				$order->save();
				$res = [
					'type'    => 'success',
					'message' => 'Cancelled order successfully.',
					'data'    => new OrderResource($order->load('user', 'shipper', 'employee', 'store', 'payment', 'actualOrder'))
				];		
				return response($res ,200);
			}
			$res = [
				'type'    => 'error',
				'message' => 'Something went wrong.',
				'data'    => []
			];
			return response($res, 500);
		} 
	}
	//CHANGE STATUS ORDER
	public function changeStatusOrder(Request $request, $id) {

		if($request->confirm && $request->filled('orderId', 'step')) {
			$status = OrderStatus::where('number_order', '=', $request->step)->where('number_order', '<=', 5)->where('order_status_name', '!=', 'Hủy')->first();
			if(!is_null($status)) {
				$order            = RegularOrder::where('id', '=', $request->orderId)->first();
				$order->status_id = $status->id;
				$order->save();

				if($request->step == $this->orderCompleted->number_order) {
					foreach($order->products as $item) {
						$item->count = $item->count + $item->pivot->quantity;
						$item->save();
					}
					if(is_null($order->coupon) && is_null($order->secret) && $order->store->verified) {
						if($order->subtotal_amount >= 50000) {
							$point               = $this->calculatePoint($order->subtotal_amount);
							$order->user->points = $order->user->points + $point;
							$order->user->save();
						}
					}
				}

				$res   = [
					'type'    => 'success',
					'message' => 'Get order details successfully.',
					'data'    => new OrderResource($order->load('products', 'user', 'store', 'employee', 'shipper', 'actualOrder'))
				];	
				return response($res, 200);
			}
		}
	}
	//GET ORDER DETAILS
	public function getOrderDetail(Request $request, $id) {
		$status = OrderStatus::where('order_status_name', '=', 'Đang xử lý')->first();
		if($request->filled('orderId') && (int)$request->orderId == (int)$id) {
			$order = RegularOrder::where('id', '=', $request->orderId)->first();
			if(is_null($order->employee_id)) {
				$order->employee_id = auth('api')->user()->id;
				$order->status_id 	= $status->id;
				$order->updated_at  = new DateTime;
				$order->save();
			}
			$res   = [
				'type'    => 'success',
				'message' => 'Get order details successfully.',
				'data'    => new OrderResource($order->load('products', 'user', 'store', 'employee', 'shipper', 'actualOrder'))
			];	
			return response($res, 200);
		}
	} 
	//GET ORDER BY FILTER
	public function getOrderByFilter(Request $request) {
		$fromDate = $request->fromDate;
		$toDate   = $request->toDate;
		$status   = (int)$request->statusId;
		if($status == 0) {
			$orders  = RegularOrder::where(function($query) use ($fromDate, $toDate) {
				$query->whereDate('created_at', '>=', $fromDate);
				$query->whereDate('created_at', '<=', $toDate);
			})->orderBy('id', 'desc')->get();
		} else {
			$orders  = RegularOrder::where(function($query) use ($fromDate, $toDate, $status) {
				$query->whereDate('created_at', '>=', $fromDate);
				$query->whereDate('created_at', '<=', $toDate);
			})->where('status_id', '=', $status)->orderBy('id', 'desc')->get();
		}

		$res     = [
			'type'    => 'success',
			'message' => 'Get history information successfully.',
			'data'    => OrderResource::collection($orders->load('user', 'shipper', 'employee', 'store', 'payment', 'actualOrder'))
		];

		return response($res, 200);
	}

	public function calculatePoint($amount) {
		
		$surplus = (int)$amount/50000;
		$value   = explode(".", $surplus);
		$point   = (int)$value[0];
		return $point;
	}
}
