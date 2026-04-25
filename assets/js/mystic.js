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
    let isBusy = false;
    let hasAnswered = false;

    /* =========================
       ANALYZE
    ========================= */
    function analyze(text) {

        const lower = text.toLowerCase();

        let result = {
            type: 'neutral',
            intensity: 0,
            effect: 'neutral'
        };

        if (text.length < 10) result.intensity++;
        if (text.length > 80) result.intensity += 2;

        for (const [type, words] of Object.entries(keywords)) {
            if (words.some(w => lower.includes(w))) {
                result.type = type;
                result.intensity += 2;
                break;
            }
        }

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
       STATE
    ========================= */
    function setState(state) {

        if (!el.npc) return;

        el.npc.dataset.state = state;
        el.npc.classList.remove('shake', 'glow', 'doom', 'fade');

        const map = {
            loading: 'shake',
            blessing: 'glow',
            doom: 'doom',
            death: 'fade'
        };

        if (map[state]) el.npc.classList.add(map[state]);
    }

    /* =========================
       THINKING
    ========================= */
    function renderThinking() {

        if (!el.zone) return;

        el.zone.innerHTML = '';

        const bubble = document.createElement('div');
        bubble.className = 'mystic-answer mystic-thinking';
        bubble.textContent = '...';

        el.zone.appendChild(bubble);
    }

    /* =========================
       UPDATE ANSWER
    ========================= */
    function updateAnswer(text) {

        const bubble = el.zone.querySelector('.mystic-answer');
        if (!bubble) return;

        bubble.classList.remove('mystic-thinking');
        bubble.textContent = text;
    }

    /* =========================
       BUTTON TRANSFORM
    ========================= */
    function transformToExit() {

        if (!el.button) return;

        el.button.blur();
        el.button.textContent = 'Return to your world';
        el.button.classList.add('bc-btn-exit');

        el.button.replaceWith(el.button.cloneNode(true));
        const newBtn = document.getElementById('btnPlayInput');

        newBtn.addEventListener('click', () => {
            window.location.href = '/index.php';
        });
    }

    /* =========================
       VOICE PICKER (IMPROVED)
    ========================= */
    function pickBestVoice() {

        const voices = speechSynthesis.getVoices();
        if (!voices || !voices.length) return null;

        const preferred = [
            'Samantha',
            'Google UK English Female',
            'Google US English',
            'Microsoft Zira',
            'Victoria'
        ];

        for (const name of preferred) {
            const v = voices.find(x => x.name.includes(name));
            if (v) return v;
        }

        const fallback = voices.find(v =>
            v.lang?.startsWith('en') &&
            /female|zira|samantha|woman/i.test(v.name)
        );

        return fallback || voices.find(v => v.lang?.startsWith('en')) || voices[0];
    }

    /* =========================
       SPEAK
    ========================= */
    function speak(text, analysis) {

        if (!window.speechSynthesis) return Promise.resolve();

        return new Promise(resolve => {

            const utter = new SpeechSynthesisUtterance(text);

            // base human tone
            utter.rate = 0.75;
            utter.pitch = 1.05;
            utter.volume = 1;

            // mood shaping
            if (analysis.effect === 'doom') {
                utter.rate = 0.75;
                utter.pitch = 0.6;
            }

            if (analysis.effect === 'blessing') {
                utter.rate = 1.0;
                utter.pitch = 1.2;
            }

            if (analysis.effect === 'mystic') {
                utter.rate = 0.8;
                utter.pitch = 0.95;
            }

            const voice = pickBestVoice();
            if (voice) utter.voice = voice;

            utter.onstart = () => {
                el.npc.classList.add('talking');
            };

            utter.onend = () => {
                el.npc.classList.remove('talking');
                resolve();
            };

            utter.onerror = () => {
                el.npc.classList.remove('talking');
                resolve();
            };
            
            utter.onboundary = () => {
                el.npc.classList.add('talking-fast');
            };
            speechSynthesis.cancel();
            speechSynthesis.speak(utter);
        });
    }

    /* =========================
       MAIN FLOW
    ========================= */
    async function ask() {

        if (isBusy || hasAnswered) return;

        const question = el.input.value.trim();
        if (!question) return;

        isBusy = true;

        const analysis = analyze(question);

        setState('loading');
        renderThinking();

        const delay = 800 + (analysis.intensity * 300);

        try {

            await new Promise(r => setTimeout(r, delay));

            const res = await fetch(API_URL, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ question })
            });

            if (!res.ok) throw new Error(res.status);

            const data = await res.json();
            const answer = data.answer || 'The mist refuses to answer...';

            setState(analysis.effect);

            updateAnswer(answer);

            await new Promise(r => setTimeout(r, 300));

            await speak(answer, analysis);

            hasAnswered = true;

            el.input.disabled = true;
            el.input.placeholder = 'The oracle has spoken...';

            transformToExit();

        } catch (err) {

            console.error(err);

            setState('doom');
            updateAnswer('Lynne hisses… something went wrong.');

        } finally {
            isBusy = false;
        }
    }

    /* =========================
       INIT
    ========================= */
    function init() {

        console.log('MysticEngine init');

        el = {
            input: document.getElementById('mysticInput'),
            button: document.getElementById('btnPlayInput'),
            zone: document.getElementById('talkingHeadZone'),
            npc: document.getElementById('npc_lynne')
        };

        if (!el.button || !el.input) {
            console.error('MysticEngine missing DOM elements', el);
            return;
        }

        el.button.addEventListener('click', ask);

        // 🔥 FIX: character counter
        el.input.addEventListener('input', updateCounter);
        updateCounter();

        // voice preload safety
        if (window.speechSynthesis) {
            speechSynthesis.onvoiceschanged = () => {
                console.log('Voices loaded:', speechSynthesis.getVoices().length);
            };
        }
    }

    return { init };

})();

/* BOOT */
window.addEventListener('DOMContentLoaded', () => {
    window.MysticEngine.init();
});

/* COUNTER */
function updateCounter() {
    const i = document.getElementById('mysticInput');
    const c = document.getElementById('charCount');
    if (!i || !c) return;
    c.textContent = `${i.value.length} / 200`;
}