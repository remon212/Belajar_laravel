<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; // Mengimpor AuthController

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

// Rute untuk mendapatkan informasi pengguna yang terautentikasi
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rute untuk pendaftaran admin
Route::post("register_admin", [AuthController::class, "register"]);
Route::get("/get_user",[AuthController::class,"getUser"]);
Route::get("/get_detail_user/{id}",[AuthController::class,"getDetailUser"]);
Route::put("/update_user/{id}",[AuthController::class,"update_user"]);
Route::delete("/hapus_user/{id}",[AuthController::class,"hapus_user"]);
Route::post("/login", [AuthController::class, "login"]);
Route::get("/logout", [AuthController::class, "logout"]);