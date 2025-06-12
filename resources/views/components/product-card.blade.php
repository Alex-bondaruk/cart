@props(['product'])

<article class="bg-white rounded shadow">
    <div class="relative pb-[60%] bg-gray-100">
        @if ($product->image && file_exists(public_path('storage/' . $product->image)))
            <img src="{{ asset('storage/' . $product->image) }}" 
                 alt="{{ $product->name }}"
                 class="absolute inset-0 w-full h-full object-cover">
        @else
            <div class="absolute inset-0 flex items-center justify-center">
                <x-icons.no-image class="w-12 h-12 text-gray-300" />
            </div>
        @endif
    </div>

    <div class="p-4 flex flex-col">
        <h2 class="text-lg font-bold mb-2">{{ $product->name }}</h2>
        <p class="text-gray-600 text-sm mb-4 flex-grow">
            {{ Str::limit($product->description, 100) }}
        </p>
        <div class="text-xl font-bold mb-4">${{ number_format($product->price, 2) }}</div>
        
        <form action="{{ route('cart.add', $product->id) }}" 
              method="POST" 
              x-data="{ submitting: false }"
              @submit="submitting = true">
            @csrf
            <div class="flex gap-2 mb-3">
                <label for="quantity-{{ $product->id }}">Quantity:</label>
                <input type="number" 
                       name="quantity" 
                       id="quantity-{{ $product->id }}" 
                       value="1"
                       min="1"
                       required 
                       class="w-20 border rounded text-center">
            </div>
            <button type="submit" 
                    class="w-full bg-blue-600 text-white rounded p-2"
                    :disabled="submitting">
                <span x-text="submitting ? 'Adding...' : 'Add to Cart'">Add to Cart</span>
            </button>
        </form>
    </div>
</article>
