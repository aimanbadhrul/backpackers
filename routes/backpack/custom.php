<?php

use Illuminate\Support\Facades\Route;

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
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('user', 'UserCrudController');
    Route::crud('tag', 'TagCrudController');
    Route::crud('application', 'ApplicationCrudController');
    Route::crud('event', 'EventCrudController');
    Route::crud('role', 'RoleCrudController');
    Route::crud('permission', 'PermissionCrudController');
    Route::crud('draft-event', 'DraftEventCrudController');
    Route::crud('submitted-event', 'SubmittedEventCrudController');
    Route::crud('approved-event', 'ApprovedEventCrudController');
    Route::crud('user-profile', 'UserProfileController');
    Route::crud('pending-application', 'PendingApplicationCrudController');
    Route::crud('approved-application', 'ApprovedApplicationCrudController');
    Route::crud('confirmed-application', 'ConfirmedApplicationCrudController');
    Route::crud('rejected-application', 'RejectedApplicationCrudController');
    Route::crud('completed-event', 'CompletedEventCrudController');
    Route::crud('completed-application', 'CompletedApplicationCrudController');
}); // this should be the absolute last line of this file

/**
 * DO NOT ADD ANYTHING HERE.
 */
