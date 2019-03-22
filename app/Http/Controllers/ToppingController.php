<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topping;
class ToppingController extends Controller
{
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
}
