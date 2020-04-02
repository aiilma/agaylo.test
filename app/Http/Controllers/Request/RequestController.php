<?php

namespace App\Http\Controllers\Request;

use App\Http\Controllers\Controller;
use App\Filter\RequestsFilter;
use App\Services\Attachment\AttachmentService;
use App\Services\Attachment\RequestService;
use Illuminate\Contracts\Auth\Authenticatable;
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
     * @param RequestsFilter $filters
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, RequestsFilter $filters)
    {
        $user = auth()->user();

        if ($user->can('filter', SupportRequest::class)) {
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
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', SupportRequest::class);
        return response()->view('requests.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param AttachmentService $attachment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, AttachmentService $attachment)
    {
        $user = auth()->user();
        $HOURS_LIM = 24;

        if ($user->cannot('create', SupportRequest::class)) {
            return back()->with('error', "Вы должны иметь привелегии пользователя");
        }

        // если прошло N часов с момента добавления последней заявки, то можно добавить еще одну заявку
        $recentlyRequests = $user->recentlyRequests($HOURS_LIM);
        if ($recentlyRequests->get()->isNotEmpty()) {
            $message = "Вы можете отправить еще одну заявку {$recentlyRequests->getExpireForNextRequest($HOURS_LIM)}";
            return back()->with('error', $message);
        }

        RequestService::saveNewRequest($request, $user, $attachment);
        return back()->with('success', "OK!");
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = auth()->user();

        $req = SupportRequest::findOrFail($id);

        RequestService::read($user, $req);

        return response()->view('requests.show', compact('req'));
    }

    /**
     * Manager accepts a new request
     *
     * @param SupportRequest $req
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id)
    {
        $user = auth()->user();
        $req = SupportRequest::findOrFail($id);

        if ($user->can('acceptRequest', $req)) {
            RequestService::accept($user, $req);
        }

        return redirect()->route('requests.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = auth()->user();

        $req = SupportRequest::findOrFail($id);

        if ($user->can('close', $req)) {
            RequestService::close($req);
            RequestService::noteOnMail($req);
        }

        return redirect()->route('requests.index');
    }
}
