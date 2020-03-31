<?php

namespace App\Http\Controllers\Request;

use App\Attachment\Attachment;
use App\Http\Controllers\Controller;
use App\Jobs\SendRequestLetterJob;
use App\Filter\RequestsFilter;
use Illuminate\Http\Request;
use App\Models\Request as SupportRequest;

class RequestController extends Controller
{
    protected $HOURS_LIM = 24;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param RequestsFilter $filters
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, RequestsFilter $filters)
    {
        $user = auth()->user();

        if ($user->isManager()) {
            $reqs = SupportRequest::with('dialogue')->filter($filters)->get();
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
        $HOURS_LIM = $this->HOURS_LIM;
        $user = auth()->user();

        // если прошло N часов с момента добавления последней заявки, то можно добавить еще одну заявку
        $recentlyRequests = $user->recentlyRequests($HOURS_LIM);

        if ($recentlyRequests->get()->isNotEmpty()) {
            $message = "Вы можете отправить еще одну заявку {$recentlyRequests->getExpireForNextRequest($HOURS_LIM)}";
            return back()->with('error', $message);
        }

        $this->saveNewRequest($request);
        return back()->with('success', "ОК");
    }

    private function saveNewRequest($request)
    {
        $user = auth()->user();
        $fileName = (new Attachment($request))->saveToDisk();
        return SupportRequest::create([
            'subject' => $request->subject,
            'client_id' => $user->id,
//            'manager_id' => 1 // можно убрать (тогда любой другой менеджер должен принять заявку самостоятельно)
        ])->dialogue()->create([
            'body' => $request->body,
            'attachment' => $fileName,
            'author_id' => $user->id,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param SupportRequest $req
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = auth()->user();
        $req = SupportRequest::find($id);
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
        $req = SupportRequest::find($id);
        $user = auth()->user();

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
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $req = SupportRequest::find($id);
        $user = auth()->user();

        if (!$user->isManager() && $req->status !== 0) {
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
