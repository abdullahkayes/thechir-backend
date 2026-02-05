<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class ChatController extends Controller
{
    public function handleChat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid input'], 400);
        }

        $userMessage = trim(strip_tags($request->input('message')));

        $products = $this->searchProducts($userMessage);

        if (count($products) === 1) {
            $product = $products->first();
            $inventory = $product->inventories->first();
            $price = $inventory ? $inventory->price : 'Not available';
            $stock = $inventory ? $inventory->quantity : 0;
            $category = $product->rel_to_cat->name ?? 'Not specified';

            $message = "Here's the information about {$product->product_name}:\n";
            $message .= "Price: $" . $price . "\n";
            $message .= "Stock: " . $stock . "\n";
            $message .= "Category: " . $category . "\n";
            $message .= "Description: " . ($product->short_desp ?: $product->long_desp ?: 'Not available');

            return response()->json([
                'type' => 'product_detail',
                'message' => $message,
                'product' => $this->formatProductForFrontend($product)
            ]);
        } elseif (count($products) > 1) {
            $message = "I found several products that might interest you. Here are some options:\n";
            foreach ($products as $product) {
                $inventory = $product->inventories->first();
                $price = $inventory ? $inventory->price : 'Not available';
                $message .= "- {$product->product_name}: $" . $price . "\n";
            }

            return response()->json([
                'type' => 'product_list',
                'message' => $message,
                'products' => $products->map(fn($p) => $this->formatProductForFrontend($p))->toArray()
            ]);
        } else {
            $message = "I'm sorry, but I couldn't find any products matching your query. Please try again with different keywords or let me know if you need help with something else.";

            return response()->json([
                'type' => 'text',
                'response' => $message
            ]);
        }
    }

    private function searchProducts($message)
    {
        $keywords = array_filter(explode(' ', strtolower($message)), fn($w) => strlen($w) > 2);

        if (empty($keywords)) return collect();

        $query = Product::query();

        foreach ($keywords as $keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('product_name', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('sku', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('short_desp', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('long_desp', 'LIKE', '%' . $keyword . '%')
                  ->orWhereHas('rel_to_cat', fn($catQuery) => $catQuery->where('name', 'LIKE', '%' . $keyword . '%'))
                  ->orWhere('tag_id', 'LIKE', '%' . $keyword . '%');
            });
        }

        return $query->with(['rel_to_cat', 'inventories'])->limit(10)->get();
    }

    private function formatProductForFrontend($product)
    {
        $inventory = $product->inventories->first();
        $gallery = $product->rel_to_gal->first();

        return [
            'id' => $product->id,
            'name' => $product->product_name,
            'description' => $product->short_desp ?: $product->long_desp,
            'price' => $inventory->price ?? 0,
            'stock' => $inventory->quantity ?? 0,
            'category' => $product->rel_to_cat->name ?? '',
            'image' => $gallery ? asset('upload/product/preview/' . $gallery->photo_name) : null,
            'url' => '/products/detailes/' . $product->id,
            'features' => [],
            'warranty' => null,
            'delivery_info' => 'Standard delivery available'
        ];
    }

    public function getChatContext()
    {
        return response()->json(['context' => 'Chat context']);
    }
}