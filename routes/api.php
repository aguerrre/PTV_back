<?php

use App\User;
use Illuminate\Http\Request;

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

// RUTAS AUTENTICACIÓN

Route::group(['middleware' => ['cors']], function () {
    /**
     * Método GET para validar a un token.
     */
    Route::get('validate-token', function (Request $request) {
        return ['valid' => true, 'user' => $request->user()];
    })->middleware('auth:api')->name('validate-token');
    /**
     * Método POST para autenticar y/o registrar un usuario si las credenciales son correctas.
     */
    Route::post('login', 'LoginController@login')->name('login');
    /**
     * Método GET para cerrar sesión de un usuario (revocar token).
     */
    Route::get('logout', function (Request $request) {
        $request->user()->token()->revoke();
        return ['ok' => true, 'msg' => 'Usuario cerró sesión.'];
    })->middleware('auth:api')->name('logout');
    /**
     * Método POST para crear un formulario y enviar el pdf por email.
     */
    Route::post('forms/create', 'FormController@createAndSendEmail')->middleware('auth:api')->name('create-form');
});




// RUTAS FORMULARIOS