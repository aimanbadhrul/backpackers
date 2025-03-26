@extends(backpack_view('layouts.top_left'))

@section('content')
    <h2>Event Dashboard: {{ $event->name }}</h2>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5>Details</h5>
                    <p><strong>Date:</strong> {{ $event->start_date }} - {{ $event->end_date }}</p>
                    <p><strong>Location:</strong> {{ $event->location }}</p>
                    <p><strong>Max Participants:</strong> {{ $event->max_participants }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5>Participants</h5>
                    <p>{{ $event->applications->count() }} / {{ $event->max_participants }} signed up</p>
                </div>
            </div>
        </div>
    </div>
@endsection
