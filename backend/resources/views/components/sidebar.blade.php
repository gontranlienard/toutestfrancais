<aside class="sidebar">

    <h4 class="sidebar-title">Catégories</h4>

    @if(isset($categories) && $categories->count())
        <ul class="category-list">
            @foreach($categories as $category)
                @include('components.category-item', ['category' => $category])
            @endforeach
        </ul>
    @else
        <p class="no-category">Aucune catégorie</p>
    @endif

</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {

    console.log("SIDEBAR INIT OK");

    const isTouchDevice = window.matchMedia("(hover: none)").matches;

    const items = document.querySelectorAll('.category-item');

    items.forEach(function (item) {

        const button = item.querySelector('.toggle-btn');
        const submenu = item.querySelector('.subcategory-list');

        if (!submenu) return;

        /* ===============================
           📱 MOBILE → CLIC
        =============================== */
        if (isTouchDevice) {

            if (button) {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    submenu.classList.toggle('open');
                    button.classList.toggle('rotate');
                });
            }

        } 
        /* ===============================
           🖥 DESKTOP → HOVER
        =============================== */
        else {

            item.addEventListener('mouseenter', function () {
                submenu.classList.add('open');
                if (button) button.classList.add('rotate');
            });

            item.addEventListener('mouseleave', function () {
                submenu.classList.remove('open');
                if (button) button.classList.remove('rotate');
            });

        }

    });

    /* =====================================
       🔥 OUVERTURE AUTO DE LA BRANCHE ACTIVE
    ===================================== */
    const activeLink = document.querySelector('.category-link.active');

    if (activeLink) {

        let parent = activeLink.closest('.category-item');

        while (parent) {

            const submenu = parent.querySelector('.subcategory-list');
            const toggle = parent.querySelector('.toggle-btn');

            if (submenu) submenu.classList.add('open');
            if (toggle) toggle.classList.add('rotate');

            parent = parent.parentElement.closest('.category-item');
        }

    }

});
</script>