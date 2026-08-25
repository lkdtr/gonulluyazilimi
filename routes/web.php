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

Route::middleware(['auth', 'role:1,2'])->group(function () {
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
});

Route::middleware(['auth', 'role:1'])->group(function () {
    Route::post('/send-penguen-welcome/{user_id}', [App\Http\Controllers\AdminController::class, 'sendPenguenWelcome'])->name('send-penguen-welcome');
    Route::delete('/remove-penguen/{user_id}', [App\Http\Controllers\AdminController::class, 'removePenguen'])->name('remove-penguen');
    Route::delete('/remove-user/{user_id}', [App\Http\Controllers\AdminController::class, 'removeUser'])->name('remove-user');
    Route::patch('/set-manager-role/{user_id}', [App\Http\Controllers\AdminController::class, 'setManagerRole'])->name('set-manager-role');
    Route::patch('/set-owner-role/{user_id}', [App\Http\Controllers\AdminController::class, 'setOwnerRole'])->name('set-owner-role');
    Route::patch('/set-user-role/{user_id}', [App\Http\Controllers\AdminController::class, 'setUserRole'])->name('set-user-role');
    Route::post('/tc-kimlik-dogrula/{user_id}', [App\Http\Controllers\AdminController::class, 'tcKimlikDogrula'])->name('tc-kimlik-dogrula');
});

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

Route::get('/email-change-request', [App\Http\Controllers\EmailChangeRequestController::class, 'create'])->middleware('auth')->name('email-change-requests.create');
Route::post('/email-change-request', [App\Http\Controllers\EmailChangeRequestController::class, 'store'])->middleware('auth')->name('email-change-requests.store');

Route::get('/announcements', [App\Http\Controllers\AnnouncementController::class, 'getList'])->name('announcements');
Route::get('/new-announcement', [App\Http\Controllers\AnnouncementController::class, 'getCreate'])->name('new-announcement');
Route::post('/new-announcement', [App\Http\Controllers\AnnouncementController::class, 'postCreate']);

Route::get('/edit-announcement/{id}', [App\Http\Controllers\AnnouncementController::class, 'getEdit'])->name('edit-announcement');
Route::post('/edit-announcement/{id}', [App\Http\Controllers\AnnouncementController::class, 'postEdit']);

Route::get('/create-seminar-request', [App\Http\Controllers\SeminarController::class, 'getCreate'])->name('create-seminar-request');
Route::get('/create-seminar-request/create', [App\Http\Controllers\SeminarController::class, 'getCreate'])->middleware('auth')->name('seminar-request.start');
Route::post('/create-seminar-request', [App\Http\Controllers\SeminarController::class, 'postCreate'])->middleware('auth')->name('seminar-request.store');
Route::get('/create-seminar-offer', [App\Http\Controllers\SeminarOfferController::class, 'create'])->name('create-seminar-offer');
Route::post('/create-seminar-offer', [App\Http\Controllers\SeminarOfferController::class, 'store'])->name('seminar-offer.store');
Route::get('/admin/seminar-requests', [App\Http\Controllers\SeminarController::class, 'getList'])->middleware(['auth', 'role:1'])->name('admin.seminar-requests');
Route::get('/admin/seminar-offers', [App\Http\Controllers\SeminarOfferController::class, 'index'])->middleware(['auth', 'role:1'])->name('admin.seminar-offers');
Route::patch('/admin/seminar-subject-proposals/{seminarSubjectProposal}/accept', [App\Http\Controllers\SeminarOfferController::class, 'acceptSubjectProposal'])->middleware(['auth', 'role:1'])->name('admin.seminar-subject-proposals.accept');
Route::get('/admin/email-change-requests', [App\Http\Controllers\EmailChangeRequestController::class, 'index'])->middleware(['auth', 'role:1'])->name('admin.email-change-requests');
Route::patch('/admin/email-change-requests/{emailChangeRequest}/approve', [App\Http\Controllers\EmailChangeRequestController::class, 'approve'])->middleware(['auth', 'role:1'])->name('admin.email-change-requests.approve');
Route::patch('/admin/email-change-requests/{emailChangeRequest}/reject', [App\Http\Controllers\EmailChangeRequestController::class, 'reject'])->middleware(['auth', 'role:1'])->name('admin.email-change-requests.reject');
Route::get('/new-seminar-subject', [App\Http\Controllers\SeminarController::class, 'getCreateSubject'])->name('new-seminar-subject');
Route::post('/new-seminar-subject', [App\Http\Controllers\SeminarController::class, 'postCreateSubject']);
Route::get('/edit-seminar-subject/{id}', [App\Http\Controllers\SeminarController::class, 'getEditSubject'])->name('edit-seminar-subject');
Route::post('/edit-seminar-subject/{id}', [App\Http\Controllers\SeminarController::class, 'postEditSubject']);
Route::get('/seminar-subjects', [App\Http\Controllers\SeminarController::class, 'getSubjectList'])->name('seminar-subjects');


