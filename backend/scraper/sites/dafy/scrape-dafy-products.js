const mysql = require("mysql2/promise");
const puppeteer = require("puppeteer-extra");
const StealthPlugin = require("puppeteer-extra-plugin-stealth");
const fs = require("fs");
const config = require("./config");

puppeteer.use(StealthPlugin());

const SITE_ID = 1;

async function getCategories(connection) {
    const [rows] = await connection.execute(
        "SELECT id, name, url FROM site_categories WHERE site_id = ? AND url IS NOT NULL",
        [SITE_ID]
    );
    return rows;
}

async function scrapeCategory(page, category) {

    console.log("🔎 Scraping:", category.name);
    console.log("➡ URL:", category.url);

    await page.goto(category.url, {
        waitUntil: "networkidle2",
        timeout: 60000
    });

    try {
        await page.waitForSelector(".product-card__wrapper", {
            timeout: 20000
        });
    } catch {
        console.log("⛔ Aucun produit détecté");
        return [];
    }

    const items = await page.evaluate(() => {

        const results = [];

        document.querySelectorAll(".product-card__wrapper").forEach(item => {

            const nameEl = item.querySelector(".product-card__name");
            const priceEl = item.querySelector(".product-card__price.price");
            const imgEl = item.querySelector(".product-card__img");

            const name = nameEl ? nameEl.innerText.trim() : null;
            const url = nameEl ? nameEl.href : null;
            const image = imgEl ? imgEl.src : null;
            const priceText = priceEl ? priceEl.innerText : null;

            let price = null;

            if (priceText) {
                const cleaned = priceText
                    .replace(/\s/g, "")
                    .replace(/[^\d,]/g, "")
                    .replace(",", ".");
                price = parseFloat(cleaned);
            }

            if (name && price) {
                results.push({
                    name,
                    price,
                    url,
                    image
                });
            }
        });

        return results;
    });

    console.log("➡ Produits trouvés:", items.length);

    return items.map(p => ({
        ...p,
        site_category_id: category.id
    }));
}

(async () => {

    const connection = await mysql.createConnection(config.db);
    const categories = await getCategories(connection);

    const browser = await puppeteer.launch({
        headless: "new",
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1366, height: 768 });

    let allProducts = [];

    for (const category of categories) {
        try {
            const products = await scrapeCategory(page, category);
            allProducts = allProducts.concat(products);
        } catch (e) {
            console.log("⚠ Erreur sur", category.name);
        }
    }

    fs.writeFileSync(
        "scraper/output/dafy-products.json",
        JSON.stringify(allProducts, null, 2)
    );

    console.log("✅ Produits exportés :", allProducts.length);

    await browser.close();
    await connection.end();

})();


