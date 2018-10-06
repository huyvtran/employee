<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Store;
use App\Http\Resources\ProductResource;
use DateTime;
use Illuminate\Support\Str;
class ProductController extends Controller
{
    const PUBLIC_PATH = '/var/www/dofuu.com/public';

    //GET PRODUCT
    public function getProduct($id) {
        $products = Store::findorFail($id)->products()->orderByPriority('desc')->orderByName('asc')->get();
        
        return $this->respondSuccess('Get product', $products, 200, 'many');
    }

    //ADD PRODUCT
    public function addProduct(ProductRequest $request, $id) {
        $catalogue_id = $request->catalogue_id;
        
        $name         = Str::lower($request->name);
        
        $find         = Store::with(['products' => function($query) use ($name, $catalogue_id) {
            return $query->likeName($name)->byCatalogueId($catalogue_id);
        }])->findorFail($id);

        if(count($find->products)>0){

            $res      = [
                'type'    => 'error',
                'message' => 'Đã tồn tại '.$name,
                'data'    => []
            ];

            return response($res, 422);

        }
        $product = Product::create([
            'name'         => Str::lower($request->name),
            '_name'        => Str::lower($request->_name),
            'price'        => 0,
            'have_size'    => $request->haveSize,
            'have_topping' => $request->haveTopping,
            'priority'     => $request->priority,
            'catalogue_id' => $request->catalogue_id,
            'status_id'    => $request->status_id,
            'description'  => $request->description
        ]);

        foreach($request->sizes as $size) {
            
            $product->sizes()->attach([
                $size['id'] => ['price' => $size['price']]
            ]);

        }

        return $this->respondSuccess('Updated product', $product, 201, 'one');
    }

    //EDIT PRODUCT
    public function updateProduct(ProductRequest $request, $id) {
        
        $product_id   = $request->id;
        $catalogue_id = $request->catalogue_id;
        $name         = Str::lower($request->name);
        
        $find         = Store::with(['products' => function($query) use ($name, $catalogue_id, $product_id) {
            return $query->likeName($name)->byCatalogueId($catalogue_id)->hasNotId($product_id);
        }])->findorFail($id);
        
        if(count($find->products)>0){
            
            $res      = [
                'type'    => 'error',
                'message' => 'Đã tồn tại '.$name,
                'data'    => []
            ];
            
            return response($res, 422);

        }

        $product           = Product::find($request->id);
        $product->update([
            'name'         => Str::lower($request->name),
            '_name'        => Str::lower($request->_name),
            'price'        => 0,
            'have_size'    => $request->haveSize,
            'have_topping' => $request->haveTopping,
            'priority'     => $request->priority,
            'catalogue_id' => $request->catalogue_id,
            'status_id'    => $request->status_id,
            'description'  => $request->description
        ]);

        $product->sizes()->detach();

        if($product->have_size) {
            foreach($request->sizes as $size) {
                
                $product->sizes()->attach([
                    $size['id'] => ['price' => $size['price']]
                ]);

            }
        }       

        return $this->respondSuccess('Updated product', $product, 200, 'one');
    }

    //DESTROY PRODUCT
    public function destroyProduct(Request $request, $id) {
        $productId = (int) $request->id;
        
        try{
            
            $product = Product::findorFail($productId);
            $this->handleRemoveImage($product->image);
            
            Product::destroy($product->id);

            return response('Sản phẩm đã được xóa', 204);

        } catch(Exception $e) {
            
            return response('Có lỗi trong xóa sản phẩm', 500);

        }
    }

    public function updateAvatar(Request $request, $productId) {

        $avatar    = $request->avatar;
        $storeId   = (int)$request->storeId;
        $product   = Product::findorFail($productId);
        $this->handleRemoveImage($product->image);
        
        $dir       = '/storage/st/'. $storeId . '/pr/';
        
        // $path   = ProductController::PUBLIC_PATH . $dir;
        $path      = public_path($dir);
        
        $imageName = $this->imageName($product->name);
        
        $imageUrl  = $dir . $imageName;
        
        $this->handleUploadedImage($avatar, $path, $imageName);
  

        $product->update([
            'image' => $imageUrl,
        ]);

	    return $this->respondSuccess('Update image', $product, 200, 'one');
    }


    protected function imageName($name) {
        return str_replace(' ', '-', 'dofuu-6' . str_replace('-', '', date('Y-m-d')) . '-6' . md5($name) . '-6' . time() . '.jpeg');
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
                $url = public_path($image);
                // $url = ProductController::PUBLIC_PATH . $image;
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
            $res['products'] = ProductResource::collection($data);
            break;
        }

        return response($res, $status);
    }
}
