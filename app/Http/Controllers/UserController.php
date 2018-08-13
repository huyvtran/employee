<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
	public function __construct() {
		$this->partner  = Role::where('name', 'Partner')->first();
		$this->shipper  = Role::where('name', 'Shipper')->first();
        $this->customer = Role::where('name', 'Customer')->first();
        $this->middleware('auth:api');
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    
    public function getCustomer() {
        $users = User::where('role_id', '=', $this->customer->id)->get();

        $res   = [
            'type'    => 'success',
            'message' => 'Get customer information successfully.',
            'data'    => UserResource::collection($users)
        ];

        return response($res, 200);
    }

    public function showCustomer($id) {
        $user = User::findorFail($id);
        if(!empty($user)) {

            $res   = [
                'type'    => 'success',
                'message' => 'Get customer information successfully.',
                'data'    => new UserResource($user)
            ];

            return response($res, 200);
        }

        $res   = [
            'type'    => 'error',
            'message' => 'Something went wrong.'
        ];

        return response($res, 500);

    }

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
