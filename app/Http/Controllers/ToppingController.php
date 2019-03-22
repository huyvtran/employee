<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ToppingResource;
use App\Models\Topping;
use App\Models\Store;
class ToppingController extends Controller
{

	//GET TOPPING
	public function get($id) {
		$toppings = Store::findorFail($id)->toppings()->get();
		return $this->respondSuccess('Get topping', $toppings);
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
