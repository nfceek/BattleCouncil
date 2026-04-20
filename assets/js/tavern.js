const TavernEngine = {

    // -----------------------------
    // HEAD CONFIG (NOW WITH BG + VOICE)
    // -----------------------------
    headMap: {
        barWench: {
            src: "/images/tavern/heads/bar_wench-001.png",
            x: 50,
            y: 85,
            size: 400,
            type: "npc",
            bg: true,
            voice: "female",
            sceneBg: "/images/tavern/bg/bg_wench.jpg"
        },
        blueDragon: {
            src: "/images/tavern/heads/blueDragon.png",
            x: 50,
            y: 85,
            size: 400,
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
            sceneBg: "/images/tavern/bg/bg_ambient.png"
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

        console.log("Tavern Engine online");
    },

    // -----------------------------
    // VOICE SYSTEM
    // -----------------------------
    loadVoices() {
        this.voices = speechSynthesis.getVoices();

        if (!this.voices.length) {
            speechSynthesis.onvoiceschanged = () => {
                this.voices = speechSynthesis.getVoices();
                //console.log("Voices loaded:", this.voices);
            };
        }
    },

    getVoice(type = "neutral") {
        if (!this.voices.length) return null;

        const femaleHints = ["female", "zira", "aria", "jenny", "samantha"];
        const maleHints = ["male", "david", "mark"];

        if (type === "female") {
            return this.voices.find(v =>
                femaleHints.some(h => v.name.toLowerCase().includes(h))
            );
        }

        if (type === "male") {
            return this.voices.find(v =>
                maleHints.some(h => v.name.toLowerCase().includes(h))
            );
        }

        return this.voices[0];
    },

    // -----------------------------
    // LOOP SYSTEM
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
    // BACKGROUND CONTROL (NEW)
    // -----------------------------
    setSceneBackground(src) {
        const bg = document.querySelector(".tavern-bg");
        if (!bg || !src) return;

        if (bg.dataset.current === src) return;

        // fade out
        bg.style.opacity = 0;

        setTimeout(() => {
            bg.src = src;
            bg.dataset.current = src;
            bg.style.opacity = 1;
        }, 150);
    },

    // -----------------------------
    // NPC SETUP
    // -----------------------------
    bootNPCs() {
        this.npcs = document.querySelectorAll(".npc");

        this.npcs.forEach(npc => {
            const id = npc.dataset.npc;
            this.setNPCState(id, npc.dataset.state || "idle");
        });
    },

    setNPCState(id, state) {
        const el = document.querySelector(`[data-npc="${id}"]`);
        if (!el) return;
        el.dataset.state = state;
    },

    // -----------------------------
    // SPEAK SYSTEM
    // -----------------------------
    speak(text, headId = "barWench") {

        if (this.state.busy) {
            this.state.queue.push({ text, headId });
            return;
        }

        this.state.busy = true;

        const zone = document.getElementById("talkingHeadZone");

        if (!zone) {
            console.warn("talkingHeadZone missing");
            this.state.busy = false;
            return;
        }

        this.playTalkingHead(zone, text, headId);
    },

    // -----------------------------
    // RENDER (HEAD + BG)
    // -----------------------------
    playTalkingHead(zone, text, headId) {

        zone.innerHTML = "";

        const config = this.headMap[headId] || this.headMap.barWench;

        // 🔥 APPLY BACKGROUND
        this.setSceneBackground(config.sceneBg);

        // wrapper
        const wrapper = document.createElement("div");
        wrapper.className = "talking-actor";
        wrapper.style.position = "absolute";
        wrapper.style.left = config.x + "%";
        wrapper.style.top = config.y + "%";
        wrapper.style.transform = "translate(-50%, -100%)";

        // optional background plate
        if (config.bg) {
            const plate = document.createElement("div");
            plate.className = "actor-bg";
            wrapper.appendChild(plate);
        }

        // image
        const img = document.createElement("img");
        img.src = config.src;
        img.style.height = config.size + "px";

        wrapper.appendChild(img);

        // bubble
        const bubble = document.createElement("div");
        bubble.className = "talking-bubble";
        bubble.innerText = text;

        bubble.style.position = "absolute";
        bubble.style.left = "50%";
        bubble.style.top = "410px";
        bubble.style.transform = "translate(-50%, -100%)";

        wrapper.appendChild(bubble);
        zone.appendChild(wrapper);

        this.playAudio(text, config);

        setTimeout(() => {
            this.state.busy = false;
            this.next();
        }, 3000);
    },

    // -----------------------------
    // AUDIO
    // -----------------------------
    playAudio(text, config) {
        if (!("speechSynthesis" in window)) return;

        const utter = new SpeechSynthesisUtterance(text);

        const voice = this.getVoice(config.voice);
        if (voice) utter.voice = voice;

        if (config.voice === "female") {
            utter.rate = 0.80;
            utter.pitch = 1.05;
        } else {
            utter.rate = 0.95;
            utter.pitch = 1;
        }

        speechSynthesis.cancel();
        speechSynthesis.speak(utter);
    },

    next() {
        if (!this.state.queue.length) return;

        const nextItem = this.state.queue.shift();
        this.speak(nextItem.text, nextItem.headId);
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
        this.speak(lines[Math.floor(Math.random() * lines.length)], "barWench");
    },

    barWenchIdle() {
        const lines = [
            "Hmph...",
            "Another quiet night...",
            "You gonna order or just stand there?",
            "These mugs don’t clean themselves..."
        ];
        this.speak(lines[Math.floor(Math.random() * lines.length)], "barWench");
    },

    ambientNoise() {
        const lines = [
            "*murmuring voices*",
            "*a mug slams on wood*",
            "*laughter erupts briefly*"
        ];
        this.speak(lines[Math.floor(Math.random() * lines.length)], "ambient");
    },

    triggerRumor() {
        const rumorFeed = document.getElementById("rumorFeed");
        if (!rumorFeed) return;

        const rumors = [
            "A clan is amassing power in the east...",
            "Someone cracked a citadel last night.",
            "Rare creatures spotted beyond the ridge."
        ];

        rumorFeed.innerHTML = `<ul><li>${rumors[Math.floor(Math.random() * rumors.length)]}</li></ul>`;
    },

    // -----------------------------
    // STOP
    // -----------------------------
    stopAll() {

        if (this.state.loopInterval) {
            clearInterval(this.state.loopInterval);
            this.state.loopInterval = null;
        }

        this.state.queue = [];

        if ("speechSynthesis" in window) {
            speechSynthesis.cancel();
        }

        this.state.busy = false;

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

        if (input && counter) {
            input.addEventListener("input", () => {
                counter.textContent = `${input.value.length} / 200`;
            });
        }

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