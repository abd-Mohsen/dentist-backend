<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubOrderResource;
use App\Models\SubOrder;
use Illuminate\Http\Request;

class SubOrderController extends Controller
{
    public function index(Request $request)
    {
        //$this->authorize('viewaAny', SubOrder::class);
        $userId = $request->user()->id;
        $subOrders = SubOrder::where($userId,'supplier_id');

        // all suborders for a supplier
        // make 3 requests,
        // one for all suborders,
        // second for the pending suborders,
        // another grouped by customer
    }



    public function store(Request $request)
    {
        //no need
    }



    public function show(string $id)
    {
        $subOrder = SubOrder::findOrFail($id);
        return new SubOrderResource($subOrder);
    }



    public function update(Request $request, string $id)
    {
        //update in product_order table
        //or update the whole order?
    }



    public function destroy(string $id)
    {
        //cancel ?
    }
}
