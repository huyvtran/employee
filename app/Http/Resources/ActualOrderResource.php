<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActualOrderResource extends JsonResource
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
            'deliveryPrice'   => $this->delivery_price,
            'subTotal'        => $this->subtotal_amount,
            'total'           => $this->amount,
            'coupon'          => $this->coupon,
            'secret'          => $this->secret,
            'discountPercent' => $this->discount,
            'discountTotal'   => $this->discount_total,
            'items'           => $this->products->map(function($query) {                
                $query->pivot->toppings = unserialize($query->pivot->toppings);
                return $query;
            })
        ];
    }
}
