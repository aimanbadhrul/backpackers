<?php

use App\Models\User;
use App\Livewire\Dashboard;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Backpack\CRUD\app\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\ApplicationCrudController;
use App\Http\Controllers\Admin\EventApprovalCrudController;
use Lab404\Impersonate\Models\Impersonate;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/home', function () {
    return view('home');
})->name('home');
Route::get('/dashboard', Dashboard::class)->middleware('auth')->name('dashboard');

//Register / Login Process
Route::get('/register', Register::class)->name('register');
Route::get('/login', Login::class)->name('login');
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::middleware(['role:superadmin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});

//Application Process
Route::get('application/{id}/approve', [ApplicationCrudController::class, 'approve'])->name('admin.application.approve');
Route::get('application/{id}/reject', [ApplicationCrudController::class, 'reject'])->name('admin.application.reject');

//Event Approval Process
Route::get('event-approval/{id}/approve', [EventApprovalCrudController::class, 'approve'])->name('admin.event-approval.approve');
Route::get('event-approval/{id}/reject', [EventApprovalCrudController::class, 'reject'])->name('admin.event-approval.reject');
