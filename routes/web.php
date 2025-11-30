<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoriaController; 
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LugarController;
use App\Http\Controllers\LugarTuristicoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ComentarioController;
use Illuminate\Support\Facades\Route;

// ==========================================
// 🌍 PÁGINA PRINCIPAL PÚBLICA
// ==========================================
Route::get('/', function () {
    return view('welcome');
});

// ==========================================
// 🔐 DASHBOARD (usuarios autenticados)
// ==========================================
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ==========================================
// 👤 PERFIL DE USUARIO
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete'); 
});

// ==========================================
// 🌍 RUTAS PÚBLICAS - Ver lugares (sin autenticación)
// ==========================================
Route::get('/explorar-lugares', [LugarTuristicoController::class, 'explorar'])->name('lugares.explorar');
Route::get('/lugar/{id}', [LugarTuristicoController::class, 'mostrar'])->name('lugar.mostrar');

// ==========================================
// 👑 RUTAS PARA ADMINISTRADORES
// ==========================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Panel de administración
    Route::get('/panel', function () {
        return 'Bienvenido al panel del administrador';
    })->name('admin.panel');

    // Categorías (CRUD completo)
    Route::resource('categorias', CategoriaController::class);

    // Usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios/{user}/rol', [UsuarioController::class, 'updateRole'])->name('usuarios.updateRole');
    Route::delete('/usuarios/{user}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

    // Lugares turísticos (CRUD completo - solo admin)
    Route::resource('lugares', LugarTuristicoController::class);

    // Moderación de comentarios
    Route::post('/comentario/{id}/moderar', [ComentarioController::class, 'moderar'])->name('comentarios.moderar');
});

// ==========================================
// 👤 RUTAS PARA USUARIOS AUTENTICADOS
// ==========================================
Route::middleware(['auth'])->group(function () {
    // Reservas
    Route::get('/reservas', [ReservaController::class, 'index'])->name('reservas.index');
    
    // 💬 COMENTARIOS Y RESEÑAS
    Route::post('/lugar/{id}/comentario', [ComentarioController::class, 'store'])->name('comentarios.store');
    Route::delete('/comentario/{id}', [ComentarioController::class, 'destroy'])->name('comentarios.destroy');
});

// ==========================================
// 🎉 EVENTOS (públicos y autenticados)
// ==========================================

// Rutas públicas de eventos
Route::get('/eventos', [App\Http\Controllers\EventoController::class, 'index'])->name('eventos.index');
Route::get('/eventos/{id}', [App\Http\Controllers\EventoController::class, 'show'])->name('eventos.show');

// Rutas de reservas (usuarios autenticados)
Route::middleware(['auth'])->group(function () {
    Route::post('/reservas', [ReservaController::class, 'store'])->name('reservas.store');
    Route::delete('/reservas/{id}', [ReservaController::class, 'destroy'])->name('reservas.destroy');
});

// Rutas de gestión de eventos (solo admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/eventos/crear', [App\Http\Controllers\EventoController::class, 'create'])->name('eventos.create');
    Route::post('/admin/eventos', [App\Http\Controllers\EventoController::class, 'store'])->name('eventos.store');
    Route::get('/admin/eventos/{id}/editar', [App\Http\Controllers\EventoController::class, 'edit'])->name('eventos.edit');
    Route::put('/admin/eventos/{id}', [App\Http\Controllers\EventoController::class, 'update'])->name('eventos.update');
    Route::delete('/admin/eventos/{id}', [App\Http\Controllers\EventoController::class, 'destroy'])->name('eventos.destroy');
});

require __DIR__.'/auth.php';