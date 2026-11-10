<?php

use App\Http\Middleware\EnsureAdminAccess;
use App\Livewire\AdminLogin;
use App\Livewire\CreateMovie;
use App\Livewire\ListMovies;
use App\Livewire\ShowMovie;
use Illuminate\Support\Facades\Route;

// 1. Página de Listagem (Home)
Route::get('/', ListMovies::class)->name('home');

// 2. Página de Cadastro
Route::get('/admin/login', AdminLogin::class)->name('admin.login');

Route::get('/admin/logout', function () {
    request()->session()->forget('madeflix_admin_authenticated');

    return redirect()->route('home');
})->name('admin.logout');

Route::get('/cadastrar', CreateMovie::class)
    ->middleware(EnsureAdminAccess::class)
    ->name('create');

// 3. Página de Visualização do Filme (com parâmetro {film})
Route::get('/ver/{film}', ShowMovie::class)->name('see');
