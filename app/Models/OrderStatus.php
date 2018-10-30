<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
	protected $table = 'ec_order_status';

	protected $guarded = [];

	protected $hidden = [];
	
	public function scopeByStep($query, $step) {
		return $query->where('number_order', $step);
	}

	public function scopeIncomplete($query) {
		return $query->where('number_order', '<=', 5);
	}

	public function scopeDontCancel($query) {
		return $query->where('order_status_name', '!=', 'Hủy');
	}

	public function orders() {
		return $this->hasMany('App\Models\RegularOrder', 'order_id');
	}
}
