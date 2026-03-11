<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $product->name }}</title>
    <style>
        body { font-family: Arial; padding:20px; }

        table { width:100%; border-collapse:collapse; }

        th,td {
            padding:10px;
            border-bottom:1px solid #ddd;
        }

        th { background:#f4f4f4; }

        img {
            width:120px;
            height:120px;
            object-fit:contain;
        }

        .btn {
            padding:6px 10px;
            background:#1976d2;
            color:white;
            text-decoration:none;
            border-radius:4px;
        }
    </style>
</head>
<body>

<h1>{{ $product->name }}</h1>

<table>
    <thead>
        <tr>
            <th>Image</th>
            <th>Site</th>
            <th>Couleur</th>
            <th>Prix</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($product->offers->sortBy('price') as $offer)
            <tr>
                <td>
                    @if($offer->image)
                        <img src="{{ $offer->image }}">
                    @endif
                </td>
                <td>{{ $offer->site->name }}</td>
                <td>{{ $offer->color ?? '-' }}</td>
                <td>{{ number_format($offer->price,2,',',' ') }} €</td>
                <td>
                    <a href="{{ $offer->url }}" target="_blank" class="btn">
                        Voir l'offre
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<br>
<a href="{{ route('home') }}">← Retour</a>

</body>
</html>
