<?php

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

Route::get('/', 'PagesController@root')->name('root');


Auth::routes();

Route::group(['middleware' => 'auth'], function() {
    // 把些路由放在 auth 这个中间件的路由组里面，因为只有已经登录的用户才能看到这个提示界面。
    Route::get('email_verification/send', 'EmailVerificationController@send')->name('email_verification.send');
    Route::get('/email_verify_notice', 'PagesController@emailVerifyNotice')->name('email_verify_notice');
    Route::get('/email_verification/verify', 'EmailVerificationController@verify')->name('email_verification.verify');
    Route::group(['middleware' => 'email_verified'], function() {

    });

});

// Route::get('send_mail',function() {
//     mail('greycoder0@gmail.com','测试邮件','这是一封测试邮件');
//     return 'send mail';
// });
