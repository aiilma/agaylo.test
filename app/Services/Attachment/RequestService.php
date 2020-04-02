<?php


namespace App\Services\Attachment;

use App\Jobs\SendRequestLetterJob;
use App\Models\Request as SupportRequest;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class RequestService
{
    public static function saveNewRequest(Request $request, Authenticatable $user, AttachmentService $attachment)
    {
        return SupportRequest::create([
            'subject' => $request->subject,
            'client_id' => $user->id,
//            'manager_id' => 1 // можно убрать (тогда любой другой менеджер должен принять заявку самостоятельно)
        ])->dialogue()->create([
            'body' => $request->body,
            'attachment' => $attachment->saveToDisk(),
            'author_id' => $user->id,
        ]);
    }

    public static function read(Authenticatable $user, SupportRequest $req)
    {
        $newMessages = $req->getNewMessages($user->id)->get();

        if ($newMessages->isNotEmpty()) {
            foreach ($newMessages as $msg) {
                $msg->is_checked = 1;
                $msg->save();
            }

            return true;
        }

        return false;
    }

    public static function accept(Authenticatable $user, SupportRequest $req)
    {
        $req->manager_id = $user->id;
        $req->save();
    }

    public static function close(SupportRequest $req)
    {
        if ($req->isOpened()) {
            $req->status = 0;
            $req->save();

            return true;
        }

        return false;
    }

    public static function noteOnMail(SupportRequest $req)
    {
        // note manager if exists
        if ($email = $req->getManagerEmail()) {
            $details = [
                'email' => $email,
                'message' => 'Клиент закрыл заявку',
            ];
            dispatch(new SendRequestLetterJob($details));

            return true;
        }

        return false;
    }
}
