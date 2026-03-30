// =========================
// COMBAT ENGINE MODULE (prod-ready)
// =========================
const combatEngine = (() => {

    // -------------------------
    // Build monster objects
    // -------------------------
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

    // -------------------------
    // Calculate units needed for 1 monster group
    // -------------------------
    function calcUnitsNeeded(monsterMaxHealth, creatureStrength, percent = 0) {
        const boosted = creatureStrength * (1 + percent / 100);
        let units = Math.ceil(monsterMaxHealth / boosted);
        if (units < 1) return 1;
        if (units > 500) return '✖';
        return units;
    }

    // -------------------------
    // Run 1-to-many simulation (existing)
    // -------------------------
    function runSimulation({ units, creature, monsters }) {
        let currentUnits = units;
        let totalLost = 0;
        let rounds = [];

        const creatureUnitHP  = creature.health * (1 + (creature.percent || 0) / 100);
        const creatureUnitStr = creature.strength * (1 + (creature.percent || 0) / 100);

        for (let i = 0; i < monsters.length; i++) {
            let m = monsters[i];
            let monsterRemaining = m.qty;
            if (monsterRemaining <= 0) continue;

            let round = 0;

            while (monsterRemaining > 0 && currentUnits > 0 && round < 10) {
                round++;

                // Monster attacks first
                const monsterAttack = monsterRemaining * m.baseStr;
                let creatureLoss = Math.ceil(monsterAttack / creatureUnitHP);
                creatureLoss = Math.min(creatureLoss, currentUnits);
                currentUnits -= creatureLoss;

                // Creature attacks back
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

                if (currentUnits <= 0) break;
            }
        }

        return {
            lost: units - currentUnits,
            remaining: currentUnits,
            rounds
        };
    }

    // -------------------------
    // Many-to-Many Simulation (new)
    // -------------------------
    function runManyToManySimulation(creatures, monsters, maxRounds = 20) {
        const rounds = [];
        const creatureState = creatures.map(c => ({
            ...c,
            units: c.units || 1
        }));

        const monsterState = monsters.map(m => ({
            ...m,
            qty: m.qty || 1
        }));

        let roundNumber = 0;

        while (
            creatureState.some(c => c.units > 0) &&
            monsterState.some(m => m.qty > 0) &&
            roundNumber < maxRounds
        ) {
            roundNumber++;

            creatureState.forEach((creature, ci) => {
                if (creature.units <= 0) return;

                // Each creature targets the first alive monster group
                const target = monsterState.find(m => m.qty > 0);
                if (!target) return;

                // Monster attack
                const monsterAttack = target.qty * target.baseStr;
                let creatureLoss = Math.ceil(monsterAttack / creature.health);
                creatureLoss = Math.min(creatureLoss, creature.units);
                creature.units -= creatureLoss;

                // Creature attack
                const creatureAttack = creature.units * creature.strength;
                let monsterLoss = Math.ceil(creatureAttack / target.baseHth);
                monsterLoss = Math.min(monsterLoss, target.qty);
                target.qty -= monsterLoss;

                rounds.push({
                    round: roundNumber,
                    creatureIndex: ci,
                    monsterIndex: monsters.indexOf(target),
                    creatureLost: creatureLoss,
                    monsterLost: monsterLoss,
                    creatureRemaining: creature.units,
                    monsterRemaining: target.qty
                });
            });
        }

        return {
            creatureState,
            monsterState,
            rounds
        };
    }

    // Expose public API
    return {
        buildMonsters,
        calcUnitsNeeded,
        runSimulation,
        runManyToManySimulation
    };
})();

// Make global for inline scripts
window.CombatEngine = combatEngine;