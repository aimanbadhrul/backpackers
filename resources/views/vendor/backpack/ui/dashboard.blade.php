@extends(backpack_view('blank'))

@php
    use App\Models\User;
    use App\Models\Event;
    use App\Models\Application;

    $widgets['before_content'][] = [
            'type'        => 'jumbotron',
            'heading' => 'Welcome, ' . (backpack_user()->full_name ?? backpack_user()->name),
            'heading_class' => 'display-3 '.(backpack_theme_config('layout') === 'horizontal_overlap' ? ' text-white' : ''),
            'content' => 'Today is ' . \Carbon\Carbon::now()->format('l, F j, Y'),
            'content_class' => backpack_theme_config('layout') === 'horizontal_overlap' ? 'text-white' : '',
            // 'button_link' => backpack_url('logout'),
            // 'button_text' => trans('backpack::base.logout'),
        ];

        $user = backpack_user(); // get current logged-in user

    $widgets['before_content'][] = [
        'type'    => 'div',
        'class'   => 'row',
        'content' => array_filter([
            
            $user->can('manage events') ? [
                'type'     => 'card',
                'wrapper'  => ['class' => 'col-md-4'],
                'class'    => 'card bg-success text-white',
                'content'  => [
                    'header' => 'Total Events',
                    'body'   => '<a href="'.backpack_url('event').'" style="text-decoration: none; color: inherit;"><h2>'.Event::where('status', '!=', 'completed')->count().'</h2></a>',
                ],
            ] : null,

        $user->can('manage events') ? [
            'type'     => 'card',
            'wrapper'  => ['class' => 'col-md-4'],
            'class'    => 'card bg-success text-white',
            'content'  => [
                'header' => 'Approved Events',
                'body'   => '<a href="'.route('approved-event.index').'" style="text-decoration: none; color: inherit;"><h2>'
                            . Event::where('status', 'approved')->count() . '</h2></a>',
            ],
        ] : null,

        // ✅ Shown only to users who CANNOT manage events
        !$user->can('manage events') ? [
            'type'     => 'card',
            'wrapper'  => ['class' => 'col-md-4'],
            'class'    => 'card bg-success text-white',
            'content'  => [
                'header' => 'Available Events',
                'body'   => '<a href="'.route('event-list.index').'" style="text-decoration: none; color: inherit;"><h2>'
                            . Event::where('status', 'approved')->count() . '</h2></a>',
            ],
        ] : null,

            // Total Applications (only shown if user has permission)
            $user->can('manage applications') ? [
                'type'     => 'card',
                'wrapper'  => ['class' => 'col-md-4'],
                'class'    => 'card bg-warning text-white',
                'content'  => [
                    'header' => 'Total Applications',
                    'body'   => '<a href="'.backpack_url('application').'" 
                    style="text-decoration: none; color: inherit;">
                        <h2>'.Application::where('status', '!=', 'completed')->count().'</h2>
                        </a>',
                ],
            ] : null,
        ]),
    ];
@endphp



@section('content')
@endsection
