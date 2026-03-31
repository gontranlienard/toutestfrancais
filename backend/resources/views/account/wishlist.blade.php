@extends('layouts.app')

@section('content')

<div class="account-layout">

    {{-- MENU GAUCHE (identique mon-compte) --}}
    <div class="account-menu">
		<ul>
        <li><a href="{{ route('account.dashboard') }}" class="account-menu-item">
            Mon compte
        </a></li>

        <li><a href="{{ route('account.wishlist') }}" class="account-menu-item">
            Mes favoris
        </a></li>

        <li><a href="#" class="account-menu-item">
            Changer mon mot de passe
        </a></li>

        <li><form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="account-menu-item">
                Déconnexion
            </button>
        </form></li>
		</ul>
    </div>


    {{-- CONTENU --}}
    <div class="account-content">

        <h1>Mes favoris</h1>

        <table class="wishlist-table">

<thead>
<tr>
<th>Produit</th>
<th>Nom</th>
<th>Prix lors de l'ajout</th>
<th>Prix actuel</th>
<th></th>
</tr>
</thead>

<tbody>

@foreach($wishlists as $item)

@php
$product = $item->variant->product;

$currentPrice = null;

foreach($item->variant->offers as $offer){
    if($currentPrice === null || $offer->price < $currentPrice){
        $currentPrice = $offer->price;
    }
}
@endphp

<tr>

<td>
<img src="{{ $product->image }}" width="60">
</td>

<td>
<a href="{{ route('product.show',$product->slug) }}">
{{ $product->name }}
</a>
</td>

<td>
{{ number_format($item->price_when_added,2,',',' ') }} €
</td>

<td>
@if($currentPrice)
{{ number_format($currentPrice,2,',',' ') }} €
@else
—
@endif
</td>

<td>

<form method="POST" action="{{ route('wishlist.delete',$item->id) }}">
@csrf
@method('DELETE')

<button class="btn-remove">
Supprimer
</button>

</form>

</td>

</tr>

@endforeach

</tbody>

</table>

    </div>

</div>

@endsection