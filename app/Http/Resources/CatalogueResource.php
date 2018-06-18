<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CatalogueResource extends JsonResource
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
            'id'       => $this->id,
            'name'     => $this->catalogue,
            '_name'    => $this->_catalogue,
            'priority' => $this->priority,
            'isShowed' => $this->catalogue_show,
            'slug'     => $this->slug,
            'products' => ProductResource::collection($this->whenLoaded('products', function() {
                return $this->products->sortByDesc('priority')->sortBy('name');
            }))
        ];
    }
}