Route::get('/reference-requests', [App\Http\Controllers\ReferenceController::class, 'getList'])->name('reference-requests');
Route::get('/create-reference-request', [App\Http\Controllers\ReferenceController::class, 'getCreate'])->name('create-reference-request');
Route::post('/create-reference-request', [App\Http\Controllers\ReferenceController::class, 'postCreate']);

Route::get('/process-logs', [App\Http\Controllers\ProcessLogController::class, 'getList'])->name('process-logs');

Route::get('/join-lkd-young', [App\Http\Controllers\LkdYoungController::class, 'getJoinLkdYoung'])->name('join-lkd-young');
Route::post('/join-lkd-young', [App\Http\Controllers\LkdYoungController::class, 'postJoinLkdYoung']);
Route::get('/lkd-young/announcements', [App\Http\Controllers\LkdYoungController::class, 'announcements'])->name('lkd-young.announcements');
Route::get('/lkd-young/announcements/create', [App\Http\Controllers\LkdYoungController::class, 'createAnnouncement'])->name('lkd-young.announcements.create');
Route::post('/lkd-young/announcements', [App\Http\Controllers\LkdYoungController::class, 'storeAnnouncement'])->name('lkd-young.announcements.store');
Route::get('/admin/lkd-young', [App\Http\Controllers\LkdYoungController::class, 'admin'])->middleware(['auth','role:1'])->name('admin.lkd-young');
Route::patch('/admin/lkd-young/representatives/{rep}/approve', [App\Http\Controllers\LkdYoungController::class, 'approveRepresentative'])->middleware(['auth','role:1'])->name('admin.lkd-young.representatives.approve');
Route::patch('/admin/lkd-young/representatives/{rep}/reject', [App\Http\Controllers\LkdYoungController::class, 'rejectRepresentative'])->middleware(['auth','role:1'])->name('admin.lkd-young.representatives.reject');
Route::patch('/admin/lkd-young/representatives/{rep}/announcement-permission', [App\Http\Controllers\LkdYoungController::class, 'setAnnouncementPermission'])->middleware(['auth','role:1'])->name('admin.lkd-young.representatives.announcement-permission');
Route::patch('/admin/lkd-young/announcements/{a}/approve', [App\Http\Controllers\LkdYoungController::class, 'approveAnnouncement'])->middleware(['auth','role:1'])->name('admin.lkd-young.announcements.approve');
Route::get('/representations', [App\Http\Controllers\LegalRepresentationController::class, 'index'])->middleware('auth')->name('representations.index');
Route::get('/representations/{representation}/contact-consent', [App\Http\Controllers\LegalRepresentationController::class, 'consent'])->middleware('auth')->name('representations.consent');
Route::post('/representations/{representation}/contact-consent', [App\Http\Controllers\LegalRepresentationController::class, 'storeConsent'])->middleware('auth')->name('representations.consent.store');
Route::get('/representation-candidate', [App\Http\Controllers\LegalRepresentationController::class, 'candidateForm'])->middleware('auth')->name('representations.candidate');
Route::post('/representation-candidate', [App\Http\Controllers\LegalRepresentationController::class, 'storeCandidate'])->middleware('auth')->name('representations.candidate.store');
Route::get('/representation-announcements', [App\Http\Controllers\LegalRepresentationController::class, 'announcements'])->middleware('auth')->name('representations.announcements');
Route::post('/representation-announcements', [App\Http\Controllers\LegalRepresentationController::class, 'storeAnnouncement'])->middleware('auth')->name('representations.announcements.store');
Route::get('/admin/representations', [App\Http\Controllers\LegalRepresentationController::class, 'admin'])->middleware(['auth','role:1'])->name('admin.representations');
Route::get('/admin/representations/{representation}/edit', [App\Http\Controllers\LegalRepresentationController::class, 'edit'])->middleware(['auth','role:1'])->name('admin.representations.edit');
Route::patch('/admin/representations/{representation}', [App\Http\Controllers\LegalRepresentationController::class, 'update'])->middleware(['auth','role:1'])->name('admin.representations.update');
Route::patch('/admin/representation-candidates/{candidate}/approve', [App\Http\Controllers\LegalRepresentationController::class, 'approveCandidate'])->middleware(['auth','role:1'])->name('admin.representations.candidates.approve');
Route::patch('/admin/representation-candidates/{candidate}/reject', [App\Http\Controllers\LegalRepresentationController::class, 'rejectCandidate'])->middleware(['auth','role:1'])->name('admin.representations.candidates.reject');
Route::patch('/admin/representations/{representation}/announcement-permission', [App\Http\Controllers\LegalRepresentationController::class, 'setAnnouncementPermission'])->middleware(['auth','role:1'])->name('admin.representations.announcement-permission');
Route::patch('/admin/representations/{representation}/user', [App\Http\Controllers\LegalRepresentationController::class, 'assignUser'])->middleware(['auth','role:1'])->name('admin.representations.assign-user');
Route::patch('/admin/representation-announcements/{announcement}/approve', [App\Http\Controllers\LegalRepresentationController::class, 'approveAnnouncement'])->middleware(['auth','role:1'])->name('admin.representations.announcements.approve');
