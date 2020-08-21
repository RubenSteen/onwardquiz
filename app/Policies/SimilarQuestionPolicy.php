<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SimilarQuestionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can attach similar questions.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function attach(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can detach similar questions.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function detach(User $user)
    {
        return $user->isEditor();
    }
}
