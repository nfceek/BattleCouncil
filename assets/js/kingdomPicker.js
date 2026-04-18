document.addEventListener('DOMContentLoaded', () => {

    const input = document.getElementById('kingdomSearch');
    const resultsBox = document.getElementById('kingdomResults');
    const hidden = document.getElementById('kingdomID');

    if (!input) return; // safety for other pages

    input.addEventListener('input', async () => {
        const q = input.value.trim();

        if (q.length < 2) {
            resultsBox.innerHTML = '';
            return;
        }

        try {
            const res = await fetch(`/public/api/kingdom_search.php?q=${q}`);
            const data = await res.json();

            resultsBox.innerHTML = data.map(k =>
                `<div class="result-item" data-id="${k.kingdomID}">
                    Kingdom ${k.kingdomID}
                </div>`
            ).join('');

        } catch (err) {
            console.error('Kingdom search failed', err);
        }
    });

    resultsBox.addEventListener('click', e => {
        const item = e.target.closest('.result-item');
        if (!item) return;

        input.value = item.textContent;
        hidden.value = item.dataset.id;
        resultsBox.innerHTML = '';
    });

});