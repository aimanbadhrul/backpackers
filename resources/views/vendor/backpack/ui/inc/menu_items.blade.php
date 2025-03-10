{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<x-backpack::menu-item title="Events" icon="la la-calendar" :link="backpack_url('event')" />
<x-backpack::menu-item title="Event Submissions" icon="la la-file-alt" :link="backpack_url('event-submission')" />
@if(backpack_user()->can('approve events'))
<x-backpack::menu-item title="Event Approvals" icon="la la-check-circle" :link="backpack_url('event-approval')" />
@endif
<x-backpack::menu-item title="Approved Events" icon="la la-calendar-check" :link="backpack_url('approved-event')" />
<x-backpack::menu-item title="Applications" icon="la la-clipboard-list" :link="backpack_url('application')" />
<hr>
<x-backpack::menu-item title="Users" icon="la la-user" :link="backpack_url('user')" />
<x-backpack::menu-item title="Roles" icon="la la-users-cog" :link="backpack_url('role')" />
<x-backpack::menu-item title="Permissions" icon="la la-key" :link="backpack_url('permission')" />
<x-backpack::menu-item title="Tags" icon="la la-tag" :link="backpack_url('tag')" />