<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\SubOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OrderResource;
use App\Models\Product;

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
        // make sure that each cart quantity value is between min and max_purchace_qty
        // assure that there is no duplicates id's in product_order for this order
        $this->authorize('create', Order::class);
        $customerId = $request->user()->id;

        $data = $request->validate([
            '*.product_id' => 'required|integer',
            '*.quantity' => 'required|integer',
            '*.price' => 'required|numeric',
            '*.supplier_id' => 'required|integer',
        ]);

        $order = null;

        try {
            DB::beginTransaction();
        
            $order = Order::create(['customer_id' => $customerId]);
        
            $products_by_supplier = collect($data)
                ->groupBy(function ($cartItem) {
                    // if(User::findOrFail($cartItem["supplier_id"])?->role->title != 'supllier')
                    //  return response()->json('a product doesnt belong to a supplier',401);
                    return $cartItem["supplier_id"];
                });
        
            foreach ($products_by_supplier as $supplierId => $cartItems) {
                $suborder = Suborder::create([
                    'order_id' => $order->id,
                    'supplier_id' => $supplierId,
                    'status' => 'pending',
                ]);
        
                foreach ($cartItems as $cartItem) {
                    $product = Product::findOrFail($cartItem['product_id']);
                    if($product->min_purchase_qty > $cartItem['quantity'] || $product->max_purchase_qty < $cartItem['quantity']){
                        return response()->json([
                            'message' => 'quantity of \''. $product->name .'\' must be in range '. $product->min_purchase_qty .' - '. $product->max_purchase_qty ,
                        ], 400);
                    }
                    $suborder->products()->attach(
                        $cartItem['product_id'],
                        ['quantity' => $cartItem['quantity'], 'price' => $cartItem['price']]
                    );
                }
            }
        
            DB::commit();
        
            return response()->json([
                'message' => 'order has been submitted',
                'order' => new OrderResource($order),
            ], 201);   
        }

        catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'order submission failed',
                'error' => $e->getMessage(),
            ], 500);
        }    

    }



    public function show(string $id)
    {
        $order = Order::findOrFail($id);
        $this->authorize('view', $order);
        return response()->json(new OrderResource($order));
    }



    public function update(Request $request, string $id)
    {
        // do we need it?
        return response()->json("orders cannot be edited directly", 400);
    }


   
    public function destroy(string $id)
    {
        // in policy block all actions on cancelled orders
        $order = Order::findOrFail($id);
        $this->authorize('delete', $order); // in policy, if order is old, dont cancel it 
        //cancel all sub-orders
        return response()->json("cancelled successfully");
    }

    // make a request to cancel all sub orders
}
