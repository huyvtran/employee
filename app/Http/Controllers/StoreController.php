<?php

namespace App\Http\Controllers;

use App\Http\Resources\StoreResource;
use App\Models\Role;
use App\Models\Store;
use App\Models\StoreStatus;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{

    const PUBLIC_PATH = '/var/www/dofuu.com/public';

    public function __construct()
    {
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
        $stores = Store::orderByPriority('desc')->get();

        return $this->respondSuccess('Get store', $stores->load('activities', 'user'), 200, 'many');
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

        $validator = Validator::make($request->all(), [
            'user.email' => 'unique:ec_users,email',
            'user.phone' => 'unique:ec_users,phone',
        ]);

        if ($validator->fails()) {
            return response($validator->errors()->getMessages(), 422);
        }

        if ((int) $this->role_partner->id === (int) $userForm['role_id']) {
            //Add user
            $user = new User;
            $user = User::create([
                'name'       => $userForm['name'],
                'email'      => $userForm['email'],
                'password'   => bcrypt($userForm['password']),
                'birthday'   => $userForm['birthday'],
                'gender'     => $userForm['gender'],
                'address'    => $userForm['address'],
                'lat'        => $userForm['lat'],
                'lng'        => $userForm['lng'],
                'phone'      => $userForm['phone'],
                'have_store' => 1,
                'actived'    => $userForm['isActived'],
                'role_id'    => $this->role_partner->id,
            ]);

            $store = Store::create([
                'store_name'    => $storeForm['name'],
                'store_slug'    => str_slug($storeForm['name'], '-'),
                'store_phone'   => $storeForm['phone'],
                'preparetime'   => (int) $storeForm['preparetime'],
                'store_address' => $storeForm['address'],
                'lat'           => $storeForm['lat'],
                'lng'           => $storeForm['lng'],
                'priority'      => $storeForm['priority'],
                'discount'      => $storeForm['discount'],
                'user_id'       => $user->id,
                'district_id'   => (int) $storeForm['district_id'],
                'type_id'       => (int) $storeForm['type_id'],
                'status_id'     => $this->store_status->id,
                'verified'      => $storeForm['isVerified'],
                'store_show'    => $storeForm['isShowed'],
            ]);

            return $this->respondSuccess('Add store', $store->load('activities', 'user'), 201, 'one');
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

        return $this->respondSuccess('Show store', $store->load('activities', 'user'), 200, 'one');
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
        $store = Store::find($id);
        $store->update([
            'store_name'    => $request->name,
            'store_slug'    => str_slug($request->name, '-'),
            'store_phone'   => $request->phone,
            'preparetime'   => (int) $request->preparetime,
            'store_address' => $request->address,
            'lat'           => $request->lat,
            'lng'           => $request->lng,
            'priority'      => $request->priority,
            'discount'      => $request->discount,
            'district_id'   => (int) $request->district_id,
            'type_id'       => (int) $request->type_id,
            'status_id'     => $this->store_status->id,
            'verified'      => $request->isVerified,
            'store_show'    => $request->isShowed,
        ]);

        return $this->respondSuccess('Update store', $store->load('activities', 'user'), 200, 'one');
    }
    
    public function updateActivity(Request $request, $id)
    {
        $store = Store::findorFail($id);
        $data  = $request->data;
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['times'] = serialize($data[$i]['times']);
        }
        $store->activities()->detach();
        $store->activities()->sync($data);
        return $this->respondSuccess('Update activities', $store->load('activities', 'user'), 200, 'one');
    }

    public function updateAvatar(Request $request, $store_id)
    {
        $avatar    = $request->avatar;
        $storeId   = (int) $store_id;
        $store     = Store::findorFail($storeId);
        $dir       = '/storage/' . $store->user_id . '/stores/av/';
        $path      = StoreController::PUBLIC_PATH . $dir;
        // $path   = public_path($dir);
        $imageName = str_replace(' ', '-', 'dofuu-6' . str_replace('-', '', date('Y-m-d')) . '-6' . md5($store->id) . '-6' . time() . '.jpeg');
        $imageUrl  = $dir . $imageName;

        $this->handleUploadedImage($avatar, $path, $imageName);
        $this->handleRemoveImage($store->store_avatar);

        $store->update([
            'store_avatar' => $imageUrl,
        ]);

        return $this->respondSuccess('Update image', $store->load('activities', 'user'), 200, 'one');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    protected function handleUploadedImage($image, $path, $name)
    {

        if (!is_null($image)) {
            $data              = $image;
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data              = base64_decode($data);

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            file_put_contents($path . $name, $data);
        }
    }

    protected function handleRemoveImage($image)
    {

        if (!is_null($image)) {
            if (substr($image, 1, 7) === 'storage') {
                // $url = public_path($image);
                $url = UserController::PUBLIC_PATH . $image;
                unlink($url);
            }
        }
    }

    

    protected function respondSuccess($message, $data, $status = 200, $type, $pagination = [])
    {
        $res = [
            'type'    => 'success',
            'message' => $message . ' successfully.',
        ];

        switch ($type) {
            case 'one':
            $res['store']  = new StoreResource($data);
            break;

            case 'many':
            $res['stores'] = StoreResource::collection($data);
            break;
        }

        return response($res, $status);
    }
}
