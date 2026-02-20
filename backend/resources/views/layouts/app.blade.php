<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Comparateur Moto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<header class="header">
    <div class="container header-inner">
        <div class="logo">
            <a href="/">Comparateur Moto</a>
        </div>

        <form class="search-form">
            <input type="text" placeholder="Rechercher un produit...">
        </form>
    </div>
</header>

<div class="layout">

    @include('partials.sidebar')

    <main class="main-content">
        @yield('content')
    </main>

</div>

</body>
</html>




