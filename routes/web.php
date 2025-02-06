<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCateoryController;
use App\Http\Controllers\PaymentController;

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


// Authenticated routes
Route::group(['middleware' => 'auth'], function () {
    // Admin prefix routes
    Route::group(['middleware' => 'auth', 'prefix' => 'admin'], function () {
        // Access only for admin
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
        Route::get('/loadChangePass', [AdminController::class, 'changePass'])->name('admin.loadChangePass');
        Route::post('/changePassword', [AdminController::class, 'updatePass'])->name('admin.changePassword');

        // Edit Admin Profile
        Route::get('/edit_profile',[AdminController::class, 'edit_profile'])->name('admin.edit_profile');
        Route::put('/update_profile',[AdminController::class, 'update_profile'])->name('admin.update_profile');

        // edit admin profile
        Route::get('/edit_profile',[AdminController::class, 'edit_profile'])->name('admin.edit_profile');
        Route::put('/update_profile',[AdminController::class, 'update_profile'])->name('admin.update_profile');
        // User
        Route::get('/users', [CustomerController::class, 'index'])->name('admin.user.index');
        Route::get('/users/add', [CustomerController::class, 'create'])->name('admin.user.create');
        Route::post('/users/add', [CustomerController::class, 'store'])->name('admin.user.store');
        Route::get('/users/edit/{id}', [CustomerController::class, 'edit'])->name('admin.user.edit');
        Route::put('/users/update/{id}', [CustomerController::class, 'update'])->name('admin.user.update');
        Route::delete('/users/destroy/{id}', [CustomerController::class, 'destroy'])->name('admin.user.destroy');
        Route::post('/users/list', [CustomerController::class, 'getUsersList'])->name('admin.users.getUsersList');

        // Products entries
        Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
        Route::get('/products/add', [ProductController::class, 'create'])->name('admin.products.create');
        Route::post('/products/add', [ProductController::class, 'store'])->name('admin.products.store');
        Route::get('/products/edit/{id}', [ProductController::class, 'edit'])->name('admin.products.edit');
        Route::put('/products/update/{id}', [ProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/products/destroy/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
        Route::post('/products/list', [ProductController::class, 'getProductsList'])->name('admin.products.getProductsList');


        //Group
        Route::get('/products/groups', [GroupController::class, 'index'])->name('admin.products.groups.index');
        Route::get('/products/groups/add', [GroupController::class, 'create'])->name('admin.products.groups.create');
        Route::post('/products/groups/add', [GroupController::class, 'store'])->name('admin.products.groups.store');
        Route::get('/products/groups/edit/{id}', [GroupController::class, 'edit'])->name('admin.products.groups.edit');
        Route::put('/products/groups/update/{id}', [GroupController::class, 'update'])->name('admin.products.groups.update');
        Route::delete('/products/groups/destroy/{id}', [GroupController::class, 'destroy'])->name('admin.products.groups.destroy');


        // product categories entry
        Route::get('/products/categories/index', [ProductCateoryController::class, 'index'])->name('admin.products.category.index');
        Route::get('/products/categories/create', [ProductCateoryController::class, 'create'])->name('admin.products.category.create');
        Route::post('/products/categories/create', [ProductCateoryController::class, 'store'])->name('admin.products.category.store');
        Route::get('/products/categories/{id}/edit', [ProductCateoryController::class, 'edit'])->name('admin.products.category.edit');
        Route::put('/products/categories/{id}', [ProductCateoryController::class, 'update'])->name('admin.products.category.update');
        Route::delete('/products/categories/{id}', [ProductCateoryController::class, 'destroy'])->name('admin.products.category.destroy');
       
        Route::get('/payment', [PaymentController::class, 'index'])->name('admin.payment.index');
        Route::get('/payment/create', [PaymentController::class, 'create'])->name('admin.payment.create');
        Route::post('/payment/create', [PaymentController::class, 'store'])->name('admin.payment.store');
        Route::delete('/payment/destroy/{id}', [PaymentController::class, 'destroy'])->name('admin.payment.destroy');

        //Orders
        Route::get('/orders', [OrdersController::class, 'index'])->name('admin.orders.index');
        Route::get('/orders/add', [OrdersController::class, 'create'])->name('admin.orders.create');
        Route::post('/orders/add', [OrdersController::class, 'store'])->name('admin.orders.store');
        Route::get('/orders/edit/{id}', [OrdersController::class, 'edit'])->name('admin.orders.edit');
        Route::put('/orders/update/{id}', [OrdersController::class, 'update'])->name('admin.orders.update');
        Route::delete('/orders/destroy/{id}', [OrdersController::class, 'destroy'])->name('admin.orders.destroy');

        //Cart
        Route::post('/carts/add', [CartController::class, 'addToCart'])->name('admin.carts.addToCart');
    });
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('/', [HomeController::class, 'home'])->name('home');
    Route::get('/home', [HomeController::class, 'home'])->name('home.home');
    Route::get('/admin', [HomeController::class, 'login'])->name('login');
    Route::get('/login', [HomeController::class, 'login'])->name('home.login');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login');
    Route::get('/register', [HomeController::class, 'register'])->name('register');
    Route::post('/register', [AdminController::class, 'createUser'])->name('createUser');



    Route::get('/thanks', [HomeController::class, 'thanks'])->name('page.thanks');

    //Page Not Found
    // Route::fallback([PageController::class, 'pageNotFound'])->name('pageNotFound');
});
