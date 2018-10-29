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

	public function getCityWithStore() {

		$city = City::show()->default()->with(['stores' => function($query) {
			return $query->show()->verified();
		}])->first();

		return $this->respondSuccess('Get city with stores', $city, 200, 'one');
	}

	protected function respondSuccess($message, $data, $status = 200, $type)
    {
        $res = [
            'type'    => 'success',
            'message' => $message . ' successfully.',
        ];

        switch ($type) {
            case 'one':
            $res['city']   = new CityResource($data);
            break;

            case 'many':
            $res['cities'] = CityResource::collection($data);
            break;
        }

        return response($res, $status);
    }

}
