<?php

namespace App\Http\Controllers\Request;

use App\Http\Controllers\Controller;
use App\Jobs\SendRequestLetterJob;
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $params = $request->except('_token');
        $req = SupportRequest::find($params['id']);

        $fileName = null;
        if ($request->hasFile('attachment')) {
            $fileStoragePath = $request->attachment->store('support/attachments');
            $fileFullPath = storage_path("app/$fileStoragePath");
            $fileName = basename($fileFullPath);
        }

        $req->dialogue()->create([
            'body' => $params['body'],
            'attachment' => $fileName,
            'author_id' => $user->id,
        ]);

        $details = [];
        if ($user->isManager()) {
            $details['email'] = $req->client->email;
            $details['message'] = 'Вам ответил менеджер';
        } else {
            $details['email'] = $req->manager->email;
            $details['message'] = 'Поступил ответ клиента';
        }

        dispatch(new SendRequestLetterJob($details));

        return back()->with('success', "ОК");
    }
}
