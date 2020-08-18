<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionPicturePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create question pictures.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can update the question picture.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can delete the question picture.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can restore the question picture.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function restore(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can permanently delete the question picture.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function forceDelete(User $user)
    {
        return $user->isSuperAdmin();
    }
}
