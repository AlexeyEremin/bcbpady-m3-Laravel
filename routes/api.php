<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;


Route::post('/authorization', [UserController::class, 'login']);
Route::post('/registration', [UserController::class, 'reg']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/files', [FileController::class, 'addFiles']);
    Route::post('/files/{file_id}/accesses', [FileController::class, 'addAccess']);
    Route::delete('/files/{file_id}/accesses', [FileController::class, 'deleteAccess']);
    Route::get('/files/disk', [FileController::class, 'getDisk']);
    Route::get('/shared', [FileController::class, 'getShared']);
});


//    Status: 403
//Content-Type: application/json
//Body:
//{
//    "message": "Login failed"
//}
//
//При попытке доступа авторизованным пользователем к функциям недоступным для него во всех запросах необходимо возвращать ответ следующего вида:
//
//Status: 403
//Content-Type: application/json
//Body:
//{
//    "message": "Forbidden for you"
//}
//
//При попытке получить не существующий ресурс необходимо возвращать ответ следующего вида:
//
//Status: 404
//Content-Type: application/json
//Body:
//{
//    "message": "Not found"
//}
//
//В случае ошибок связанных с валидацией данных во всех запросах необходимо возвращать следующее тело ответа:
//
//Status: 422
//Content-Type: application/json
//Body:
//{
//    "success": false,
//   "message": {
//    <key>: [<error message>]
//      }
//}
