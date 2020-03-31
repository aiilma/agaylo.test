@extends('layouts.app')

@section('content')
    <div class="container">
        @if (auth()->user()->isManager())
            @include('ui.filter')
        @else
            <div class="row py-1">
                <div class="col-3">
                    <div class="btn-group special" role="group" aria-label="Basic example">
                        <a class="btn btn-info" href="{{route('requests.create')}}" role="button">Создать</a>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Тема</th>
                        <th scope="col">Менеджер</th>
                        <th scope="col">Новые сообщения</th>
                        <th scope="col">Статус</th>
                        <th scope="col">Действия</th>
                    </tr>
                    </thead>
                    <tbody>


                    @foreach($requests as $r)
                        @component('components.request-item', [
                            'id' => $r->id,
                            'subject' => $r->subject,
                            'email' => $r->manager ? $r->manager->email : 'Нет менеджера',
                            'newMessagesCount' => $r->getNewMessages(auth()->user()->id)->count(),
                            'status' => $r->status,
                            ])
                        @endcomponent
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
