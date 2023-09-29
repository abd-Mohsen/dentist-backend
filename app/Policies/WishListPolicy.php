<?php

namespace App\Policies;

use App\Models\User;
use App\Models\wishlist;
use Illuminate\Auth\Access\Response;

class WishListPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role->title === "dentist";
    }


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role->title === "dentist";
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Wishlist $wishlist): bool
    {
        return $user->id === $wishlist->user_id;
    }

}
