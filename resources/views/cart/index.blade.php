<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">My Cart</h1>
            <a href="{{ route('products.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition duration-200">
                Back to Products
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if($cartItems->isEmpty())
            <div class="text-center py-8 text-gray-600">
                Your cart is empty.
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($cartItems as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">${{ number_format($item->price, 2) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('cart.update', $item->product->id) }}" method="POST" class="flex items-center space-x-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" 
                                               name="quantity" 
                                               value="{{ $item->quantity }}" 
                                               min="1"
                                               class="w-20 px-2 py-1 border border-gray-300 rounded-md text-center">
                                        <button type="submit" 
                                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md text-sm transition duration-200">
                                            Update
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        ${{ number_format($item->price * $item->quantity, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('cart.remove', $item->product->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm transition duration-200">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8 bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <div class="text-gray-600">Total Items:</div>
                    <div class="text-xl font-bold text-gray-900">{{ $cart->total_quantity ?? 0 }}</div>
                </div>
                <div class="flex justify-between items-center mb-6">
                    <div class="text-gray-600">Total Amount:</div>
                    <div class="text-2xl font-bold text-gray-900">${{ number_format($cart->total_amount ?? 0, 2) }}</div>
                </div>

                <form action="{{ route('cart.clear') }}" method="POST" class="text-right">
                    @csrf
                    <button type="submit" 
                            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                        Clear Cart
                    </button>
                </form>
            </div>
        @endif
    </div>
</body>
</html>