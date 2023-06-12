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
Route::post('/user-infos/{user_id}', [App\Http\Controllers\UserController::class, 'postUserInfos']);

Route::get('/my-infos', [App\Http\Controllers\UserController::class, 'getMyInfos'])->name('my-infos');
Route::post('/my-infos', [App\Http\Controllers\UserController::class, 'postMyInfos']);

Route::get('/email-redirects', [App\Http\Controllers\EmailRedirectsController::class, 'getValidation']);
Route::post('/email-redirects', [App\Http\Controllers\EmailRedirectsController::class, 'postValidation'])->name('email-redirects');

Route::get('/email-forwarding', function () {
     return redirect('/email-redirects');
});
Route::post('/email-forwarding', [App\Http\Controllers\EmailRedirectsController::class, 'postForwarding'])->name('email-forwarding');

Route::get('/announcements', [App\Http\Controllers\AnnouncementController::class, 'getList'])->name('announcements');
Route::get('/new-announcement', [App\Http\Controllers\AnnouncementController::class, 'getCreate'])->name('new-announcement');
Route::post('/new-announcement', [App\Http\Controllers\AnnouncementController::class, 'postCreate']);

Route::get('/seminar-requests', [App\Http\Controllers\SeminarController::class, 'getList'])->name('seminar-requests');
Route::get('/create-seminar-request', [App\Http\Controllers\SeminarController::class, 'getCreate'])->name('create-seminar-request');
Route::post('/create-seminar-request', [App\Http\Controllers\SeminarController::class, 'postCreate']);
Route::get('/new-seminar-subject', [App\Http\Controllers\SeminarController::class, 'getCreateSubject'])->name('new-seminar-subject');
Route::post('/new-seminar-subject', [App\Http\Controllers\SeminarController::class, 'postCreateSubject']);
Route::get('/seminar-subjects', [App\Http\Controllers\SeminarController::class, 'getSubjectList'])->name('seminar-subjects');


Route::get('/reference-requests', [App\Http\Controllers\ReferenceController::class, 'getList'])->name('reference-requests');
Route::get('/create-reference-request', [App\Http\Controllers\ReferenceController::class, 'getCreate'])->name('create-reference-request');
Route::post('/create-reference-request', [App\Http\Controllers\ReferenceController::class, 'postCreate']);

Route::get('/process-logs', [App\Http\Controllers\ProcessLogController::class, 'getList'])->name('process-logs');

