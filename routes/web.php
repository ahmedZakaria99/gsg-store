<?php

use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\NotificationsController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Models\Order;
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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/get-user', [HomeController::class, 'getUser'])->name('get-user');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';

Route::resource('/admin/categories', CategoriesController::class)->middleware('auth');

Route::get('/admin/products/trash', [ProductsController::class, 'trash'])
    ->name('products.trash');
Route::put('/admin/products/trash/{id?}', [ProductsController::class, 'restore'])
    ->name('products.restore');
Route::delete('/admin/products/trash/{id?}', [ProductsController::class, 'forceDelete'])
    ->name('products.force-delete');
Route::resource('/admin/products', ProductsController::class)
    ->middleware('auth');
Route::resource('/admin/roles', RolesController::class)
    ->middleware('auth');

Route::get('/admin/notifications', [NotificationsController::class, 'index'])
    ->name('notifications');
Route::get('/admin/notification/{id}', [NotificationsController::class, 'show'])
    ->name('notification.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');

Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('auth');

Route::get('/orders', function () {
    return Order::all();
})->name('orders');

Route::get('products', [\App\Http\Controllers\ProductsController::class, 'index'])
    ->name('products');
Route::get('products/{slug}', [\App\Http\Controllers\ProductsController::class, 'show'])
    ->name('product.details');

