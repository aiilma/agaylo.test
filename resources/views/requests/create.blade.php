@extends('layouts.form', [
    'caption' => __('Create Request Form')
])

@section('form-inner')
    <form method="POST" action="{{ route('login') }}">
        @csrf

        @include('ui.input', [
            'name' => 'subject',
            'label' => __('Subject'),
            'type' => 'text'
        ])

        @include('ui.textarea', [
            'name' => 'body',
            'label' => __('Body'),
        ])

        @include('ui.button', [
            'type' => 'submit',
            'text' => __('Create'),
        ])

    </form>
@endsection
