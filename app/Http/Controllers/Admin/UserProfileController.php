<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Backpack\CRUD\app\Http\Controllers\CrudController;

class UserProfileController extends CrudController
{
    public function index()
    {
        $user = Auth::user(); // Get the logged-in user
        return view('admin.user_profile', compact('user'));
    }
    public function setup()
    {
        $this->crud->setModel('App\Models\User');
        $this->crud->setRoute(backpack_url('user-profile'));
        $this->crud->setEntityNameStrings('User Profile', 'User Profile');

        $this->crud->addClause('where', 'id', backpack_auth()->id()); // Only show the logged-in user's profile

        $this->crud->addFields([
            [
                'name' => 'name',
                'label' => 'Name',
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'label' => 'Email',
                'type' => 'email',
            ],
            [
                'name' => 'full_name',
                'label' => 'Full Name',
                'type' => 'text',
            ],
            [
                'name' => 'phone_number',
                'label' => 'Phone Number',
                'type' => 'text',
            ],
            [
                'name' => 'social_links',
                'label' => 'Social Links',
                'type' => 'textarea',
            ],
            [
                'name' => 'bio',
                'label' => 'Bio',
                'type' => 'textarea',
            ],
            [
                'name' => 'profile_picture',
                'label' => 'Profile Picture',
                'type' => 'upload',
                'upload' => true,
                'disk' => 'public',
            ],
        ]);

        $this->crud->removeButton('delete'); // Prevent users from deleting their profile
        $this->crud->removeButton('create'); // Prevent users from creating new profiles
        $this->crud->denyAccess(['list']); // Hide list view, only allow editing
    }

    public function update(Request $request)
    {
        $user = backpack_auth()->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'full_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'social_links' => 'nullable|string',
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $user->update($data);

        Alert::success('Profile updated successfully.')->flash();
        return redirect()->back();
    }
}

