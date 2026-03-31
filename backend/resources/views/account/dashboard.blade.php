@extends('layouts.app')

@section('content')

<div class="account-layout">

    <div class="account-menu">

        <ul>
            <li><a href="{{ route('account.dashboard') }}">Mon compte</a></li>
            <li><a href="{{ route('account.wishlist') }}">Mes favoris</a></li>
            <li><a href="#">Changer mot de passe</a></li>

            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Déconnexion</button>
                </form>
            </li>
        </ul>

    </div>


    <div class="account-content">

        <h1>Bienvenue {{ Auth::user()->name }}</h1>
        

    </div>

</div>

@endsection