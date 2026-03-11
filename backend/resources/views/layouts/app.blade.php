<!DOCTYPE html>
<html lang="fr">
<head>
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
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <title>Komparo-Moto.fr</title>
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
<header class="topbar">
    <div class="topbar-container">
        <a href="{{ route('home') }}" class="logo">
            Komparo-<span>Moto.fr</span>
        </a>

        <form method="GET" action="{{ route('home') }}" class="search-form">
            <input 
                type="text" 
                name="q" 
                placeholder="Rechercher un produit..."
                value="{{ request('q') }}"
            >
            <button type="submit">Rechercher</button>
        </form>
    </div>
</header>

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
            <a href="{{ route('contact') }}">Contact</a>
            <a href="{{ route('cgu') }}">CGU</a>
        </div>
    </div>
</footer>
</body>
</html>




