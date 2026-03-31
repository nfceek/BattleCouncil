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