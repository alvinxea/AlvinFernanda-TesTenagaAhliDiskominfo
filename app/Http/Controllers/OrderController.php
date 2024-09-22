<?php

namespace App\Http\Controllers;

use App\Models\Order;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * Get all orders.
     */
    public function index(): JsonResponse
    {
        $orders = Order::with('products')->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'message' => 'Order List',
                'data' => []
            ], 200);
        }

        return response()->json([
            'message' => 'Order List',
            'data' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'products' => $order->products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => $product->price,
                            'quantity' => $product->pivot->quantity,
                            'stock' => $product->stock - $product->pivot->quantity, // Mengurangi stock
                            'sold' => $product->sold + $product->pivot->quantity, // Menambah sold
                            'created_at' => $product->created_at,
                            'updated_at' => $product->updated_at,
                        ];
                    }),
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ];
            })
        ], 200);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $productsData = $validated['products'];

        $products = [];
        $orderProducts = [];

        foreach ($productsData as $productData) {
            $product = Product::find($productData['id']);

            if ($product->stock < $productData['quantity']) {
                return response()->json([
                    'message' => 'Product out of stock'
                ], 400);
            }

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found'
                ], 404);
            }

            $product->stock -= $productData['quantity'];
            $product->sold += $productData['quantity'];
            $product->save();

            $products[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $productData['quantity'],
                'stock' => $product->stock,
                'sold' => $product->sold,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at
            ];

            $orderProducts[] = [
                'product_id' => $product->id,
                'quantity' => $productData['quantity'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $order = Order::create([
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $order->products()->attach($orderProducts);

        return response()->json([
            'message' => 'Order created',
            'data' => [
                'id' => $order->id,
                'products' => $products,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at
            ]
        ], 200);
    }

    public function show($id)
    {
        $order = Order::with('products')->find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        $products = $order->products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->pivot->quantity,
                'stock' => $product->stock,
                'sold' => $product->sold,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at
            ];
        });

        return response()->json([
            'message' => 'Order Detail',
            'data' => [
                'id' => $order->id,
                'products' => $products,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at
            ]
        ], 200);
    }

    public function destroy($id)
    {
        $order = Order::with('products')->find($id);

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        foreach ($order->products as $product) {
            $product->stock += $product->pivot->quantity;
            $product->sold -= $product->pivot->quantity; 
            $product->save();
        }

        $products = $order->products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $product->pivot->quantity,
                'stock' => $product->stock,
                'sold' => $product->sold,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at
            ];
        });

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully',
            'data' => [
                'id' => $order->id,
                'products' => $products,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at
            ]
        ], 200);
    }
}
