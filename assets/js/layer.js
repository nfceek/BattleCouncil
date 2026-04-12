window.BattleState = {
    state: {
        difficulty: 'Rare',
        bonusStr: 100,
        bonusHlh: 100,
        squadID: null,
        playerLevel: 6,
        troops: {}
    },

    set(key, value) {
        this.state[key] = value;
        console.log('STATE UPDATE:', key, value);
    },

    get(key) {
        return this.state[key];
    },

    snapshot() {
        return structuredClone
            ? structuredClone(this.state)
            : JSON.parse(JSON.stringify(this.state));
    }
};

document.addEventListener('DOMContentLoaded', () => {
    console.log('layer.js INIT');

    if (!window.LayerEngine) {
        console.error('LayerEngine NOT loaded');
        return;
    }

    /* ======================
       DIFFICULTY SYNC → STATE
    ====================== */
    document.querySelectorAll('input[name="difficulty"]').forEach(radio => {
        radio.addEventListener('change', async (e) => {

            const value = e.target.value || 'Rare';

            BattleState.set('difficulty', value);

            console.log('DIFFICULTY CHANGED 1:', value);

            // 🔥 THIS WAS MISSING → RELOAD DATA
            await refreshLayerData();
        });
    });
    initLayerPage();
});

async function refreshLayerData() {

    const payload = buildPayload();

    console.log('REFRESH PAYLOAD:', payload);

    const res = await fetch(`${BASE_URL}/public/api/buildLayerPlan.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });

    if (!res.ok) {
        console.error('Refresh failed:', res.status);
        return;
    }

    const data = await res.json();

    console.log('REFRESH RESPONSE:', data);

    // 🔥 UPDATE SQUAD SELECT
    const squadSelect = document.getElementById('squadSelect');

    if (squadSelect) {
        squadSelect.innerHTML = '<option value="">-- Choose Squad --</option>';

        (data.squads || []).forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.squadID;
            opt.textContent = `${s.name} (Lvl ${s.level})`;
            squadSelect.appendChild(opt);
        });
    }

    // 🔥 UPDATE LAYER COUNT (important)
    updateLayerCount(data.layerCount);

    // 🔥 UPDATE MONSTERS
    renderPlan([]); // clear old view first
}

function updateLayerCount(count) {

    const section = document.querySelector('.layer-section');
    if (!section) return;

    section.innerHTML = '';

    for (let i = 1; i <= count; i++) {
        section.innerHTML += `
            <div class="layer-block" data-layer="${i}">
                <div class="layer-header-round"></div>
                <div class="layer-row">
                    <div class="layer-monster">
                        <div class="monster-meta">Waiting for plan...</div>
                    </div>
                </div>
            </div>
        `;
    }
}
/* ======================
   INIT CONTROLLER
====================== */
function initLayerPage() {

    const generateBtn = document.getElementById('generatePlanBtn');
    const troopCards = document.querySelectorAll('.troop-card');
    const troopCheckboxes = document.querySelectorAll('.troop-checkbox');
    const selectAll = document.getElementById('selectAllTroops');
    const globalRadios = document.querySelectorAll('.global-level-radio');

        // 🔥 ENSURE STATE IS ALWAYS INITIALIZED
    const selectedDifficulty =
        document.querySelector('input[name="difficulty"]:checked')?.value || 'Rare';

    if (window.BattleState) {
        BattleState.set('difficulty', selectedDifficulty);
    }

    document.querySelectorAll('input[name="difficulty"]').forEach(radio => {
        radio.addEventListener('change', async (e) => {

            const value = e.target.value || 'Rare';

            BattleState.set('difficulty', value);

            console.log('DIFFICULTY CHANGED 2:', value);

            // 🔥 THIS WAS MISSING → RELOAD DATA
            await refreshLayerData();
        });
    });
    
    document.querySelector('[name="bonusStr"]')?.addEventListener('change', (e) => {
    BattleState.set('bonusStr', parseInt(e.target.value || 100));
    });

    document.querySelector('[name="bonusHlh"]')?.addEventListener('change', (e) => {
        BattleState.set('bonusHlh', parseInt(e.target.value || 100));
    });

    /* ======================
       TROOP STATE TOGGLE
    ====================== */
    troopCards.forEach(card => {
        const checkbox = card.querySelector('.troop-checkbox');
        const radios = card.querySelectorAll('.troop-level-radio');
        const levels = card.querySelector('.troop-levels');

        if (!checkbox) return;

        const update = () => {
            const enabled = checkbox.checked;

            radios.forEach(r => r.disabled = !enabled);
            if (levels) levels.classList.toggle('disabled', !enabled);
        };

        checkbox.addEventListener('change', update);
        update();
    });

    /* ======================
       SELECT ALL
    ====================== */
    if (selectAll) {
        selectAll.addEventListener('change', () => {

            troopCheckboxes.forEach(cb => {
                cb.checked = selectAll.checked;

                const card = cb.closest('.troop-card');
                if (!card) return;

                const radios = card.querySelectorAll('.troop-level-radio');
                const levels = card.querySelector('.troop-levels');

                radios.forEach(r => r.disabled = !cb.checked);
                if (levels) levels.classList.toggle('disabled', !cb.checked);
            });
        });
    }

    /* ======================
       GLOBAL LEVEL SYNC
    ====================== */
    globalRadios.forEach(gr => {
        gr.addEventListener('change', () => {

            const globalValue = gr.value;

            troopCards.forEach(card => {
                const checkbox = card.querySelector('.troop-checkbox');
                if (!checkbox || !checkbox.checked) return;

                const radios = card.querySelectorAll('.troop-level-radio');
                radios.forEach(r => r.checked = r.value === globalValue);
            });
        });
    });

    /* ======================
       GENERATE PLAN
    ====================== */
    if (!generateBtn) return;

    generateBtn.addEventListener('click', async (e) => {
        e.preventDefault();

        const payload = buildPayload();

        console.log('FINAL PAYLOAD →', payload);

        const res = await fetch(`${BASE_URL}/public/api/buildLayerPlan.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        console.log('API RESPONSE:', data);

        const result = LayerEngine.buildAttackPlan(
            data.fighterOptions,
            data.monsters,
            {
                fighterStrBonus: data.bonusStr,
                fighterHlhBonus: data.bonusHlh
            }
        );

        if (!result || result.error) {
            console.warn('ENGINE ERROR:', result?.error || result);
            return;
        }

        updateTotals(result.totals ?? { dominance: 0, leadership: 0 });
        renderPlan(result.plan ?? []);
    });
}


