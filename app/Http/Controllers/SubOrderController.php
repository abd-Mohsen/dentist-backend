<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubOrderResource;
use App\Http\Resources\SubOrderResource2;
use App\Models\SubOrder;
use Illuminate\Http\Request;

class SubOrderController extends Controller
{
    public function index(Request $request)
    {
        //$this->authorize('viewaAny', SubOrder::class);
        $userId = $request->user()->id;
        $subOrders = SubOrder::where('supplier_id', $userId)
                             ->with('order.customer')
                             ->orderBy('created_at', 'desc')
                             ->get();

        return response()->json(SubOrderResource2::collection($subOrders));
        // all suborders for a supplier
        // make 3 requests,
        // one for all suborders,
        // second for the pending suborders,
        // another grouped by customer
    }



    public function pendingSubOrders(Request $request)
    {
        //$this->authorize('viewaAny', SubOrder::class);
        $userId = $request->user()->id;
        $subOrders = SubOrder::where('supplier_id', $userId)
                             ->where('status', 'pending')
                             ->with('order.customer')
                             ->orderBy('created_at', 'desc')
                             ->get();

        return response()->json(SubOrderResource2::collection($subOrders));
    }



    public function subOrdersGrouped(Request $request)
    {
        //$this->authorize('viewaAny', SubOrder::class);
        $userId = $request->user()->id;
        $subOrders = SubOrder::where('supplier_id', $userId)
                             ->where('status', 'pending')
                             ->with('order.customer')
                             ->orderBy('created_at', 'desc')
                             ->get()
                             ->groupBy(fn($subOrder) => $subOrder->order->customer);

        return response()->json($subOrders);
        return response()->json(SubOrderResource::collection($subOrders)); // return {"user":___, "suborder":[____] }
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
        // dont let user add product, just delete or edit qty
        // also make a QR code scan for verifying delivery
    }



    public function destroy(string $id)
    {
        //cancel ?
    }
}
