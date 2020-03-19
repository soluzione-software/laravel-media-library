<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/laravel-media-library')
    ->name('laravel-media-library.')
    ->namespace('\SoluzioneSoftware\LaravelMediaLibrary\Http\Controllers')
    ->group(function (){
        Route::prefix('/pending')
            ->name('pending.')
            ->group(function (){
                Route::post('/', 'PendingController@store')->name('store');
                Route::put('/{pendingMedia}', 'PendingController@update')->name('update');
                Route::delete('/{pendingMedia}', 'PendingController@delete')->name('delete');
            });
    });
