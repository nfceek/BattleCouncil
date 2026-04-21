const TavernEngine = {

    // -----------------------------
    // HEAD CONFIG (NPC + VOICE + SCENE BG)
    // -----------------------------
    headMap: {
        barWench: {
            src: "/images/tavern/heads/bar_wench-001.png",
            x: 50,
            y: 100,
            size: 400,
            type: "npc",
            bg: true,
            voice: "female",
            sceneBg: "/images/tavern/bg/bg_wench.jpg"
        },

        blueDragon: {
            src: "/images/tavern/heads/blueDragon.png",
            x: 50,
            y: 100,
            size: 450,
            type: "npc",
            bg: true,
            voice: "male",
            sceneBg: "/images/tavern/bg/bg_tan.jpg"
        },

        ambient: {
            src: "/images/tavern/heads/ambient.png",
            x: 50,
            y: 80,
            size: 300,
            type: "ambient",
            voice: "neutral",
            sceneBg: "/images/tavern/bg/bg_ambient.jpg"
        }
    },

    // -----------------------------
    // STATE
    // -----------------------------
    state: {
        queue: [],
        busy: false,
        loopInterval: null,
        autoEvents: false
    },

    voices: [],

    // -----------------------------
    // INIT
    // -----------------------------
    init() {
        this.loadVoices();
        this.bindEvents();
        this.bootNPCs();
        this.startLoop();

        // default preview
        this.renderPreview("barWench");

        console.log("Tavern Engine online");
    },

    // -----------------------------
    // VOICE SYSTEM (SAFE LOAD)
    // -----------------------------
    loadVoices() {
        const load = () => {
            this.voices = speechSynthesis.getVoices();
        };

        load();

        if ("speechSynthesis" in window) {
            speechSynthesis.onvoiceschanged = load;
        }
    },

    getVoice(type = "neutral") {
        if (!this.voices.length) return null;

        const femaleHints = ["female", "zira", "samantha", "aria", "jenny"];
        const maleHints = ["male", "david", "mark", "daniel"];

        if (type === "female") {
            return this.voices.find(v =>
                femaleHints.some(h => v.name.toLowerCase().includes(h))
            ) || this.voices[0];
        }

        if (type === "male") {
            return this.voices.find(v =>
                maleHints.some(h => v.name.toLowerCase().includes(h))
            ) || this.voices[0];
        }

        return this.voices[0];
    },

    // -----------------------------
    // LOOP
    // -----------------------------
    startLoop() {
        if (this.state.loopInterval) return;

        this.state.loopInterval = setInterval(() => {
            this.tick();
        }, 5000);
    },

    tick() {
        if (!this.state.autoEvents) return;
        if (this.state.busy) return;

        this.randomEvent();
    },

    randomEvent() {
        const events = [
            () => this.barWenchIdle(),
            () => this.triggerRumor(),
            () => this.ambientNoise()
        ];

        events[Math.floor(Math.random() * events.length)]();
    },

    // -----------------------------
    // BACKGROUND SWITCH (SMOOTH)
    // -----------------------------
    setSceneBackground(src) {
        const bg = document.querySelector(".tavern-bg");
        if (!bg || !src) return;

        if (bg.dataset.current === src) return;

        bg.style.opacity = 0;

        setTimeout(() => {
            bg.src = src;
            bg.dataset.current = src;
            bg.style.opacity = 1;
        }, 150);
    },

    // -----------------------------
    // PREVIEW ONLY (dropdown)
    // -----------------------------
    renderPreview(headId) {
        const cfg = this.headMap[headId];
        if (!cfg) return;

        this.setSceneBackground(cfg.sceneBg);

        const zone = document.getElementById("talkingHeadZone");
        if (!zone) return;

        zone.innerHTML = "";

        const img = document.createElement("img");
        img.src = cfg.src;
        img.style.position = "absolute";
        img.style.left = cfg.x + "%";
        img.style.top = cfg.y + "%";
        img.style.height = cfg.size + "px";
        img.style.transform = "translate(-50%, -100%)";

        zone.appendChild(img);
    },

    // -----------------------------
    // MAIN SPEAK
    // -----------------------------
    speak(text, headId = "barWench") {

        if (this.state.busy) {
            this.state.queue.push({ text, headId });
            return;
        }

        this.state.busy = true;

        const zone = document.getElementById("talkingHeadZone");
        if (!zone) return;

        this.playTalkingHead(zone, text, headId);
    },

    // -----------------------------
    // RENDER ACTOR
    // -----------------------------
    /*
    playTalkingHead(zone, text, headId) {

        zone.innerHTML = "";

        const config = this.headMap[headId] || this.headMap.barWench;

        this.setSceneBackground(config.sceneBg);

        const wrapper = document.createElement("div");
        wrapper.className = "talking-actor";

        wrapper.style.position = "absolute";
        wrapper.style.left = config.x + "%";
        wrapper.style.top = config.y + "%";
        wrapper.style.transform = "translate(-50%, -100%)";

        if (config.bg) {
            const plate = document.createElement("div");
            plate.className = "actor-bg";
            wrapper.appendChild(plate);
        }

        const img = document.createElement("img");
        img.src = config.src;
        img.style.height = config.size + "px";

        wrapper.appendChild(img);

        const bubble = document.createElement("div");
        bubble.className = "talking-bubble";
        bubble.innerText = text;

        wrapper.appendChild(bubble);
        zone.appendChild(wrapper);

        this.playAudio(text, config);

        setTimeout(() => {
            this.state.busy = false;
            this.next();
        }, 3200);
    },
*/
    playTalkingHead(zone, text, headId) {

        zone.innerHTML = "";

        const config = this.headMap[headId] || this.headMap.barWench;

        // background swap
        this.setSceneBackground(config.sceneBg);

        const wrapper = document.createElement("div");
        wrapper.className = "talking-actor";

        wrapper.style.position = "absolute";
        wrapper.style.left = config.x + "%";
        wrapper.style.top = config.y + "%";
        wrapper.style.transform = "translate(-50%, -100%)";

        // optional glow plate
        if (config.bg) {
            const plate = document.createElement("div");
            plate.className = "actor-bg";
            wrapper.appendChild(plate);
        }

        // head image
        const img = document.createElement("img");
        img.src = config.src;
        img.style.height = config.size + "px";

        wrapper.appendChild(img);

        // -----------------------------
        // 🔥 FIX: speech bubble restored properly
        // -----------------------------
        const bubble = document.createElement("div");
        bubble.className = "talking-bubble";
        bubble.innerText = text;

        wrapper.appendChild(bubble);

        zone.appendChild(wrapper);

        // audio
        this.playAudio(text, config);

        setTimeout(() => {
            this.state.busy = false;
            this.next();
        }, 3200);
    },
    // -----------------------------
    // AUDIO (VOICE CONTROL)
    // -----------------------------
    playAudio(text, config) {
        if (!("speechSynthesis" in window)) return;

        const utter = new SpeechSynthesisUtterance(text);

        const voice = this.getVoice(config.voice);
        if (voice) utter.voice = voice;

        if (config.voice === "female") {
            utter.rate = 0.78;
            utter.pitch = 1.05;
        } else if (config.voice === "male") {
            utter.rate = 0.9;
            utter.pitch = 0.95;
        } else {
            utter.rate = 0.92;
            utter.pitch = 1;
        }

        speechSynthesis.cancel();
        speechSynthesis.speak(utter);
    },

    next() {
        if (!this.state.queue.length) return;

        const next = this.state.queue.shift();
        this.speak(next.text, next.headId);
    },

    // -----------------------------
    // NPC BEHAVIOR
    // -----------------------------
    barWenchReact() {
        const lines = [
            "Stop staring and order.",
            "You look broke.",
            "Another traveler... sigh."
        ];

        this.speak(this.pick(lines), "barWench");
    },

    barWenchIdle() {
        const lines = [
            "Hmph...",
            "Another quiet night...",
            "You gonna order or just stand there?",
            "These mugs don’t clean themselves..."
        ];

        this.speak(this.pick(lines), "barWench");
    },

    ambientNoise() {
        const lines = [
            "*murmuring voices*",
            "*a mug slams on wood*",
            "*laughter erupts briefly*"
        ];

        this.speak(this.pick(lines), "ambient");
    },

    triggerRumor() {
        const rumorFeed = document.getElementById("rumorFeed");
        if (!rumorFeed) return;

        const rumors = [
            "A clan is amassing power in the east...",
            "Someone cracked a citadel last night.",
            "Rare creatures spotted beyond the ridge."
        ];

        rumorFeed.innerHTML = `<ul><li>${this.pick(rumors)}</li></ul>`;
    },

    pick(arr) {
        return arr[Math.floor(Math.random() * arr.length)];
    },

    // -----------------------------
    // STOP SYSTEM
    // -----------------------------
    stopAll() {

        if (this.state.loopInterval) {
            clearInterval(this.state.loopInterval);
            this.state.loopInterval = null;
        }

        this.state.queue = [];
        this.state.busy = false;

        if ("speechSynthesis" in window) {
            speechSynthesis.cancel();
        }

        const zone = document.getElementById("talkingHeadZone");
        if (zone) zone.innerHTML = "";

        console.log("Tavern stopped");
    },

    // -----------------------------
    // EVENTS
    // -----------------------------
    bindEvents() {

        const input = document.getElementById("tavernInput");
        const counter = document.getElementById("charCount");
        const playBtn = document.getElementById("btnPlayInput");
        const npcSelect = document.getElementById("npcSelect");

        // dropdown preview change
        if (npcSelect) {
            npcSelect.addEventListener("change", (e) => {
                this.renderPreview(e.target.value);
            });
        }

        // input counter
        if (input && counter) {
            input.addEventListener("input", () => {
                counter.textContent = `${input.value.length} / 200`;
            });
        }

        // speak input
        if (playBtn && input) {
            playBtn.addEventListener("click", () => {

                const text = input.value.trim();
                if (!text) return;

                const npc = npcSelect?.value || "barWench";

                this.speak(text, npc);

                input.value = "";
                counter.textContent = "0 / 200";
            });
        }

        document.getElementById("barArea")?.addEventListener("click", () => {
            this.barWenchReact();
        });

        document.getElementById("btnSpeak")?.addEventListener("click", () => {
            this.speak("Speak your business, traveler...");
        });

        document.getElementById("btnTestTavern")?.addEventListener("click", () => {
            this.speak("The tavern comes alive...");
            this.barWenchReact();
        });

        document.getElementById("btnStopTavern")?.addEventListener("click", () => {
            this.stopAll();
        });
    }
};

// GLOBAL
window.TavernEngine = TavernEngine;

// BOOT
document.addEventListener("DOMContentLoaded", () => {
    TavernEngine.init();
});