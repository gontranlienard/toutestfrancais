<div class="product-card">

    {{-- IMAGE --}}
    <div class="product-image">
        @if(!empty($product->image))
            <img src="{{ $product->image }}" alt="{{ $product->name }}">
        @else
            <img src="/images/no-image.png" alt="Image indisponible">
        @endif

       
    </div>


    {{-- INFOS --}}
    <div class="product-info">

        <h3 class="product-title">
            {{ $product->name }}
        </h3>

        @if(isset($product->brand) && $product->brand)

		<div class="product-brand-row">
			<a href="{{ route('brand.show', $product->brand->slug) }}" class="brand-link">
				{{ $product->brand->name }}
			</a>
			@php
			$variant = $product->variants->first();
			$isFavorite = $variant && in_array($variant->id, $wishlist ?? []);
			@endphp
			<span 
			class="favorite-star {{ $isFavorite ? 'favorited' : '' }}"
			data-variant="{{ $variant->id }}"
			>
			★
			</span>
		</div>

		@endif


					{{-- PRIX --}}
					@php
					$minPrice = $product->offers_min_price;
					@endphp

					<div class="price-block">
					@if($minPrice)
						<span class="price-label">
							Dès
						</span>
						<span class="current-price">
							{{ number_format($minPrice, 2, ',', ' ') }} €
						</span>
					@else
						<span class="no-price">
							Prix indisponible
						</span>
					@endif
					</div>


        {{-- CTA --}}
        <a href="{{ route('product.show', $product->slug) }}" class="btn-product">
            Voir le produit
        </a>

    </div>

</div>