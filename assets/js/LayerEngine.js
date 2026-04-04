// LayerEngine.js
const LayerEngine = (() => {

    /**
     * Validate selected fighters against monster squad
     * @param {Array} selectedFighters - [{ type, level }]
     * @param {Number} monsterGroups - number of monster groups
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
     */
    function buildAttackPlan(selectedFighters, monsterGroups) {
        const validation = validateAttackGroups(selectedFighters, monsterGroups.length);
        if (!validation.valid) return { error: validation.message };

        return {
            plan: monsterGroups.map((group, i) => {
                const fighter = selectedFighters[i % selectedFighters.length];
                return {
                    groupID: group.groupID,
                    groupName: group.name,
                    fighterType: fighter.type,
                    fighterLevel: fighter.level
                };
            })
        };
    }

    return {
        validateAttackGroups,
        buildAttackPlan
    };

})();