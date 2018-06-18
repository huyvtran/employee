<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\District;
use App\Http\Resources\DistrictResource;

class DistrictController extends Controller
{
    public function __construct() {
    	$this->middleware('auth:api');
    }

    public function getDistrict() {

    	$districts = District::get();

    	$res = [
            'type'    => 'success',
            'message' => 'Get stores successfully',
            'data'    => DistrictResource::collection($districts)
        ];        
        
        return response($res, 200);
    }
}
