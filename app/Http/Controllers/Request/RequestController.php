<?php

namespace App\Http\Controllers\Request;

use App\Http\Controllers\Controller;
use App\Jobs\SendRequestLetterJob;
use App\Filter\RequestsFilter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Request as SupportRequest;

class RequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->isManager()) {
//            $reqs = SupportRequest::all();
            $reqs = SupportRequest::with('dialogue');

            $reqs = (new RequestsFilter($reqs, $request))->apply()->get();
        } else {
            $reqs = $user->requests;
        }

        return response()->view('requests.index', ['requests' => $reqs,]);
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
//            'manager_id' => 1 // можно убрать (тогда любой другой менеджер должен принять заявку самостоятельно)
        ])->dialogue()->create([
            'body' => $params['body'],
            'attachment' => $fileName,
            'author_id' => $user->id
        ]);

        return back()->with('success', "ОК");
    }

    /**
     * Display the specified resource.
     *
     * @param SupportRequest $req
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $req = SupportRequest::find($id);
        $user = auth()->user();

        $newMessages = $req->getNewMessages($user->id)->get();

        if ($newMessages->isNotEmpty()) {
            foreach ($newMessages as $msg) {
                $msg->is_checked = 1;
                $msg->save();
            }
        }

        return response()->view('requests.show', compact('req'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $user = auth()->user();
        $req = SupportRequest::find($id);

        if ($user->isManager() && $req->manager_id !== $user->id) {
            $req->manager_id = $user->id;
            $req->save();
        }

        return response()->view('requests.show', compact('req'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $req = SupportRequest::find($id);

        if (!auth()->user()->isManager() && $req->status !== 0) {
            $req->status = 0;
            $req->save();
        }

        $details = [
            'email' => $req->manager->email,
            'message' => 'Клиент закрыл заявку',
        ];
        dispatch(new SendRequestLetterJob($details));

        return redirect(route('requests.index'));
    }
}
