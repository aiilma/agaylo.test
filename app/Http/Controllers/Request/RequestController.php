<?php

namespace App\Http\Controllers\Request;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Request as SupportRequest;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->view('requests.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view('requests.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $params = $request->except('_token');
        $user = auth()->user();

        // если прошло N времени с момента добавления последней заявки, то можно добавить еще одну заявку
        $recentlyRequests = $user->requests()->where('created_at', '>', Carbon::parse('-24 hours'));

        if ($recentlyRequests->get()->isNotEmpty()) {
            $futureDt = $recentlyRequests->first()->created_at->addHours(24);
            $expire = $futureDt->diffForHumans();
            return back()->with('error', "Вы можете отправить еще одну заявку $expire");
        }

        $fileName = null;
        if ($request->hasFile('attachment')) {
            $fileStoragePath = $request->attachment->store('support/attachments');
            $fileFullPath = storage_path("app/$fileStoragePath");
            $fileName = basename($fileFullPath);
        }

        SupportRequest::create([
            'subject' => $params['subject'],
            'client_id' => $user->id,
            'manager_id' => 1 // можно убрать (тогда любой другой менеджер должен быть выставлен вручную в БД)
        ])->dialogue()->create([
            'body' => $params['body'],
            'attachment' => $fileName,
        ]);

        return back()->with('success', "ОК");
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->view('requests.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->view('requests.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $id;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return __method__;
    }
}
