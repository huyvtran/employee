<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Type;
use App\Http\Resources\TypeResource;
class TypeController extends Controller
{
	public function __construct() {
		$this->middleware('auth:api');
	}

	public function getType() {

		$types = Type::get();

		$res = [
			'type'    => 'success',
			'message' => 'Get stores successfully',
			'data'    => TypeResource::collection($types)
		];     

		return response($res, 200);
		
	}
}
