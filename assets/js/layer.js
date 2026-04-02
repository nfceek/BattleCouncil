// layer.js

document.addEventListener('DOMContentLoaded', () => {

    const toggle = document.querySelector('input[name="useMtdFighters"]');
    const radios = document.querySelectorAll('input[name="mtdLevel"]');

    if (!toggle || radios.length === 0) return;

    function updateMountedLevels() {
        radios.forEach(r => {
            r.disabled = !toggle.checked;
        });
    }

    toggle.addEventListener('change', updateMountedLevels);

    // Initial state
    updateMountedLevels();
});

document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.unit-group').forEach(group => {

        const select = group.querySelector('.unit-select');
        const radios = group.querySelectorAll('input[type="radio"]');

        function update() {
            const enabled = select.value !== '';
            radios.forEach(r => r.disabled = !enabled);
        }

        select.addEventListener('change', update);
        update();
    });

});

document.addEventListener('DOMContentLoaded', () => {

    const radios = document.querySelectorAll('input[name="difficulty"]');
    const squadSelect = document.getElementById('squadSelect');
    const generateBtn = document.getElementById('generatePlanBtn');

    radios.forEach(radio => {
        radio.addEventListener('click', async () => {

            const difficulty = radio.value;

            // lock UI while loading
            squadSelect.disabled = true;
            squadSelect.innerHTML = `<option>Loading...</option>`;
            generateBtn.disabled = true;

            try {
                const res = await fetch(`/api/getSquads.php?difficulty=${difficulty}`);
                const squads = await res.json();

                let options = `<option value="">-- Choose Squad --</option>`;

                squads.forEach(s => {
                    options += `<option value="${s.squadID}">
                        ${s.name} (Lvl ${s.level})
                    </option>`;
                });

                squadSelect.innerHTML = options;
                squadSelect.disabled = false;

            } catch (err) {
                squadSelect.innerHTML = `<option>Error loading squads</option>`;
                console.error(err);
            }
        });
    });

    squadSelect.addEventListener('change', () => {
        generateBtn.disabled = !squadSelect.value;
    });

});

document.addEventListener('DOMContentLoaded', () => {

    const radios = document.querySelectorAll('input[name="difficulty"]');
    const squadSelect = document.getElementById('squadSelect');
    const generateBtn = document.getElementById('generatePlanBtn');

    radios.forEach(radio => {
        radio.addEventListener('click', async () => {

            const difficulty = radio.value;

            squadSelect.disabled = true;
            squadSelect.innerHTML = `<option>Loading...</option>`;

            const res = await fetch(`/api/getSquads.php?difficulty=${difficulty}`);
            const squads = await res.json();

            let options = `<option value="">-- Choose Squad --</option>`;

            squads.forEach(s => {
                options += `<option value="${s.squadID}">
                    ${s.name} (Lvl ${s.level})
                </option>`;
            });

            squadSelect.innerHTML = options;
            squadSelect.disabled = false;

            generateBtn.disabled = true;
        });
    });

    squadSelect.addEventListener('change', () => {
        generateBtn.disabled = !squadSelect.value;
    });

});

document.querySelectorAll('.layer-block').forEach(block => {

    const round1 = block.querySelector('select[name*="[unit1]"]');
    const round2 = block.querySelector('.round2');
    const radios2 = block.querySelectorAll('input[name*="[level2]"]');

    if (!round1 || !round2) return;

    round1.addEventListener('change', () => {

        const enabled = !!round1.value;

        round2.disabled = !enabled;
        radios2.forEach(r => r.disabled = !enabled);
    });

});