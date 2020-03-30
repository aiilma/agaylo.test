<tr>
    <th scope="row">{{$id}}</th>
    <td>{{$subject}}</td>
    <td>{{$email}}</td>
    <td>{{$statusText}}</td>
    <td>
        <form action="{{route("requests.update", $id)}}" method="POST">
            {{ csrf_field() }}
            @method('PUT')

            <a role="button" class="btn btn-success" href="{{route("requests.show", $id)}}">
                <i class="fas fa-edit"></i>
            </a>

            <button type="submit" class="btn btn-danger">
                <i class="far fa-window-close"></i>
            </button>
        </form>
    </td>
</tr>
