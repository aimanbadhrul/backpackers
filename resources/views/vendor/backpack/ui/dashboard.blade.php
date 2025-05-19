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

    $widgets['before_content'][] = [
        'type'    => 'div',
        'class'   => 'row',
        'content' => [
            // [
            //     'type'     => 'card',
            //     'wrapper'  => ['class' => 'col-md-4'],
            //     'class'    => 'card bg-primary text-white',
            //     'content'  => [
            //         'header' => 'Users Registered',
            //         'body'   => '<a href="'.backpack_url('user').'" style="text-decoration: none; color: inherit;"><h2>'.User::count().'</h2></a>',
            //         ],
            // ],
            [
                'type'     => 'card',
                'wrapper'  => ['class' => 'col-md-4'],
                'class'    => 'card bg-success text-white',
                'content'  => [
                    'header' => 'Total Events',
                    'body'   => '<a href="'.backpack_url('event').'" style="text-decoration: none; color: inherit;"><h2>'.Event::count().'</h2></a>',
                    ],
            ],
            [
                'type'     => 'card',
                'wrapper'  => ['class' => 'col-md-4'],
                'class'    => 'card bg-warning text-white',
                'content'  => [
                    'header' => 'Total Applications',
                    'body'   => '<a href="'.backpack_url('application').'" style="text-decoration: none; color: inherit;"><h2>'.Application::count().'</h2></a>',
                    ],
            ],
        ],
    ];
@endphp



@section('content')
@endsection
