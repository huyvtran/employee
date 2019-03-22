<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ToppingResource;
use App\Models\Topping;
use App\Models\Store;
use Illuminate\Support\Str;
class ToppingController extends Controller
{

	//GET TOPPING
	public function get($id) {
		$toppings = Store::findorFail($id)->toppings()->get();
		return $this->respondSuccess('Get topping', $toppings);
	}

	//ADD TOPPING
	public function add(Request $request, $id) {

		// $topping             = new Topping;
		// $topping->name       = Str::lower($request->name);
		// $topping->_name      = Str::lower($request->_name);
		// $topping->price      = (float)$request->price;
		// $topping->store_id   = $id;
		// $topping->created_at = new DateTime;
		// $topping->save();

		$topping = Topping::create([
				'name'     => Str::lower($request->name),
				'_name'    => Str::lower($request->_name),
				'price'    => (float)$request->price,
				'store_id' => (int) $id
        ]);

		return $this->respondSuccess('Add topping', $topping, 'one', 201);
	}

	//DELETE TOPPING
    public function destroy(Request $request, $id) {
    	$toppingId = (int) $request->id;
		try {
    		$topping = Topping::byStoreId($id)->findorFail($toppingId);
			$topping->delete();
			return response('Phần thêm đã được xóa', 204);
		} catch(Exception $e) {
            return response('Có lỗi trong xóa phần thêm', 500);
        }
    }

    protected function respondError($message, $status = 200)
    {   
        $res = [
            'type'    => 'error',
            'message' => $message,
            'data'    => null
        ];

        return response($res, $status);
    }

    protected function respondSuccess($message, $data, $type = 'many', $status = 200)
    {
        $res = [
            'type'    => 'success',
            'message' => $message. ' successfully.',
        ];

        switch ($type) {
            case 'one':
            $res['topping']  = new ToppingResource($data);
            break;

            case 'many':
            $res['toppings'] = ToppingResource::collection($data);
            break;
        }

        return response($res, $status);
    }
}
