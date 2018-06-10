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
    return view('/home');
});

Auth::routes();
Route::get('login/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('login/{provider}/callback', 'Auth\LoginController@handleProviderCallback');

Route::get('/home', 'HomeController@index')->name('home');
Route::resource('accounts', 'AccountController');
Route::get('/accounts/{id}/confirm', 'AccountController@confirm');
Route::group(['middleware' => ['account']], function () {
  Route::get('account/{accountId}/transactions', 'TransactionController@index');
  Route::get('account/{accountId}/transaction/create', 'TransactionController@create');
  Route::post('account/{accountId}/transaction', 'TransactionController@store');
  Route::post('account/{accountId}/uploadOfx', 'TransactionController@uploadOfx');
  
  Route::get('account/{accountId}/invoices', 'InvoiceController@index');
  Route::get('account/{accountId}/invoice/create', 'InvoiceController@create');
  Route::post('account/{accountId}/invoice', 'InvoiceController@store');
});
Route::group(['middleware' => ['account', 'transaction']], function () {
  Route::get('account/{accountId}/transaction/{transactionId}/edit', 'TransactionController@edit');
  Route::get('account/{accountId}/transaction/{transactionId}/confirm', 'TransactionController@confirm');
  Route::put('account/{accountId}/transaction/{transactionId}', 'TransactionController@update');
  Route::delete('account/{accountId}/transaction/{transactionId}', 'TransactionController@destroy');
});
Route::group(['middleware' => ['account', 'invoice']], function () {
  Route::get('account/{accountId}/invoice/{invoiceId}/edit', 'InvoiceController@edit');
  Route::get('account/{accountId}/invoice/{invoiceId}/confirm', 'InvoiceController@confirm');
  Route::put('account/{accountId}/invoice/{invoiceId}', 'InvoiceController@update');
  Route::delete('account/{accountId}/invoice/{invoiceId}', 'InvoiceController@destroy');
});
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

