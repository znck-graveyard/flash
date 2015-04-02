@if (Session::has('flash_notification'))
    @foreach(session('flash_notification') as $notification)
        @if(array_get($notification, 'title', false))
            @include('flash::modal', $notification)
        @else
            @include('flash::message', $notification)
        @endif
    @endforeach
@endif