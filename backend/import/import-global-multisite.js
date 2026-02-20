const fs = require("fs");
const path = require("path");
const mysql = require("mysql2/promise");
const slugify = require("slugify");

// 📂 Chemins (on est dans scraper/import/)
const DAFY_FILE = path.join(__dirname, "../output/dafy-products-clean.json");
const SPEEDWAY_FILE = path.join(__dirname, "../output/speedway-products-full.json");

// 🔹 Normalisation nom produit
function normalizeName(name) {
    if (!name) return null;

    return name
        .toLowerCase()
        .replace(/noir|blanc|rouge|bleu|gris/g, "")
        .replace(/\s+/g, " ")
        .trim();
}

// 🔹 Sécurisation anti-undefined
function safe(value) {
    if (value === undefined || value === "") return null;
    return value;
}

async function main() {

    const connection = await mysql.createConnection({
        host: "127.0.0.1",
        user: "gontran-admin",
        password: "Tifenn98*$",
        database: "toutestfrancais"
    });

    if (!fs.existsSync(DAFY_FILE) || !fs.existsSync(SPEEDWAY_FILE)) {
        console.log("❌ Fichier JSON introuvable");
        process.exit();
    }

    const dafy = JSON.parse(fs.readFileSync(DAFY_FILE));
    const speedway = JSON.parse(fs.readFileSync(SPEEDWAY_FILE));

    const allOffers = [...dafy, ...speedway];

    console.log(`📦 ${allOffers.length} offres à importer`);

    for (const offer of allOffers) {

        if (!offer || !offer.name || !offer.site) {
            console.log("⚠️ Offre invalide ignorée :", offer);
            continue;
        }

        const normalized = normalizeName(offer.name);
        if (!normalized) continue;

        const slug = slugify(normalized, { lower: true });

        // =========================
        // 🔎 PRODUIT
        // =========================
        const [productRows] = await connection.execute(
            "SELECT id FROM products WHERE slug = ? LIMIT 1",
            [slug]
        );

        let productId;

        if (productRows.length) {
            productId = productRows[0].id;
        } else {
            const [insertProduct] = await connection.execute(
                "INSERT INTO products (name, slug, image, created_at) VALUES (?, ?, ?, NOW())",
                [
                    safe(offer.name),
                    slug,
                    safe(offer.image)
                ]
            );
            productId = insertProduct.insertId;
            console.log(`🆕 Produit créé : ${offer.name}`);
        }

        // =========================
        // 🔎 SITE
        // =========================
        const [siteRows] = await connection.execute(
            "SELECT id FROM sites WHERE slug = ? LIMIT 1",
            [offer.site]
        );

        if (!siteRows.length) {
            console.log(`❌ Site inconnu : ${offer.site}`);
            continue;
        }

        const siteId = siteRows[0].id;

        // =========================
        // 🔎 OFFRE
        // =========================
        const [offerRows] = await connection.execute(
            "SELECT id, price FROM offers WHERE product_id = ? AND site_id = ? LIMIT 1",
            [productId, siteId]
        );

        const price = safe(parseFloat(offer.price));
        const url = safe(offer.url);

        if (offerRows.length) {

            const existingOffer = offerRows[0];

            if (parseFloat(existingOffer.price) !== price) {

                await connection.execute(
                    "UPDATE offers SET price = ?, updated_at = NOW() WHERE id = ?",
                    [price, existingOffer.id]
                );

                await connection.execute(
                    "INSERT INTO price_histories (offer_id, price, recorded_at, created_at) VALUES (?, ?, NOW(), NOW())",
                    [existingOffer.id, price]
                );

                console.log(`🔄 Prix mis à jour : ${offer.name}`);
            }

        } else {

            const [insertOffer] = await connection.execute(
                `INSERT INTO offers
                (product_id, site_id, price, url, created_at)
                VALUES (?, ?, ?, ?, NOW())`,
                [
                    productId,
                    siteId,
                    price,
                    url
                ]
            );

            await connection.execute(
                "INSERT INTO price_histories (offer_id, price, recorded_at, created_at) VALUES (?, ?, NOW(), NOW())",
                [insertOffer.insertId, price]
            );

            console.log(`✅ Nouvelle offre : ${offer.name}`);
        }
    }

    await connection.end();
    console.log("🎉 Import multi-site terminé avec succès");
}

main().catch(err => {
    console.error("❌ Erreur globale :", err);
});



