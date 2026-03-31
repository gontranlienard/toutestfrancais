@extends('layouts.app')

@section('content')

<div class="contact-wrapper">

    <div class="contact-card">

        <h1 class="contact-title">Connexion</h1>

        <p class="contact-subtitle">
            Connectez-vous à votre compte Komparons Moto.
        </p>

        @if(session('status'))
            <div class="contact-success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="contact-form">
            @csrf

            {{-- EMAIL --}}
            <div class="contact-field">
                <label>Email *</label>
                <input
                    type="email"
                    name="email"
                    class="contact-input"
                    value="{{ old('email') }}"
                    placeholder="votre@email.com"
                    required
                    autofocus
                >
                @error('email')
                    <div class="contact-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- PASSWORD --}}
            <div class="contact-field">
                <label>Mot de passe *</label>
                <input
                    type="password"
                    name="password"
                    class="contact-input"
                    required
                >
                @error('password')
                    <div class="contact-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- REMEMBER --}}
            <div class="contact-field" style="display:flex; align-items:center; gap:8px;">
                <input
                    type="checkbox"
                    name="remember"
                    id="remember"
                    {{ old('remember') ? 'checked' : '' }}
                >
                <label for="remember" style="margin:0;">
                    Restez connecté
                </label>
            </div>

            <button type="submit" class="contact-button">
                Connexion
            </button>

            @if (Route::has('password.request'))
                <div style="text-align:center; margin-top:15px;">
                    <a href="{{ route('password.request') }}" style="color: var(--primary); text-decoration:none;">
                        Mot de passe oublié ?
                    </a>
                </div>
            @endif

        </form>

    </div>

</div>

@endsection