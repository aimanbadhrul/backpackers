<?php

use App\Livewire\Dashboard;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ImpersonateController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Admin\Events\EventCrudController;
use App\Http\Controllers\Admin\Events\EventDashboardController;
use App\Http\Controllers\Admin\Events\SubmittedEventCrudController;
use App\Http\Controllers\Admin\Applications\ApplicationCrudController;

// 🏠 PUBLIC ROUTES
Route::get('/', fn () => view('home'))->name('home');
Route::get('/home', fn () => view('home'));

// 🧑‍💻 LIVEWIRE AUTH ROUTES (separate from Backpack)
Route::get('/register', Register::class)->name('register');
Route::get('/login', Login::class)->name('login');
Route::post('/logout', function () {
    Auth::guard('backpack')->logout(); // logout Backpack user
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::middleware('auth')->get('/dashboard', Dashboard::class)->name('dashboard'); // Livewire's user dashboard

// 🛠 BACKPACK ADMIN ROUTES (uses 'admin' prefix, middleware, guard)
Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () {

    // Dashboard (fix Route [admin.dashboard])
    Route::get('/', function () {
        return view(backpack_view('dashboard'));
    })->name('admin.dashboard');

    // Profile
    Route::get('user-profile', [UserProfileController::class, 'index'])->name('admin.user-profile');

    // CRUD Actions
    Route::get('application/{id}/approve', [ApplicationCrudController::class, 'approve'])->name('admin.application.approve');
    Route::get('application/{id}/reject', [ApplicationCrudController::class, 'reject'])->name('admin.application.reject');
    Route::get('application/{application}/confirm', [ApplicationCrudController::class, 'confirm'])->name('admin.application.confirm');

    Route::get('submitted-event/{id}/approve', [SubmittedEventCrudController::class, 'approve'])->name('admin.submitted-event.approve');
    Route::get('submitted-event/{id}/reject', [SubmittedEventCrudController::class, 'reject'])->name('admin.submitted-event.reject');

    Route::get('event/{id}/submit', [EventCrudController::class, 'submit'])->name('admin.event.submit');

    // Event Dashboard
    Route::get('event-dashboard/{event}', [EventDashboardController::class, 'show'])
        ->middleware('can:view events')
        ->name('admin.event-dashboard');

    // Impersonation routes
    Route::impersonate();

    // Route::get('impersonate/{id}', [ImpersonateController::class, 'loginAs'])->name('impersonate');
    // Route::get('impersonate/leave', [ImpersonateController::class, 'leave'])->name('impersonate.leave');

    // Optional: your CRUD definitions
    // Route::crud('event', EventCrudController::class);
    // Route::crud('application', ApplicationCrudController::class);
    // etc.
});




