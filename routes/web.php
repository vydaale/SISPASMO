<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\DocenteLoginController;
use App\Http\Controllers\Auth\AlumnoLoginController;
use App\Http\Controllers\Auth\AspiranteAuthController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\AspiranteController;
use App\Http\Controllers\CoordinadorController;
use App\Http\Controllers\QuejaController;
use App\Http\Controllers\ModuloController;
use App\Http\Controllers\TallerController;
use App\Http\Controllers\FichaMedicaController;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\Aspirante\CitaController as AspiranteCitaController;
use App\Http\Controllers\Admin\CitaController as AdminCitaController;



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
    
    Route::get('/dashboard', [AspiranteController::class, 'dashboard'])->name('aspirante.dashboard');

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
    Route::resource('admin', AdminController::class)->names('admin');
    Route::resource('modulos', ModuloController::class)->names('modulos');
    Route::resource('extracurricular', TallerController::class)->names('extracurricular');
    
    
});

/* Usuarios autenticados (todos los roles) */
Route::middleware(['auth'])->group(function () {
    Route::get('/quejas/nueva', [QuejaController::class, 'create'])->name('quejas.create');
    Route::post('/quejas', [QuejaController::class, 'store'])->name('quejas.store');
    Route::get('/quejas/mis', [QuejaController::class, 'mine'])->name('quejas.propias');
    Route::get('/quejas/{queja}', [QuejaController::class, 'show'])->name('quejas.read');
});

// ADMIN/COORD: listar, ver, eliminar Fichas Médicas
Route::middleware(['auth','role:administrador,coordinador'])->group(function () {
    Route::get('/fichas',               [FichaMedicaController::class, 'index'])->name('fichasmedicas.index');
    Route::get('/fichas/{ficha}',       [FichaMedicaController::class, 'show'])->name('fichasmedicas.show');
    Route::delete('/fichas/{ficha}',    [FichaMedicaController::class, 'destroy'])->name('fichasmedicas.destroy');
});

// ALUMNO: crear/editar su propia ficha
Route::middleware(['auth','role:alumno'])->prefix('mi-ficha')->name('mi_ficha.')->group(function () {
    Route::get('/crear',  [FichaMedicaController::class, 'createMine'])->name('create');
    Route::post('/',      [FichaMedicaController::class, 'storeMine'])->name('store');
    Route::get('/editar', [FichaMedicaController::class, 'editMine'])->name('edit');
    Route::put('/',       [FichaMedicaController::class, 'updateMine'])->name('update');
    Route::get('/',       [FichaMedicaController::class, 'showMine'])->name('show');
});

/* Administración: solo Administrador */
Route::middleware(['auth','role:Administrador'])->prefix('admin')->group(function () {
    Route::get('/quejas', [QuejaController::class, 'index'])->name('quejas.index');
    Route::get('/quejas/{queja}/edit', [QuejaController::class, 'edit'])->name('quejas.edit');
    Route::put('/quejas/{queja}', [QuejaController::class, 'update'])->name('quejas.update');
    Route::delete('/quejas/{queja}', [QuejaController::class, 'destroy'])->name('quejas.destroy');
});


// Alumno (solo sus recibos)
Route::middleware(['auth','role:alumno'])->group(function () {
    Route::get('/recibos',                [ReciboController::class, 'indexAlumno'])->name('recibos.index');
    Route::get('/recibos/crear',          [ReciboController::class, 'create'])->name('recibos.create');
    Route::post('/recibos',               [ReciboController::class, 'store'])->name('recibos.store');
    Route::get('/recibos/{recibo}',       [ReciboController::class, 'show'])->name('recibos.show');
});

// Administrador / Coordinador / Superadmin
Route::middleware(['auth','role:administrador,coordinador,superadmin'])->group(function () {
    Route::get('/admin/recibos',                  [ReciboController::class, 'indexAdmin'])->name('recibos.admin.index');
    Route::get('/admin/recibos/{recibo}/editar',  [ReciboController::class, 'edit'])->name('recibos.edit');
    Route::put('/admin/recibos/{recibo}',         [ReciboController::class, 'update'])->name('recibos.update');
    Route::delete('/admin/recibos/{recibo}',      [ReciboController::class, 'destroy'])->name('recibos.destroy');
    Route::post('/admin/recibos/{recibo}/validar',[ReciboController::class, 'validar'])->name('recibos.validar');
});

// ALUMNO: ver solo sus calificaciones
Route::middleware(['auth','role:alumno'])->group(function () {
    Route::get('/calificaciones', [CalificacionController::class, 'indexAlumno'])->name('calif.alumno.index');
});

// DOCENTE: CRUD sobre sus grupos (capturar/editar/eliminar de los alumnos que atiende)
Route::middleware(['auth','role:docente'])->group(function () {
    Route::get('/docente/calificaciones',             [CalificacionController::class, 'indexDocente'])->name('calif.docente.index');
    Route::get('/docente/calificaciones/crear',       [CalificacionController::class, 'create'])->name('calif.create');
    Route::post('/docente/calificaciones',            [CalificacionController::class, 'store'])->name('calif.store');
    Route::get('/docente/calificaciones/{calif}/edit',[CalificacionController::class, 'edit'])->name('calif.edit');
    Route::put('/docente/calificaciones/{calif}',     [CalificacionController::class, 'update'])->name('calif.update');
    Route::delete('/docente/calificaciones/{calif}',  [CalificacionController::class, 'destroy'])->name('calif.destroy');
});

// ADMIN/COORD: ver todo y editar si se requiere
Route::middleware(['auth','role:administrador,coordinador,superadmin'])->group(function () {
    Route::get('/admin/calificaciones', [CalificacionController::class, 'indexAdmin'])->name('calif.admin.index');
});

// GESTIÓN CITAS
Route::middleware(['auth'])->group(function () {
    // ASPIRANTE
    Route::prefix('aspirante/citas')->name('aspirante.citas.')->group(function () {
        Route::get('/',        [AspiranteCitaController::class, 'index'])->name('index'); 
        Route::get('/crear',   [AspiranteCitaController::class, 'create'])->name('create');
        Route::post('/',       [AspiranteCitaController::class, 'store'])->name('store');
        Route::delete('/{cita}', [AspiranteCitaController::class, 'cancel'])->name('cancel');
    });

    // ADMIN
    Route::prefix('admin/citas')->name('admin.citas.')->group(function () {
        Route::get('/', [AdminCitaController::class, 'index'])->name('index');                // todas
        Route::patch('/{cita}/estatus', [AdminCitaController::class, 'updateStatus'])->name('updateStatus'); // cambiar estatus
    });
});

Route::patch('citas/{cita}/status', [CitaController::class, 'updateStatus'])->name('admin.citas.updateStatus');
Route::resource('citas', CitaController::class)->names('citas');