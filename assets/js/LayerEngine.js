// LayerEngine.js
console.log('LayerEngine LOADED');

window.LayerEngine = (() => {

    // =========================
    // VALIDATION
    // =========================
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

    // =========================
    // BUILD ATTACK PLAN
    // =========================
    function buildAttackPlan(fighters, monsters, options = {}) {
        const check = validateAttackGroups(fighters, monsters);
        if (!check.valid) return { error: check.message };

        // =========================
        // CONFIG
        // =========================
        const CONFIG = {
            typeBlockThreshold: options.typeBlockThreshold ?? 25,
            fighterStrBonus: options.fighterStrBonus ?? 1,
            fighterHlhBonus: options.fighterHlhBonus ?? 1,
            monsterStrBonus: options.monsterStrBonus ?? 1,
            monsterHlhBonus: options.monsterHlhBonus ?? 1
        };

        // =========================
        // HELPERS
        // =========================
        function calcMonsterHlh(m) {
            return ((m.strength * m.quantity) * 3) * CONFIG.monsterHlhBonus;
        }

        function calcFighterHlh(f) {
            return (f.strength * 3) * CONFIG.fighterHlhBonus;
        }

        // =========================
        // PREP MONSTERS
        // =========================
        const preparedMonsters = monsters.map((m, i) => ({
            ...m,
            monsterID: m.monsterID ?? i + 1,
            monsterHlh: calcMonsterHlh(m),
            monsterClass: {
                mel: m.bonus_mel ?? 0,
                mtd: m.bonus_Mtd ?? 0,
                rng: m.bonus_Rng ?? 0,
                fly: m.bonus_Fly ?? 0,
                oth: m.bonus_Oth ?? 0
            }
        }));

        // SORT by HLH DESC
        preparedMonsters.sort((a, b) => b.monsterHlh - a.monsterHlh);

        // =========================
        // PLAN VARIABLES
        // =========================
        const fightersPool = [...fighters]; // shallow copy
        let creatureUsed = 0;
        const CREATURE_LIMIT = 1; // max bst in round 1

        // =========================
        // PICK BEST FIGHTER
        // =========================
        function pickBestFighter(monster, fighters) {
            const normal = [];
            const creatures = [];

            fighters.forEach(f => {
                const fighterClass = f.class ?? f.type;
                if (fighterClass === 'bst') creatures.push(f);
                else normal.push(f);
            });

            // Filter viable fighters respecting monster bonus
            const filterViable = (list) => list.filter(f => {
                const fighterClass = f.class ?? f.type;
                return (monster.monsterClass[fighterClass] ?? 0) <= CONFIG.typeBlockThreshold;
            });

            let viableNormal = filterViable(normal);
            let viableCreatures = filterViable(creatures);

            if (viableNormal.length === 0) viableNormal = normal;
            if (viableCreatures.length === 0) viableCreatures = creatures;

            // Sort by health
            viableNormal.sort((a, b) => calcFighterHlh(b) - calcFighterHlh(a));
            viableCreatures.sort((a, b) => calcFighterHlh(b) - calcFighterHlh(a));

            let chosen;

            if (creatureUsed < CREATURE_LIMIT && viableCreatures.length > 0) {
                const bestCreature = viableCreatures[0];
                const bestNormal = viableNormal[0];

                if (!bestNormal || calcFighterHlh(bestCreature) > calcFighterHlh(bestNormal)) {
                    chosen = bestCreature;
                    creatureUsed++;
                } else {
                    chosen = bestNormal;
                }
            } else {
                chosen = viableNormal[0] ?? viableCreatures[0];
            }

            return chosen;
        }

        // =========================
        // BUILD PLAN
        // =========================
const plan = preparedMonsters.map((m, i) => {
    const f = pickBestFighter(m, fightersPool);

    // remove fighter from pool
    const idx = fightersPool.indexOf(f);
    if (idx > -1) fightersPool.splice(idx, 1);

    const fighterHlh = calcFighterHlh(f);
    const fighterClass = f?.class ?? f?.type;
    const bonusVsFighter = m.monsterClass[fighterClass] ?? 0;
    const blocked = bonusVsFighter > CONFIG.typeBlockThreshold;

    // ===== NEW: units calculation =====
    const boosted = f.strength * CONFIG.fighterStrBonus;
    const unitsNeeded = Math.ceil(m.monsterHlh / boosted);
    const fighterMaxHealth = (fighterHlh * unitsNeeded); // if monster strikes first

    console.log(`Mapping [${i}] →`, {
        monster: m.name,
        fighter: f.name,
        unitsNeeded,
        fighterMaxHealth,
        boosted,
        monsterHlh: m.monsterHlh
    });

    return {
        monsterID: m.monsterID,
        monsterName: m.name,
        monsterQty: m.quantity,
        monsterStr: m.strength,
        monsterHlh: m.monsterHlh,

        fighterName: f.name,
        fighterType: f.type,
        fighterClass,
        fighterLevel: f.level,
        fighterStr: boosted,
        fighterHlh,
        unitsNeeded,
        fighterMaxHealth,

        vsFighterBonus: bonusVsFighter,
        blockedMatch: blocked
    };
});

        return { plan };
    }

    // =========================
    // EXPORT
    // =========================
    return {
        validateAttackGroups,
        buildAttackPlan
    };

})();