<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Request as SupportRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create requests.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->isManager()) return false;

        return true;
    }

    public function filter(User $user)
    {
        if ($user->isManager()) return true;

        return false;
    }

    /**
     * Determine whether the user can update the request.
     *
     * @param User $user
     * @param SupportRequest $request
     * @return mixed
     */
    public function acceptRequest(User $user, SupportRequest $req)
    {
        if ($user->isManager() && $req->manager_id !== $user->id) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the request.
     *
     * @param User $user
     * @param SupportRequest $request
     * @return mixed
     */
    public function close(User $user, SupportRequest $request)
    {
        if (!$user->isManager()) return true;

        return false;
    }
}
