<div class="{{$isOutgoing === true ? 'outgoing_msg' : 'incoming_msg'}}">
    @if($isOutgoing === true)
        <div class="sent_msg">
            <p>{{$msg}}</p>
        </div>
    @else
        <div class="incoming_msg_img">
            <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil">
        </div>
        <div class="received_msg">
            <div class="received_withd_msg">
                <p>{{$msg}}</p>
            </div>
        </div>
    @endif
</div>
