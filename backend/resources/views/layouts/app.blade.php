<!DOCTYPE html>
<html lang="fr">
<head>
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-SNBLM4E094"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-SNBLM4E094');
</script>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PPMJWMX8');</script>
<!-- End Google Tag Manager -->
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
	
    <title>Komparons-Moto.fr - Comparateur ecommerce pour les motos et les motards</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PPMJWMX8"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<script>
document.addEventListener('DOMContentLoaded', function() {

    const isTouchDevice = window.matchMedia("(hover: none)").matches;

    document.querySelectorAll('.toggle-btn').forEach(button => {

        const currentLi = button.closest('li');
        const submenu = currentLi.querySelector(':scope > .subcategory-list');

        if (!submenu) return;

        // ==============================
        // 📱 MOBILE → comportement actuel
        // ==============================
        if (isTouchDevice) {

            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Fermer les autres
                document.querySelectorAll('.subcategory-list.open').forEach(menu => {
                    if (!currentLi.contains(menu)) {
                        menu.classList.remove('open');
                    }
                });

                document.querySelectorAll('.toggle-btn.rotate').forEach(btn => {
                    if (btn !== button) {
                        btn.classList.remove('rotate');
                    }
                });

                submenu.classList.toggle('open');
                button.classList.toggle('rotate');
            });

        } else {

            // ==============================
            // 🖥 DESKTOP → hover propre
            // ==============================

            currentLi.addEventListener('mouseenter', function() {
                submenu.classList.add('open');
                button.classList.add('rotate');
            });

            currentLi.addEventListener('mouseleave', function() {
                submenu.classList.remove('open');
                button.classList.remove('rotate');
            });

        }

    });

});
</script>
<header>
	<div class="topbar">
    <div class="topbar-container">

        <button id="mobileMenuBtn" class="mobile-categories-btn" onclick="toggleCategories()">
            ☰
        </button>

        <a href="{{ route('home') }}" class="logo">
            Komparons-<span>Moto.fr</span>
        </a>

        <div class="header-icons-mobile">
            

            @if(auth()->check())
                <a href="{{ route('account.dashboard') }}" class="mobile-account-btn">👤</a>
            @else
                <a href="{{ route('login') }}" class="mobile-account-btn">👤</a>
            @endif
        </div>

        <div class="search-area">

    <form method="GET" action="{{ route('search') }}" class="search-form">

        <div class="header-account">
            @if(auth()->check())
                <a href="{{ route('account.dashboard') }}" class="account-link">
                    {{ auth()->user()->name }}
                </a>
            @else
                <a href="{{ route('login') }}" class="account-link">
                    Connexion / Créer un compte
                </a>
            @endif
        </div>

        <input type="text"
               name="q"
               placeholder="Rechercher un produit..."
               value="{{ request('q') }}">

        <button type="submit">Rechercher</button>

    </form>
</div>
</div>
</div>
<div class="menu-haut">
<div class="menu-haut-container">
    <select class="brand-select" onchange="if(this.value) window.location.href=this.value;">
                <option value="">Choisir une marque</option>

                @foreach($brands as $brand)
                    <option value="{{ route('brand.show', $brand->slug) }}">
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
<a href="{{ route('brand.index') }}" class="all-brands-link">
        Toutes les marques
    </a>
</div>
</div>	
</header>
				
				@include('components.cookie-banner')
				<div class="layout">
					<aside class="sidebar">
						@include('components.sidebar')
					</aside>

					<main class="content">
					
						@yield('content')
					</main>
				</div>
				<footer class="site-footer">
					<div class="footer-container">
						<div class="footer-left">
							© {{ date('Y') }} Comparateur Moto
						</div>

						<div class="footer-links">
							<button onclick="resetCookies()">Gérer mes cookies</button>
							<script>
							function resetCookies() {
								localStorage.removeItem('cookie_consent');
								location.reload();
							}
							</script>
							<a href="{{ route('mentions-legales') }}">Mentions légales</a>
							<a href="{{ route('contact') }}">Contact</a>
							<a href="{{ route('cgu') }}">CGU</a>
						</div>
					</div>
</footer>
<script src="/js/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
</body>
</html>




