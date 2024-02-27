<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;

Route::post('/authorization', [UserController::class, 'login']);
Route::post('/registration', [UserController::class, 'reg']);

Route::post('/files', [FileController::class, 'addFiles']);
Route::post('/files/{file_id}/accesses', [FileController::class, 'addAccess']);
Route::delete('/files/{file_id}/accesses', [FileController::class, 'deleteAccess']);
Route::get('/files/disk', [FileController::class, 'getDisk']);
Route::get('/shared', [FileController::class, 'getShared']);

Route::get('/', function () {
  return view('welcome');
});
