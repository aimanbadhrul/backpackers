<?php

use App\Models\User;
use App\Livewire\Dashboard;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Lab404\Impersonate\Models\Impersonate;
use App\Http\Controllers\Admin\UserProfileController;
use Backpack\CRUD\app\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\EventDashboardController;
use App\Http\Controllers\Admin\ApplicationCrudController;
use App\Http\Controllers\Admin\EventApprovalCrudController;
use App\Http\Controllers\Admin\SubmittedEventCrudController;

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
Route::middleware(['admin'])->group(function () {
    Route::get('admin/user-profile', [UserProfileController::class, 'index'])->name('admin.user-profile');
});

//Application Process
Route::get('application/{id}/approve', [ApplicationCrudController::class, 'approve'])->name('admin.application.approve');
Route::get('application/{id}/reject', [ApplicationCrudController::class, 'reject'])->name('admin.application.reject');

//Event Approval Process
Route::get('submitted-event/{id}/approve', [SubmittedEventCrudController::class, 'approve'])->name('admin.submitted-event.approve');
Route::get('submitted-event/{id}/reject', [SubmittedEventCrudController::class, 'reject'])->name('admin.submitted-event.reject');

//Event Submission Button
Route::get('/admin/event/{id}/submit', function ($id) {
    $event = \App\Models\Event::findOrFail($id);

    if (backpack_user()->hasRole('Event Leader') && $event->created_by === backpack_user()->id) {
        $event->update(['status' => 'submitted']);
        return redirect('/admin/draft-event')->with('success', 'Event submitted for approval.');
    }

    abort(403, 'You are not allowed to submit this event.');
})->middleware(['admin']);

//Event Dashboard
Route::middleware(['admin'])->group(function () {
    Route::get('admin/event-dashboard/{event}', [EventDashboardController::class, 'show'])
        ->name('admin.event-dashboard')
        ->middleware('can:view events');
});


