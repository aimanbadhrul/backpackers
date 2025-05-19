@if($entry->status !== 'pending')
    <div class="card mt-3">
        <div class="card-body">
            @include('crud::buttons.update')
            @include('crud::buttons.delete')
        </div>
    </div>
@endif