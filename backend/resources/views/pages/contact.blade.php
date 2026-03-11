@extends('layouts.app')

@section('content')

<div class="contact-wrapper">

    <div class="contact-card">

        <h1 class="contact-title">Contact</h1>

        <p class="contact-subtitle">
            Une question, un bug ou un partenariat ? Envoyez-nous un message.
        </p>

        @if(session('success'))
            <div class="contact-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('contact.send') }}" class="contact-form">

            @csrf

            <div class="contact-field">
                <label>Adresse email *</label>
                <input
                    type="email"
                    name="email"
                    class="contact-input"
                    placeholder="votre@email.com"
                    required
                >
            </div>

            <div class="contact-field">
                <label>Sujet *</label>
                <select name="subject" class="contact-select" required>
                    <option value="">Choisir un sujet</option>
                    <option value="partenariat">Demande de partenariat</option>
                    <option value="bug">Signaler un bug</option>
                    <option value="produit">Je cherche un produit</option>
                </select>
            </div>

            <div class="contact-field">
                <label>Votre message *</label>
                <textarea
                    name="message"
                    class="contact-textarea"
                    placeholder="Votre message..."
                    required
                ></textarea>
            </div>

            <button type="submit" class="contact-button">
                Envoyer le message
            </button>

        </form>

    </div>

</div>

@endsection