/* ======================
   BUILD PAYLOAD
====================== 
function buildPayload() {

    const state = BattleState.snapshot();

    const troopCheckboxes = document.querySelectorAll('.troop-checkbox');

    const troops = {};

    troopCheckboxes.forEach(cb => {
        const type = cb.dataset.troopType;

        const levelRadio = document.querySelector(
            `.troop-level-radio[data-troop-type="${type}"]:checked`
        );

        troops[type] = {
            enabled: cb.checked ? 'on' : null,
            level: levelRadio ? levelRadio.value : null
        };
    });

    return {
        troops,
        playerLevel: state.playerLevel,
        difficulty: state.difficulty,
        squadID: document.getElementById('squadSelect')?.value || state.squadID,
        useCreatures: true,
        useFighters: true,

        bonusStr: state.bonusStr,
        bonusHlh: state.bonusHlh
    };
}
*/
function buildPayload() {

    const troopCheckboxes = document.querySelectorAll('.troop-checkbox');

    const payload = {
        troops: {},
        playerLevel: parseInt(document.querySelector('[name="playerLevel"]')?.value || 6),

        difficulty:
            (BattleState?.get('difficulty') ||
             document.querySelector('input[name="difficulty"]:checked')?.value ||
             'Rare'),

        squadID: document.getElementById('squadSelect')?.value || null,

        useCreatures: true,
        useFighters: true,

        bonusStr: BattleState?.get('bonusStr') ?? 100,
        bonusHlh: BattleState?.get('bonusHlh') ?? 100
    };

    troopCheckboxes.forEach(cb => {
        const type = cb.dataset.troopType;

        const levelRadio = document.querySelector(
            `.troop-level-radio[data-troop-type="${type}"]:checked`
        );

        payload.troops[type] = {
            enabled: cb.checked ? 'on' : null,
            level: levelRadio ? levelRadio.value : null
        };
    });

    console.log('PAYLOAD:', payload);

    return payload;
}

/* ======================
   TROOP MAP BUILDER
====================== */
function buildTroopMap(troopCheckboxes) {

    const map = {};

    troopCheckboxes.forEach(cb => {

        const type = cb.dataset.troopType;

        const levelRadio = document.querySelector(
            `.troop-level-radio[data-troop-type="${type}"]:checked`
        );

        map[type] = {
            enabled: cb.checked ? 'on' : null,
            level: levelRadio ? levelRadio.value : null
        };
    });

    return map;
}


/* ======================
   RENDER PLAN
====================== */
function renderPlan(plan = []) {

    if (!Array.isArray(plan)) {
        console.warn('renderPlan received invalid data:', plan);
        return;
    }

    plan.forEach((row, i) => {
        const block = document.querySelector(`[data-layer="${i + 1}"]`);
        if (!block) return;

        const header = block.querySelector('.layer-header-round');
        if (header) {
            header.innerHTML = `
                <div class="unit-round-label">
                    ${row.unitsNeeded} ${row.fighterName} V. ${row.monsterQty} ${row.monsterName}
                </div>
            `;
        }

        const monsterEl = block.querySelector('.layer-monster');
        if (monsterEl) {
            monsterEl.innerHTML = `
                <div class="monster-text-block">
                    <div class="fighter-image-container">
                        <img src="${row.fighterImg}" class="fighter-img">
                    </div>
                    <div class="layer-versus">V.</div>
                    <div class="monster-image-container">
                        <img src="${row.monsterImg}" class="monster-img">
                    </div>
                </div>
            `;
        }
    });
}


/* ======================
   TOTALS
====================== */
function updateTotals(totals = {}) {

    const dom = document.querySelector('.dominance-value');
    const lead = document.querySelector('.leadership-value');

    const dominance = totals?.dominance ?? 0;
    const leadership = totals?.leadership ?? 0;

    if (dom) dom.textContent = dominance;
    if (lead) lead.textContent = leadership;

    console.log('TOTALS:', totals);
}