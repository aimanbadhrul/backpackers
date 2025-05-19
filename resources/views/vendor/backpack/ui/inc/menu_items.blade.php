{{-- This file is used for menu items by any Backpack v6 theme --}}
<x-backpack::menu-item title="User Profile" icon="la la-user-circle" :link="backpack_url('user-profile')" />
<x-backpack::menu-item title="{{ trans('backpack::base.dashboard') }}" icon="la la-home" :link="backpack_url('dashboard')" />
<x-backpack::menu-item title="Event List" icon="la la-list" :link="backpack_url('event-list')" />
{{-- <x-backpack::menu-item title="Application List" icon="la la-question" :link="backpack_url('application-list')" /> --}}

@if(backpack_user()->can('manage events'))
<x-backpack::menu-dropdown title="Events" icon="las la-calendar" class="open">
    <x-backpack::menu-dropdown-item title="All Events" icon="la la-calendar" :link="backpack_url('event')" />
    <x-backpack::menu-dropdown-item title="Drafts" icon="la la-file-alt" :link="backpack_url('draft-event')" />
    <x-backpack::menu-dropdown-item title="Submitted" icon="la la-check-circle" :link="backpack_url('submitted-event')" />
    <x-backpack::menu-dropdown-item title="Approved" icon="la la-calendar-check" :link="backpack_url('approved-event')" />
    <x-backpack::menu-dropdown-item title="Completed" icon="la la-check-circle" :link="backpack_url('completed-event')" />
</x-backpack::menu-dropdown>
@endif

<x-backpack::menu-dropdown title="Applications" icon="las la-edit" class="open">
    <x-backpack::menu-dropdown-item title="All Applications" icon="la la-clipboard-list" :link="backpack_url('application')" />
    <x-backpack::menu-dropdown-item title="Pending" icon="la la-file-alt" :link="backpack_url('pending-application')" />
    <x-backpack::menu-dropdown-item title="Approved" icon="la la-file-alt" :link="backpack_url('approved-application')" />
    <x-backpack::menu-dropdown-item title="Confirmed" icon="la la-file-alt" :link="backpack_url('confirmed-application')" />
    <x-backpack::menu-dropdown-item title="Completed" icon="la la-question" :link="backpack_url('completed-application')" />
    <x-backpack::menu-dropdown-item title="Rejected" icon="la la-file-alt" :link="backpack_url('rejected-application')" />
</x-backpack::menu-dropdown>

<hr>
@if(backpack_user()->can('manage users'))
<x-backpack::menu-item title="Users" icon="la la-user" :link="backpack_url('user')" />
@endif
@if(backpack_user()->can('manage roles'))
<x-backpack::menu-item title="Roles" icon="la la-users-cog" :link="backpack_url('role')" />
@endif
@if(backpack_user()->can('manage permissions'))
<x-backpack::menu-item title="Permissions" icon="la la-key" :link="backpack_url('permission')" />
@endif

@if (session()->has('impersonated_by'))
    <div class="p-3 ">
        <strong>Logged in as</strong> {{ backpack_user()->name }}
        <br>
        <a href="{{ route('impersonate.leave') }}" class="btn btn-sm btn-danger ml-2 mt-2">Leave</a>
    </div>
@endif

