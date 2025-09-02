<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\DocenteLoginController;
use App\Http\Controllers\Auth\AlumnoLoginController;
use App\Http\Controllers\Auth\AspiranteAuthController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AspiranteController;
use App\Http\Controllers\CoordinadorController;

/* Páginas públicas */
Route::view('/', 'inicio')->name('inicio');
Route::view('/oferta', 'oferta')->name('oferta');
Route::view('/contacto', 'contacto')->name('contacto');

/* Administrador (login + dashboard) */
Route::get('/administrador/login',  [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/administrador/login', [AdminLoginController::class, 'login'])->name('admin.login.post');
Route::post('/administrador/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

Route::middleware(['auth','role:Administrador,Coordinador'])->group(function () {
    Route::view('/administrador/dashboard', 'administrador.dashboardadmin')->name('admin.dashboard');
});

/* Docente (login + dashboard) */
Route::get('/docente/login',  [DocenteLoginController::class, 'showLoginForm'])->name('docente.login');
Route::post('/docente/login', [DocenteLoginController::class, 'login'])->name('docente.login.post');
Route::post('/docente/logout', [DocenteLoginController::class, 'logout'])->name('docente.logout');

Route::middleware(['auth','role:Docente'])->group(function () {
    Route::view('/docente/dashboard', 'docente.dashboarddocente')->name('docente.dashboard');
});

/* Alumno (login + dashboard) */
Route::get('/alumno/login',  [AlumnoLoginController::class, 'showLoginForm'])->name('alumno.login');
Route::post('/alumno/login', [AlumnoLoginController::class, 'login'])->name('alumno.login.post');
Route::post('/alumno/logout', [AlumnoLoginController::class, 'logout'])->name('alumno.logout');

Route::middleware(['auth','role:Alumno'])->group(function () {
    Route::view('/alumno/dashboard', 'alumno.dashboardalumno')->name('alumno.dashboard');
});

/* Aspirante selector, registro y login propios */
Route::prefix('aspirante')->group(function () {
    // Pantalla para elegir Ingresar o Registrarse
    Route::get('/', [AspiranteAuthController::class, 'select'])->name('aspirante.select');

    // Registro público (crea usuarios + aspirantes)
    Route::get('/registro',  [AspiranteAuthController::class, 'showRegisterForm'])->name('aspirante.register.show');
    Route::post('/registro', [AspiranteAuthController::class, 'register'])->name('aspirante.register');

    // Login/Logout de aspirante
    Route::get('/login',  [AspiranteAuthController::class, 'showLoginForm'])->name('aspirante.login');
    Route::post('/login', [AspiranteAuthController::class, 'login'])->name('aspirante.login.post');
    Route::post('/logout', [AspiranteAuthController::class, 'logout'])->name('aspirante.logout');

    // Dashboard exclusivo aspirante
    Route::middleware(['auth','role:Aspirante'])->group(function () {
        Route::view('/dashboard', 'aspirante.dashboardaspirante')->name('aspirante.dashboard');
    });
});

/* CRUDs de administración */
Route::middleware(['auth','role:Administrador,Coordinador'])->prefix('admin')->group(function () {
    Route::view('/', 'admin.dashboard')->name('admin.home');

    Route::resource('alumnos', AlumnoController::class)->names('alumnos');
    Route::resource('docentes', DocenteController::class)->names('docentes');
    Route::resource('aspirantes', AspiranteController::class)->names('aspirantes');
    Route::resource('coordinadores', CoordinadorController::class)->parameters([
        'coordinadores' => 'coordinador',
    ]);
});
