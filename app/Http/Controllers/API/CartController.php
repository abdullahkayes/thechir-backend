<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ProductInventory;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Safely get authenticated user from any guard
     */
    private function getAuthenticatedUser()
    {
        // First try to get user from request (set by middleware)
        if (request()->user()) {
            return request()->user();
        }

        // Fallback: manually check token for customer
        $token = request()->bearerToken();
        if ($token) {
            $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
                if ($user instanceof \App\Models\Coustomer || $user instanceof \App\Models\Reseller ||
                    $user instanceof \App\Models\B2b || $user instanceof \App\Models\Distributer ||
                    $user instanceof \App\Models\Amazon) {
                    return $user;
                }
            }
        }

        return null;
    }

function add_cart(Request $request){
    $request->validate([
        'product_id'=>'required',
        'quantity'=>'required|integer|min:1',
    ],[
        'product_id.required'=>'Product ID is required',
        'quantity.required'=>'Quantity is required',
        'quantity.integer'=>'Quantity must be a number',
        'quantity.min'=>'Quantity must be at least 1',
    ]);

    $fromFamilyBundles = $request->boolean('from_family_bundles', false);

    // Detect user type and get user ID
    $user = $this->getAuthenticatedUser();
    if (!$user) {
        return response()->json(['error' => 'Authentication required'], 401);
    }

    $userData = [];
    $userType = get_class($user);

   // Handle different user types
   if ($user instanceof \App\Models\Coustomer || $user instanceof \App\Models\User) {
       $userData['coustomer_id'] = $user->id;
   } elseif ($user instanceof \App\Models\Reseller) {
       $userData['reseller_id'] = $user->id;
   } elseif ($user instanceof \App\Models\B2b) {
       $userData['b2b_id'] = $user->id;
   } elseif ($user instanceof \App\Models\Distributer) {
       $userData['distributer_id'] = $user->id;
   } elseif ($user instanceof \App\Models\Amazon) {
       $userData['amazon_id'] = $user->id;
   } else {
       return response()->json(['error' => 'Invalid user type: ' . $userType], 403);
   }

    // Get color_id and size_id from inventory if not provided
    $color_id = $request->color_id;
    $size_id = $request->size_id;

    // If color_id or size_id not provided, get from inventory
    if (!$color_id || !$size_id) {
        $inventory = ProductInventory::where('product_id', $request->product_id)->first();
        if ($inventory) {
            $color_id = $color_id ?: $inventory->color_id;
            $size_id = $size_id ?: $inventory->size_id;
        }
    }

    // Ensure color_id and size_id are not null (required by database schema)
    if (!$color_id) {
        return response()->json([
            'error' => 'Color selection is required for this product'
        ], 422);
    }
    if (!$size_id) {
        return response()->json([
            'error' => 'Size selection is required for this product'
        ], 422);
    }

    // Validate color_id and size_id if they exist
    if ($color_id) {
        $request->validate(['color_id'=>'exists:colors,id'], ['color_id.exists'=>'Invalid color selected']);
    }
    if ($size_id) {
        $request->validate(['size_id'=>'exists:sizes,id'], ['size_id.exists'=>'Invalid size selected']);
    }

    // Check inventory quantity
    $inventory = ProductInventory::where('product_id', $request->product_id)
                        ->when($color_id, fn($q) => $q->where('color_id', $color_id))
                        ->when($size_id, fn($q) => $q->where('size_id', $size_id))
                        ->first();

    if (!$inventory) {
        return response()->json([
            'error' => 'Selected product variant is not available'
        ], 422);
    }

    $existingCartQuantity = Cart::where('product_id', $request->product_id)
                                ->when($color_id, fn($q) => $q->where('color_id', $color_id))
                                ->when($size_id, fn($q) => $q->where('size_id', $size_id))
                                ->sum('quantity');

    if ($inventory && $existingCartQuantity + $request->quantity > $inventory->quantity) {
        return response()->json([
            'error' => 'Insufficient stock. Available quantity: ' . ($inventory->quantity - $existingCartQuantity)
        ], 422);
    }

    // Determine user and set price accordingly using multi-guard auth
    if ($user && $user instanceof \App\Models\Reseller && $user->status === 'approved' && $inventory->reseller_price) {
        $price = $inventory->reseller_price;
    } elseif ($user && $user instanceof \App\Models\B2b && $user->status === 'approved' && $inventory->wholesale_price) {
        $price = $inventory->wholesale_price;
    } elseif ($user && $user instanceof \App\Models\Distributer && $user->status === 'approved' && $inventory->distributer_price) {
        $price = $inventory->distributer_price;
    } elseif ($user && $user instanceof \App\Models\Amazon && $user->status === 'approved' && $inventory->amazon_price) {
        $price = $inventory->amazon_price;
    } else {
        $price = $inventory->discount_price ?? $inventory->price;
    }

    // Check for existing cart item for this user type
    $existingCartQuery = Cart::where('product_id', $request->product_id)
        ->when($color_id, fn($q) => $q->where('color_id', $color_id))
        ->when($size_id, fn($q) => $q->where('size_id', $size_id));

    // Add user type condition
    if (isset($userData['coustomer_id'])) {
        $existingCartQuery->where('coustomer_id', $userData['coustomer_id']);
    } elseif (isset($userData['reseller_id'])) {
        $existingCartQuery->where('reseller_id', $userData['reseller_id']);
    } elseif (isset($userData['b2b_id'])) {
        $existingCartQuery->where('b2b_id', $userData['b2b_id']);
    } elseif (isset($userData['distributer_id'])) {
        $existingCartQuery->where('distributer_id', $userData['distributer_id']);
    } elseif (isset($userData['amazon_id'])) {
        $existingCartQuery->where('amazon_id', $userData['amazon_id']);
    }

    if ($existingCartQuery->exists()) {
        $existingCartQuery->increment('quantity', $request->quantity);

        return response()->json([
            'success' => 'Product Added To Cart Successfully'
        ]);
    } else {
        try {
            $cart = new Cart();
            $cart->product_id = $request->product_id;
            $cart->color_id = $color_id;
            $cart->size_id = $size_id;
            $cart->quantity = $request->quantity;
            $cart->price = $price;
            $cart->sell_price = $price;
            $cart->cost_price = $inventory->buy_price;
            $cart->from_family_bundles = $fromFamilyBundles;
            // Calculate total weight: product weight * quantity
            $cart->weight_grams = $inventory->weight_grams * $request->quantity;

            // Set the appropriate user ID based on user type
            if (isset($userData['coustomer_id'])) {
                $cart->coustomer_id = $userData['coustomer_id'];
            } elseif (isset($userData['reseller_id'])) {
                $cart->reseller_id = $userData['reseller_id'];
            } elseif (isset($userData['b2b_id'])) {
                $cart->b2b_id = $userData['b2b_id'];
            } elseif (isset($userData['distributer_id'])) {
                $cart->distributer_id = $userData['distributer_id'];
            } elseif (isset($userData['amazon_id'])) {
                $cart->amazon_id = $userData['amazon_id'];
            }

            $cart->save();

            return response()->json([
                'success' => 'Product Added To Cart Successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Cart creation failed: ' . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'user_type' => get_class($user) ?? 'unknown',
                'request_data' => $request->all(),
                'user_data' => $userData,
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to add product to cart. Please try again.'
            ], 500);
        }
    }
}


