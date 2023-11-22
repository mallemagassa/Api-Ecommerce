<?php


use App\Http\Controllers\Api\V1\ConversationController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProfilController;
use App\Http\Controllers\Api\V1\RegisterController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);


Route::group(['middleware'=> ['auth:sanctum']], function(){
    Route::prefix('v1')->group(function (){
        Route::apiResources([
            '/users' => UserController::class,
            '/products' => ProductController::class,
            '/orders' => OrderController::class,
            '/profils' => ProfilController::class,
            '/messages' => MessageController::class,
            '/conversations' => ConversationController::class,
        ]);

        Route::get('/selectconver/{conversation}/{receiver}', [ConversationController::class, 'selectConversation']);
    });
});