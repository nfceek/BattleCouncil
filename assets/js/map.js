/* =========================
   BATTLECOUNCIL MAP ENGINE (HYBRID)
========================= */

const MapEngine = {

    mode: null,
    kingdomData: [],
    currentKingdom: null,

    /* =========================
       DOM CACHE (SAFE ACCESS LAYER)
    ========================== */

    dom: {},

    bindDOM() {
        this.dom = {
            map: document.getElementById("map"),
            mapBg: document.getElementById("mapBg"),
            capitalPin: document.getElementById("capitalPin"),
            mapPins: document.getElementById("mapPins"),
            mapInfo: document.getElementById("mapInfo"),
            backBtn: document.getElementById("backToWorldBtn")
        };
    },

    el(key) {
        return this.dom[key] || document.getElementById(key);
    },

    /* =========================
       INIT
    ========================== */

    init(mode) {
        this.mode = mode;
        this.bindDOM();

        if (mode === "hybrid") {
            this.loadWorld();
            this.bindKingdomUI();

            if (typeof PRELOAD_KINGDOM !== "undefined" && PRELOAD_KINGDOM) {
                this.loadKingdom(PRELOAD_KINGDOM);
            }
        }
    },

    /* =========================
       API LAYER
    ========================== */

    api(url) {
        return fetch(url)
            .then(async res => {
                const text = await res.text();

                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error("API RAW RESPONSE:", text);
                    throw e;
                }
            })
            .catch(err => {
                console.error("API error:", err);
                return {};
            });
    },

    loadMapData(params = "") {
        return this.api(`${BASE_URL}/public/api/map.php${params}`);
    },

    /* =========================
       WORLD MODE
    ========================== */

    loadWorld() {

        if (this.kingdomData.length) {
            this.switchToWorldUI();
            this.renderWorld(this.kingdomData);
            return;
        }

        this.loadMapData()
            .then(data => {

                if (data.mode !== 'world') {
                    console.warn("Unexpected mode:", data);
                    return;
                }

                this.kingdomData = data.kingdoms || [];
                window.kingdomData = this.kingdomData;

                this.switchToWorldUI();
                this.renderWorld(this.kingdomData);
            })
            .catch(err => console.error("World map error:", err));
    },

    renderWorld(kingdoms) {
        const map = this.el("map");
        if (!map) return;

        map.innerHTML = "";

        kingdoms.forEach(k => this.addWorldPin(map, k));
    },

    addWorldPin(map, k) {
        const el = document.createElement("div");

        el.className = "kingdom-pin";

        el.style.position = "absolute";
        el.style.width = "40px";
        el.style.height = "40px";
        el.style.transform = "translate(-50%, -50%)";
        el.style.backgroundSize = "contain";
        el.style.backgroundRepeat = "no-repeat";

        el.style.left = k.capital_x + "%";
        el.style.top  = k.capital_y + "%";

        const icon = k.capital_bldg || "default";
        el.style.backgroundImage = `url(/images/capitals/${icon}.png)`;

        el.title = `Kingdom ${k.kingdomID}`;

        el.addEventListener("click", () => {
            this.loadKingdom(k.kingdomID);
        });

        map.appendChild(el);
    },

    /* =========================
       KINGDOM MODE
    ========================== */

    loadKingdom(id) {
        if (!id) return;

        this.loadMapData(`?k=${id}`)
            .then(data => {

                if (data.mode !== 'kingdom') {
                    console.warn("Unexpected mode:", data);
                    return;
                }

                this.currentKingdom = data.kingdom;

                this.switchToKingdomUI();
                this.renderKingdom(data);
            })
            .catch(err => console.error("Kingdom load error:", err));
    },

    renderKingdom(data) {
        this.setMapImage();
        this.renderCapital(data.kingdom);
        this.renderPins(data.clans || []);
    },

    setMapImage() {
        const el = this.el("mapBg");
        if (!el) return;

        el.src = "/images/maps/kingdom.png";
    },

    normalizeToPercent(x, y) {
        const minX = 5;
        const maxX = 960;
        const minY = 40;
        const maxY = 960;

        const clamp = (val, min = 0, max = 100) =>
            Math.max(min, Math.min(max, val));

        let percentX = ((x - minX) / (maxX - minX)) * 100;
        let percentY = ((y - minY) / (maxY - minY)) * 100;

        return {
            x: clamp(percentX),
            y: clamp(percentY)
        };
    },

    renderCapital(kingdom) {
        const pin = this.el("capitalPin");
        if (!pin || !kingdom) return;

        // fallback BEFORE normalization
        const rawX = kingdom.capital_x ?? 5;
        const rawY = kingdom.capital_y ?? 40;

        const pos = this.normalizeToPercent(rawX, rawY);

        pin.style.left = pos.x + "%";
        pin.style.top  = pos.y + "%";

        const img = pin.querySelector("img");
        if (!img) return;

        img.src = kingdom.icon
            ? `/images/capitals/${kingdom.icon}.png`
            : `/images/capitals/default.png`;
    },
