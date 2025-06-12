<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Shows the cart contents.
     */
    public function index()
    {
        $cartItems = $this->cartService->getCartContents();
        $cart = $this->cartService->getCart();

        return view('cart.index', compact('cartItems', 'cart'));
    }

    /**
     * Adds a product to the cart.
     */
    public function add(Request $request, Product $product)
    {
        $quantity = $request->input('quantity', 1);

        if (!is_numeric($quantity) || $quantity < 1) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please enter a valid quantity (minimum 1).');
        }

        try {
            $this->cartService->addProduct($product, (int)$quantity);
            return redirect()->back()->with('success', 'Product added to cart successfully!');
        } catch (\Exception $e) {
            Log::error('Error adding product to cart: ' . $e->getMessage());
            $errorMessage = 'Failed to add product to cart. ';
            $errorMessage .= app()->environment('local') ? $e->getMessage() : 'Please try again later.';
            
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Updates the quantity of a product in the cart.
     */
    public function update(Request $request, Product $product)
    {
        $quantity = $request->input('quantity');

        if (!is_numeric($quantity) || $quantity < 0) {
            return redirect()->back()->with('error', 'Quantity must be a positive number.');
        }

        try {
            $this->cartService->updateProductQuantity($product, (int)$quantity);
            return redirect()->back()->with('success', 'Product quantity updated.');
        } catch (\Exception $e) {
            Log::error('Error updating product quantity: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update product quantity.');
        }
    }

    /**
     * Removes a product from the cart.
     */
    public function remove(Product $product)
    {
        try {
            $this->cartService->removeProduct($product);
            return redirect()->back()->with('success', 'Product removed from cart.');
        } catch (\Exception $e) {
            Log::error('Error removing product: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to remove product.');
        }
    }

    /**
     * Clears the entire cart.
     */
    public function clear()
    {
        try {
            $this->cartService->clearCart();
            return redirect()->back()->with('success', 'Cart cleared.');
        } catch (\Exception $e) {
            Log::error('Error clearing cart: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to clear cart.');
        }
    }
}