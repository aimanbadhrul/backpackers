<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Prologue\Alerts\Facades\Alert;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class ImpersonateController extends Controller
{
    public function loginAs($id)
    {
        $targetUser = User::findOrFail($id);
        $impersonatorId = backpack_user()->id;
    
        session()->put('impersonated_by', $impersonatorId);
    
        Auth::guard('backpack')->login($targetUser);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        session(['backpack_user' => $targetUser]);
    
        return redirect()->route('admin.dashboard')->with('success', 'Now impersonating ' . $targetUser->name);
    }
    
    public function leave()
    {
        if (session()->has('impersonated_by')) {
            $originalUserId = session()->pull('impersonated_by');
            $originalUser = User::find($originalUserId);
    
            if ($originalUser) {
                Auth::guard('backpack')->login($originalUser);
                return redirect()->route('admin.dashboard')->with('success', 'Returned to your account');
            }
        }
    
        return redirect()->route('admin.dashboard')->with('error', 'Original user not found');
    }
}

