<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;

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
    $products = App\Models\Product::all();
    return view('products.index', compact('products'));
})->name('products.index');

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('add/{product}', [CartController::class, 'add'])->name('add');
    Route::put('update/{product}', [CartController::class, 'update'])->name('update');
    Route::delete('remove/{product}', [CartController::class, 'remove'])->name('remove');
    Route::post('clear', [CartController::class, 'clear'])->name('clear');
});