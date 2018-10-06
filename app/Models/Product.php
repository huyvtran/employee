<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	protected $table    = 'ec_products';
	
	protected $fillable = ['name', '_name', 'price', 'count', 'have_size', 'have_topping', 'image', 'priority', 'status_id', 'catalogue_id', 'description'];
	
	protected $hidden   = [];

	public function catalogue() {
		return $this->belongsTo('App\Models\Catalogue', 'catalogue_id');
	}    

	public function status() {
		return $this->belongsTo('App\Models\ProductStatus', 'status_id');
	}

	// ORDER BY PRIORITY
    public function scopeOrderByPriority($query, $sort) {
        return $query->orderBy('priority', $sort);
    }
    // ORDER BY NAME
    public function scopeOrderByName($query, $sort) {
        return $query->orderBy('name', $sort);
    }
    //LIKE NAME
    public function scopeLikeName($query, $name) {
    	return $query->where('name', 'LIKE BINARY', $name);
    }
    //WHERE BY CATALOGUE ID
    public function scopeByCatalogueId($query, $catalogueId) {
    	$catalogue_id = (int) $catalogueId;
    	return $query->where('catalogue_id', $catalogue_id);
    }

    public function scopeHasNotId($query, $productId) {
    	$product_id = (int) $productId;
    	return $query->where('ec_products.id', '!=', $product_id);
    }

	public function getHaveSizeAttribute($value) {
		if($value) {
			return true;
		} 
		return false;
	}

	public function getHaveToppingAttribute($value) {
		if($value) {
			return true;
		} 
		return false;
	}

	public function sizes() {
		return $this->belongsToMany('App\Models\Size','ec_product_ec_size','product_id', 'size_id')->withPivot(['price']);
	}
}
