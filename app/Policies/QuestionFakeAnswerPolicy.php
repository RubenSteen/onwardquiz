<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionFakeAnswerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create questions.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isEditor();
    }
}
