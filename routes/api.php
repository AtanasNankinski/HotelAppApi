<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PositionsController;
use App\Http\Controllers\HotelActionsController;
use App\Http\Controllers\TestsController;

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

/* Unprotected Routes */
// Get requests
Route::get('/test_api', [TestsController::class, 'testApi']);
Route::get('/hotels', [HotelActionsController::class, 'getHotels']);
Route::get('/owners', [PositionsController::class, 'getAllOwners']);
Route::get('/managers', [PositionsController::class, 'getAllManagers']);
Route::get('/receptionists', [PositionsController::class, 'getAllReceptionists']);
// Post requests
//Route::post('/create_admin', [AuthController::class, 'createAdmin']); - Route for creating admin
Route::post('/login', [AuthController::class, 'login']);

/* Protected Routes */
Route::group(['middleware' => ['auth:sanctum']], function() {
	Route::post('/logout', [AuthController::class, 'logout']);
	Route::post('/register_owner', [PositionsController::class, 'registerOwner']);
	Route::post('/register_manager', [PositionsController::class, 'registerManager']);
	Route::post('/register_receptionist', [PositionsController::class, 'registerReceptionist']);
	Route::post('/add_hotel', [HotelActionsController::class, 'addHotel']);

	Route::put('/set_hotel_manager', [PositionsController::class, 'setHotelManager']);
	Route::put('/set_hotel_owner', [PositionsController::class, 'setHotelOwner']);
});