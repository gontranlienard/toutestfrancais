const fs = require("fs");
const puppeteer = require("puppeteer");

(async () => {

    const browser = await puppeteer.launch({
        headless: true
    });

    const page = await browser.newPage();

    await page.goto("https://www.motoblouz.com/", {
        waitUntil: "networkidle2"
    });

    const categories = await page.evaluate(() => {

        function parseNode(node) {

            const link = node.querySelector(":scope > a");
            if (!link) return null;

            const category = {
                name: link.innerText.trim(),
                children: []
            };

            const subMenu = node.querySelector(":scope > ul");

            if (subMenu) {
                const items = subMenu.querySelectorAll(":scope > li");

                items.forEach(li => {
                    const child = parseNode(li);
                    if (child) category.children.push(child);
                });
            }

            return category;
        }

        const results = [];

        const rootItems = document.querySelectorAll("nav ul > li");

        rootItems.forEach(li => {
            const cat = parseNode(li);
            if (cat) results.push(cat);
        });

        return results;
    });

    fs.mkdirSync("scraper/output", { recursive: true });

    fs.writeFileSync(
        "scraper/output/motoblouz.json",
        JSON.stringify(categories, null, 2)
    );

    console.log("✅ Motoblouz exporté");

    await browser.close();
})();
