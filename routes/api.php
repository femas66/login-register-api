<?php

use App\Helpers\ResponseHelper;
use App\Models\User;
use App\Models\visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', function () {
        return ResponseHelper::success(auth()->user());
    });
    Route::post('logout', function (Request $request) {
        Auth::user()->currentAccessToken()->delete();
        $data = ['ip' => $request->ip()];
        visitor::create($data);
        return ResponseHelper::success(Auth::user()->token,'success logout');
    });
});

Route::post('login', function (Request $request) {
    if (auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
        $data['token'] = auth()->user()->createToken('auth_token')->plainTextToken;
        $data['user'] = auth()->user();
        $x = ['ip' => $request->ip()];
        visitor::create($x);

        return ResponseHelper::success($data);
    }
    else {
        return ResponseHelper::error(null, "Username / password salah");
    }
});

Route::post('register', function (Request $request) {
    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password)
    ]);
    return ResponseHelper::success(null, "Berhasil register");
});
