<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RestaurantController as AdminRestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\TermController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController as UserUserController;
use App\Http\Controllers\RestaurantController as UserRestaurantController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\FavoriteController;

use App\Http\Middleware\Subscribed;
use App\Http\Middleware\NotSubscribed;
use App\Http\Controllers\SubscriptionController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// 管理者としてログインしていない状態でのみアクセス可能にするルーティング
Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::resource('restaurants', UserRestaurantController::class)->only(['index', 'show']);
    // ユーザーのルーティング
    Route::group(['middleware' => ['auth', 'verified']], function () {
        Route::resource('user',UserController::class)->only(['index', 'edit', 'update']);
        Route::resource('restaurants.reviews', ReviewController::class)->only(['index']);

        //一般ユーザとしてログイン済かつメール認証済で有料プラン未登録の場合
        Route::group(['middleware' => [NotSubscribed::class]], function () {
            Route::get('subscription/create', [SubscriptionController::class, 'create'])->name('subscription.create');
            Route::post('subscription', [SubscriptionController::class, 'store'])->name('subscription.store');
        });
        //一般ユーザとしてログイン済かつメール認証済で有料プラン登録済の場合
        Route::group(['middleware' => [Subscribed::class]], function () {
            Route::get('subscription/edit', [SubscriptionController::class, 'edit'])->name('subscription.edit');
            Route::patch('subscription', [SubscriptionController::class, 'update'])->name('subscription.update');
            Route::get('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
            Route::delete('subscription', [SubscriptionController::class, 'destroy'])->name('subscription.destroy');
            Route::resource('restaurants.reviews', ReviewController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
            Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
            Route::get('restaurants/{restaurant}/reservations/create', [ReservationController::class, 'create'])->name('restaurants.reservations.create');
            Route::post('restaurants/{restaurant}/reservations', [ReservationController::class, 'store'])->name('restaurants.reservations.store');
            Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
            Route::get('favorites', [FavoriteController::class, 'index'])->name('favorites.index');
            Route::post('favorites/{restaurant_id}', [FavoriteController::class, 'store'])->name('favorites.store');
            Route::delete('favorites/{restaurant_id}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
        });
    }); 
});



require __DIR__.'/auth.php';

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home');
    Route::resource('/users', Admin\UserController::class)->only(['index', 'show']);
    Route::resource('/restaurants', Admin\RestaurantController::class);
    Route::resource('/categories', Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('/company', Admin\CompanyController::class);
    Route::resource('/terms', Admin\TermController::class);
    
});