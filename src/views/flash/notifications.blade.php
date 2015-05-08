@foreach(flash()->get() as $notification)
    @if(array_get($notification, 'title', false))
        @include('znck::flash.modal', $notification)
    @else
        @include('znck::flash.message', $notification)
    @endif
@endforeach