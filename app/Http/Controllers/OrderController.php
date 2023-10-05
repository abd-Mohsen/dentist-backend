<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function index(Request $request) //all orders of current customer
    {
        $userId = $request->user()->id;

        $orders = Order::where('customer_id', $userId)->get();

        return response()->json(OrderResource::collection($orders));
    }



    public function store(Request $request) //make a new order
    {
        $this->authorize('create', Order::class);

        $data = $request->validate([
            //hashmap of products and quantities
        ]);

        // create order
        // create suborders and pivots
    }



    public function show(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
