<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;

Route::post('/authorization', [UserController::class, 'login']);
Route::post('/registration', [UserController::class, 'reg']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/files/{file}', [FileController::class, 'downloadFile']);
    Route::post('/files', [FileController::class, 'addFiles']);
    Route::delete('/files/{file}', [FileController::class, 'deleteFile']);
    Route::post('/files/{file}/accesses', [FileController::class, 'addAccess']);
    Route::delete('/files/{file}/accesses', [FileController::class, 'deleteAccess']);
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
