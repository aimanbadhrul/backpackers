<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use App\Models\Traits\CanImpersonateTrait;
use Lab404\Impersonate\Models\Impersonate;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use CrudTrait;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;
    use Impersonate;
    // use CanImpersonateTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    protected $guard_name = 'backpack';
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getImpersonateButton()
    {
        if (backpack_user()->can('impersonate users') && backpack_user()->id !== $this->id) {
            return '<a class="btn btn-sm btn-link" href="'.route('impersonate', $this->id).'"><i class="la la-user-secret"></i> Login as</a>';
        }
    
        return '<a class="btn btn-sm btn-link disabled" href=""><i class="la la-user-secret"></i> Login as</a>';
    }
    
}
