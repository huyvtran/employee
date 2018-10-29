<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'coupon'          => $this->coupon,
            'title'           => $this->title,
            'notes'           => $this->notes,
            'discountPercent' => $this->discount_percent,
            'discountPrice'   => $this->discount_price,
            'maxPrice'        => $this->max_price,
            'totalAmount'     => $this->total_amount,
            'maxCoupons'      => $this->max_coupons,
            'couponUsed'      => $this->coupon_used,
            'startedAt'       => $this->started_at,
            'endedAt'         => $this->ended_at,
            'expiresAt'       => $this->expires_at,
            'token'           => $this->token,
            'actived'         => $this->actived,
            'public'          => $this->public,
            'statusId'        => $this->status_id,
            'stores'          => $this->whenLoaded('stores', function() {  
                return StoreResource::collection($this->stores->map(function($query) {return $query;})->sortBy('store_name'));                
            }),
            'status'          => new CouponStatusResource($this->status)
        ];
    }
}
