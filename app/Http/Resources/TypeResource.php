<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TypeResource extends JsonResource
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
            'id'         => $this->id,
            'name'       => $this->type_name,
            'slug'       => $this->type_slug,
            'icon'       => $this->type_icon,
            'storeCount' => $this->stores->count()
        ];
    }
}
