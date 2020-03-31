<tr>
    <th scope="row">{{$id}}</th>
    <td>{{$subject}}</td>
    <td>{{$email}}</td>
    <td>{{$newMessagesCount}}</td>
    <td>{{$status === 0 ? 'Закрытая' : 'Открытая'}}</td>
    <td>
        <form action="{{route("requests.destroy", $id)}}" method="POST">
            {{ csrf_field() }}
            @method('DELETE')

            <a role="button" class="btn btn-success" href="{{route("requests.show", $id)}}">
                <i class="fas fa-edit"></i>
            </a>

            @if(!auth()->user()->isManager() && $status !== 0)
                <button type="submit" class="btn btn-danger">
                    <i class="far fa-window-close"></i>
                </button>
            @endif
        </form>
    </td>
</tr>
