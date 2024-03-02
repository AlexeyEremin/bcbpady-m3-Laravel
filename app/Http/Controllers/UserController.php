<?php
/**
 * ВНИМАНИЕ!!!!!!
 * Не забывай делать комментарии это твой балл.
 * Кроме того используй PHPStorm еще раз повторюсь.
 * Для создания комментариев которые у меня используй /** и нажать ENTER
 * перед методом, а после не большой свой комментарий сверху опиши
 */

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\registerRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Регистрация в системе
     * @param  registerRequest  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function reg(registerRequest $request)
    {
        $user = User::create($request->validated());
        $user->api_token = (string)Str::uuid();
        $user->save();

        return response([
            "success" => true,
            "message" => "Success",
            "token" => $user->api_token,
        ]);
    }

    /**
     * Авторизация в системе
     * @param  LoginRequest  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $user = auth()->attempt($request->validated());
        if (!$user) {
            return response([
                "success" => false,
                "message" => "Login failed",
            ], 401);
        }

        $user = auth()->user();
        $user->api_token = (string)Str::uuid();
        $user->save();

        return response([
            "success" => true,
            "message" => "Success",
            "token" => $user->api_token,
        ]);
    }

    /**
     * Выход из системы
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function logout()
    {
        $user = auth()->user();
        $user->api_token = '';
        $user->save();

        return response([
            "success" => true,
            "message" => "Logout",
        ]);
    }
}
