<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use DateTime;
use App\Models\OrderStatus;
use App\Http\Resources\OrderStatusResource;

class OrderStatusController extends Controller
{
	public function __construct() {
		$this->middleware('auth:api');
	}

	public function getOrderStatus()
	{
		$status = OrderStatus::get();
		
		$res    = [
			'type'    => 'success',
			'message' => 'Get order status successfully!',
			'data'    => OrderStatusResource::collection($status)
		];

		return response($res, 200);
	}
}
