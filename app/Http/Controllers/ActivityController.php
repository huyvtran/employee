<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Http\Resources\ActivityResource;
use DateTime;
class ActivityController extends Controller
{
	public function __construct() {
        $this->middleware('auth:api');
    }

    public function getActivity() {
    	$activity = Activity::get();

        $res = [
            'type'    => 'success',
            'message' => 'Get activity successfully',
            'data'    => ActivityResource::collection($activity)
        ];
        
        return response($res, 200);
    }
}
