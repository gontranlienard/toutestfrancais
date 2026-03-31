console.log("app.js chargé");
// ===============================
// TRACK CLICK AFFILIE
// ===============================
window.addEventListener("beforeunload", function() {

    let timeSpent = Math.round((Date.now() - startTime) / 1000);

    fetch('/log-event', {
        method: 'POST',
        keepalive: true, // 🔥 important pour unload
        headers: {
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content') || '',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            type: 'time',
            url: window.location.href,
            referrer: document.referrer,
            value: timeSpent + "s"
        })
    });

});


// ===============================
// TEMPS PASSE SUR PAGE
// ===============================
let startTime = Date.now();

window.addEventListener("beforeunload", function() {

    let timeSpent = Math.round((Date.now() - startTime) / 1000);

    navigator.sendBeacon('/log-event', JSON.stringify({
        type: 'time',
        url: window.location.href,
        referrer: document.referrer,
        value: timeSpent + "s"
    }));

});

// debug clic simple
document.querySelectorAll('.favorite-star').forEach(star => {
    star.addEventListener('click', function () {
        console.log("clic étoile", this.dataset.variant);
    });
});

// wishlist
document.addEventListener("click", function(e){

    const star = e.target.closest(".favorite-star");
    if(!star) return;

    const variantId = star.dataset.variant;

    console.log("clic étoile", variantId);

    fetch('/wishlist/' + variantId, {

        method: 'POST',

        headers: {
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content'),

            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }

    })
    .then(response => {

        console.log("status HTTP :", response.status);

        return response.text();

    })
    .then(text => {

        console.log("réponse brute :", text);

        let data;

        try {
            data = JSON.parse(text);
        } catch(e) {
            console.error("Réponse non JSON :", text);
            return;
        }

        console.log("data :", data);

        if(data.status === "added"){
            star.classList.add("favorited");
        }

        if(data.status === "removed"){
            star.classList.remove("favorited");
        }

        if(data.error === "not_logged"){

            if(confirm("Vous devez être connecté pour ajouter un favori.\n\nVoulez-vous vous connecter ?")){
                window.location.href = "/login?redirect=" + encodeURIComponent(window.location.href);
            }
        }

    })
    .catch(error => {
        console.error("Erreur fetch :", error);
    });

});

// responsive
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('mobileMenuBtn');
    const menu = document.querySelector('.sidebar');

    if (!btn || !menu) return;

    btn.addEventListener('click', function () {
        menu.classList.toggle('open');
    });
});

function toggleCategories() {
    const menu = document.querySelector('.mobile-menu');
    if (!menu) return;
    menu.classList.toggle('open');
}

// tom-select (JS pur, sans balise <script>)
document.addEventListener("DOMContentLoaded", function(){

    if(typeof TomSelect === "undefined") return;

    const el = document.querySelector("#brand-select");
    if(!el) return;

    new TomSelect(el, {
        create: false,
        sortField: {
            field: "text",
            direction: "asc"
        }
    });

});
