@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $caption }}</div>

                    <div class="card-body">
                        @yield('form-inner')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
