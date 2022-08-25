<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
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



Route::get('/', function () {
    return view('solicitud');
});

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');


Route::post('/registro-solicitante', 'App\Http\Controllers\SolicitanteController@registroSolicitante');
Route::get('/validar-dinardap/{cedula}', 'App\Http\Controllers\SolicitanteController@consultaDinardap');

Route::get('/video-chat', 'App\Http\Controllers\VideoChatController@index')->middleware('auth');

Route::post('/video/call-user', 'App\Http\Controllers\VideoChatController@callUser');
Route::post('/video/call-solicitante', 'App\Http\Controllers\VideoChatController@callSolicitante');
Route::post('/video/logueo', 'App\Http\Controllers\VideoChatController@loguearSolicitante');
Route::post('/video/accept-call', 'App\Http\Controllers\VideoChatController@acceptCall');
Route::post('/video/end-call', 'App\Http\Controllers\VideoChatController@endCall');


Route::group(['middleware' => ['auth']], function () {

    Route::get('/video-chat2', function () {
        // fetch all users apart from the authenticated user
        $users = User::where('id', '<>', Auth::id())->get();
        $usuario=User::where('id', Auth::id())->first()['tipo_usuario'];
        return view('video-chat', ['users' => $users, 'usuario_logueado' => $usuario]);
    });

    // Endpoints to alert call or receive call.
    // Route::post('/video/call-user', 'App\Http\Controllers\VideoChatController@callUser');
    // Route::post('/video/accept-call', 'App\Http\Controllers\VideoChatController@acceptCall');

  
});

/**
 * When you clone the repository, comment out
 *  Auth::routes(['register' => false]);
 * and uncomment
 *   Auth::routes()
 * so that you can register new users. I disabled the registration endpoint so that my hosted demo won't be abused.
 * 
 */
Auth::routes();
// Auth::routes(['register' => false]);
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index']);
