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

    renderCapital(kingdom) {
        const pin = this.el("capitalPin");
        if (!pin) return;

        const x = kingdom.capital_x ?? 50;
        const y = kingdom.capital_y ?? 50;

        pin.style.left = x + "%";
        pin.style.top  = y + "%";

        const img = pin.querySelector("img");
        if (!img) return;

        img.src = kingdom.icon
            ? `/images/capitals/${kingdom.icon}.png`
            : `/images/capitals/default.png`;
    },

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
                    <h3>${clan.clan_name}</h3>
                    <p><strong>ROE:</strong> ${clan.roe || ""}</p>
                    <p><strong>Members:</strong> ${clan.members || ""}</p>
                    <p><strong>Language:</strong> ${clan.language || ""}</p>
                `;
            });

            container.appendChild(pin);
        });
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