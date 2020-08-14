<?php

namespace App\Policies;

use App\Question;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionPolicy
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

    /**
     * Determine whether the user can update the question.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can delete the question.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can restore the question.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function restore(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can permanently delete the question.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function forceDelete(User $user)
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can create question pictures.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function createPicture(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can update the question picture.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function updatePicture(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can delete the question picture.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function deletePicture(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can restore the question picture.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function restorePicture(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can permanently delete the question picture.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function forceDeletePicture(User $user)
    {
        return $user->isSuperAdmin();
    }
}
