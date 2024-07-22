<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ActivationAccountController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\CategoriesControllerResource;
use App\Http\Controllers\ServicesControllerResource;
use App\Http\Controllers\PropertiesControllerResource;
use App\Http\Controllers\PropertiesHeadingControllerResource;
use App\Http\Controllers\CouponsControllerResource;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\RatesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeneralServiceController;
use App\Http\Controllers\SubjectsControllerResource;
use App\Http\Controllers\SubjectsVideosControllerResource;
use App\Http\Controllers\SubscriptionsControllerResource;
use App\Http\Controllers\VideoViewController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware'=>'changeLang'],function (){
    // auth module
    Route::group(['prefix'=>'/auth'],function (){
        Route::post('/login',[LoginController::class,'login']);
        Route::post('/activate-account',[ActivationAccountController::class,'index']);
        Route::post('/register',[RegisterController::class,'register']);
        Route::post('/forget-password',[ForgetPasswordController::class,'index']);
        Route::post('/new-password',[ForgetPasswordController::class,'new_password']);
        Route::post('/logout',[LoginController::class,'logout']);
    });
    // orders
    Route::group(['prefix'=>'/orders','middleware'=>'auth:sanctum'],function (){
       Route::get('/',[OrdersController::class,'index']);
       Route::post('/create',[OrdersController::class,'create']);
       Route::post('/update-status',[OrdersController::class,'update_status'])->middleware('admin');
       Route::post('/remove-item',[OrdersController::class,'remove_item']);
       Route::post('/cancel',[OrdersController::class,'cancel']);
       Route::post('/validate-coupon',[OrdersController::class,'validate_coupon']);
    });
    // rates
    Route::group(['prefix'=>'/rates','middleware'=>'auth:sanctum'],function (){
        Route::post('/create',[RatesController::class,'create']);
    });
    // notifications
    Route::group(['prefix'=>'/notifications','middleware'=>'auth:sanctum'],function (){
        Route::get('/',[NotificationsController::class,'index']);
        Route::post('/read-at',[NotificationsController::class,'seen']);
    });
    // profile
    Route::group(['prefix'=>'/profile','middleware'=>'auth:sanctum'],function (){
        Route::post('/update-info',[ProfileController::class,'update_info']);
    });
    // profile
    Route::group(['prefix'=>'/video-view','middleware'=>'auth:sanctum'],function (){
        Route::post('/seen',[VideoViewController::class,'seen']);
        Route::get('/statics',[VideoViewController::class,'statics']);
    });
    // admin panel
    Route::group(['prefix'=>'/dashboard','middleware'=>'auth:sanctum'],function (){
        Route::get('/users',[DashboardController::class,'users']);
        Route::get('/orders-statistics',[DashboardController::class,'orders']);
        Route::get('/orders-summary',[DashboardController::class,'orders_summary']);
        Route::post('/add-money-to-wallet',[DashboardController::class,'add_money_to_wallet']);
        Route::post('/update-tax',[DashboardController::class,'update_tax']);
        Route::get('/get-tax',[DashboardController::class,'get_tax']);
        Route::group(['prefix'=>'/notifications-schedule','middleware'=>'auth:sanctum'],function (){
            Route::post('/save',[DashboardController::class,'create_notification_content']);
        });
    });
    // resources
    Route::resources([
        'categories'=>CategoriesControllerResource::class,
        'subjects'=>SubjectsControllerResource::class,
        'subjects-videos'=>SubjectsVideosControllerResource::class,
        'subscriptions'=>SubscriptionsControllerResource::class
    ]);

    Route::post('/deleteitem',[GeneralServiceController::class,'delete_item']);

});

