// LayerEngine.js

export const LayerEngine = (() => {

    /**
     * Validate selected fighters against monster squad
     * @param {Array} selectedFighters - [{ type, level }]
     * @param {Number} monsterGroups - number of monster groups
     * @returns {{ valid: boolean, message: string|null }}
     */
    function validateAttackGroups(selectedFighters, monsterGroups) {
        const selectedCount = selectedFighters.length;

        if (selectedCount < monsterGroups) {
            return {
                valid: false,
                message: `Not enough attack groups: ${selectedCount} selected, but monster squad has ${monsterGroups} groups.`
            };
        }

        return { valid: true, message: null };
    }

    /**
     * Build an attack plan
     * @param {Array} selectedFighters - [{ type, level }]
     * @param {Array} monsterGroups - [{ groupID, name }]
     * @returns {Object} - attack plan mapping fighters to monster groups
     */
    function buildAttackPlan(selectedFighters, monsterGroups) {
        const validation = validateAttackGroups(selectedFighters, monsterGroups.length);
        if (!validation.valid) return { error: validation.message };

        // Simple assignment: map fighters to monster groups one-to-one
        const plan = monsterGroups.map((group, i) => {
            const fighter = selectedFighters[i % selectedFighters.length]; // wrap around if more groups than fighters
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
     * Example debug runner
     */
    function runDebug() {
        const selectedFighters = [
            { type: 'mtd', level: 6 },
            { type: 'rng', level: 7 }
        ];
        const monsterGroups = [
            { groupID: 1, name: 'Monsters A' },
            { groupID: 2, name: 'Monsters B' },
            { groupID: 3, name: 'Monsters C' }
        ];

        const result = buildAttackPlan(selectedFighters, monsterGroups);

        if (result.error) {
            console.warn(result.error);
        } else {
            console.log('Attack Plan:', result.plan);
        }
    }

    return {
        validateAttackGroups,
        buildAttackPlan,
        runDebug
    };

})();
