<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topping extends Model
{
	protected $table = 'ec_toppings';

	protected $fillable = ['name', '_name', 'price', 'store_id'];

	protected $guarded = [];

	protected $hidden = [];

	//LIKE NAME
    public function scopeLikeName($query, $name) {
    	return $query->where('name', 'LIKE BINARY', $name);
    }

    //DIFFERENT ID
    public function scopeHasNotId($query, $id) {
    	return $query->where('id', '!=', (int) $id);
    }

	public function scopeByStoreId($query, $storeId) {
		return $query->where('store_id', (int) $storeId);
	}

	public function store() {
		return $this->belongsTo('App\Models\Store', 'store_id');
	}
}
