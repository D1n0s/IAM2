<?php

use App\Http\Controllers\VintedController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
Route::get('/', [VintedController::class, 'index']);

Route::post('/sort', [VintedController::class, 'sort'])->name('sort.sort');
Route::post('/search', [VintedController::class, 'search'])->name('search.search');
Route::post('/searchsize', [VintedController::class, 'searchsize'])->name('searchsize.searchsize');;

