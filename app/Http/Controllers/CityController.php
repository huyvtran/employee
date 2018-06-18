<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Http\Resources\CityResource;
class CityController extends Controller
{
	public function __construct() {
		$this->middleware('auth:api');
	}

	public function getCity() {
		
		$cities = City::get();
		
		$res = [
			'type'    => 'success',
			'message' => 'Get stores successfully',
			'data'    => CityResource::collection($cities)
		]; 

		return response($res, 200);
	}


}
