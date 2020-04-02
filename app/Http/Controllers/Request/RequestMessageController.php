<?php

namespace App\Http\Controllers\Request;

use App\Http\Controllers\Controller;
use App\Jobs\SendRequestLetterJob;
use App\Services\Attachment\AttachmentService;
use Illuminate\Http\Request;
use App\Models\Request as SupportRequest;

class RequestMessageController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $req = SupportRequest::find($request->id);

        $this->saveNewMessage($request, $req);
        $this->sendLetter($req);

        return back()->with('success', "ОК");
    }

    private function saveNewMessage($request, $req)
    {
        $user = auth()->user();
        $fileName = (new AttachmentService($request))->saveToDisk();

        return $req->dialogue()->create([
            'body' => $request->body,
            'attachment' => $fileName,
            'author_id' => $user->id,
        ]);
    }

    private function sendLetter($req)
    {
        $user = auth()->user();
        $letter = [];

        if ($user->isManager()) {
            $letter['email'] = $req->client->email;
            $letter['message'] = 'Вам ответил менеджер';
        } else {
            $letter['email'] = $req->manager !== null ? $req->manager->email : null;
            $letter['message'] = 'Поступил ответ клиента';
        }

        if ($letter['email'] !== null) dispatch(new SendRequestLetterJob($letter));
    }

}
