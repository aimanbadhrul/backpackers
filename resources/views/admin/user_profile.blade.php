@extends(backpack_view('layouts.vertical'))

@section('content')
    <div class="container">
        <h1>User Profile</h1>
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
    </div>
@endsection