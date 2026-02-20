<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Comparateur Moto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body { margin:0; font-family:Arial; background:#f4f6f9; }

        .top-menu {
            background:#111;
            padding:12px 20px;
        }

        .top-menu ul {
            list-style:none;
            margin:0;
            padding:0;
            display:flex;
        }

        .top-menu li {
            position:relative;
            margin-right:25px;
        }

        .top-menu a {
            color:white;
            text-decoration:none;
        }

        .top-menu a:hover {
            color:#ff3c00;
        }

        .dropdown {
            position:absolute;
            top:100%;
            left:0;
            background:white;
            min-width:250px;
            padding:15px;
            display:none;
            box-shadow:0 8px 20px rgba(0,0,0,0.1);
            z-index:1000;
        }

        .top-menu li:hover > .dropdown {
            display:block;
        }

        .dropdown a {
            display:block;
            color:#333;
            padding:5px 0;
        }

        .dropdown a:hover {
            color:#ff3c00;
        }

        .container {
            padding:30px;
        }

        .product-grid {
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(250px,1fr));
            gap:20px;
        }

        .product-card {
            background:white;
            padding:15px;
            border-radius:6px;
            box-shadow:0 2px 6px rgba(0,0,0,0.1);
        }

        .product-card img {
            width:100%;
            height:200px;
            object-fit:contain;
        }

        .price {
            font-weight:bold;
            color:#ff3c00;
        }
    </style>
</head>
<body>

<nav class="top-menu">
    <ul>
        @foreach($menuCategories as $category)
            <li>
                <a href="{{ route('category', $category->slug) }}">
                    {{ $category->name }}
                </a>

                @if($category->children->count())
                    @include('front.partials.menu-dropdown', [
                        'categories' => $category->children
                    ])
                @endif
            </li>
        @endforeach
    </ul>
</nav>

<div class="container">

    @if($currentCategory)
        <div style="margin-bottom:20px;">
            @foreach($currentCategory->breadcrumb() as $crumb)
                <a href="{{ route('category', $crumb->slug) }}">
                    {{ $crumb->name }}
                </a>
                @if(!$loop->last) > @endif
            @endforeach
        </div>
    @endif

    <div class="product-grid">
        @foreach($products as $product)
            <div class="product-card">
                <img src="{{ $product->image }}" alt="{{ $product->name }}">
                <h4>{{ $product->name }}</h4>
                <div class="price">
                    {{ number_format($product->offers_min_price, 2, ',', ' ') }} €
                </div>
            </div>
        @endforeach
    </div>

    <div style="margin-top:20px; text-align:center;">
        {{ $products->withQueryString()->links() }}
    </div>

</div>

</body>
</html>

