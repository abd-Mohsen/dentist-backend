<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\SubOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OrderResource;

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
        $customer_id = $request->user()->id;

        $data = $request->validate([
            '*.product_id' => 'required|integer',
            '*.quantity' => 'required|integer',
            '*.price' => 'required|double',
        ]);

        $order = null;

        DB::transaction(function () use ($data, $customer_id) {
            // Create the order
            $order = Order::create([
                'customer_id' => $customer_id,
            ]);
        
            // Group the products by supplier
            $products_by_supplier = collect($data)
                ->groupBy(function ($item) {
                    $product = Product::findOrFail($item['product_id']);
                    return $product->supplier_id;
                });
        
            // Create a suborder for each group of products from the same supplier
            foreach ($products_by_supplier as $supplier_id => $products) {
                $suborder = Suborder::create([
                    'order_id' => $order->id,
                    'supplier_id' => $supplier_id,
                    'status' => 'pending',
                ]);
        
                // Add the products to the suborder
                foreach ($products as $product) {
                    $suborder->products()->attach($product['id'], [
                        'quantity' => $product['quantity'],
                        'price' => $product['price'],
                    ]);
                }
            }
        });
        
        return response()->json([
            'message' => 'order has been submitted',
            'order' => new OrderResource($order),
        ], 201);
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
