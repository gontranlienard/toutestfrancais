const fs = require("fs");
const axios = require("axios");
const cheerio = require("cheerio");
const path = require("path");

(async () => {

    try {

        console.log("🌍 Récupération menu Dafy...");

        const response = await axios.post(
            "https://www.dafy-moto.com/ajax/load-full-menu",
            {},
            {
                headers: {
                    "User-Agent": "Mozilla/5.0",
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json",
                    "Origin": "https://www.dafy-moto.com",
                    "Referer": "https://www.dafy-moto.com/"
                }
            }
        );

        const rawHtml = response.data.htmlList[0].html;

        const $ = cheerio.load(rawHtml);

        const categories = [];

        $(".main-nav__level-1 > li").each((i, el) => {

            const level1 = {
                name: $(el).find("> .main-nav__title .main-nav__title-text").first().text().trim(),
                children: []
            };

            $(el).find("> .main-nav__submenu ul > li").each((j, sub) => {

                const level2Name = $(sub).find("> .main-nav__title .main-nav__title-text").first().text().trim();

                const level2 = {
                    name: level2Name,
                    children: []
                };

                $(sub).find(".main-nav__submenu-accordion .js-last-level").each((k, subsub) => {

                    const level3Name = $(subsub).find(".main-nav__title-text").text().trim();
                    const url = $(subsub).find("a").attr("href");

                    level2.children.push({
                        name: level3Name,
                        url: url
                    });

                });

                if (level2.name) {
                    level1.children.push(level2);
                }

            });

            if (level1.name) {
                categories.push(level1);
            }

        });

        const outputDir = path.join(__dirname, "../output");

        if (!fs.existsSync(outputDir)) {
            fs.mkdirSync(outputDir, { recursive: true });
        }

        fs.writeFileSync(
            path.join(outputDir, "dafy.json"),
            JSON.stringify(categories, null, 2)
        );

        console.log("✅ Menu structuré généré");

    } catch (error) {
        console.error("❌ ERREUR :", error.message);
    }

})();








