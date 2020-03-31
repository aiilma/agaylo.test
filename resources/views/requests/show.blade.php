@extends('layouts.app')

@section('content')
    @component('layouts.dialogue', ['req' => $req,])
        @slot('messages')
            @foreach($req->dialogue as $msg)
                @component('components.message-item', [
                    'isOutgoing' => auth()->user()->id === $msg->author->id,
                    'msg' => $msg->body,
                    ])@endcomponent
            @endforeach
        @endslot
    @endcomponent
@endsection
