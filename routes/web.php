<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
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

        // Products entries
        Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
        Route::get('/products/add', [ProductController::class, 'create'])->name('admin.products.create');
        Route::post('/products/add', [ProductController::class, 'store'])->name('admin.products.store');
        Route::get('/products/edit/{id?}', [ProductController::class, 'edit'])->name('admin.products.edit');
        Route::get('/products/update/{id}', [ProductController::class, 'update'])->name('admin.products.update');

        // product categories entry

        Route::get('/products/categories/index', [ProductCateoryController::class, 'index'])->name('admin.products.category.index');
        Route::get('/products/categories/create', [ProductCateoryController::class, 'create'])->name('admin.products.category.create');
        Route::post('/products/categories/create', [ProductCateoryController::class, 'store'])->name('admin.products.category.store');
        Route::get('/products/categories/{id}/edit', [ProductCateoryController::class, 'edit'])->name('admin.products.category.edit');
        Route::put('/products/categories/{id}', [ProductCateoryController::class, 'update'])->name('admin.products.category.update');
      
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
