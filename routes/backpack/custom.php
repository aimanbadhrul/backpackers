<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TagCrudController;
use App\Http\Controllers\Admin\RoleCrudController;
use App\Http\Controllers\Admin\UserCrudController;
use App\Http\Controllers\Admin\ImpersonateController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\Admin\PermissionCrudController;
use App\Http\Controllers\Admin\Events\EventCrudController;
use App\Http\Controllers\Admin\Events\EventListCrudController;
use App\Http\Controllers\Admin\Events\DraftEventCrudController;
use App\Http\Controllers\Admin\Events\ApprovedEventCrudController;
use App\Http\Controllers\Admin\Events\CompletedEventCrudController;
use App\Http\Controllers\Admin\Events\SubmittedEventCrudController;
use App\Http\Controllers\Admin\Applications\ApplicationCrudController;
use App\Http\Controllers\Admin\Applications\ApplicationListCrudController;
use App\Http\Controllers\Admin\Applications\PendingApplicationCrudController;
use App\Http\Controllers\Admin\Applications\ApprovedApplicationCrudController;
use App\Http\Controllers\Admin\Applications\RejectedApplicationCrudController;
use App\Http\Controllers\Admin\Applications\CompletedApplicationCrudController;
use App\Http\Controllers\Admin\Applications\ConfirmedApplicationCrudController;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\CRUD.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
], function () { // custom admin routes
    Route::crud('user', UserCrudController::class);
    Route::crud('tag', TagCrudController::class);
    Route::crud('application', ApplicationCrudController::class);
    Route::crud('event', EventCrudController::class);
    Route::crud('role', RoleCrudController::class);
    Route::crud('permission', PermissionCrudController::class);
    Route::crud('draft-event', DraftEventCrudController::class);
    Route::crud('submitted-event', SubmittedEventCrudController::class);
    Route::crud('approved-event', ApprovedEventCrudController::class);
    Route::crud('user-profile', UserProfileController::class);
    Route::crud('pending-application', PendingApplicationCrudController::class);
    Route::crud('approved-application', ApprovedApplicationCrudController::class);
    Route::crud('confirmed-application', ConfirmedApplicationCrudController::class);
    Route::crud('rejected-application', RejectedApplicationCrudController::class);
    Route::crud('completed-event', CompletedEventCrudController::class);
    Route::crud('completed-application', CompletedApplicationCrudController::class);
    Route::crud('event-list', EventListCrudController::class);
    Route::crud('application-list', ApplicationListCrudController::class);
}); // this should be the absolute last line of this file

/**
 * DO NOT ADD ANYTHING HERE.
 */
