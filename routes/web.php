<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\DocenteLoginController;
use App\Http\Controllers\Auth\AlumnoLoginController;

/*--------------------------------------------------------------------------
| Páginas públicas
--------------------------------------------------------------------------*/
Route::view('/', 'inicio')->name('inicio');
Route::view('/oferta', 'oferta')->name('oferta');
Route::view('/contacto', 'contacto')->name('contacto');

// Rutas para el administrador
Route::get('/administrador/login',  [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/administrador/login', [AdminLoginController::class, 'login'])->name('admin.login.post');
Route::post('/administrador/logout',[AdminLoginController::class, 'logout'])->name('admin.logout');

Route::middleware(['auth','role:Administrador,Coordinador'])->group(function () {
    Route::view('/administrador/dashboard', 'administrador.dashboardadmin')->name('admin.dashboard');
});


// Rutas para el docente
Route::get('/docente/login',  [DocenteLoginController::class, 'showLoginForm'])->name('docente.login');
Route::post('/docente/login', [DocenteLoginController::class, 'login'])->name('docente.login.post');
Route::post('/docente/logout', [DocenteLoginController::class, 'logout'])->name('docente.logout');

Route::middleware(['auth', 'role:Docente'])->group(function () {
    Route::view('/docente/dashboard', 'docente.dashboarddocente')->name('docente.dashboard');
});

// Rutas para el alumno
Route::get('/alumno/login',  [AlumnoLoginController::class, 'showLoginForm'])->name('alumno.login');
Route::post('/alumno/login', [AlumnoLoginController::class, 'login'])->name('alumno.login.post');
Route::post('/alumno/logout',[AlumnoLoginController::class, 'logout'])->name('alumno.logout');

Route::middleware(['auth','role:Alumno'])->group(function () {
    Route::view('/alumno/dashboard', 'alumno.dashboardalumno')->name('alumno.dashboard');
});
