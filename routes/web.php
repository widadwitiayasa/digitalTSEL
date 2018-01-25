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



Route::get('/', function () {
    return view('welcome');
});
Route::get('/upload', 'Admin\CreateController@uploadGET');
Route::post('/upload', 'Admin\CreateController@uploadPOST');

Route::get('/request','Manager\CreateController@requestGET');
Route::post('/request', 'Manager\CreateController@requestPOST');
Route::get('/report','Manager\ReadController@reportGET');

//ajax
Route::get('/type','Manager\ReadController@findType');

//KALAU MENGIRIM DATA ATAU DI FORM ITU ADA METHOD POST MAKA METHOD DI ROUTE NYA POST
//SELAIN POST DAN MISAL CUMA NGAKSES HALAMAN TANPA INPUT DATA BERARTI METHODNYA GET

Route::get('/target','Admin\TargetController@showTarget');
Route::post('/target','Admin\TargetController@inputTarget');
Route::post('/target/{ID}','Admin\TargetController@editTarget');
Route::get('/download','Admin\DownloadController@downloadCSV');
Route::get('/artikel','PostsController@store');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
