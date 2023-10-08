<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
// dont let operations happen on cancelled orders, suborders

    public function viewAny(User $user): bool
    {
        return true;
    }


    public function view(User $user, Order $order): bool
    {
        //return $user->role->title === 'admin' || $order->customer_id === $order->id;
        return $user->role->title === 'admin' ||
         $order->customer_id === $user->id ||
         $order->supplier_id === $user->id;
    }


    public function create(User $user): bool
    {
        return $user->role->title === 'dentist';
    }


    public function delete(User $user, Order $order): bool
    {
        return $user->role->title === 'admin';
    }


    public function restore(User $user): bool
    {
        return $user->role->title === 'admin';
    }


    public function forceDelete(User $user): bool
    {
        return $user->role->title === 'admin';
    }
}
