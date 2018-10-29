<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
	protected $table    = 'ec_coupons';
	
	protected $fillable = ['title', 'coupon', 'notes', 'discount_percent', 'discount_price', 'max_price', 'total_amount', 'max_coupons', 'coupon_used', 'started_at', 'ended_at', 'expires_at', 'token', 'actived', 'public', 'status_id'];
	
	protected $hidden   = ['pivot'];

    protected $dates   = ['started_at', 'ended_at'];

	public function getActivedAttribute($value)
    {
        if ($value) {
            return true;
        }
        return false;
    }

    public function getPublicAttribute($value)
    {
        if ($value) {
            return true;
        }
        return false;
    }

    public function scopeCheckToken($query, $token) {
    	return $query->where('token', $token);
    }

    public function scopeById($query, $coupon_id) {
    	$couponId = (int) $coupon_id;
    	return $query->where('id', $couponId);
    }

	public function stores() {
		return $this->belongsToMany('App\Models\Store', 'ec_coupon_ec_store', 'coupon_id', 'store_id');
	}
	public function cities() {
		return $this->belongsToMany('App\Models\City', 'ec_coupon_ec_store', 'coupon_id', 'city_id');
	}
	public function status() {
		return $this->belongsTo('App\Models\CouponStatus', 'status_id');
	}
}
