// =========================
// COMBAT ENGINE MODULE
// =========================
const CombatEngine = (() => {

    function buildMonsters(qtyList, healthList, strengthList) {
        return qtyList.map((qty, i) => ({
            qty: Number(qty),
            totalHealth: Number(healthList[i]),
            totalStrength: Number(strengthList[i]),
            baseHth: Number(healthList[i]) / Number(qty),
            baseStr: Number(strengthList[i]) / Number(qty),
            bonus: 0
        }));
    }

    function calcUnitsNeeded(monsterMaxHealth, creatureStrength, percent = 0) {
        const boosted = creatureStrength * (1 + percent / 100);
        let units = Math.ceil(monsterMaxHealth / boosted);

        if (units < 1) return 1;
        if (units > 500) return '✖';

        return units;
    }

    function runSimulation({ units, creature, monsters }) {

        let currentUnits = units;
        let totalLost = 0;

        const unitHP  = creature.health * (1 + creature.percent / 100);
        const unitStr = creature.strength * (1 + creature.percent / 100);

        for (let i = 0; i < monsters.length; i++) {
            if (currentUnits <= 0) break;

            const m = monsters[i];

            // ---- MONSTER ATTACK FIRST
            let loss = Math.ceil(m.totalStrength / unitHP);
            loss = Math.min(loss, currentUnits);

            currentUnits -= loss;
            totalLost += loss;

            if (currentUnits <= 0) break;

            // ---- CREATURE ATTACK
            const attack = unitStr * currentUnits;

            let monsterLoss = Math.ceil(attack / m.baseHth);
            monsterLoss = Math.min(monsterLoss, m.qty);

            // future: reduce m.qty if partial carryover needed
        }

        return {
            lost: totalLost,
            remaining: currentUnits
        };
    }

    return {
        buildMonsters,
        calcUnitsNeeded,
        runSimulation
    };

})();