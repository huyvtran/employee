<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CatalogueRequest;
use App\Http\Requests\ToppingRequest;
use App\Http\Resources\CatalogueResource;
use App\Http\Resources\ToppingResource;
use App\Http\Resources\SizeResource;
use App\Http\Resources\ProductResource;
use App\Models\Store;
use App\Models\Catalogue;
use App\Models\Topping;
use DateTime;
use Illuminate\Support\Str;

class MenuController extends Controller
{
	//GET MENU
	public function getMenu($id) {
		$store = Store::findorFail($id);
		
		$res   = [
			'type'    => 'success',
			'message' => 'Get menu successfully',
			'data'    => CatalogueResource::collection($store->catalogues->load('products'))
		];
		
		return response($res, 200);
	}
	//GET CATALOGUE
	public function getCatalogue($id) {
		$catalogues = Store::findorFail($id)->catalogues()->orderBy('priority', 'desc')->orderBy('catalogue', 'asc')->get();
		
		$res   = [
			'type'    => 'success',
			'message' => 'Get catalogue successfully',
			'data'    => CatalogueResource::collection($catalogues)
		];

		return response($res, 200);
	}
	//ADD CATALOGUE
	public function addCatalogue(CatalogueRequest $request, $id) {

		$catalogue                 = new Catalogue;
		$catalogue->catalogue      = Str::lower($request->name);
		$catalogue->_catalogue     = Str::lower($request->_name);
		$catalogue->slug           = str_slug($request->name, '-');
		$catalogue->priority       = $request->priority;
		$catalogue->catalogue_show = $request->isShowed;
		$catalogue->store_id       = $id;
		$catalogue->created_at     = new DateTime;
		$catalogue->save();

		$res                       = [
			'type'    => 'success',
			'message' => 'Add catalogue successfully',
			'data'    => new CatalogueResource($catalogue)
		];

		return response($res, 201);
	}
	//EDIT CATALOGUE
	public function editCatalogue(CatalogueRequest $request, $id) {

		$find = Catalogue::where('catalogue', '=', Str::lower($request->name))->where('store_id', '=', $id)->where('id', '!=', $request->id)->first();

		if(!is_null($find)){
			return response(['Already exists taken'], 422);
		}

		$catalogue                 = Catalogue::find($request->id);
		$catalogue->catalogue      = Str::lower($request->name);
		$catalogue->_catalogue     = Str::lower($request->_name);
		$catalogue->slug           = str_slug($request->name, '-');
		$catalogue->priority       = $request->priority;
		$catalogue->catalogue_show = $request->isShowed;
		$catalogue->store_id       = $id;
		$catalogue->updated_at     = new DateTime;
		$catalogue->save();

		$res                       = [
			'type'    => 'success',
			'message' => 'Updated catalogue successfully',
			'data'    => new CatalogueResource($catalogue)
		];

		return response($res, 200);
	}
	//GET TOPPING
	public function getTopping($id) {
		$toppings = Store::findorFail($id)->toppings()->get();
		
		$res   = [
			'type'    => 'success',
			'message' => 'Get topping successfully',
			'data'    => ToppingResource::collection($toppings)
		];

		return response($res, 200);
	}
	//ADD TOPPING
	public function addTopping(ToppingRequest $request, $id) {

		$topping             = new Topping;
		$topping->name       = Str::lower($request->name);
		$topping->_name      = Str::lower($request->_name);
		$topping->price      = (float)$request->price;
		$topping->store_id   = $id;
		$topping->created_at = new DateTime;
		$topping->save();

		$res                       = [
			'type'    => 'success',
			'message' => 'Add topping successfully',
			'data'    => new ToppingResource($topping)
		];

		return response($res, 201);
	}
	//EDIT TOPPING
	public function editTopping(ToppingRequest $request, $id) {

		$find = Topping::where('name', '=', Str::lower($request->name))->where('store_id', '=', $id)->where('id', '!=', $request->id)->first();

		if(!is_null($find)){
			return response(['Already exists taken'], 422);
		}

		$topping             = Topping::find($request->id);
		$topping->name       = Str::lower($request->name);
		$topping->_name      = Str::lower($request->_name);
		$topping->price      = (float)$request->price;
		$topping->store_id   = $id;
		$topping->updated_at = new DateTime;
		$topping->save();

		$res                       = [
			'type'    => 'success',
			'message' => 'Edit topping successfully',
			'data'    => new ToppingResource($topping)
		];

		return response($res, 200);
	}
	//GET SIZE
	public function getSize($id) {
		$sizes = Store::findorFail($id)->sizes()->get();
		
		$res   = [
			'type'    => 'success',
			'message' => 'Get size successfully',
			'data'    => SizeResource::collection($sizes)
		];

		return response($res, 200);
	}
	//ADD SIZE
	public function addSize(SizeRequest $request, $id) {

		$size             = new Size;
		$size->name       = $request->name;
		$size->_name      = $request->_name;
		$size->price      = $request->price;
		$size->store_id   = $id;
		$size->created_at = new DateTime;
		$size->save();

		$res                       = [
			'type'    => 'success',
			'message' => 'Add size successfully',
			'data'    => new SizeResource($size)
		];

		return response($res, 201);
	}
	//EDIT SIZE
	public function editSize(SizeRequest $request, $id) {

		$find = Size::where('name', '=', Str::lower($request->name))->where('store_id', '=', $id)->where('id', '!=', $request->id)->first();

		if(!is_null($find)){
			return response(['Already exists taken'], 422);
		}

		$size             = Size::find($id);
		$size->name       = $request->name;
		$size->_name      = $request->_name;
		$size->price      = $request->price;
		$size->store_id   = $id;
		$size->updated_at = new DateTime;
		$size->save();

		$res                       = [
			'type'    => 'success',
			'message' => 'Edit size successfully',
			'data'    => new SizeResource($size)
		];

		return response($res, 201);
	}
	//GET PRODUCT
	public function getProduct($id) {
		$products = Store::findorFail($id)->products()->orderBy('priority', 'desc')->orderBy('name', 'asc')->get();
		
		$res   = [
			'type'    => 'success',
			'message' => 'Get product successfully',
			'data'    => ProductResource::collection($products)
		];

		return response($res, 200);
	}
	//ADD PRODUCT
	public function addProduct(CatalogueRequest $request, $id) {

		$catalogue                 = new Catalogue;
		$catalogue->catalogue      = Str::lower($request->name);
		$catalogue->_catalogue     = Str::lower($request->_name);
		$catalogue->slug           = str_slug($request->name, '-');
		$catalogue->priority       = $request->priority;
		$catalogue->catalogue_show = $request->isShowed;
		$catalogue->store_id       = $id;
		$catalogue->created_at     = new DateTime;
		$catalogue->save();

		$res                       = [
			'type'    => 'success',
			'message' => 'Add catalogue successfully',
			'data'    => new CatalogueResource($catalogue)
		];

		return response($res, 201);
	}
	//EDIT PRODUCT
	public function editProduct(CatalogueRequest $request, $id) {

		$find = Catalogue::where('catalogue', '=', Str::lower($request->name))->where('store_id', '=', $id)->where('id', '!=', $request->id)->first();

		if(!is_null($find)){
			return response(['Already exists taken'], 422);
		}

		$catalogue                 = Catalogue::find($request->id);
		$catalogue->catalogue      = Str::lower($request->name);
		$catalogue->_catalogue     = Str::lower($request->_name);
		$catalogue->slug           = str_slug($request->name, '-');
		$catalogue->priority       = $request->priority;
		$catalogue->catalogue_show = $request->isShowed;
		$catalogue->store_id       = $id;
		$catalogue->updated_at     = new DateTime;
		$catalogue->save();

		$res                       = [
			'type'    => 'success',
			'message' => 'Updated catalogue successfully',
			'data'    => new CatalogueResource($catalogue)
		];

		return response($res, 200);
	}
}
