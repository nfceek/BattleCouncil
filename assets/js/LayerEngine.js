window.LayerEngine = (() => {

    // =========================
    // VALIDATION
    // =========================
    function validateAttackGroups(fighters, monsters) {
        if (!fighters?.length) {
            return { valid: false, message: 'No fighters selected.' };
        }

        if (fighters.length < monsters.length) {
            return {
                valid: false,
                message: `Not enough fighters (${fighters.length}) for ${monsters.length} monsters.`
            };
        }

        return { valid: true };
    }

    // =========================
    // ENGINE
    // =========================
    function buildAttackPlan(fighters, monsters, options = {}) {

        const check = validateAttackGroups(fighters, monsters);
        if (!check.valid) return { error: check.message };

        // =========================
        // CONFIG (SAFE DEFAULT = 100%)
        // =========================
        const CONFIG = {
            fighterStrBonus: (options.fighterStrBonus ?? 100) / 100,
            fighterHlhBonus: (options.fighterHlhBonus ?? 100) / 100,
            typeBlockThreshold: options.typeBlockThreshold ?? 25
        };

        // =========================
        // HELPERS
        // =========================
        const isCreature = (f) => (f.type ?? '').toLowerCase() === 'bst';

        const calcFighterStr = (f) =>
            (f.strength || 0) * CONFIG.fighterStrBonus;

        const calcFighterHlh = (f) =>
            (f.strength || 0) * 3 * CONFIG.fighterHlhBonus;

        const calcMonsterHlh = (m) =>
            (m.strength || 0) * 3 * (m.quantity || 1);

        const calcMonsterStr = (m) =>
            (m.strength || 0);

        // =========================
        // PREP MONSTERS
        // =========================
        const preparedMonsters = monsters.map((m, i) => ({
            ...m,
            monsterID: m.monsterID ?? i + 1,
            monsterHlh: calcMonsterHlh(m),
            monsterStr: calcMonsterStr(m),
            monsterClass: {
                mel: m.bonus_mel ?? 0,
                mtd: m.bonus_Mtd ?? 0,
                rng: m.bonus_Rng ?? 0,
                fly: m.bonus_Fly ?? 0,
                oth: m.bonus_Oth ?? 0
            }
        })).sort((a, b) => b.monsterHlh - a.monsterHlh);

        // =========================
        // STATE
        // =========================
        const pool = [...fighters];
        let creatureUsed = 0;
        const CREATURE_LIMIT = 1;

        // =========================
        // PICK BEST FIGHTER
        // =========================
        function pickBestFighter(monster, list) {

            const normal = [];
            const creatures = [];

            list.forEach(f => {
                (isCreature(f) ? creatures : normal).push(f);
            });

            const viable = (arr) =>
                arr.filter(f =>
                    (monster.monsterClass[f.class ?? f.type] ?? 0) <= CONFIG.typeBlockThreshold
                );

            let vNormal = viable(normal);
            let vCreatures = viable(creatures);

            if (!vNormal.length) vNormal = normal;
            if (!vCreatures.length) vCreatures = creatures;

            const sort = (a, b) =>
                calcFighterStr(b) - calcFighterStr(a);

            vNormal.sort(sort);
            vCreatures.sort(sort);

            let chosen;

            if (creatureUsed < CREATURE_LIMIT && vCreatures.length) {
                const c = vCreatures[0];
                const n = vNormal[0];

                chosen = (!n || calcFighterStr(c) > calcFighterStr(n)) ? c : n;
                if (chosen === c) creatureUsed++;
            } else {
                chosen = vNormal[0] ?? vCreatures[0];
            }

            return chosen;
        }

        // =========================
        // BUILD PLAN
        // =========================
        const plan = preparedMonsters.map((m, i) => {

            const f = pickBestFighter(m, pool);

            const idx = pool.indexOf(f);
            if (idx > -1) pool.splice(idx, 1);

            // =========================
            // APPLY BONUSES
            // =========================
            const fighterStr = calcFighterStr(f);
            const fighterHlh = calcFighterHlh(f);

            // STR ONLY drives quantity
            const unitsNeeded = Math.max(
                1,
                Math.ceil(m.monsterHlh / (fighterStr || 1))
            );

            // =========================
            // CAPACITY SYSTEM
            // =========================
            const dominance = f.dominance || 0;
            const leadership = f.leadership || 0;

            const requiredDominance = dominance * unitsNeeded;
            const requiredLeadership = leadership * unitsNeeded;

            // =========================
            // MONSTER MATCH
            // =========================
            const fighterClass = f.class ?? f.type;
            const bonusVs = m.monsterClass[fighterClass] ?? 0;

            const blockedMatch = bonusVs > CONFIG.typeBlockThreshold;

            return {
                monsterID: m.monsterID,
                monsterName: m.name,
                monsterQty: m.quantity,
                monsterStr: m.monsterStr,
                monsterHlh: m.monsterHlh,
                monsterImg: m.img || m.imgpath || null,

                fighterName: f.name,
                fighterType: f.type,
                fighterClass,
                fighterLevel: f.level,

                fighterStr,
                fighterHlh,

                fighterStrBonus: CONFIG.fighterStrBonus,
                fighterHlhBonus: CONFIG.fighterHlhBonus,

                unitsNeeded,

                dominance,
                leadership,

                requiredDominance,
                requiredLeadership,

                fighterMaxHealth: fighterHlh * unitsNeeded,
                fighterImg: f.img || f.imgpath || null,

                vsFighterBonus: bonusVs,
                blockedMatch
            };
        });

        // =========================
        // TOTALS
        // =========================
        const totals = plan.reduce((acc, r) => {
            acc.dominance += r.requiredDominance || 0;
            acc.leadership += r.requiredLeadership || 0;
            return acc;
        }, { dominance: 0, leadership: 0 });

        return { plan, totals };
    }

    return {
        validateAttackGroups,
        buildAttackPlan
    };

})();