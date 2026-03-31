<div id="cookie-banner" class="cookie-banner hidden">

    <div class="cookie-container">

        <div class="cookie-text">
            <strong>🍪 Gestion des cookies</strong>

            <p class="cookie-more">
                J'optimise votre trajectoire comme un vrai motard :
				nes cookies nme permette d’améliorer votre expérience et de vous proposer les meilleurs prix.
.
               <a href="{{ route('cookies') }}">En savoir plus</a> 
            </p>
        </div>

        <div class="cookie-actions">
            <button id="accept-cookies" class="cookie-btn accept">
                Accepter
            </button>

            <button id="refuse-cookies" class="cookie-btn refuse">
                Refuser
            </button>
        </div>

    </div>

</div>

<style>
/* =========================================================
   COOKIE BANNER
========================================================= */

.cookie-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background: #1F2933;
    color: #fff;
    padding: 20px;
    z-index: 9999;
    box-shadow: 0 -5px 20px rgba(0,0,0,0.2);
}

.cookie-container {
    max-width: 1200px;
    margin: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.cookie-text {
    max-width: 70%;
}

.cookie-text strong {
    display: block;
    margin-bottom: 5px;
    font-size: 16px;
}

.cookie-text p {
    margin: 5px 0;
    font-size: 14px;
    color: #D1D5DB;
}

.cookie-more a {
    color: #00AEEF;
    text-decoration: underline;
}

.cookie-actions {
    display: flex;
    gap: 10px;
}

/* Boutons */

.cookie-btn {
    padding: 10px 16px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 500;
    transition: 0.2s;
}

.cookie-btn.accept {
    background: #00AEEF;
    color: #fff;
}

.cookie-btn.accept:hover {
    background: #0095cc;
}

.cookie-btn.refuse {
    background: transparent;
    border: 1px solid #6B7280;
    color: #fff;
}

.cookie-btn.refuse:hover {
    background: #374151;
}

/* Responsive */

@media (max-width: 768px) {

    .cookie-container {
        flex-direction: column;
        align-items: flex-start;
    }

    .cookie-text {
        max-width: 100%;
    }

    .cookie-actions {
        width: 100%;
        justify-content: flex-end;
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const banner = document.getElementById('cookie-banner');

    if (!banner) return;

    const consent = localStorage.getItem('cookie_consent');

    // 👉 Si déjà accepté ou refusé → ne PAS afficher
    if (consent === 'accepted' || consent === 'refused') {
        banner.style.display = 'none';
        return;
    }

    // 👉 Sinon on affiche
    banner.classList.remove('hidden');

    document.getElementById('accept-cookies').onclick = function () {
        localStorage.setItem('cookie_consent', 'accepted');
        banner.style.display = 'none';
        loadTracking();
    };

    document.getElementById('refuse-cookies').onclick = function () {
        localStorage.setItem('cookie_consent', 'refused');
        banner.style.display = 'none';
    };

});
</script>