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
     //return redirect('/home');
});

Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

Route::get('/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/password/confirm', [App\Http\Controllers\Auth\ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('/password/confirm', [App\Http\Controllers\Auth\ConfirmPasswordController::class, 'confirm']);
Route::get('/change-password', [App\Http\Controllers\PasswordChangeController::class, 'edit'])->middleware('auth')->name('password.change.edit');
Route::put('/change-password', [App\Http\Controllers\PasswordChangeController::class, 'update'])->middleware('auth')->name('password.change.update');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'home'])->name('home');
Route::post('/home', [App\Http\Controllers\HomeController::class, 'postHome']);

Route::get('/user-agreement', [App\Http\Controllers\AgreementController::class, 'userAgreement']);
Route::get('/email-agreement', [App\Http\Controllers\AgreementController::class, 'emailAgreement']);

Route::post('/phone-number-verification-request', [App\Http\Controllers\MobileVerificationController::class, 'postPhoneNumberVerificationRequest'])->middleware('throttle:3,1');
Route::post('/phone-number-verification', [App\Http\Controllers\MobileVerificationController::class, 'postPhoneNumberVerification'])->middleware('throttle:10,1');

Route::get('/my-infos', [App\Http\Controllers\UserController::class, 'getMyInfos'])->name('my-infos');
Route::post('/my-infos', [App\Http\Controllers\UserController::class, 'postMyInfos']);
