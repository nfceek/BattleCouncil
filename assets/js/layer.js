document.addEventListener('DOMContentLoaded', () => {
    console.log('layer.js INIT');

    if (!window.LayerEngine) {
        console.error('LayerEngine NOT loaded');
        return;
    }

    initLayerPage();
});

/* ======================
   INIT (MAIN CONTROLLER)
====================== */
function initLayerPage() {
    const generateBtn = document.getElementById('generatePlanBtn');
    const troopCards = document.querySelectorAll('.troop-card');
    const troopCheckboxes = document.querySelectorAll('.troop-checkbox');
    const selectAll = document.getElementById('selectAllTroops');
    const globalRadios = document.querySelectorAll('.global-level-radio');

    /* ======================
       TROOP CARD STATE
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

        const payload = buildPayload(troopCheckboxes);

        const selected = Object.values(payload.troops)
            .filter(t => t.enabled && t.level);

        if (selected.length === 0) {
            console.warn('No fighters selected.');
            return;
        }

        try {
            console.log('CALLING API...');

            const res = await fetch(`${BASE_URL}/public/api/buildLayerPlan.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data = await res.json();
            console.log('API RESPONSE:', data);

            const result = LayerEngine.buildAttackPlan(
                data.fighterOptions,
                data.monsters
            );

            if (result.error) {
                console.warn(result.error);
                return;
            }

            console.log('ATTACK PLAN:', result.plan);

            renderPlan(result.plan); // ✅ THIS WAS MISSING

        } catch (err) {
            console.error('API ERROR:', err);
        }
    });
}

/* ======================
   BUILD PAYLOAD
====================== */
function buildPayload(troopCheckboxes) {
    const payload = {
        troops: {},
        playerLevel: parseInt(document.querySelector('[name="playerLevel"]')?.value || 6),
        difficulty: document.querySelector('input[name="difficulty"]:checked')?.value || 'rare',
        squadID: document.getElementById('squadSelect')?.value || null,
        useCreatures: true,
        useFighters: true
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
   RENDER PLAN
====================== */
function renderPlan(plan) {
    plan.forEach((row, i) => {
        const block = document.querySelector(`[data-layer="${i+1}"]`);
        if (!block) return;

        // MONSTER
        const monsterEl = block.querySelector('.layer-monster');
        if (monsterEl) {
            monsterEl.innerHTML = `
                <div class="layer-header-round unit-round-label"><strong>${row.unitsNeeded} ${row.fighterName} V. ${row.monsterQty} ${row.monsterName}</strong></div>
                    <div class="monster-text-block" style="display:flex; gap:15px; align-items:flex-start; padding-left:8px;">
                        <div class="fighter-image-container">
                            <img src="${row.fighterImg}" class="fighter-img">
                        </div>

                        <div class="monster-image-container">
                            <img src="${row.monsterImg}" class="monster-img">
                        </div>
                        <div style="flex-grow:1;">
                            <div class="monster-text-middle" style="padding-top:8px;padding-bottom:8px;">
                                <div class="monster-info-container">
                                <div class="unit-round-label"><strong>Qty: ${row.monsterQty} ${row.monsterName}</strong> <small>(${row.monsterType})</small></div>
                            </div
                        </div>
                    </div>                
                </div>
            `;
        }

        // ATTACK 1
        const attack1 = block.querySelector('.attack1');
        if (attack1) {
            attack1.innerHTML = `
                <div class="fighter-text-block" style="display:flex; gap:15px; align-items:flex-start; padding-left:8px;">
                    <div class="fighter-image-container">
                        <img src="${row.fighterImg}" class="fighter-img">
                    </div>
                    <div style="flex-grow:1;">
                        <div class="fighter-info-container">
                            <div class="unit-round-label"><strong>${row.unitsNeeded} ${row.fighterName}</strong> <small>(${row.fighterType})</small></div>
                        </div
                        <div class="fighter-text-middle" style="padding-top:8px;padding-bottom:8px;">
                        </div>
                    </div>                
                </div>
            `;
        }
    });
}