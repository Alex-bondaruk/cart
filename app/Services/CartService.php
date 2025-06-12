<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartService
{
    /**
     * Gets or creates a cart for the current authenticated user.
     * Uses session ID for non-authenticated users.
     */
    public function getCart(): Cart
    {
        $user = Auth::user();
        $sessionId = session()->getId();

        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            if ($sessionId && $cart->session_id && $cart->session_id !== $sessionId) {
                $this->mergeSessionCartToUserCart($cart, $sessionId);
            }
        } else {
            $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        }

        if ($cart->session_id !== $sessionId) {
            $cart->session_id = $sessionId;
            $cart->save();
        }

        return $cart;
    }

    /**
     * Adds a product to the cart or updates its quantity.
     */
    public function addProduct(Product $product, int $quantity = 1): CartItem
    {
        $cart = $this->getCart();

        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            $cartItem = $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);
        }

        $this->updateCartTotals($cart);

        return $cartItem;
    }

    /**
     * Updates the quantity of a product in the cart.
     */
    public function updateProductQuantity(Product $product, int $quantity): ?CartItem
    {
        $cart = $this->getCart();

        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            if ($quantity <= 0) {
                $cartItem->delete();
                $cartItem = null;
            } else {
                $cartItem->quantity = $quantity;
                $cartItem->save();
            }
            $this->updateCartTotals($cart);
        }

        return $cartItem;
    }

    /**
     * Removes a product from the cart.
     */
    public function removeProduct(Product $product): void
    {
        $cart = $this->getCart();
        $cart->items()->where('product_id', $product->id)->delete();
        $this->updateCartTotals($cart);
    }

    /**
     * Clears the entire cart.
     */
    public function clearCart(): void
    {
        $cart = $this->getCart();
        $cart->items()->delete();
        $this->updateCartTotals($cart);
    }

    /**
     * Gets the cart contents with associated products.
     */
    public function getCartContents()
    {
        return $this->getCart()->items()->with('product')->get();
    }

    /**
     * Updates the total quantity and amount in the cart.
     */
    protected function updateCartTotals(Cart $cart): void
    {
        $totalQuantity = 0;
        $totalAmount = 0;

        foreach ($cart->items as $item) {
            $totalQuantity += $item->quantity;
            $totalAmount += $item->quantity * $item->price;
        }

        $cart->total_quantity = $totalQuantity;
        $cart->total_amount = $totalAmount;
        $cart->save();
    }

    /**
     * Merges the session cart into the user's cart upon login.
     */
    public function mergeSessionCartToUserCart(Cart $userCart, string $sessionId): void
    {
        $sessionCart = Cart::where('session_id', $sessionId)->first();

        if ($sessionCart && $sessionCart->id !== $userCart->id) {
            foreach ($sessionCart->items as $sessionItem) {
                $existingItem = $userCart->items()->where('product_id', $sessionItem->product_id)->first();

                if ($existingItem) {
                    $existingItem->quantity += $sessionItem->quantity;
                    $existingItem->save();
                } else {
                    $userCart->items()->create([
                        'product_id' => $sessionItem->product_id,
                        'quantity' => $sessionItem->quantity,
                        'price' => $sessionItem->price,
                    ]);
                }
                $sessionItem->delete();
            }
            $sessionCart->delete();
            $this->updateCartTotals($userCart);
        }
    }
}