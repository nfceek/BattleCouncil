// =========================
// BATTLE STATE ENGINE
// =========================

window.BattleState = (() => {

    const state = {
        difficulty: 'Rare',
        squadID: null,
        bonusStr: 100,
        bonusHlh: 100,
        playerLevel: 6,
        useCreatures: true,
        useFighters: true
    };

    function set(key, value) {
        state[key] = value;
        console.log('[STATE UPDATE]', key, value, state);
    }

    function get() {
        return state;
    }

    function setBulk(obj) {
        Object.assign(state, obj);
        console.log('[STATE BULK UPDATE]', state);
    }

    return {
        state,
        set,
        get,
        setBulk
    };

})();