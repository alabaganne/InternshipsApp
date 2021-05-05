<?php

namespace App\Policies;

use App\Models\Internship;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class InternshipPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Internship  $internship
     * @return mixed
     */
    public function view(User $user, Internship $internship)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isCompany();
    }

    /**
     * Determine whether the user owns a specific internship
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Internship  $internship
     * @return mixed
     */
    public function isOwner(User $user, Internship $internship)
    {
        return $user->isCompany() && $user->userable->id === $internship->company_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Internship  $internship
     * @return mixed
     */
    public function update(User $user, Internship $internship)
    {
        return $this->isOwner($user, $internship);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Internship  $internship
     * @return mixed
     */
    public function delete(User $user, Internship $internship)
    {
        return $this->isOwner($user, $internship);
    }
}
