<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Store;
use App\Http\Resources\ProductResource;
class ProductController extends Controller
{
    public function updateAvatar(Request $request, $productId) {

		$avatar    = $request->avatar;
		$storeId   = (int)$request->storeId;
		$product   = Product::findorFail($productId);
		$store     = Store::findorFail($storeId);
		
		$dir       = '/storage/' . $store->user_id . '/stores/products/';

		$path   = StoreController::PUBLIC_PATH . $dir;
		// $path      = public_path($dir);

		$imageName = str_replace(' ', '-', 'dofuu-6' . str_replace('-', '', date('Y-m-d')) . '-6' . md5($product->name) . '-6' . time() . '.jpeg');
	
		$imageUrl  = $dir . $imageName;

        $this->handleUploadedImage($avatar, $path, $imageName);
     	$this->handleRemoveImage($product->image);

        $product->update([
            'image' => $imageUrl,
        ]);

	    return $this->respondSuccess('Update image', $product, 200, 'one');
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

    

    protected function respondSuccess($message, $data, $status = 200, $type)
    {
        $res = [
            'type'    => 'success',
            'message' => $message. ' successfully.',
        ];

        switch ($type) {
            case 'one':
            $res['product']  = new ProductResource($data);
            break;

            case 'many':
            $res['product'] = ProductResource::collection($data);
            break;
        }

        return response($res, $status);
    }
}
