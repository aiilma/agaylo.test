@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $caption }}</div>

                    <div class="card-body">
                        @if ($message = Session::get('success'))
                            @include('ui.alerts.success', ['message' => $message])
                        @endif

                        @if ($message = Session::get('error'))
                            @include('ui.alerts.error', ['message' => $message])
                        @endif

                        @yield('form-inner')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
