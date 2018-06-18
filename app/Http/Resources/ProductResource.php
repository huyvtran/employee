<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id'           => $this->id,
            'name'         => $this->name,
            '_name'        => $this->_name,
            'description'  => $this->description,
            'price'        => $this->price,
            'count'        => $this->count,
            'image'        => $this->image,
            'priority'     => $this->priority,
            'haveSize'     => $this->have_size,
            'haveTopping'  => $this->have_topping,
            'status_id'    => $this->status_id,
            'status'       => $this->status->product_status_name,
            'catalogue_id' => $this->catalogue_id
        ];
    }
}
