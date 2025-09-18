<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\DocenteLoginController;
use App\Http\Controllers\Auth\AlumnoLoginController;
use App\Http\Controllers\Auth\AspiranteAuthController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AspiranteController;
use App\Http\Controllers\CoordinadorController;
use App\Http\Controllers\QuejaController;
use App\Http\Controllers\ModuloController;
use App\Http\Controllers\TallerController;
use App\Http\Controllers\FichaMedicaController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\CitaController; 
use App\Http\Controllers\Aspirante\CitaController as AspiranteCitaController;
use App\Http\Controllers\Admin\CitaController as AdminCitaController;
use App\Http\Controllers\ReporteAlumnosEdadController;
/* Páginas públicas */
Route::view('/', 'inicio')->name('inicio');
Route::view('/oferta', 'oferta')->name('oferta');
Route::view('/contacto', 'contacto')->name('contacto');

// Rutas de autenticación
Route::prefix('administrador')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
});

Route::prefix('docente')->group(function () {
    Route::get('/login', [DocenteLoginController::class, 'showLoginForm'])->name('docente.login');
    Route::post('/login', [DocenteLoginController::class, 'login'])->name('docente.login.post');
    Route::post('/logout', [DocenteLoginController::class, 'logout'])->name('docente.logout');
});

Route::prefix('alumno')->group(function () {
    Route::get('/login', [AlumnoLoginController::class, 'showLoginForm'])->name('alumno.login');
    Route::post('/login', [AlumnoLoginController::class, 'login'])->name('alumno.login.post');
    Route::post('/logout', [AlumnoLoginController::class, 'logout'])->name('alumno.logout');
});

Route::prefix('aspirante')->group(function () {
    Route::get('/', [AspiranteAuthController::class, 'select'])->name('aspirante.select');
    Route::get('/registro',  [AspiranteAuthController::class, 'showRegisterForm'])->name('aspirante.register.show');
    Route::post('/registro', [AspiranteAuthController::class, 'register'])->name('aspirante.register');
    Route::get('/login',  [AspiranteAuthController::class, 'showLoginForm'])->name('aspirante.login');
    Route::post('/login', [AspiranteAuthController::class, 'login'])->name('aspirante.login.post');
    Route::post('/logout', [AspiranteAuthController::class, 'logout'])->name('aspirante.logout');
});

// Rutas de paneles con protección de middleware
Route::middleware(['auth','role:Administrador,Coordinador'])->prefix('administrador')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/metrics', [DashboardController::class, 'metrics'])->name('admin.dashboard.metrics');

    Route::resource('alumnos', AlumnoController::class)->names('alumnos');
    Route::resource('docentes', DocenteController::class)->names('docentes');
    Route::resource('aspirantes', AspiranteController::class)->names('aspirantes');
    Route::resource('coordinadores', CoordinadorController::class)->parameters(['coordinadores' => 'coordinador']);
    Route::resource('admin', AdminController::class)->names('admin');
    Route::resource('modulos', ModuloController::class)->names('modulos');
    Route::resource('extracurricular', TallerController::class)->names('extracurricular');

    Route::view('/reportes', 'administrador.reportes.reportes')->name('admin.reportes');

});

Route::middleware(['auth', 'role:Docente'])->prefix('docente')->group(function () {
    Route::get('/dashboard', [DocenteController::class, 'dashboard'])->name('docente.dashboard');
});

Route::middleware(['auth', 'role:Alumno'])->prefix('alumno')->group(function () {
    Route::get('/dashboard', [AlumnoController::class, 'dashboard'])->name('alumno.dashboard');
});

Route::middleware(['auth', 'role:Aspirante'])->prefix('aspirante')->group(function () {
    Route::get('/dashboard', [AspiranteController::class, 'dashboard'])->name('aspirante.dashboard');
});

/* Otras rutas con middleware */
Route::middleware(['auth'])->group(function () {
    Route::get('/quejas/nueva', [QuejaController::class, 'create'])->name('quejas.create');
    Route::post('/quejas', [QuejaController::class, 'store'])->name('quejas.store');
    Route::get('/quejas/mis', [QuejaController::class, 'mine'])->name('quejas.propias');
    Route::get('/quejas/{queja}', [QuejaController::class, 'show'])->name('quejas.read');
});

Route::middleware(['auth','role:administrador,coordinador'])->group(function () {
    Route::get('/fichas', [FichaMedicaController::class, 'index'])->name('fichasmedicas.index');
    Route::get('/fichas/{ficha}', [FichaMedicaController::class, 'show'])->name('fichasmedicas.show');
    Route::delete('/fichas/{ficha}', [FichaMedicaController::class, 'destroy'])->name('fichasmedicas.destroy');
});

