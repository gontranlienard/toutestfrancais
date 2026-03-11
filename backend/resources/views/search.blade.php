@extends('layouts.app')

@section('title', 'Recherche "' . request('q') . '"')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    <form action="{{ route('search') }}" method="GET" class="mb-8">
        <div class="flex gap-3">
            <input 
                type="text" 
                name="q" 
                value="{{ request('q') }}"
                placeholder="Rechercher un produit, une marque, un EAN..."
                class="w-full border rounded-xl px-4 py-3"
            >
            <button class="bg-black text-white px-6 py-3 rounded-xl">
                Rechercher
            </button>
        </div>
    </form>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-semibold">
            {{ $products->total() }} résultats
        </h1>

        <form method="GET">
            <input type="hidden" name="q" value="{{ request('q') }}">
            <select name="sort" onchange="this.form.submit()" class="border rounded-lg px-3 py-2">
                <option value="relevance" {{ request('sort') == 'relevance' ? 'selected' : '' }}>Pertinence</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
            </select>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">

        @foreach($products as $product)
        <div class="border rounded-2xl p-4 hover:shadow-lg transition">

            <a href="{{ route('product.show', $product->slug) }}">
                <img 
                    src="{{ $product->image }}" 
                    alt="{{ $product->name }}"
                    class="w-full h-56 object-contain mb-4"
                >
            </a>

            <div class="text-sm text-gray-500 mb-1">
                {{ $product->brand }}
            </div>

            <a href="{{ route('product.show', $product->slug) }}">
                <h2 class="font-semibold mb-2 line-clamp-2">
                    {{ $product->name }}
                </h2>
            </a>

            <div class="mt-3">
                <div class="text-lg font-bold">
                    À partir de {{ number_format($product->offers_min_price, 2, ',', ' ') }} €
                </div>
                <div class="text-sm text-gray-500">
                    {{ $product->offers_count }} marchands
                </div>
            </div>

            <a 
                href="{{ route('product.show', $product->slug) }}"
                class="block text-center mt-4 bg-black text-white py-2 rounded-xl"
            >
                Comparer les prix
            </a>

        </div>
        @endforeach

    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>

</div>
@endsection

