<div class="type_msg">
    <div class="input_msg_write">
        @if($isManager === true && $dialogueManagerId !== auth()->user()->id)
            <form action="{{route('requests.update', $id)}}" method="POST">
                {{csrf_field()}}
                @method('PATCH')

                <button class="col btn btn-warning" type="sumbit">Принять</button>
            </form>
        @else
            <form action="{{route('messages.store', ['id' => $id])}}" method="POST">
                {{csrf_field()}}

                <input name="body" type="text" class="write_msg" placeholder="Введите сообщение..."/>
                <input id="attachment" type="file" class="@error('attachment') is-invalid @enderror" name="attachment"
                       autofocus>
                <button class="msg_send_btn btn btn-outline-info" type="sumbit">Отправить</button>
            </form>
        @endif
    </div>
</div>
