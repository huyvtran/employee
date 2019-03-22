<?php

use App\Mail\TestMail;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
use Illuminate\Support\Facades\Mail;

Route::group(['middleware' => ['api']], function () {
	//TEST MAIL
	Route::get('/SendMail', function () {
		Mail::to('sp.dofuu@gmail.com')->send(new TestMail());
	});
	//LOGIN
	Route::post('/login', 'AuthController@login');
	//LOGOUT
	Route::post('/logout', 'AuthController@logout');
	//REFRESH TOKEN
	Route::post('/refresh', 'AuthController@refresh');
	//GET INFORMATION
	Route::post('/dfe', 'AuthController@me');
	//GET CITY
	Route::get('/GetCity', 'CityController@getCity');

	Route::group(['prefix' => 'City'], function() {
		Route::get('/GetCityWithStore', 'CityController@getCityWithStore');
	});
	//GET DISTRICT
	Route::get('/GetDistrict', 'DistrictController@getDistrict');
	//GET STORE
	Route::get('/GetStore', 'StoreController@getStore');
	
	Route::group(['prefix' => 'Store'], function() {
		//GET STORE VERIFIED
		Route::get('/GetStoreVerified', 'StoreController@getStoreVerified');
		
	});

	//GET STORE BY ID
	Route::get('/GetStore/{sid}', 'StoreController@showStore');
	//ADD STORE
	Route::post('/AddStore', 'StoreController@addStore');
	//UPDATE STORE
	Route::put('/UpdateStore/{sid}', 'StoreController@updateStore');
	//UPDATE IMAGE STORE
	Route::post('/UpdateStore/{sid}/Avatar', 'StoreController@updateAvatar');
	//UPDATE ACTIVITY FOR STORE
	Route::post('/UpdateStore/{sid}/Activity', 'StoreController@updateActivity');
	//GET TYPE
	Route::get('/GetType', 'TypeController@getType');
	//GET ACTIVITY
	Route::get('/GetActivity', 'ActivityController@getActivity');
	//GET MENU
	Route::get('/GetStore/{sid}/Menu', 'MenuController@getMenu');
	//GET CATALOGUE
	Route::get('/GetStore/{sid}/Menu/Catalogue', 'MenuController@getCatalogue');
	//ADD CATALOGUE
	Route::post('/GetStore/{sid}/Menu/Catalogue/Add', 'MenuController@addCatalogue');
	//EDIT CATALOGUE
	Route::post('/GetStore/{sid}/Menu/Catalogue/Edit', 'MenuController@editCatalogue');
	//GET TOPPING
	Route::get('/GetStore/{sid}/Menu/Topping', 'ToppingController@get');
	//ADD TOPPING
	Route::post('/GetStore/{sid}/Menu/Topping/Add', 'ToppingController@add');
	//EDIT TOPPING
	Route::post('/GetStore/{sid}/Menu/Topping/Edit', 'ToppingController@edit');
	//REMOVE TOPPING
	Route::post('/GetStore/{sid}/Menu/Topping/Delete', 'ToppingController@destroy');
	//GET SIZE
	Route::get('/GetStore/{sid}/Menu/Size', 'MenuController@getSize');
	//ADD SIZE
	Route::post('/GetStore/{sid}/Menu/Size/Add', 'MenuController@addSize');
	//EDIT SIZE
	Route::post('/GetStore/{sid}/Menu/Size/Edit', 'MenuController@editSize');
	//GET PRODUCT
	Route::get('/GetStore/{sid}/Menu/Product', 'ProductController@getProduct');
	//ADD PRODUCT
	Route::post('/GetStore/{sid}/Menu/Product/Add', 'ProductController@addProduct');
	//EDIT PRODUCT
	Route::post('/GetStore/{sid}/Menu/Product/Edit', 'ProductController@updateProduct');
	//DELETE PRODUCT
	Route::post('/GetStore/{sid}/Menu/Product/Delete', 'ProductController@destroyProduct');
	//EDIT PRODUCT AVATAR
	Route::post('/Menu/Product/{productId}/UpdateAvatar', 'ProductController@updateAvatar');
	//GET PRODUCT STATUS
	Route::get('/GetProductStatus', 'MenuController@getProductStatus');
	//GET ORDER STATUS
	Route::get('/GetOrderStatus', 'OrderStatusController@getOrderStatus');
	//GET ORDER
	Route::post('/GetOrderByFilter', 'OrderController@getOrderByFilter');
	//GET ORDER DETAILS
	Route::post('/GetOrder/{oid}/Details', 'OrderController@getOrderDetail');
	//PROCESS ORDER
	Route::post('/GetOrder/{oid}/Details/ChangeStatus', 'OrderController@changeStatusOrder');
	//CHOOSE SHIPPER
	Route::post('/GetOrder/{oid}/Details/ChooseShipper', 'OrderController@chooseShipper');
	//CANCEL ORDER
	Route::post('/GetOrder/{oid}/Details/CancelOrder', 'OrderController@cancelOrder');
	//GET CUSTOMER
	Route::get('/GetUser/Customers', 'UserController@getCustomer');
	//GET CUSTOMER DETAIL
	Route::get('/GetUser/{uid}/Customer', 'UserController@showCustomer');
	//GET SHIPPER
	Route::post('/GetUser/Shippers', 'UserController@getShipper');
	//GET READ NOTIFICATIONS
	Route::post('/ReadNotification', 'NotificationController@readNotification');
	//GET NOTIFICATIONS
	Route::post('/GetNotification', 'NotificationController@getNotification');
	//GET TEST
	Route::get('/GetTest', 'OrderController@calculatePoint');
	

	Route::group(['prefix' => 'Coupon'], function() {
		//GET COUPON STATUS
		Route::get('/GetCouponStatus', 'CouponStatusController@getStatus');
		//GET COUPON
		Route::get('/GetCoupon', 'CouponController@getCoupon');
		//SHOW COUPON
		Route::get('/{couponId}/ShowCoupon', 'CouponController@showCoupon');
		//ADD COUPON
		Route::post('/AddCoupon', 'CouponController@addCoupon');
		//UPDATE COUPON
		Route::post('/{couponId}/UpdateCoupon/', 'CouponController@updateCoupon');
		//UPDATE COUPON STORE
		Route::post('/{couponId}/UpdateStore', 'CouponController@updateStore');
	});	


});