/*
    renderPins(clans) {
        const container = this.el("mapPins");
        const infoBox = this.el("mapInfo");

        if (!container) return;

        container.innerHTML = "";

        clans.forEach(clan => {

            const pin = document.createElement("div");

            pin.className = "pin";
            pin.style.left = clan.x + "%";
            pin.style.top  = clan.y + "%";

            pin.innerHTML = `<img src="/images/icons/pin.png">`;

            pin.addEventListener("mouseenter", () => {
                if (!infoBox) return;

                infoBox.innerHTML = `
                    <div class="pin-container">
                        <div class="pin-header">
                            <h3>${clan.name}</h3>
                        </div>
                        <div class="pin-datapoints">
                            <div class="pin-datapoints-body">
                                <p><strong>Abbr:</strong> ${clan.shortname || ""}</p>
                                <p><strong>Leader:</strong> ${clan.leader || ""}</p>
                                <p><strong>Language:</strong> ${clan.language || ""}</p>
                                <p><strong>ROE:</strong> ${clan.roe || ""}</p>
                                <br />
                            </div>
                            <div class="pin-datapoints-location">
                                <p><strong>Location:</strong> k: ${clan.kingdom || ""} x: ${clan.x || ""} y: ${clan.y || ""}</p>
                            </div>
                            <br />
                            <br />
                        </div>
                    </div>
                `;
            });
            container.appendChild(pin);
        });
    },
*/
renderPins(clans) {
    const container = this.el("mapPins");
    const infoBox = this.el("mapInfo");

    if (!container) return;

    container.innerHTML = "";

    const fragment = document.createDocumentFragment();

    clans.forEach(clan => {
        // normalize input types (handles "0", null, undefined)
        const rawX = Number(clan.x);
        const rawY = Number(clan.y);

        const hasValidCoords =
            Number.isFinite(rawX) &&
            Number.isFinite(rawY) &&
            rawX !== 0 &&
            rawY !== 0;

        // skip pins with no usable coordinates
        if (!hasValidCoords) return;

        const pos = this.normalizeToPercent(rawX, rawY);

        const pin = document.createElement("div");
        pin.className = "pin";

        pin.style.left = pos.x + "%";
        pin.style.top  = pos.y + "%";

        pin.innerHTML = `<img src="/images/icons/pin.png" alt="">`;

        // hover info
        if (infoBox) {
            pin.addEventListener("mouseenter", () => {
                infoBox.innerHTML = `
                    <div class="pin-container">
                        <div class="pin-header">
                            <h3>${clan.name || ""}</h3>
                        </div>
                        <div class="pin-datapoints">
                            <p><strong>Abbr:</strong> ${clan.shortname || ""}</p>
                            <p><strong>Leader:</strong> ${clan.leader || ""}</p>
                            <p><strong>Language:</strong> ${clan.language || ""}</p>
                            <p><strong>ROE:</strong> ${clan.roe || ""}</p>
                            <p><strong>Location:</strong> k: ${clan.kingdom || ""}x: ${rawX || ""} y: ${rawY || ""}</p>
                        </div>
                    </div>
                `;
            });
        }

        fragment.appendChild(pin);
    });

    container.appendChild(fragment);
},

    /* =========================
       UI SWITCHING (SAFE)
    ========================== */

    switchToWorldUI() {

        const { map, mapBg, capitalPin, mapPins, backBtn } = this.dom;

        if (map) map.style.display = "block";
        if (mapBg) mapBg.style.display = "none";
        if (capitalPin) capitalPin.style.display = "none";
        if (backBtn) backBtn.style.display = "none";
        if (mapPins) mapPins.innerHTML = "";
    },

    switchToKingdomUI() {

        const { map, mapBg, capitalPin, mapPins, backBtn } = this.dom;

        if (map) map.style.display = "none";
        if (mapBg) mapBg.style.display = "block";
        if (capitalPin) capitalPin.style.display = "block";
        if (backBtn) backBtn.style.display = "inline-block";
        if (mapPins) mapPins.innerHTML = "";
    },

    /* =========================
       UI CONTROLS
    ========================== */

    bindKingdomUI() {

        const btn = document.getElementById("loadKingdomBtn");
        const select = document.getElementById("kingdomSelect");
        const input = document.getElementById("kingdomQuickInput");

        btn?.addEventListener("click", () => {
            const id = select.value || input.value;
            this.loadKingdom(id);
        });

        this.bindBackButton();
    },

    bindBackButton() {

        const btn = this.el("backToWorldBtn");

        btn?.addEventListener("click", () => {
            this.loadWorld();
        });
    }
};



/* =========================
   AUTO INIT
========================= */

document.addEventListener("DOMContentLoaded", () => {
    if (typeof MAP_MODE !== "undefined") {
        MapEngine.init(MAP_MODE);
    }
});