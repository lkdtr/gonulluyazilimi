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
     return redirect('/home');
});

Auth::routes();

if($_SERVER["REMOTE_ADDR"]!="46.197.157.200") {
    Route::get('/login', function () {
        return redirect('/register');
    })->name('login');
}

Route::get('/home', [App\Http\Controllers\HomeController::class, 'home'])->name('home');
Route::post('/home', [App\Http\Controllers\HomeController::class, 'postHome']);

Route::get('/user-agreement', [App\Http\Controllers\AgreementController::class, 'userAgreement']);

Route::post('/phone-number-verification-request', [App\Http\Controllers\MobileVerificationController::class, 'postPhoneNumberVerificationRequest']);
Route::post('/phone-number-verification', [App\Http\Controllers\MobileVerificationController::class, 'postPhoneNumberVerification']);
