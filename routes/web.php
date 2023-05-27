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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'home'])->name('home');
Route::post('/home', [App\Http\Controllers\HomeController::class, 'postHome']);

Route::get('/user-agreement', [App\Http\Controllers\AgreementController::class, 'userAgreement']);
Route::get('/email-agreement', [App\Http\Controllers\AgreementController::class, 'emailAgreement']);

Route::post('/phone-number-verification-request', [App\Http\Controllers\MobileVerificationController::class, 'postPhoneNumberVerificationRequest']);
Route::post('/phone-number-verification', [App\Http\Controllers\MobileVerificationController::class, 'postPhoneNumberVerification']);

Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');

Route::get('/set-manager-role/{user_id}', [App\Http\Controllers\AdminController::class, 'setManagerRole'])->name('set-manager-role');
Route::get('/set-owner-role/{user_id}', [App\Http\Controllers\AdminController::class, 'setOwnerRole'])->name('set-owner-role');
Route::get('/set-user-role/{user_id}', [App\Http\Controllers\AdminController::class, 'setUserRole'])->name('set-user-role');

Route::get('/user-infos/{user_id}', [App\Http\Controllers\UserController::class, 'getUserInfos'])->name('user-infos');
Route::get('/my-infos', [App\Http\Controllers\UserController::class, 'getMyInfos'])->name('my-infos');

Route::get('/email-redirects', [App\Http\Controllers\EmailRedirectsController::class, 'getValidation']);
Route::post('/email-redirects', [App\Http\Controllers\EmailRedirectsController::class, 'postValidation'])->name('email-redirects');

Route::get('/email-forwarding', function () {
     return redirect('/email-redirects');
});
Route::post('/email-forwarding', [App\Http\Controllers\EmailRedirectsController::class, 'postForwarding'])->name('email-forwarding');

Route::get('/create-announcement', [App\Http\Controllers\AnnouncementController::class, 'getCreate'])->name('create-announcement');
Route::post('/create-announcement', [App\Http\Controllers\AnnouncementController::class, 'postCreate']);

Route::get('/list-announcements', [App\Http\Controllers\AnnouncementController::class, 'getList'])->name('list-announcements');

Route::get('/process-logs', [App\Http\Controllers\ProcessLogController::class, 'getList'])->name('process-logs');

