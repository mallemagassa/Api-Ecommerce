<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProfilController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\RegisterController;
use App\Http\Controllers\Api\V1\SendVerificationCode;
use App\Http\Controllers\Api\V1\ConversationController;
use App\Http\Controllers\Api\V1\FirebasePushController;

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

Broadcast::routes(["middleware" => ["auth:sanctum"]]);

require base_path('routes/channels.php');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verifyNumber', [RegisterController::class, 'verifyNumber']);
Route::post('/verifyNumberAuth', [AuthController::class, 'verifyNumberAuth']);


Route::group(['middleware'=> ['auth:sanctum']], function(){
    Route::prefix('v1')->group(function (){
        Route::apiResources([
            '/users' => UserController::class,
            //'/products' => ProductController::class, //->middleware('restrictRole:1')
            '/orders' => OrderController::class,
            '/profils' => ProfilController::class,
            '/messages' => MessageController::class,
            '/conversations' => ConversationController::class,
        ]);

        Route::apiResource('/products', ProductController::class)->middleware('restrictRole:1');
        Route::get('/checkUserIsLine/{user}', [UserController::class, 'checkUserIsLine']);
        Route::get('/selectconver/{conversation}/{receiver}', [ConversationController::class, 'selectConversation']);
        Route::get('/con/{user}/{conversation}', [ConversationController::class, 'con']);
        Route::post('/sendPhone', [SendVerificationCode::class, 'sendVerificationCode']);
        Route::get('/verifyImge', [ProfilController::class, 'verifyImge']);
        Route::get('/logout', [RegisterController::class, 'logout']);
        Route::get('/getProfilImage', [ProfilController::class, 'getProfilImage']);
        Route::get('/userAuth', [RegisterController::class, 'profile']);
        Route::post('/createCompteSeller', [UserController::class, 'createCompteSeller']);
        Route::get('/sellers', [UserController::class, 'seller']);
        Route::get('/getProductImage/{url}', [ProductController::class, 'getProductImage']);
        Route::get('/myProducts', [ProductController::class, 'myProducts']);
        Route::get('/getProfilUser/{url}', [ProfilController::class, 'getProfilUser']);
        Route::get('/getOrderImage/{url}', [OrderController::class, 'getOrderImage']);
        Route::get('/sellerProduct/{id}', [ProductController::class, 'sellerProduct']);
        Route::get('/getImageMessage/{url}', [MessageController::class, 'getImageMessage']);
        Route::get('/getImageProductM/{url}', [MessageController::class, 'getImageProductM']);
        Route::get('/deleteImageMessage/{url}', [MessageController::class, 'deleteImageMessage']);
        Route::get('/getOrderWithUser', [OrderController::class, 'getOrderWithUser']);
        Route::get('/getOrderAuth', [OrderController::class, 'getOrderAuth']);
        Route::post('/setToken', [FirebasePushController::class, 'setToken']);
        Route::post('/notification', [FirebasePushController::class, 'notification']);
        //Route::get('/deleteMessage/{messageId}', [MessageController::class, 'deleteMessage']);
    });
});

// Broadcast::routes(['middleware' => ['auth:sanctum']]);
// require base_path('routes/channels.php');