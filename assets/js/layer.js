// layer.js

document.addEventListener('DOMContentLoaded', () => {

    const generateBtn = document.getElementById('generatePlanBtn');
    const troopCards = document.querySelectorAll('.troop-card');
    const troopCheckboxes = document.querySelectorAll('.troop-checkbox');
    const selectAll = document.getElementById('selectAllTroops');

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

            radios.forEach(r => {
                r.disabled = !enabled;
            });

            if (levels) {
                levels.classList.toggle('disabled', !enabled);
            }
        };

        checkbox.addEventListener('change', update);
        update();
    });

    /* ======================
       SELECT ALL (SAFE)
    ====================== */
    if (selectAll) {
        selectAll.addEventListener('change', () => {

            troopCheckboxes.forEach(cb => {
                cb.checked = selectAll.checked;

                const card = cb.closest('.troop-card');
                const radios = card.querySelectorAll('.troop-level-radio');
                const levels = card.querySelector('.troop-levels');

                radios.forEach(r => {
                    r.disabled = !cb.checked;
                });

                if (levels) {
                    levels.classList.toggle('disabled', !cb.checked);
                }
            });

        });
    }

    /* ======================
       BUILD PAYLOAD (SOURCE OF TRUTH)
    ====================== */
    function buildPayload() {
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
       GENERATE PLAN (API)
    ====================== */
    if (generateBtn) {
        generateBtn.addEventListener('click', async (e) => {
            e.preventDefault();

            const payload = buildPayload();

            // quick validation BEFORE API
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

                // 🔥 Pass ONLY API data into engine
                const result = LayerEngine.buildAttackPlan(
                    data.fighterOptions,
                    data.monsters
                );

                if (result.error) {
                    console.warn(result.error);
                } else {
                    console.log('ATTACK PLAN:', result.plan);
                }

            } catch (err) {
                console.error('API ERROR:', err);
            }
        });
    }

});