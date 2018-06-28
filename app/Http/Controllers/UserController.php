<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
	public function __construct() {
		$this->partner = Role::where('name', 'Partner')->first();
		$this->shipper = Role::where('name', 'Shipper')->first();
		$this->middleware('auth:api');
	}

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    
    public function getShipper() {

    	$users = User::where('role_id', '=', $this->shipper->id)->get();

    	$res   = [

    		'type'    => 'success',
    		'message' => 'Get shipper information successfully.',
    		'data'    => UserResource::collection($users)
    	];

    	return response($res, 200);
    }

}
