<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SubOrder;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\SubOrderResource;
use App\Http\Resources\SubOrderResource2;

class SubOrderController extends Controller
{
    public function index(Request $request) // all suborders for a supplier
    {
        //$this->authorize('viewaAny', SubOrder::class);
        $userId = $request->user()->id;
        $subOrders = SubOrder::where('supplier_id', $userId)
                             ->with('order.customer')
                             ->orderBy('created_at', 'desc')
                             ->get();

        return response()->json(SubOrderResource2::collection($subOrders));
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
                             ->with('order.customer') //should call customer_id not customer
                             ->orderBy('created_at', 'desc')
                             ->get()
                             ->groupBy(fn($subOrder) => $subOrder->order->customer_id);

        $response = [];
        foreach ($subOrders as $customerId => $customerSubOrders) {
            $customer = User::find($customerId);
            $customerData = [
                'user' => new UserResource($customer),
                'sub_orders' => SubOrderResource2::collection($customerSubOrders),
            ];
            $response[] = $customerData;
        }
                             
        return response()->json($response);
    }



    public function markAsDelivered(Request $request)
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
        $subOrder = SubOrder::with('order')->findOrFail($id);
        $this->authorize('update', $subOrder);

        $data = $request->validate([
            'new_quantities' => 'nullable|array', // its a map not an array though
            'new_qantities.*' => 'integer|min:0|max:999',
            'deletions' => 'nullable|array',
            'deletions.*' => 'nullable|integer|min:1',
        ]);

        $newQuantities = $data['new_quantities'];
        $deletions = $data['deletions'];

        foreach ($newQuantities as $productId => $newQuantity) {
            $product = $subOrder->products->find($productId);
            if(!$product){
             return response()->json([
                'message' => 'product with id '. $productId .' does not exist in this sub-order',
                ],404);
             }
            $product->pivot->quantity = $newQuantity;
            $product->pivot->save();
        }

        if($subOrder->products()->count() <= count($deletions)){
            return response()->json(['message' => 'at least one product must remain'], 400);
        }
        
        $subOrder->products()->detach($deletions); //can delations[] be empty ?
        
        return response()->json(['message' => 'Suborder updated successfully']);

        // if the order is day old for example dony let user edit it (grey out the option in mobile app) (in policy)
        // also make a QR code scan for verifying delivery
    }



    public function destroy(string $id)
    {
        //cancel ?
    }
}
