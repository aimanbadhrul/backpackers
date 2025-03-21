<div class="p-3">
    <h5>Participants</h5>
    @if (!empty($event->participants) && is_iterable($event->participants))
        <ul>
            @foreach ($event->participants as $participant)
                <li>{{ $participant->name }} ({{ $participant->email }})</li>
            @endforeach
        </ul>
    @else
        <p>No participants available.</p>
    @endif
</div>
