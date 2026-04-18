
document.addEventListener('DOMContentLoaded', () => {

  //initMobileMenu();

  if (document.body.classList.contains('page-monster-hunt')) {
    initMonsterHunt();
  }

/* =========================
   GLOBAL: Mobile Menu
========================= */
    const toggle = document.getElementById("menuToggle");
    const menu = document.getElementById("mobileMenu");

    if (!toggle || !menu) {
        console.warn("Menu elements not found");
        return;
    }

    toggle.addEventListener("click", () => {
        menu.classList.toggle("active");
    });
});


/* =========================
   PAGE: Monster Hunt
========================= */
function initMonsterHunt() {

  // Prevent double init
  if (window.__monsterHuntInit) return;
  window.__monsterHuntInit = true;

  /* =========================
     STATE
  ========================= */
  window.currentGroup = 0;

  if (!window.attackGroups || window.attackGroups.length === 0) {
    console.warn('No attackGroups found or empty');
  }

  /* =========================
     BUTTON STATE (NO DOM READY NEEDED)
  ========================= */
  const checkboxes = document.querySelectorAll(
    'input[name="useFighters"], input[name="useCreatures"]'
  );

  const button = document.getElementById('buildPlanBtn');
  const hint = document.getElementById('unitHint');

  function updateButtonState() {
    const isChecked = [...checkboxes].some(cb => cb.checked);
    if (button) button.disabled = !isChecked;
    if (hint) hint.style.display = isChecked ? 'none' : 'block';
  }

  updateButtonState();
  checkboxes.forEach(cb => cb.addEventListener('change', updateButtonState));


  /* =========================
     GLOBAL CLICK HANDLER
  ========================= */
  document.addEventListener('click', async function(e) {

    // CLEAR RESULTS
    if (e.target.matches('.clear-btn, [data-clear-results]')) {
      const results = document.getElementById('results');
      if (results) results.innerHTML = '';
      return;
    }

    // LOAD CLAN MEMBERS
    if (e.target.matches('.show-members-btn')) {
      const clanId = e.target.dataset.clanId;
      if (!clanId) return;

      const results = document.getElementById('results');
      if (!results) return;

      const originalText = e.target.textContent;
      e.target.textContent = 'Loading...';
      e.target.disabled = true;

      try {
        const res = await fetch('get_clan_members.php?clan_id=' + encodeURIComponent(clanId));
        if (!res.ok) throw new Error(res.status);
        results.innerHTML = await res.text();
      } catch (err) {
        console.error(err);
        results.innerHTML = '<p style="color:red">Error loading members</p>';
      } finally {
        e.target.textContent = originalText;
        e.target.disabled = false;
      }
    }

    // DELETE
    if (e.target.classList.contains('delete')) {
      const row = e.target.closest('.metric-row');
      if (!row) return;

      const id = row.dataset.id;
      if (!confirm("Delete this attack?")) return;

      try {
        await fetch("includes/delete_attack.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "id=" + encodeURIComponent(id)
        });
        row.remove();
      } catch (err) {
        console.error(err);
      }
    }

    // EDIT / SAVE
    if (e.target.classList.contains('edit')) {
      const row = e.target.closest('.metric-row');
      if (!row) return;

      const data = {
        id: row.dataset.id,
        squadID: row.querySelector('.squad')?.value || '',
        level: row.querySelector('.lvl')?.value || '',
        troops: row.querySelector('.troops')?.value || '',
        loss: row.querySelector('.loss')?.value || '',
        name: row.querySelector('.name')?.value || ''
      };

      try {
        const res = await fetch("includes/update_attack.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data)
        });

        console.log(await res.text());
        alert("Saved");
      } catch (err) {
        console.error(err);
      }
    }

  });

  /* =========================
     GROUP NAV
  ========================= */
  function safeRender(index) {
    if (typeof window.renderGroup === 'function') {
      window.renderGroup(index);
    } else {
      console.warn('renderGroup not defined');
    }
  }

  if (window.attackGroups?.length) {
    safeRender(0);
  }

  document.getElementById('groupPrev')?.addEventListener('click', () => {
    if (!window.attackGroups?.length) return;

    window.currentGroup =
      (window.currentGroup - 1 + window.attackGroups.length) % window.attackGroups.length;

    safeRender(window.currentGroup);
  });

  document.getElementById('groupNext')?.addEventListener('click', () => {
    if (!window.attackGroups?.length) return;

    window.currentGroup =
      (window.currentGroup + 1) % window.attackGroups.length;

    safeRender(window.currentGroup);
  });

}