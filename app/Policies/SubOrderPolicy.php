<?php

namespace App\Policies;

use App\Models\SubOrder;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SubOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SubOrder $subOrder): bool
    {
        return $user->role->title === 'admin' ||
         $subOrder->order->customer_id === $subOrder->id ||
         $subOrder->order->supplier_id === $subOrder->id;
    }

    // /**
    //  * Determine whether the user can create models.
    //  */
    // public function create(User $user): bool
    // {
    //     return $user->role->title === 'dentist';
    // }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SubOrder $subOrder): bool
    {
        return $user->role->title === 'admin' || $subOrder->order->customer_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SubOrder $subOrder): bool
    {
        return $user->role->title === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SubOrder $subOrder): bool
    {
        return $user->role->title === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SubOrder $subOrder): bool
    {
        return $user->role->title === 'admin';
    }
}
