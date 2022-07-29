<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthCoontroller;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\SortingController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\BailingController;
use App\Http\Controllers\RecycleController;
use App\Http\Controllers\ItemsController;


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

Route::middleware('auth:sanctum', 'access')->get('/user', function (Request $request) {
    return $request->user();
});
//users
Route::post('createUser', [AuthCoontroller::class, 'register']);
Route::post('login', [AuthCoontroller::class, 'login']);
Route::group(['middleware' => ['auth:api','access']], function(){

//create and get collection
Route::post('collection', [CollectionController::class, 'collect']);
Route::get('getCollection', [CollectionController::class, 'getCollection']);


//create and get sorting
Route::post('sorting', [SortingController::class, 'sorted']);
Route::get('getSorting', [SortingController::class, 'getSorted']);

//create and get location
Route::post('location', [LocationController::class, 'location']);
Route::get('getLocation', [LocationController::class, 'getLocation']);

//create and get bailing
Route::post('bailing', [BailingController::class, 'bailing']);
Route::get('getBailing', [BailingController::class, 'getBailing']);

//create and get factory
Route::post('factory', [FactoryController::class, 'factory']);
Route::get('getFactory', [FactoryController::class, 'getFactory']);


//create, get and update  transfer
Route::post('transfer', [TransferController::class, 'transfer']);
Route::get('getTransfer', [TransferController::class, 'getTransfer']);
Route::post('updateTransfer', [TransferController::class, 'updateTransfer']);

//get history
Route::get('getHistory', [TransferController::class, 'history']);

//get all items
Route::get('bailingList', [ItemsController::class, 'bailingList']);
Route::get('itemList', [ItemsController::class, 'itemList']);


//create and get sales
Route::post('sales', [SalesController::class, 'sales']);
Route::get('getSales', [SalesController::class, 'getSales']);

//create and get recycle
Route::post('recycle', [RecycleController::class, 'recycle']);
Route::get('getRecycle', [RecycleController::class, 'getRecycle']);

Route::post('deviceId', [AuthCoontroller::class, 'deviceId']);
});