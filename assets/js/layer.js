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

    radios.forEach(radio => {
        radio.addEventListener('click', async () => {

            const difficulty = radio.value;

            // disable while loading
            squadSelect.innerHTML = `<option>Loading...</option>`;
            squadSelect.disabled = true;

            try {
                const res = await fetch(`/api/getSquads.php?difficulty=${difficulty}`);
                const squads = await res.json();

                // rebuild dropdown
                let options = `<option value="">-- Choose Squad --</option>`;

                squads.forEach(s => {
                    options += `<option value="${s.squadID}">
                        ${s.name} (Lvl ${s.level})
                    </option>`;
                });

                squadSelect.innerHTML = options;
                squadSelect.disabled = false;

            } catch (err) {
                console.error('Failed to load squads', err);
                squadSelect.innerHTML = `<option>Error loading squads</option>`;
            }
        });
    });

});

document.addEventListener('DOMContentLoaded', () => {

    const radios = document.querySelectorAll('input[name="difficulty"]');

    radios.forEach(radio => {
        radio.addEventListener('click', () => {

            const url = new URL(window.location.href);
            url.searchParams.set('difficulty', radio.value);

            // reset squad when switching difficulty
            url.searchParams.delete('squadID');

            window.location.href = url.toString();
        });
    });

});