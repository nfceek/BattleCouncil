// /assets/js/app.js

document.addEventListener('DOMContentLoaded', () => {

  initMobileMenu();

  // Page-specific hooks (safe expansion point)
  const body = document.body;

  if (body.classList.contains('page-monster-hunt')) {
    initMonsterHunt();
  }

});


/* =========================
   GLOBAL: Mobile Menu
========================= */
function initMobileMenu() {
  const toggle = document.getElementById('menuToggle');
  const menu = document.getElementById('mobileMenu');

  if (!toggle || !menu) return;

  toggle.addEventListener('click', () => {
    menu.classList.toggle('active');
  });
}


/* =========================
   PAGE: Monster Hunt
========================= */
function initMonsterHunt() {

  document.addEventListener('click', async function(e) {
    // Clear button (in results or in clan list)
    if (e.target.matches('.clear-btn') || e.target.matches('[data-clear-results]')) {
      const results = document.getElementById('results');
      if (results) results.innerHTML = '';
      return;
    }

    // Show clan members (delegated handler for dynamic content)
    if (e.target.matches('.show-members-btn')) {
      const clanId = e.target.dataset.clanId;
      if (!clanId) return;

      const results = document.getElementById('results');
      if (!results) {
        console.error('#results element not found');
        return;
      }

      // Visual feedback
      const originalText = e.target.textContent;
      e.target.textContent = 'Loading...';
      e.target.disabled = true;

      try {
        const res = await fetch('get_clan_members.php?clan_id=' + encodeURIComponent(clanId));
        if (!res.ok) throw new Error('Network response not OK: ' + res.status);
        const html = await res.text();
        results.innerHTML = html;
      } catch (err) {
        console.error(err);
        results.innerHTML = '<p style="color:red">Error loading members. See console.</p>';
      } finally {
        e.target.textContent = originalText;
        e.target.disabled = false;
      }
    }
  });

  if (!window.attackGroups || window.attackGroups.length === 0) {
      console.warn('No attackGroups found or empty');
      return;
    }

  window.currentGroup = 0;

  //console.log('Attack Groups:', window.attackGroups);

  document.addEventListener('DOMContentLoaded', () => {

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

    });

    document.querySelectorAll('.add-attack').forEach(btn=>{

    btn.addEventListener('click',function(){

    const type=this.dataset.type;
    const table=this.closest('.metric-card').querySelector('.metric-table');

      fetch("includes/add_attack.php",{
      method:"POST",
      headers:{
      "Content-Type":"application/json"
      },
      body:JSON.stringify({type:type})
      })
      .then(r=>r.json())
      .then(data=>{

      const row=document.createElement("div");
      row.className="metric-row";
      row.dataset.id=data.id;

      row.innerHTML=`
        <select class="lvl"></select>

        <span class="type">${type=='Common'?'C':'R'}</span>

        <select class="squad">
        <option value="">Select Squad</option>
          <?= $squadOptions ?>
        </select>

        <input class="troops" value="0">

        <select class="unit">
        <option value="1">Ruby Golem</option>
        </select>

        <select class="capt">
        <option value="1">Capt 1</option>
        </select>

        <span class="actions">
        <span class="edit">✏️</span>
        <span class="delete">✖</span>
        </span>
      `;
      const lvlSelect = row.querySelector('.lvl');

        for(let i=1;i<=40;i++){
          const opt=document.createElement('option');
          opt.value=i;
          opt.textContent=i;
          lvlSelect.appendChild(opt);
        }
        table.appendChild(row);

        });

      });

    });

    document.addEventListener('change',function(e){

      if(e.target.classList.contains('squad')){

      const row=e.target.closest('.metric-row');
      const lvl=row.querySelector('.lvl');

      const squadLevel=e.target.options[e.target.selectedIndex].dataset.level;

      if(squadLevel){
        lvl.value=squadLevel;
        }

      }

    });

    // init first creature
    if(attackGroups.length) renderGroup(0);

    // 🔹 BUTTONS
    document.getElementById('groupPrev')?.addEventListener('click', () => {
        currentGroup = (currentGroup - 1 + attackGroups.length) % attackGroups.length;
        renderGroup(currentGroup);
    });

    document.getElementById('groupNext')?.addEventListener('click', () => {
        currentGroup = (currentGroup + 1) % attackGroups.length;
        renderGroup(currentGroup);
    });

    /* -----------------------------
    GLOBAL CLICK HANDLER
    handles edit/delete on new rows
    ------------------------------*/

    document.addEventListener('click',function(e){

    /* DELETE */

    if(e.target.classList.contains('delete')){

    const row=e.target.closest('.metric-row');
    const id=row.dataset.id;

    if(!confirm("Delete this attack?")) return;

    fetch("includes/delete_attack.php",{
    method:"POST",
    headers:{
    "Content-Type":"application/x-www-form-urlencoded"
    },
    body:"id="+id
    })
    .then(()=>row.remove());

    }

    /* EDIT / SAVE */

    if(e.target.classList.contains('edit')){

      const row=e.target.closest('.metric-row');

      const data={
        id:row.dataset.id,
        squadID:row.querySelector('.squad').value,
        level:row.querySelector('.lvl').value,
        troops:row.querySelector('.troops').value,
        loss:row.querySelector('.loss').value  ,
        name:row.querySelector('.name').value      
      };

      fetch("includes/update_attack.php",{
      method:"POST",
      headers:{
      "Content-Type":"application/json"
      },
      body:JSON.stringify(data)
      })
      .then(r=>r.text())
      .then(res=>{
      console.log(res);
      alert("Saved");
      });

    }

    });

}