<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topping extends Model
{
	protected $table = 'ec_toppings';

	protected $guarded = [];

	protected $hidden = [];

	public function scopeByStoreId($query, $storeId) {
		return $query->where('store_id', $storeId);
	}

	public function store() {
		return $this->belongsTo('App\Models\Store', 'store_id');
	}
}
