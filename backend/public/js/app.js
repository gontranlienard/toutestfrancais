console.log("app.js chargé");
document.querySelectorAll('.favorite-star').forEach(star => {

    star.addEventListener('click', function () {

        console.log("clic étoile", this.dataset.variant);

    });

});
document.addEventListener("DOMContentLoaded", function(){

    document.querySelectorAll('.favorite-star').forEach(star => {

        star.addEventListener('click', function(){

            const variantId = this.dataset.variant;
            const starElement = this;

            fetch('/wishlist/' + variantId, {

                method: 'POST',

                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                    'Accept': 'application/json'
                }

            })
            .then(response => response.json())
            .then(data => {

                if(data.status === "added"){

                    // ajouté aux favoris → gris
                    starElement.classList.add("favorited");

                }

                if(data.status === "removed"){

                    // retiré des favoris → orange
                    starElement.classList.remove("favorited");

                }

            });

        });

    });

});