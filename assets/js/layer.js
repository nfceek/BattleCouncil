// layer.js
document.addEventListener('DOMContentLoaded', () => {

    const troopCards = document.querySelectorAll('.troop-card');
    const troopCheckboxes = document.querySelectorAll('.troop-checkbox');
    const selectAll = document.getElementById('selectAllTroops');
    const globalLevelRadios = document.querySelectorAll('.global-level-radio');
    const generateBtn = document.getElementById('generatePlanBtn');

    // ----------------------
    // Unified Troop Grid Controller
    // ----------------------
    function updateTroopCard(card, enabledLevel = null) {
        const checkbox = card.querySelector('.troop-checkbox');
        const radios = card.querySelectorAll('.troop-level-radio');
        const levelsContainer = card.querySelector('.troop-levels');

        if (!checkbox || !levelsContainer) return;

        const enabled = checkbox.checked;
        radios.forEach(r => {
            r.disabled = !enabled;
            if (enabledLevel !== null) r.checked = r.value === enabledLevel;
        });

        levelsContainer.classList.toggle('disabled', !enabled);
    }

    // Initialize troop cards
    troopCards.forEach(card => {
        const checkbox = card.querySelector('.troop-checkbox');

        checkbox.addEventListener('change', () => updateTroopCard(card));

        // initial state
        updateTroopCard(card);
    });

    // ----------------------
    // Select All
    // ----------------------
    if (selectAll) {
        selectAll.addEventListener('change', () => {
            const checked = selectAll.checked;
            troopCheckboxes.forEach(cb => {
                cb.checked = checked;
                const card = cb.closest('.troop-card');
                updateTroopCard(card);
            });
        });
    }

    // ----------------------
    // Global Level Radios
    // ----------------------
    globalLevelRadios.forEach(gr => {
        gr.addEventListener('change', () => {
            const level = gr.value;

            troopCards.forEach(card => {
                const checkbox = card.querySelector('.troop-checkbox');
                if (!checkbox.checked) return;
                updateTroopCard(card, level); // forces radio selection
            });
        });
    });

    // ----------------------
    // Select All Troops
    // ----------------------
    if (selectAll) {
        selectAll.addEventListener('change', () => {
            troopCheckboxes.forEach(cb => {
                cb.checked = selectAll.checked;
                const card = cb.closest('.troop-card');
                const radios = card.querySelectorAll('.troop-level-radio');
                radios.forEach(r => {
                    r.disabled = !cb.checked;
                    if (!cb.checked) r.checked = false;
                });
                card.querySelector('.troop-levels')?.classList.toggle('disabled', !cb.checked);
            });
        });
    }

    // ----------------------
    // Generate Attack Plan
    // ----------------------

    if (generateBtn) {
        generateBtn.addEventListener('click', () => {
            const selectedFighters = Array.from(troopCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => {
                    const type = cb.dataset.troopType;
                    const levelRadio = document.querySelector(`.troop-level-radio[data-troop-type="${type}"]:checked`);
                    return {
                        type,
                        level: levelRadio ? parseInt(levelRadio.value) : null
                    };
                });

            const monsterGroups = getMonsterGroupsFromDOM(); // replace with your actual DOM or API call

            const result = LayerEngine.buildAttackPlan(selectedFighters, monsterGroups);
            if (result.error) {
                console.warn(result.error);
            } else {
                console.log('Attack Plan:', result.plan);
            }
        });
    }

    // ----------------------
    // Utility: get monster groups from DOM
    // ----------------------
    function getMonsterGroupsFromDOM() {
        return Array.from(document.querySelectorAll('.monster-group')).map((el, i) => ({
            groupID: i + 1,
            name: el.dataset.name || `Group ${i + 1}`
        }));
    }
});