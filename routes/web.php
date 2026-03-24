<?php

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
    return view('welcome');
});

use App\Http\Controllers\Admin\BookController;
Route::controller(BookController::class)->prefix('admin')->name('admin.')->group(function() {
    Route::get('book/create', 'add')->name('book.add');
    Route::post('book/create', 'create')->name('book.create');
    Route::get('book', 'index')->name('book.index');
    Route::get('book/edit', 'edit')->name('book.edit');
    Route::post('book/edit', 'update')->name('book.update');
    Route::get('book/delete', 'delete')->name('book.delete');
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

use App\Http\Controllers\BookController as PublicBookController;
Route::get('/', [PublicBookController::class, 'index'])->name('book.index');