Route::middleware(['auth','role:alumno'])->prefix('mi-ficha')->name('mi_ficha.')->group(function () {
    Route::get('/crear',  [FichaMedicaController::class, 'createMine'])->name('create');
    Route::post('/',      [FichaMedicaController::class, 'storeMine'])->name('store');
    Route::get('/editar', [FichaMedicaController::class, 'editMine'])->name('edit');
    Route::put('/',       [FichaMedicaController::class, 'updateMine'])->name('update');
    Route::get('/',       [FichaMedicaController::class, 'showMine'])->name('show');
});

Route::middleware(['auth','role:Administrador'])->prefix('admin')->group(function () {
    Route::get('/quejas', [QuejaController::class, 'index'])->name('quejas.index');
    Route::get('/quejas/{queja}/edit', [QuejaController::class, 'edit'])->name('quejas.edit');
    Route::put('/quejas/{queja}', [QuejaController::class, 'update'])->name('quejas.update');
    Route::delete('/quejas/{queja}', [QuejaController::class, 'destroy'])->name('quejas.destroy');
});

Route::middleware(['auth','role:alumno'])->group(function () {
    Route::get('/recibos', [ReciboController::class, 'indexAlumno'])->name('recibos.index');
    Route::get('/recibos/crear', [ReciboController::class, 'create'])->name('recibos.create');
    Route::post('/recibos', [ReciboController::class, 'store'])->name('recibos.store');
    Route::get('/recibos/{recibo}', [ReciboController::class, 'show'])->name('recibos.show');
});

Route::middleware(['auth','role:administrador,coordinador,superadmin'])->prefix('admin')->group(function () {
    Route::get('/recibos', [ReciboController::class, 'indexAdmin'])->name('recibos.admin.index');
    Route::get('/recibos/{recibo}/editar', [ReciboController::class, 'edit'])->name('recibos.edit');
    Route::put('/recibos/{recibo}', [ReciboController::class, 'update'])->name('recibos.update');
    Route::delete('/recibos/{recibo}', [ReciboController::class, 'destroy'])->name('recibos.destroy');
    Route::post('/recibos/{recibo}/validar', [ReciboController::class, 'validar'])->name('recibos.validar');
});

Route::middleware(['auth','role:alumno'])->group(function () {
    Route::get('/calificaciones', [CalificacionController::class, 'indexAlumno'])->name('calif.alumno.index');
});

Route::middleware(['auth','role:docente'])->group(function () {
    Route::get('/docente/calificaciones', [CalificacionController::class, 'indexDocente'])->name('calif.docente.index');
    Route::get('/docente/calificaciones/crear', [CalificacionController::class, 'create'])->name('calif.create');
    Route::post('/docente/calificaciones', [CalificacionController::class, 'store'])->name('calif.store');
    Route::get('/docente/calificaciones/{calif}/edit',[CalificacionController::class, 'edit'])->name('calif.edit');
    Route::put('/docente/calificaciones/{calif}', [CalificacionController::class, 'update'])->name('calif.update');
    Route::delete('/docente/calificaciones/{calif}', [CalificacionController::class, 'destroy'])->name('calif.destroy');
});

Route::middleware(['auth','role:administrador,coordinador,superadmin'])->group(function () {
    Route::get('/admin/calificaciones', [CalificacionController::class, 'indexAdmin'])->name('calif.admin.index');
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('aspirante/citas')->name('aspirante.citas.')->group(function () {
        Route::get('/', [AspiranteCitaController::class, 'index'])->name('index'); 
        Route::get('/crear', [AspiranteCitaController::class, 'create'])->name('create');
        Route::post('/', [AspiranteCitaController::class, 'store'])->name('store');
        Route::delete('/{cita}', [AspiranteCitaController::class, 'cancel'])->name('cancel');
    });

    Route::prefix('admin/citas')->name('admin.citas.')->group(function () {
        Route::get('/', [AdminCitaController::class, 'index'])->name('index');
        Route::patch('/{cita}/estatus', [AdminCitaController::class, 'updateStatus'])->name('updateStatus');
    });
});

Route::patch('citas/{cita}/status', [CitaController::class, 'updateStatus'])->name('admin.citas.updateStatus');
Route::resource('citas', CitaController::class)->names('citas');

/*Reportes*/
Route::middleware(['auth'])
    ->prefix('admin/reportes/alumnos-edad')
    ->name('admin.reportes.alumnosEdad.')
    ->group(function () {
        Route::get('/',           [ReporteAlumnosEdadController::class, 'index'])->name('index');
        Route::get('/chart-data', [ReporteAlumnosEdadController::class, 'chartData'])->name('chartData');
        Route::get('/table',      [ReporteAlumnosEdadController::class, 'table'])->name('table');
        Route::post('/pdf',       [ReporteAlumnosEdadController::class, 'pdf'])->name('pdf');
        Route::get('/excel',      [ReporteAlumnosEdadController::class, 'excel'])->name('excel');
    });