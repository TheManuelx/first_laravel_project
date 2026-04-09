<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::get('/test', function () {
    return 'test route works';
});

Route::get('users/trashed', [UserController::class, 'trashed'])->name('users.trashed');
Route::patch('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.forceDelete');
Route::resource('users', UserController::class);
Route::get('users/update-or-create', [UserController::class, 'showUpdateOrCreateForm'])->name('users.showUpdateOrCreateForm');
Route::post('users/update-or-create', [UserController::class, 'updateOrCreate'])->name('users.updateOrCreate');
Route::get('users/update-or-create', [UserController::class, 'showUpdateOrCreateForm'])->name('users.showUpdateOrCreateForm');
Route::post('users/update-or-create', [UserController::class, 'updateOrCreate'])->name('users.updateOrCreate');
Route::get('users/trashed', [UserController::class, 'trashed'])->name('users.trashed');
Route::patch('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.forceDelete');
Route::get('users', [UserController::class, 'index'])->name('users.index');
Route::get('users-list', function () {
    return view('users.data');
})->name('users.list');
Route::get('users-data', [UserController::class, 'getUsersData'])->name('users.data');

//for ajax
Route::delete('users/{id}/soft-delete', [UserController::class, 'softDelete']);
Route::delete('users/{id}', [UserController::class, 'forceDelete']);
