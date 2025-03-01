<?php

use App\Http\Controllers\DailyLogController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RandomQuoteController;
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

Route::controller(InvitationController::class)
    ->prefix('invitations')
    ->as('invitations.')
    ->group(function () {
        Route::post('/invite', 'invite')->name('store');
        Route::post('/accept/{invitation:code}', 'acceptInvite')->name('accept');
    });

Route::post('/daily-logs', [DailyLogController::class, 'store'])->name('daily-logs.store')->middleware('block.jane.doe');
Route::put('/daily-logs/{dailyLog}', [DailyLogController::class, 'update'])->name('daily-logs.update');
Route::delete('/daily-logs/{dailyLog}', [DailyLogController::class, 'destroy'])->name('daily-logs.delete');

Route::put('/profile/update-avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');

Route::post('random-quote', [RandomQuoteController::class, 'store'])->name('random-quote.store');
