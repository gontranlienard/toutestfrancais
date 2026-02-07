<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.title') }}</title>
</head>
<body>

    <header>
        <h1>🏍️ {{ __('messages.welcome') }}</h1>

        <nav>
            <a href="/lang/fr">🇫🇷 FR</a> |
            <a href="/lang/en">🇬🇧 EN</a>
        </nav>
        <hr>
    </header>

    @forelse($categories as $category)
        <h2>{{ $category->name }}</h2>

        @if($category->products->isEmpty())
            <p>{{ __('messages.no_product') }}</p>
        @else
            <ul>
                @foreach($category->products as $product)
                    <li>
                        {{ $product->name }} – €{{ $product->price }}
                        |
                        <a href="{{ $product->link }}" target="_blank">
                            {{ __('messages.view_product') }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    @empty
        <p>{{ __('messages.no_category') }}</p>
    @endforelse

</body>
</html>
