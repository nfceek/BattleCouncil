window.MysticEngine = (() => {

    const API_URL = '/public/api/mystic.php';

    const keywords = {
        death: ['die', 'death', 'kill', 'dead', 'doom', 'end'],
        love: ['love', 'heart', 'kiss', 'romance'],
        war: ['war', 'fight', 'battle', 'enemy'],
        gold: ['gold', 'money', 'treasure', 'rich'],
        magic: ['magic', 'spell', 'curse', 'oracle']
    };

    let el = {};
    let isBusy = false; // prevent spam clicks

    /* =========================
       ANALYZE QUESTION
    ========================= */
    function analyze(text) {

        const lower = text.toLowerCase();

        let result = {
            type: 'neutral',
            intensity: 0,
            effect: 'neutral'
        };

        // length influence
        if (text.length < 10) result.intensity++;
        if (text.length > 80) result.intensity += 2;

        // keyword scan
        for (const [type, words] of Object.entries(keywords)) {
            if (words.some(word => lower.includes(word))) {
                result.type = type;
                result.intensity += 2;
                break; // stop at first strong match
            }
        }

        // effect mapping
        if (result.type === 'death' || result.type === 'war') {
            result.effect = 'doom';
        } else if (result.type === 'love') {
            result.effect = 'blessing';
        } else if (result.intensity >= 3) {
            result.effect = 'mystic';
        }

        return result;
    }

    /* =========================
       STATE HANDLING
    ========================= */
    function setState(state) {

        if (!el.npc) return;

        el.npc.dataset.state = state;

        el.npc.classList.remove('shake', 'glow', 'doom', 'fade');

        const stateMap = {
            loading: 'shake',
            blessing: 'glow',
            doom: 'doom',
            death: 'fade'
        };

        if (stateMap[state]) {
            el.npc.classList.add(stateMap[state]);
        }
    }

    /* =========================
       MAIN ASK FLOW
    ========================= */
    async function ask() {

        if (isBusy) return;

        const question = el.input.value.trim();
        if (!question) return;

        isBusy = true;

        const analysis = analyze(question);

        setState('loading');

        const delay = 800 + (analysis.intensity * 300);

        try {

            await new Promise(res => setTimeout(res, delay));

            const res = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ question })
            });

            if (!res.ok) {
                throw new Error(`API error: ${res.status}`);
            }

            const data = await res.json();

            const answer = data.answer || 'The mist refuses to answer...';

            setState(analysis.effect);
            renderAnswer(answer);

        } catch (err) {

            console.error('Mystic error:', err);

            setState('doom');
            renderAnswer('Lynne hisses… something went wrong.');

        } finally {
            isBusy = false;
        }
    }

    /* =========================
       RENDER OUTPUT
    ========================= */
    function renderAnswer(text) {

        if (!el.zone) return;

        el.zone.innerHTML = '';

        const bubble = document.createElement('div');
        bubble.className = 'mystic-answer';
        bubble.textContent = text;

        el.zone.appendChild(bubble);
    }

    /* =========================
       INIT
    ========================= */
    function init() {

        console.log('MysticEngine init');

        el = {
            input: document.getElementById('tavernInput'),
            button: document.getElementById('btnPlayInput'),
            zone: document.getElementById('talkingHeadZone'),
            npc: document.getElementById('npc_lynne')
        };

        if (!el.button || !el.input) {
            console.error('MysticEngine: missing DOM elements', el);
            return;
        }

        el.button.addEventListener('click', ask);
    }

    return { init };

})();

/* =========================
   BOOT
========================= */
window.addEventListener('DOMContentLoaded', () => {
    window.MysticEngine.init();
});