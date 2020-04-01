<?php

use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

Route::get('/', function () {
    return redirect(route('requests.index'));
})->name('home');

Auth::routes();

Route::resource('requests', 'Request\RequestController');
Route::resource('messages', 'Request\RequestMessageController');