function cart($id){
    // Detect user type from authenticated user
    $user = $this->getAuthenticatedUser();
    if (!$user) {
        return response()->json(['error' => 'Authentication required'], 401);
    }

    $query = Cart::with(['rel_to_product', 'color', 'size']);

    // Filter by user type
    if ($user instanceof \App\Models\Coustomer || $user instanceof \App\Models\User) {
        $query->where('coustomer_id', $user->id);
    } elseif ($user instanceof \App\Models\Reseller) {
        $query->where('reseller_id', $user->id);
    } elseif ($user instanceof \App\Models\B2b) {
        $query->where('b2b_id', $user->id);
    } elseif ($user instanceof \App\Models\Distributer) {
        $query->where('distributer_id', $user->id);
    } elseif ($user instanceof \App\Models\Amazon) {
        $query->where('amazon_id', $user->id);
    } else {
        return response()->json(['error' => 'Invalid user type'], 403);
    }

    $carts = $query->get();

    // Recalculate prices based on current user type and inventory
    foreach ($carts as $cart) {
        $inventory = ProductInventory::where('product_id', $cart->product_id)
            ->when($cart->color_id, fn($q) => $q->where('color_id', $cart->color_id))
            ->when($cart->size_id, fn($q) => $q->where('size_id', $cart->size_id))
            ->first();

        if ($inventory) {
            // Calculate correct weight: product weight * quantity
            $correctWeight = $inventory->weight_grams * $cart->quantity;

            if ($user instanceof \App\Models\Reseller && $user->status === 'approved' && $inventory->reseller_price) {
                $newPrice = $inventory->reseller_price;
            } elseif ($user instanceof \App\Models\B2b && $user->status === 'approved' && $inventory->wholesale_price) {
                $newPrice = $inventory->wholesale_price;
            } elseif ($user instanceof \App\Models\Distributer && $user->status === 'approved' && $inventory->distributer_price) {
                $newPrice = $inventory->distributer_price;
            } elseif ($user instanceof \App\Models\Amazon && $user->status === 'approved' && $inventory->amazon_price) {
                $newPrice = $inventory->amazon_price;
            } else {
                $newPrice = $inventory->discount_price ?? $inventory->price;
            }
            // Update the cart in database with correct price and weight
            $cart->price = $newPrice;
            $cart->weight_grams = $correctWeight;
            Cart::where('id', $cart->id)->update([
                'price' => $newPrice,
                'weight_grams' => $correctWeight
            ]);
        } else {
            // Set default weight if no inventory found (temporary property, not saved to DB)
            $cart->weight_grams = 500 * $cart->quantity; // Default 500g per item * quantity
        }
    }

    return response()->json([
        'carts' => $carts
    ]);
}

