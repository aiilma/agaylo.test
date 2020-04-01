<div class="container">
    <div class="row">

        <div class="col-12 messaging">
            <div class="inbox_msg">
                <div class="mesgs">
                    <h2>Тема: {{$req->subject}}</h2>
                    <div class="msg_history">

                        {{$messages}}

                    </div>

                    @if ($req->isOpened())
                        @component('components.dialogue.input', [
                            'id' => $req->id,
                            'isManager' => auth()->user()->isManager(),
                            'dialogueManagerId' => $req->manager_id,
                        ])@endcomponent
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
