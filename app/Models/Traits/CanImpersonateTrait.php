<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Session;

trait CanImpersonateTrait
{
    public function startImpersonation($id)
    {
        Session::put('impersonate', $id);
    }

    public function stopImpersonation()
    {
        Session::forget('impersonate');
    }

    public function isImpersonating()
    {
        return Session::has('impersonate');
    }

    public function getImpersonatedUser()
    {
        return $this->isImpersonating() ? self::find(Session::get('impersonate')) : null;
    }

    public function canImpersonate(): bool
    {
        return $this->hasRole('Superadmin'); // Only admins can impersonate
    }

    public function canBeImpersonated(): bool
    {
        return !$this->hasRole('Superadmin'); // Prevent impersonation of other admins
    }

    public function getImpersonateButton()
    {
        if (backpack_user()->canImpersonate() && $this->canBeImpersonated()) {
            return '<a href="' . route('impersonate', $this->id) . '" class="btn btn-sm btn-primary">Login</a>';
        }

        return '';
    }
}