function cart_update(Request $request){
    // Verify user owns the cart items
    $user = $this->getAuthenticatedUser();
    if (!$user) {
        return response()->json(['error' => 'Authentication required'], 401);
    }

    foreach($request->carts as $cart){
        // Check inventory quantity before updating
        $cartItem = Cart::find($cart['id']);
        if ($cartItem) {
            // Verify ownership
            $ownsCart = false;
            if (($user instanceof \App\Models\Coustomer || $user instanceof \App\Models\User) && $cartItem->coustomer_id == $user->id) {
                $ownsCart = true;
            } elseif ($user instanceof \App\Models\Reseller && $cartItem->reseller_id == $user->id) {
                $ownsCart = true;
            } elseif ($user instanceof \App\Models\B2b && $cartItem->b2b_id == $user->id) {
                $ownsCart = true;
            } elseif ($user instanceof \App\Models\Distributer && $cartItem->distributer_id == $user->id) {
                $ownsCart = true;
            } elseif ($user instanceof \App\Models\Amazon && $cartItem->amazon_id == $user->id) {
                $ownsCart = true;
            }

            if (!$ownsCart) {
                return response()->json(['error' => 'Unauthorized to update this cart item'], 403);
            }

            $inventory = ProductInventory::where('product_id', $cartItem->product_id)
                                  ->when($cartItem->color_id, fn($q) => $q->where('color_id', $cartItem->color_id))
                                  ->when($cartItem->size_id, fn($q) => $q->where('size_id', $cartItem->size_id))
                                  ->first();

            if ($inventory && $cart['quantity'] > $inventory->quantity) {
                return response()->json([
                    'error' => 'Insufficient stock for product ID ' . $cartItem->product_id . '. Available quantity: ' . $inventory->quantity
                ], 422);
            }

            // Calculate new total weight: product weight * new quantity
            $newWeight = $inventory ? ($inventory->weight_grams * $cart['quantity']) : (500 * $cart['quantity']);

            Cart::where('id', $cart['id'])->update([
                'quantity' => $cart['quantity'],
                'weight_grams' => $newWeight
            ]);
        } else {
            // If cart item not found, just update quantity (fallback)
            Cart::where('id', $cart['id'])->update([
                'quantity' => $cart['quantity']
            ]);
        }
    }

    return response()->json([
     'success'=>'Cart Updated Successfully'
    ]);
}

function delete_cart_item(Request $request, $id){
    // Verify user owns the cart item
    $user = $this->getAuthenticatedUser();
    if (!$user) {
        return response()->json(['error' => 'Authentication required'], 401);
    }

    $cartItem = Cart::find($id);
    if (!$cartItem) {
        return response()->json(['error' => 'Cart item not found'], 404);
    }

    // Verify ownership
    $ownsCart = false;
    if (($user instanceof \App\Models\Coustomer || $user instanceof \App\Models\User) && $cartItem->coustomer_id == $user->id) {
        $ownsCart = true;
    } elseif ($user instanceof \App\Models\Reseller && $cartItem->reseller_id == $user->id) {
        $ownsCart = true;
    } elseif ($user instanceof \App\Models\B2b && $cartItem->b2b_id == $user->id) {
        $ownsCart = true;
    } elseif ($user instanceof \App\Models\Distributer && $cartItem->distributer_id == $user->id) {
        $ownsCart = true;
    } elseif ($user instanceof \App\Models\Amazon && $cartItem->amazon_id == $user->id) {
        $ownsCart = true;
    }

    if (!$ownsCart) {
        return response()->json(['error' => 'Unauthorized to delete this cart item'], 403);
    }

    $cartItem->delete();

    return response()->json([
        'success' => 'Cart item deleted successfully'
    ]);
}



}
