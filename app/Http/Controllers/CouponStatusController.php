<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CouponStatus;
use App\Http\Resources\CouponStatusResource;

class CouponStatusController extends Controller
{
    public function getStatus() {
    	$status = CouponStatus::get();
    	return $this->respondSuccess('Get Coupon Status', $status, 200, 'many');
    }

    protected function respondSuccess($message, $data, $status = 200, $type)
	{	
		$res = [
			'type'    => 'success',
			'message' => $message. ' successfully.',
		];

		switch ($type) {
			case 'one':
			$res['status']  = new CouponStatusResource($data);
			break;
			case 'many':
			$res['status']  = CouponStatusResource::collection($data);
			break;
		}

		return response($res, $status);
	}
}
