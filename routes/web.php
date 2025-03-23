<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorksheetController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [UserController::class, 'home'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('worksheet/search/', [WorksheetController::class, "search"])->name("worksheet.search");

    Route::patch('worksheet/close/{worksheet}', [WorksheetController::class, 'close'])->name('worksheet.close');

    Route::post('worksheet/status', [WorksheetController::class, 'move'])->name('worksheet.move');

    Route::resource('worksheet', WorksheetController::class);

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('user', [UserController::class, 'show'])->name('user');

    Route::get('register', [UserController::class, 'create']);

    Route::post('register', [UserController::class, 'store'])->name('register');

    Route::post('new_password', [UserController::class, 'newPassword'])->name('user.new_password');

    Route::post('user/update-image', [UserController::class, 'setImage'])->name('user.new_image');

    Route::resource('ticket', TicketController::class);

    Route::patch('ticket/close/{ticket}', [TicketController::class, 'close'])->name('ticket.close');

    Route::post('ticket/status', [TicketController::class, 'move'])->name('ticket.move');

    Route::resource('customer', CustomerController::class);

    Route::resource('company', CompanyController::class);
});
