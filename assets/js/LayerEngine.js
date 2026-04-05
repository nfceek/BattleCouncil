// LayerEngine.js

console.log('LayerEngine LOADED');

window.LayerEngine = (() => {

    function validateAttackGroups(fighters, monsters) {
        if (!fighters || fighters.length === 0) {
            return { valid: false, message: 'No fighters selected.' };
        }

        if (fighters.length < monsters.length) {
            return {
                valid: false,
                message: `Not enough fighters (${fighters.length}) for ${monsters.length} monster groups.`
            };
        }

        return { valid: true, message: null };
    }

    function buildAttackPlan(fighters, monsters) {
        const check = validateAttackGroups(fighters, monsters);

        if (!check.valid) {
            return { error: check.message };
        }

        const plan = monsters.map((m, i) => {
            const f = fighters[i % fighters.length];

            return {
                groupID: m.monsterID ?? i + 1,
                groupName: m.name,
                fighterName: f.name,
                fighterType: f.type,
                fighterLevel: f.level
            };
        });

        return { plan };
    }

    return {
        validateAttackGroups,
        buildAttackPlan
    };

})();