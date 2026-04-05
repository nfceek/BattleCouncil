// layer.js

document.addEventListener('DOMContentLoaded', () => {

    // ----------------------
    // Mounted Fighter Toggle
    // ----------------------
    const toggle = document.querySelector('input[name="useMtdFighters"]');
    const radios = document.querySelectorAll('input[name="mtdLevel"]');

    if (toggle && radios.length > 0) {
        function updateMountedLevels() {
            radios.forEach(r => r.disabled = !toggle.checked);
        }
        toggle.addEventListener('change', updateMountedLevels);
        updateMountedLevels();
    }

    // ----------------------
    // Unit Group Selects
    // ----------------------
    document.querySelectorAll('.unit-group').forEach(group => {
        const select = group.querySelector('.unit-select');
        const radios = group.querySelectorAll('input[type="radio"]');

        if (select && radios.length) {
            function update() {
                const enabled = select.value !== '';
                radios.forEach(r => r.disabled = !enabled);
            }
            select.addEventListener('change', update);
            update();
        }
    });

    // ----------------------
    // Squad / Difficulty
    // ----------------------
    const difficultyRadios = document.querySelectorAll('input[name="difficulty"]');
    const squadSelect = document.getElementById('squadSelect');
    const generateBtn = document.getElementById('generatePlanBtn');

    if (difficultyRadios.length && squadSelect) {
        difficultyRadios.forEach(radio => {
            radio.addEventListener('click', async () => {
                const difficulty = radio.value;

                // lock UI while loading
                //squadSelect.disabled = true;
                squadSelect.innerHTML = `<option>Loading...</option>`;
                //generateBtn.disabled = true;

                try {
                    const res = await fetch(`${BASE_URL}/public/api/getSquads.php?difficulty=${difficulty}`);
                    const squads = await res.json();

                    let options = `<option value="">-- Choose Squad --</option>`;
                    squads.forEach(s => {
                        options += `<option value="${s.squadID}">${s.name} (Lvl ${s.level})</option>`;
                    });

                    squadSelect.innerHTML = options;
                    squadSelect.disabled = false;
                } catch (err) {
                    squadSelect.innerHTML = `<option>Error loading squads</option>`;
                    console.error(err);
                }
            });
        });

        squadSelect.addEventListener('change', () => {
            generateBtn.disabled = !squadSelect.value;
        });
    }

    // ----------------------
    // Troop / Attack Plan
    // ----------------------
    const globalEnable = document.getElementById('global-enable');
    const troopCards = document.querySelectorAll('.troop-card');
    const troopCheckboxes = document.querySelectorAll('.troop-checkbox');

    // Individual troop card setup
    troopCards.forEach(card => {
        const checkbox = card.querySelector('input[type="checkbox"]');
        const radios = card.querySelectorAll('input[type="radio"]');
        const levelsContainer = card.querySelector('.troop-levels');

        if (!checkbox || !levelsContainer) return;

        const updateState = () => {
            const enabled = checkbox.checked;
            radios.forEach(r => r.disabled = !enabled);
            levelsContainer.classList.toggle('disabled', !enabled);
        };

        checkbox.addEventListener('change', updateState);
        updateState(); // initial state
    });

    // Global enable checkbox
    if (globalEnable) {
        globalEnable.addEventListener('change', () => {
            const enabled = globalEnable.checked;
            troopCheckboxes.forEach(cb => {
                cb.checked = enabled;
                const card = cb.closest('.troop-card');
                const radios = card.querySelectorAll('.troop-level-radio');
                radios.forEach(r => r.disabled = !enabled);
                card.querySelector('.troop-levels')?.classList.toggle('disabled', !enabled);
            });
        });
    }

 // Global level radios (FIXED)
document.querySelectorAll('.global-level-radio').forEach(globalRadio => {
    globalRadio.addEventListener('change', () => {

        const level = globalRadio.value;

        document.querySelectorAll('.troop-card').forEach(card => {

            const checkbox = card.querySelector('.troop-checkbox');
            if (!checkbox || !checkbox.checked) return;

            const radios = card.querySelectorAll('.troop-level-radio');

            radios.forEach(r => {

                // enable radios if needed
                r.disabled = false;

                if (r.value === level) {
                    r.checked = true;

                    // 🔥 REQUIRED — triggers all other listeners
                    r.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });

        });

    });

});
    // Individual troop radio feedback (optional)
    document.querySelectorAll('.troop-level-radio').forEach(radio => {
        radio.addEventListener('change', () => {
            console.log(`${radio.dataset.troopType} set to level ${radio.value}`);
        });
    });

    // ----------------------
    // Generate Attack Plan
    // ----------------------
    if (generateBtn) {
        generateBtn.addEventListener('click', () => {
            const selectedTroops = Array.from(troopCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => {
                    const type = cb.dataset.troopType;
                    const levelRadio = document.querySelector(`.troop-level-radio[data-troop-type="${type}"]:checked`);
                    return {
                        type,
                        level: levelRadio ? levelRadio.value : null
                    };
                });

            console.log('Building attack plan with:', selectedTroops);
            // Your attack plan logic here
        });
    }

    /*----------------------
    // Clear Selection Button
    // ----------------------
    const clearBtn = document.getElementById('clear-selection');
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            troopCheckboxes.forEach(cb => cb.checked = false);
            document.querySelectorAll('.troop-level-radio').forEach(r => {
                r.checked = false;
                r.disabled = true;
            });
            if (globalEnable) globalEnable.checked = false;
            //globalLevelRadios.forEach(r => r.checked = false);
        });
    }
*/
/* ----------------------
// generate plan btn
// ---------------------
generateBtn.addEventListener('click', (e) => {
    e.preventDefault(); // 🔥 stops form submission

    const selectedTroops = Array.from(troopCheckboxes)
        .filter(cb => cb.checked)
        .map(cb => {
            const type = cb.dataset.troopType;
            const levelRadio = document.querySelector(
                `.troop-level-radio[data-troop-type="${type}"]:checked`
            );

            return {
                type,
                level: levelRadio ? levelRadio.value : null
            };
        });

    console.log('Building attack plan with:', selectedTroops);
});
*/

// ----------------------
// Select All Troops (FIXED)
// ----------------------
const selectAll = document.getElementById('selectAllTroops');

if (selectAll) {
    selectAll.addEventListener('change', () => {

        troopCheckboxes.forEach(cb => {

            cb.checked = selectAll.checked;

            const card = cb.closest('.troop-card');
            const levels = card.querySelector('.troop-levels');

            if (!levels) return;

            levels.classList.toggle('disabled', !cb.checked);

            const radios = levels.querySelectorAll('.troop-level-radio');

            radios.forEach(r => {
                r.disabled = !cb.checked;

                // only clear if explicitly unchecking via UI
                if (!cb.checked && selectAll.matches(':focus')) {
                    r.checked = false;
                }
            });

        });

    });
}

    // ----------------------
    // Layer Blocks (Round 2 logic)
    // ----------------------
    document.querySelectorAll('.layer-block').forEach(block => {
        const round1 = block.querySelector('select[name*="[unit1]"]');
        const round2 = block.querySelector('.round2');
        const radios2 = block.querySelectorAll('input[name*="[level2]"]');

        if (!round1 || !round2) return;

        round1.addEventListener('change', () => {
            const enabled = !!round1.value;
            round2.disabled = !enabled;
            radios2.forEach(r => r.disabled = !enabled);
        });
    });

});

// LayerEngine.js
// Self-contained attack plan engine

document.addEventListener('DOMContentLoaded', () => {

    const LayerEngine = (() => {

        /**
         * Validate selected fighters against monster squad
         */
        function validateAttackGroups(selectedFighters, monsterGroupsCount) {
            const selectedCount = selectedFighters.length;

            if (selectedCount < monsterGroupsCount) {
                return {
                    valid: false,
                    message: `Not enough attack groups: ${selectedCount} selected, but monster squad has ${monsterGroupsCount} groups.`
                };
            }
            return { valid: true, message: null };
        }

        /**
         * Build an attack plan mapping fighters to monster groups
         */
        function buildAttackPlan(selectedFighters, monsterGroups) {
            const validation = validateAttackGroups(selectedFighters, monsterGroups.length);
            if (!validation.valid) return { error: validation.message };

            // Map fighters to monster groups (wrap around if fewer fighters)
            const plan = monsterGroups.map((group, i) => {
                const fighter = selectedFighters[i % selectedFighters.length];
                return {
                    groupID: group.groupID,
                    groupName: group.name,
                    fighterType: fighter.type,
                    fighterLevel: fighter.level
                };
            });

            return { plan };
        }

        /**
         * Reads current selections from the DOM
         */
        function getSelectedFighters() {
            return Array.from(document.querySelectorAll('.troop-checkbox'))
                .filter(cb => cb.checked)
                .map(cb => {
                    const type = cb.dataset.troopType;
                    const levelRadio = document.querySelector(`.troop-level-radio[data-troop-type="${type}"]:checked`);
                    return {
                        type,
                        level: levelRadio ? levelRadio.value : null
                    };
                });
        }

        /**
         * Dummy monster groups for testing
         * Replace with real data from API/DOM
         */
        function getMonsterGroups() {
            return [
                { groupID: 1, name: 'Monsters A' },
                { groupID: 2, name: 'Monsters B' },
                { groupID: 3, name: 'Monsters C' }
            ];
        }

        /**
         * Hook Generate Attack Plan button
         */
        function hookGenerateButton() {
            const generateBtn = document.getElementById('generatePlanBtn');

            //console.log('Hooking generate button...', generateBtn); // ✅ now safe

            if (!generateBtn) return;

            generateBtn.addEventListener('click', () => {
                const selectedFighters = getSelectedFighters();
                const monsterGroups = getMonsterGroups();

                const result = buildAttackPlan(selectedFighters, monsterGroups);

                if (result.error) {
                    console.warn(result.error);
                } else {
                    console.log('Attack Plan:', result.plan);
                }
            });
        }

        // Initialize automatically
        hookGenerateButton();

        return {
            validateAttackGroups,
            buildAttackPlan,
            getSelectedFighters,
            //getMonsterGroups
        };

    })();

});