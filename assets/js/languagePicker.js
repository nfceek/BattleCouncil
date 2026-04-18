document.addEventListener('DOMContentLoaded', async () => {

    const select = document.getElementById('languageSelect');
    const input = document.getElementById('languageNew');

    if (!select) return;

    // LOAD LANGUAGES
    const res = await fetch(`/public/api/languages.php`);
    const data = await res.json();

    data.forEach(lang => {
        const opt = document.createElement('option');
        opt.value = lang.name;
        opt.textContent = lang.name;
        select.insertBefore(opt, select.lastElementChild);
    });

    // HANDLE "ADD NEW"
    select.addEventListener('change', () => {
        if (select.value === 'other') {
            input.style.display = 'block';
            input.focus();
        } else {
            input.style.display = 'none';
        }
    });

    // SAVE NEW LANGUAGE
    input.addEventListener('blur', async () => {
        const name = input.value.trim();
        if (!name) return;

        const res = await fetch(`/public/api/language_add.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name })
        });

        const result = await res.json();

        if (result.success) {
            const opt = document.createElement('option');
            opt.value = result.name;
            opt.textContent = result.name;
            select.insertBefore(opt, select.lastElementChild);

            select.value = result.name;
            input.style.display = 'none';
        }
    });

});