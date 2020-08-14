<?php

namespace App\Policies;

use App\Map;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MapPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create maps.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can view any maps.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can view the map.
     *
     * @param User $user
     * @param  \App\Map  $map
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can update the map.
     *
     * @param User $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can delete the map.
     *
     * @param User $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can restore the map.
     *
     * @param User $user
     * @return mixed
     */
    public function restore(User $user)
    {
        return $user->isEditor();
    }

    /**
     * Determine whether the user can permanently delete the map.
     *
     * @param User $user
     * @return mixed
     */
    public function forceDelete(User $user)
    {
        return $user->isSuperAdmin();
    }
}
