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

    const CONFIG = {
        typeBlockThreshold: options.typeBlockThreshold ?? 25,
        fighterStrBonus: options.fighterStrBonus ?? 1,
        monsterStrBonus: options.monsterStrBonus ?? 1
    };

    function calcMonsterHlh(m) {
        return (m.strength * 3) * m.quantity * CONFIG.monsterStrBonus;
    }

    function calcFighterHlh(f) {
        return f.strength * 3 * CONFIG.fighterStrBonus;
    }

    function isCreature(f) {
        return (f.type ?? '').toLowerCase() === 'bst';
    }

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

    preparedMonsters.sort((a, b) => b.monsterHlh - a.monsterHlh);

    const fightersPool = [...fighters];
    let creatureUsed = 0;
    const CREATURE_LIMIT = 1;

    function pickBestFighter(monster, fighters) {
        const normal = [];
        const creatures = [];

        fighters.forEach(f => {
            if (isCreature(f)) creatures.push(f);
            else normal.push(f);
        });

        const filterViable = (list) =>
            list.filter(f => (monster.monsterClass[f.class ?? f.type] ?? 0) <= CONFIG.typeBlockThreshold);

        let viableNormal = filterViable(normal);
        let viableCreatures = filterViable(creatures);

        if (viableNormal.length === 0) viableNormal = normal;
        if (viableCreatures.length === 0) viableCreatures = creatures;

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
        const fighterClass = f.class ?? f.type;
        const bonusVsFighter = m.monsterClass[fighterClass] ?? 0;
        const blocked = bonusVsFighter > CONFIG.typeBlockThreshold;

        // ✅ CALCULATE FIRST
        const boosted = f.strength * CONFIG.fighterStrBonus;
        const unitsNeeded = Math.ceil(m.monsterHlh / boosted);

        const fighterDominance = f.dominance || 0;
        const fighterLeadership = f.leadership || 0;

        // ✅ THEN ROLLUP
        const requiredDominance = fighterDominance * unitsNeeded;
        const requiredLeadership = fighterLeadership * unitsNeeded;

        const fighterMaxHealth = fighterHlh * unitsNeeded;

        console.log(`Mapping [${i}] →`, {
            monster: m.name,
            fighter: f.name,
            unitsNeeded,
            dominance: fighterDominance,
            leadership: fighterLeadership,
            requiredDominance,
            requiredLeadership
        });

        return {
            monsterID: m.monsterID,
            monsterName: m.name,
            monsterQty: m.quantity,
            monsterStr: m.strength,
            monsterHlh: m.monsterHlh,
            monsterImg: m.img || m.imgpath || null,

            fighterName: f.name,
            fighterType: f.type,
            fighterClass,
            fighterLevel: f.level,
            fighterStr: boosted,
            fighterHlh,
            unitsNeeded,

            dominance: fighterDominance,
            leadership: fighterLeadership,

            requiredDominance,
            requiredLeadership,

            fighterMaxHealth,
            fighterImg: f.img || f.imgpath || null,

            vsFighterBonus: bonusVsFighter,
            blockedMatch: blocked
        };
    });

    // =========================
    // TOTALS (CORRECT LOCATION)
    // =========================
    const totals = plan.reduce((acc, row) => {
        acc.dominance += row.requiredDominance || 0;
        acc.leadership += row.requiredLeadership || 0;
        return acc;
    }, { dominance: 0, leadership: 0 });

    return { plan, totals };
}

    // =========================
    // EXPORT
    // =========================
    return {
        validateAttackGroups,
        buildAttackPlan
    };

})();