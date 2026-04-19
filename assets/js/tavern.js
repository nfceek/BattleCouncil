const TavernEngine = {

    state: {
        activeNPC: null,
        queue: [],
        mood: "neutral",
        busy: false,
        loopInterval: null
    },

    // -----------------------------
    // INIT
    // -----------------------------
    init() {
        this.bindEvents();
        this.bootNPCs();
        this.startLoop();

        console.log("Tavern Engine online");
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
        if (this.state.busy) return;
        this.randomEvent();
    },

    randomEvent() {
        const events = [
            () => this.barWenchIdle(),
            () => this.triggerRumor(),
            () => this.ambientNoise()
        ];

        const event = events[Math.floor(Math.random() * events.length)];
        event();
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
    speak(text, headId = null) {

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

playTalkingHead(zone, text, headId) {

    zone.innerHTML = "";

    const headMap = {
        barWench: {
            src: "/images/heads/bar_wench-001.png",
            x: 53,
            y: 87,
            size: 400
        },
        ambient: {
            src: "/images/heads/bar_wench-001.png",
            x: 53,
            y: 87,
            size: 400
        }
    };

    const config = headMap[headId] || {
        src: "/images/heads/default.png",
        x: 50,
        y: 80,
        size: 120
    };

    // -----------------------------
    // IMAGE (ONLY DECLARED ONCE)
    // -----------------------------
    const img = document.createElement("img");
    img.src = config.src;

    img.style.position = "absolute";
    img.style.left = config.x + "%";
    img.style.top = config.y + "%";
    img.style.height = config.size + "px";
    img.style.transform = "translate(-50%, -100%)";

    // -----------------------------
    // BUBBLE
    // -----------------------------
    const bubble = document.createElement("div");
    bubble.className = "talking-bubble";
    bubble.innerText = text;

    bubble.style.position = "absolute";
    bubble.style.left = config.x + "%";
    bubble.style.top = (config.y + 10) + "%";
    bubble.style.transform = "translate(-50%, -100%)";

    // -----------------------------
    // RENDER
    // -----------------------------
    zone.appendChild(img);
    zone.appendChild(bubble);

    // AUDIO
    this.playAudio(text, headId);

    setTimeout(() => {
        this.state.busy = false;
        this.next();
    }, 3000);
},

    playAudio(text, headId) {
        if (!("speechSynthesis" in window)) return;

        const utter = new SpeechSynthesisUtterance(text);
        utter.rate = 1;
        utter.pitch = 1;

        speechSynthesis.speak(utter);
    },

    next() {
        if (this.state.queue.length === 0) return;

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

        const line = lines[Math.floor(Math.random() * lines.length)];
        this.speak(line, "barWench");
    },

    barWenchIdle() {
        const lines = [
            "Hmph...",
            "Another quiet night...",
            "You gonna order or just stand there?",
            "These mugs don’t clean themselves..."
        ];

        const line = lines[Math.floor(Math.random() * lines.length)];
        this.speak(line, "barWench");
    },

    ambientNoise() {
        const lines = [
            "*murmuring voices*",
            "*a mug slams on wood*",
            "*laughter erupts briefly*"
        ];

        const line = lines[Math.floor(Math.random() * lines.length)];
        this.speak(line, "ambient");
    },

    triggerRumor() {
        const rumorFeed = document.getElementById("rumorFeed");
        if (!rumorFeed) return;

        const rumors = [
            "A clan is amassing power in the east...",
            "Someone cracked a citadel last night.",
            "Rare creatures spotted beyond the ridge."
        ];

        const rumor = rumors[Math.floor(Math.random() * rumors.length)];

        rumorFeed.innerHTML = `<ul><li>${rumor}</li></ul>`;
    },

    // STOP
    stopAll() {
        // stop loop
        if (this.state.loopInterval) {
            clearInterval(this.state.loopInterval);
            this.state.loopInterval = null;
        }

        // clear queue
        this.state.queue = [];

        // stop speech
        if ("speechSynthesis" in window) {
            speechSynthesis.cancel();
        }

        // reset state
        this.state.busy = false;

        // clear UI
        const zone = document.getElementById("talkingHeadZone");
        if (zone) zone.innerHTML = "";

        console.log("Tavern stopped");
    },

    // -----------------------------
    // EVENTS
    // -----------------------------
    bindEvents() {

        const bar = document.getElementById("barArea");

        if (bar) {
            bar.addEventListener("click", () => {
                this.barWenchReact();
            });
        }

        const speakBtn = document.getElementById("btnSpeak");

        if (speakBtn) {
            speakBtn.addEventListener("click", () => {
                this.speak("Speak your business, traveler...");
            });
        }

        const testBtn = document.getElementById("btnTestTavern");

        if (testBtn) {
            testBtn.addEventListener("click", () => {

                console.log("Tavern Engine Test Triggered");

                this.speak("The tavern comes alive...");
                this.barWenchReact();
            });
        }

    const stopBtn = document.getElementById("btnStopTavern");

    if (stopBtn) {
        stopBtn.addEventListener("click", () => {
            this.stopAll();
        });
    }

    }
};

// GLOBAL
window.TavernEngine = TavernEngine;

// BOOT
document.addEventListener("DOMContentLoaded", () => {
    TavernEngine.init();


});