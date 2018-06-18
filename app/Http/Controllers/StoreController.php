<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Role;
use App\Models\User;
use App\Models\Store;
use App\Models\StoreStatus;
use App\Http\Resources\StoreResource;
use DateTime;

class StoreController extends Controller
{

    public function __construct() {
        $this->role_partner = Role::where('name', 'Partner')->first();
        $this->store_status = StoreStatus::where('store_status_name', 'Đóng cửa')->first(); 
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getStore()
    {
        $stores = Store::get();

        $res = [
            'type'    => 'success',
            'message' => 'Get stores successfully',
            'data'    => StoreResource::collection($stores->load('activities', 'user'))
        ];        
        
        return response($res, 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addStore(Request $request)
    {
        $userForm  = $request->user;
        $storeForm = $request->store;

        $validator = Validator::make($request->all(),[
            'user.email' => 'unique:ec_users,email',
            'user.phone' => 'unique:ec_users,phone'
        ]);
        
        if($validator->fails()){
            return response($validator->errors()->getMessages(),422);
        }

        if((int)$this->role_partner->id === (int)$userForm['role_id']) {
            //Add user
            $user             = new User;
            $user->name       = $userForm['name'];
            $user->email      = $userForm['email'];
            $user->password   = bcrypt($userForm['password']);
            $user->birthday   = $userForm['birthday'];
            $user->gender     = $userForm['gender'];
            $user->address    = $userForm['address'];
            $user->lat        = $userForm['lat'];
            $user->lng        = $userForm['lng'];
            $user->phone      = $userForm['phone'];
            $user->have_store = 1;
            $user->actived    = $userForm['isActived'];
            $user->role_id    = $this->role_partner->id;
            $user->created_at = new DateTime;
            $user->save();
            //Add store
            $store                = new Store;
            $store->store_name    = $storeForm['name'];
            $store->store_slug    = str_slug($storeForm['name'], '-');
            $store->store_phone   = $storeForm['phone'];
            $store->preparetime   = (int)$storeForm['preparetime'];
            $store->store_address = $storeForm['address'];
            $store->lat           = $storeForm['lat'];
            $store->lng           = $storeForm['lng'];
            $store->priority      = $storeForm['priority'];
            $store->user_id       = $user->id;
            $store->district_id   = (int)$storeForm['district_id'];
            $store->type_id       = (int)$storeForm['type_id'];
            $store->status_id     = $this->store_status->id;
            
            $store->verified      = $storeForm['isVerified'];
            $store->store_show    = $storeForm['isShowed'];
            $store->created_at    = new DateTime;
            $store->save();

            // Store Avatar
            if(!is_null($storeForm['avatar'])) {
                $data              = $storeForm['avatar'];
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode (',', $data);
                $data              = base64_decode($data);
                $imageName         = str_replace(' ','-', 'dofuu-'.str_replace('-','', date('Y-m-d')).'-'.$store->id.'-'.time(). '.jpeg');
                $path = '/var/www/dofuu.com/public/storage/'.$store->user_id.'/stores/av/';
                if(!file_exists($path)){
                    mkdir($path, 0755, true);
                }

                file_put_contents($path . $imageName, $data);
                $imageUrl            = '/storage/'.$user->id.'/stores/av/'.$imageName;
                $store->store_avatar = $imageUrl;
                $store->save();
            }

            $res = [
                'type'    => 'success',
                'message' => 'Added store successfully',
                'data'    => new StoreResource($store->load('activities', 'user'))
            ];      

            return response($res, 201);
        }

        return response(['Something went wrong'], 500);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showStore($id)
    {
        $store = Store::findorFail($id);

        $res = [
            'type'    => 'success',
            'message' => 'Added store successfully',
            'data'    => new StoreResource($store->load('activities', 'user'))
        ];

        return response($res, 200);      
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStore(Request $request, $id)
    {
        // $validator = Validator::make($request->all(),[
        //     'store' => Rule::unique('ec_payment_methods','payment_name')->ignore($id),
        // ]);
        // if($validator->fails()){
        //     return response($validator->errors()->getMessages(),422);
        // }
        //Update Store
        $store                = Store::find($id);
        $store->store_name    = $request->name;
        $store->store_slug    = str_slug($request->name, '-');
        $store->store_phone   = $request->phone;
        $store->preparetime   = (int)$request->preparetime;
        $store->store_address = $request->address;
        $store->lat           = $request->lat;
        $store->lng           = $request->lng;
        $store->district_id   = (int)$request->district_id;
        $store->type_id       = (int)$request->type_id;
        $store->status_id     = $this->store_status->id;
        $store->priority      = $request->priority;
        $store->store_show    = $request->isShowed;
        $store->verified      = $request->isVerified;
        $store->updated_at    = new DateTime;
        // Store Avatar
        if(!is_null($request->avatar)) {
            if($store->store_avatar !== $request->avatar && !is_null($store->store_avatar)) {
                $url               = $store->store_avatar;
                $oldPath = '/var/www/dofuu.com/public';
                unlink($oldPath.$url);
                $data              = $request->avatar;
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode (',', $data);
                $data              = base64_decode($data);
                $imageName         = str_replace(' ','-', 'dofuu-'.str_replace('-','', date('Y-m-d')).'-'.$store->id.'-'.time(). '.jpeg');

                // //path linux
                // $path = '/var/www/dofuu.xyz/public/storage/'.$store->user_id.'/stores/av/';
                $path = '/var/www/dofuu.com/public/storage/'.$store->user_id.'/stores/av/';
                // $path = public_path('storage/'.$store->user_id.'/stores/av/');
                if(!file_exists($path)){
                    mkdir($path, 0755, true);
                }
                file_put_contents($path . $imageName, $data);
                $imageUrl            = '/storage/'.$store->user_id.'/stores/av/'.$imageName;
                $store->store_avatar = $imageUrl;

            } else if(is_null($store->store_avatar)) {
                $data              = $request->avatar;
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode (',', $data);
                $data              = base64_decode($data);
                $imageName         =str_replace(' ','-', 'dofuu-'.str_replace('-','', date('Y-m-d')).'-'.$store->id.'-'.time(). '.jpeg');
                // $path              = public_path('storage/'.$store->user_id.'/stores/av/');
                // $path = '/var/www/dofuu.xyz/public/storage/'.$store->user_id.'/stores/av/';
                $path = '/var/www/dofuu.com/public/storage/'.$store->user_id.'/stores/av/';
                if(!file_exists($path)){
                    mkdir($path, 0755, true);
                }
                file_put_contents($path . $imageName, $data);
                $imageUrl            = '/storage/'.$store->user_id.'/stores/av/'.$imageName;
                $store->store_avatar = $imageUrl;
            }

        }
        $store->save();

        
        $res = [
            'type'    => 'success',
            'message' => 'Updated store successfully',
            'data'    => new StoreResource($store->load('activities', 'user'))
        ];      

        return response($res, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function updateActivity(Request $request, $id) {
        $store = Store::findorFail($id);
        $data  = $request->data;
        for($i = 0; $i < count($data); $i++) {
            $data[$i]['times'] = serialize($data[$i]['times']);
        }
        $store->activities()->detach();
        $store->activities()->sync($data);
        $res = [
            'type'    => 'success',
            'message' => 'Update activities successfully',
            'data'    => new StoreResource($store->load('activities', 'user'))
        ];
        return response($res, 200);
    }
}
