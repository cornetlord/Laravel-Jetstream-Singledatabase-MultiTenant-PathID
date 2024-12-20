<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', function () {
    if (Auth::check()) {
        Auth::logout(); // Optionally log them out for additional security
        return redirect()->route('login');
    }

    return view('welcome');
});


