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
    let rounds = [];

    const creatureUnitHP  = creature.health * (1 + creature.percent / 100);
    const creatureUnitStr = creature.strength * (1 + creature.percent / 100);

    for (let i = 0; i < monsters.length; i++) {

        let m = monsters[i];

        let monsterRemaining = m.qty;

        if (monsterRemaining <= 0) continue;

        let round = 0;

        while (monsterRemaining > 0 && currentUnits > 0 && round < 10) {
            round++;

            // =========================
            // MONSTER ATTACK FIRST
            // =========================
            const monsterAttack = monsterRemaining * m.baseStr;

            let creatureLoss = Math.ceil(monsterAttack / creatureUnitHP);
            creatureLoss = Math.min(creatureLoss, currentUnits);

            currentUnits -= creatureLoss;
            totalLost += creatureLoss;

            if (currentUnits <= 0) {
                rounds.push({
                    monsterIndex: i,
                    round,
                    creatureLost: creatureLoss,
                    monsterLost: 0,
                    creatureRemaining: 0,
                    monsterRemaining
                });
                break;
            }

            // =========================
            // CREATURE ATTACK BACK
            // =========================
            const creatureAttack = currentUnits * creatureUnitStr;

            let monsterLoss = Math.ceil(creatureAttack / m.baseHth);
            monsterLoss = Math.min(monsterLoss, monsterRemaining);

            monsterRemaining -= monsterLoss;

            rounds.push({
                monsterIndex: i,
                round,
                creatureLost: creatureLoss,
                monsterLost: monsterLoss,
                creatureRemaining: currentUnits,
                monsterRemaining
            });

            if (monsterRemaining <= 0) break;
        }
    }

    return {
        lost: totalLost,
        remaining: currentUnits,
        rounds
    };
}

    return {
        buildMonsters,
        calcUnitsNeeded,
        runSimulation
    };

})();