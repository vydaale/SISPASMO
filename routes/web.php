<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;


Route::get('/', function () {
    return view('inicio');
});

Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('adminlogin');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('adminlogin.post');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('adminlogout');

Route::middleware(['auth','role:Administrador,Coordinador'])
    ->get('/admin', function () {
        return view('dashboard');
    })
    ->name('dashboard');