const mysql = require("mysql2/promise");

function extractBrand(name) {
    if (!name) return null;

    const ignored = [
        "gants",
        "casque",
        "blouson",
        "veste",
        "bottes",
        "pantalon"
    ];

    const words = name.split(" ");

    for (const word of words) {
        if (!ignored.includes(word.toLowerCase())) {
            return word;
        }
    }

    return null;
}

async function main() {

    const connection = await mysql.createConnection({
        host: "127.0.0.1",
        user: "gontran-admin",
        password: "Tifenn98*$",
        database: "toutestfrancais"
    });

    const [products] = await connection.execute(
        "SELECT id, name FROM products WHERE brand IS NULL OR brand = ''"
    );

    console.log(`🔎 ${products.length} produits sans marque`);

    for (const product of products) {

        const brand = extractBrand(product.name);

        if (!brand) continue;

        await connection.execute(
            "UPDATE products SET brand = ? WHERE id = ?",
            [brand, product.id]
        );

        console.log(`🏷 Marque ajoutée : ${brand} → ${product.name}`);
    }

    await connection.end();
    console.log("🎉 Correction terminée");
}

main().catch(err => {
    console.error("❌ Erreur :", err);
});
