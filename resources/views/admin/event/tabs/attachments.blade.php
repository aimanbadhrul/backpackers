<div class="p-3">
    <h5>Attachments</h5>
    @if (!empty($event->attachments) && is_iterable($event->attachments) && collect($event->attachments)->isNotEmpty())
        <ul>
            @foreach ($event->attachments as $attachment)
                <li><a href="{{ asset('storage/' . $attachment->path) }}" target="_blank">{{ $attachment->name }}</a></li>
            @endforeach
        </ul>
    @else
        <p>No attachments available.</p>
    @endif
</div>
