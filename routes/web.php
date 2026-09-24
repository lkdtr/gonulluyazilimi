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

Route::get('/agreements/{key}', [App\Http\Controllers\AgreementController::class, 'show'])->where('key', '[a-z0-9-]+')->name('agreements.show');
Route::get('/user-agreement', [App\Http\Controllers\AgreementController::class, 'userAgreement']);
Route::get('/email-agreement', [App\Http\Controllers\AgreementController::class, 'emailAgreement']);

Route::post('/phone-number-verification-request', [App\Http\Controllers\MobileVerificationController::class, 'postPhoneNumberVerificationRequest'])->middleware('throttle:3,1');
Route::post('/phone-number-verification', [App\Http\Controllers\MobileVerificationController::class, 'postPhoneNumberVerification'])->middleware('throttle:10,1');

Route::get('/my-infos', [App\Http\Controllers\UserController::class, 'getMyInfos'])->name('my-infos');
Route::post('/my-infos', [App\Http\Controllers\UserController::class, 'postMyInfos']);

Route::get('/organization/logo', [App\Http\Controllers\OrganizationFileController::class, 'logo'])->name('organization.logo');
Route::get('/organization/favicon', [App\Http\Controllers\OrganizationFileController::class, 'favicon'])->name('organization.favicon');
Route::get('/organization/images/{name}', [App\Http\Controllers\OrganizationFileController::class, 'image'])->where('name', '[A-Za-z0-9]+\.(png|jpe?g|webp|gif)')->name('organization.image');

Route::middleware('auth')->group(function () {
    // The photo moved onto the profile page.
    Route::permanentRedirect('/my-photo', '/my-infos#photo');
    Route::post('/my-photo', [App\Http\Controllers\PhotoController::class, 'store'])->middleware('throttle:10,1')->name('my-photo.store');
    Route::delete('/my-photo', [App\Http\Controllers\PhotoController::class, 'destroy'])->name('my-photo.destroy');
    Route::post('/my-data-deletion', [App\Http\Controllers\DataDeletionController::class, 'store'])->middleware('throttle:5,1')->name('my-data-deletion.store');
    Route::delete('/my-data-deletion', [App\Http\Controllers\DataDeletionController::class, 'cancel'])->name('my-data-deletion.cancel');
    Route::put('/my-consents', [App\Http\Controllers\ConsentController::class, 'update'])->name('my-consents.update');
    Route::get('/photos/{photo}', [App\Http\Controllers\PhotoController::class, 'show'])->name('photos.show');
});
