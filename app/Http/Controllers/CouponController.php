<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Http\Resources\CouponResource;
use Carbon\Carbon;

class CouponController extends Controller
{
	public function getCoupon() {

		$coupons = Coupon::orderBy('id', 'desc')->get();

		return $this->respondSuccess('Get coupon', $coupons->load('stores'), 200, 'many');
	}

	public function showCoupon(Request $request, $id) {

		$coupon = Coupon::findorFail($id);

		return $this->respondSuccess('Show coupon', $coupon->load('stores'), 200, 'one');

	}

	public function addCoupon(Request $request) {

		$coupon = Coupon::create([
			'title'            => $request->title,
			'coupon'           => $request->coupon,
			'notes'            => $request->notes,
			'discount_percent' => $request->discountPercent,
			'discount_price'   => $request->discountPrice,
			'max_price'        => $request->maxPrice,
			'total_amount'     => $request->totalAmount,
			'max_coupons'      => $request->maxCoupons,
			'coupon_used'      => 0,
			'started_at'       => $request->startedAt,
			'ended_at'         => Carbon::createFromFormat('Y-m-d H:i', $request->endedAt." 23:59")->toDateTimeString(),
			'expires_at'       => Carbon::createFromFormat('Y-m-d H:i', $request->endedAt." 23:59")->timestamp,
			'actived'          => $request->actived,
			'public'           => $request->public,
			'token'            => hash_hmac('sha256', str_random(40), config('app.key')),
			'status_id'        => $request->statusId
		]);

		return $this->respondSuccess('Add coupon', $coupon->load('stores'), 201, 'one');
	}

	public function updateCoupon(Request $request, $id) {

		$coupon = Coupon::checkToken($request->secret)->find($id);

		if($coupon) {

			$coupon->update([
				'title'            => $request->title,
				'coupon'           => $request->coupon,
				'notes'            => $request->notes,
				'discount_percent' => $request->discountPercent,
				'discount_price'   => $request->discountPrice,
				'max_price'        => $request->maxPrice,		
				'total_amount'     => $request->totalAmount,				
				'max_coupons'      => $request->maxCoupons,
				'started_at'       => $request->startedAt,
				'ended_at'         => Carbon::createFromFormat('Y-m-d H:i', $request->endedAt." 23:59")->toDateTimeString(),
				'expires_at'       => Carbon::createFromFormat('Y-m-d H:i', $request->endedAt." 23:59")->timestamp,
				'actived'          => $request->actived,
				'public'           => $request->public,
				'token'            => hash_hmac('sha256', str_random(40), config('app.key')),
				'status_id'        => $request->statusId
			]);

		}

		return $this->respondSuccess('Update coupon', $coupon->load('stores'), 200, 'one');
	}

	public function updateStore(Request $request, $id) {

		$coupon   = Coupon::checkToken($request->secret)->findorFail($id);
		$storeIds = collect($request->stores)->pluck('id'); 

		if($coupon) {
			$coupon->stores()->sync($storeIds);
		}

		return $this->respondSuccess('Update coupon', $coupon->load('stores'), 200, 'one');

	}

	protected function respondSuccess($message, $data, $status = 200, $type)
	{	
		$res = [
			'type'    => 'success',
			'message' => $message. ' successfully.',
		];

		switch ($type) {
			case 'one':
			$res['coupon']  = new CouponResource($data);
			break;
			case 'many':
			$res['coupons'] = CouponResource::collection($data);
			break;
		}

		return response($res, $status);
	}
}
