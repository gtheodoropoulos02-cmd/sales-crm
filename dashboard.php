<?php
require_once 'config.php';
require_once 'layout.php';
$user = requireAdmin();
$page = $_GET['page'] ?? 'dashboard';
$ic   = icons();

$navItems = [
    ['url'=>'dashboard.php?page=dashboard',    'icon'=>$ic['home'],     'label'=>'Dashboard',           'active'=>$page==='dashboard'],
    ['url'=>'dashboard.php?page=applications', 'icon'=>$ic['list'],     'label'=>'Όλες οι Αιτήσεις',   'active'=>$page==='applications'],
    ['url'=>'dashboard.php?page=partners',     'icon'=>$ic['users'],    'label'=>'Συνεργάτες',          'active'=>$page==='partners'],
    ['url'=>'dashboard.php?page=settings',     'icon'=>$ic['settings'], 'label'=>'Ρυθμίσεις',           'active'=>$page==='settings'],
    ['url'=>'dashboard.php?page=infoportal',   'icon'=>$ic['info'],     'label'=>'InfoPortal',          'active'=>$page==='infoportal'],
];

$titles = ['dashboard'=>'Dashboard','applications'=>'Όλες οι Αιτήσεις','partners'=>'Συνεργάτες','settings'=>'Ρυθμίσεις','infoportal'=>'InfoPortal'];
$title  = $titles[$page] ?? 'Dashboard';

renderHead($title);
renderSidebar($user, $navItems);
?>
<div class="main">
  <div class="topbar">
    <div class="page-title"><?= htmlspecialchars($title) ?></div>
  </div>
  <div class="content">

<?php if ($page === 'dashboard'): ?>
<div class="welcome-banner">
  <div class="welcome-icon">🛡️</div>
  <div class="welcome-text"><h2>Admin Dashboard</h2><p>Γενική εικόνα όλων των αιτήσεων και συνεργατών</p></div>
</div>
<div class="stat-grid" id="stat-grid">
  <div class="stat-card blue"><div class="stat-label">Σύνολο Αιτήσεων</div><div class="stat-val" id="s-total">—</div><div class="stat-icon blue"><?= $ic['list'] ?></div></div>
  <div class="stat-card green"><div class="stat-label">Ενεργοποιημένες</div><div class="stat-val" id="s-active">—</div><div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><polyline points="20,6 9,17 4,12"/></svg></div></div>
  <div class="stat-card yellow"><div class="stat-label">Εκκρεμότητες</div><div class="stat-val" id="s-pending">—</div><div class="stat-icon yellow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg></div></div>
  <div class="stat-card red"><div class="stat-label">Ενεργοί Συνεργάτες</div><div class="stat-val" id="s-partners">—</div><div class="stat-icon red"><?= $ic['users'] ?></div></div>
</div>
<div class="card">
  <div class="card-header"><div class="card-title">Πρόσφατες Αιτήσεις</div><a href="dashboard.php?page=applications" class="btn btn-ghost btn-sm">Δες όλες →</a></div>
  <table><thead><tr><th>ID</th><th>Συνεργάτης</th><th>Πελάτης</th><th>Πρόγραμμα</th><th>Ημ/νία</th><th>Στάτους</th><th></th></tr></thead>
  <tbody id="recent-tbody"><tr><td colspan="7" style="text-align:center;color:var(--text3);padding:30px">Φόρτωση...</td></tr></tbody></table>
</div>
<div class="card">
  <div class="card-header"><div class="card-title">Ανά Συνεργάτη</div></div>
  <table><thead><tr><th>Συνεργάτης</th><th>Σύνολο</th><th>Ενεργοποιημένες</th><th>Εκκρεμότητες</th><th>Προς Έλεγχο</th></tr></thead>
  <tbody id="partner-stats-tbody"></tbody></table>
</div>

<?php elseif ($page === 'applications'): ?>
<div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap">
  <div class="search-bar" style="flex:1;min-width:200px">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;color:var(--text3)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="text" id="search-q" placeholder="Αναζήτηση..." oninput="filterApps()">
  </div>
  <select id="filter-status" onchange="filterApps()" style="padding:8px 12px;border-radius:8px;background:var(--bg3);border:1px solid var(--border);color:var(--text);font-family:inherit;font-size:13px">
    <option value="">Όλα τα στάτους</option>
  </select>
  <select id="filter-partner" onchange="filterApps()" style="padding:8px 12px;border-radius:8px;background:var(--bg3);border:1px solid var(--border);color:var(--text);font-family:inherit;font-size:13px">
    <option value="">Όλοι οι συνεργάτες</option>
  </select>
</div>
<div class="card">
  <div class="card-header"><div class="card-title" id="apps-title">Αιτήσεις</div></div>
  <div style="overflow-x:auto">
  <table><thead><tr><th>ID</th><th>Συνεργάτης</th><th>Πελάτης</th><th>ΑΦΜ</th><th>Πρόγραμμα</th><th>Ημ/νία</th><th>Στάτους</th><th></th></tr></thead>
  <tbody id="apps-tbody"></tbody></table>
  </div>
</div>

<?php elseif ($page === 'partners'): ?>
<div style="display:flex;justify-content:flex-end;margin-bottom:16px">
  <button class="btn btn-primary" onclick="openAddPartner()">+ Νέος Συνεργάτης</button>
</div>
<div class="card">
  <div class="card-header"><div class="card-title" id="partners-title">Συνεργάτες</div></div>
  <table><thead><tr><th>Όνομα</th><th>Username</th><th>Email</th><th>Τηλέφωνο</th><th>Αιτήσεις</th><th>Κατάσταση</th><th></th></tr></thead>
  <tbody id="partners-tbody"></tbody></table>
</div>

<?php elseif ($page === 'settings'): ?>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
  <div>
    <div class="card">
      <div class="card-header"><div class="card-title">Κατηγορίες</div></div>
      <div class="card-body">
        <div class="tag-manager" id="cats-list"></div>
        <div class="add-tag-row"><input type="text" id="new-cat" placeholder="Νέα κατηγορία..."><button class="btn btn-primary btn-sm" onclick="addCategory()">+ Προσθήκη</button></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><div class="card-title">Πάροχοι</div></div>
      <div class="card-body">
        <div class="form-group"><label>Κατηγορία</label><select id="sel-cat-prov" onchange="loadProviders()"></select></div>
        <div class="tag-manager" id="provs-list"></div>
        <div class="add-tag-row"><input type="text" id="new-prov" placeholder="Νέος πάροχος..."><button class="btn btn-primary btn-sm" onclick="addProvider()">+ Προσθήκη</button></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><div class="card-title">Προϊόντα</div></div>
      <div class="card-body">
        <div class="form-group"><label>Πάροχος</label><select id="sel-prov-prod" onchange="loadProducts()"></select></div>
        <div class="tag-manager" id="prods-list"></div>
        <div class="add-tag-row"><input type="text" id="new-prod" placeholder="Νέο προϊόν..."><button class="btn btn-primary btn-sm" onclick="addProduct()">+ Προσθήκη</button></div>
      </div>
    </div>
  </div>
  <div>
    <div class="card">
      <div class="card-header"><div class="card-title">Στάτους Αιτήσεων</div></div>
      <div class="card-body">
        <div class="tag-manager" id="statuses-list"></div>
        <div class="add-tag-row" style="margin-top:8px">
          <input type="text" id="new-status" placeholder="Νέο στάτους...">
          <select id="new-status-color" style="width:110px">
            <option value="gray">⬜ Γκρι</option><option value="blue">🟦 Μπλε</option>
            <option value="green">🟩 Πράσινο</option><option value="yellow">🟨 Κίτρινο</option><option value="red">🟥 Κόκκινο</option>
          </select>
          <button class="btn btn-primary btn-sm" onclick="addStatus()">+ Προσθήκη</button>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><div class="card-title">InfoPortal — Εταιρείες</div><button class="btn btn-primary btn-sm" onclick="openAddCompany()">+ Εταιρεία</button></div>
      <div class="card-body" id="info-settings-body"></div>
    </div>
    <div class="card">
      <div class="card-header"><div class="card-title">Αλλαγή Κωδικού Admin</div></div>
      <div class="card-body">
        <div class="form-group"><label>Νέος κωδικός</label><input type="password" id="new-admin-pass" placeholder="Τουλάχιστον 6 χαρακτήρες"></div>
        <button class="btn btn-primary" onclick="changeAdminPass()">Αλλαγή</button>
      </div>
    </div>
  </div>
</div>

<?php elseif ($page === 'infoportal'): ?>
<div style="margin-bottom:20px">
  <div class="search-bar" style="max-width:400px">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;color:var(--text3)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="text" id="info-search" placeholder="Αναζήτηση..." oninput="filterInfo()">
  </div>
</div>
<div id="info-panels"></div>
<?php endif; ?>

  </div><!-- .content -->
</div><!-- .main -->

<!-- Modal -->
<div id="modal-overlay" class="modal-overlay" style="display:none" onclick="maybeCloseModal(event)">
  <div class="modal" id="modal-box"></div>
</div>

<script>
const API = 'api.php';
async function api(action, params={}, method='GET'){
  let url=API+'?action='+action, opts={};
  if(method==='POST'){ opts={method:'POST',body:new URLSearchParams({action,...params})}; }
  else { Object.keys(params).forEach(k=>url+='&'+k+'='+encodeURIComponent(params[k])); }
  const r=await fetch(url,opts); return r.json();
}
async function apiForm(action, fd){ fd.append('action',action); const r=await fetch(API+'?action='+action,{method:'POST',body:fd}); return r.json(); }

function esc(s){ const d=document.createElement('div'); d.textContent=String(s??''); return d.innerHTML; }
function badge(name,color){ const m={blue:'badge-blue',green:'badge-green',yellow:'badge-yellow',red:'badge-red'}; return `<span class="badge ${m[color]||'badge-gray'}">${esc(name)}</span>`; }
function showModal(html){ document.getElementById('modal-box').innerHTML=html; document.getElementById('modal-overlay').style.display='flex'; }
function closeModal(){ document.getElementById('modal-overlay').style.display='none'; }
function maybeCloseModal(e){ if(e.target.id==='modal-overlay') closeModal(); }

let ALL_APPS=[], ALL_STATUSES=[], ALL_PARTNERS=[], ALL_CATS=[], ALL_PROVS=[], ALL_PRODS=[], INFO_DATA=[];

// ── Dashboard ──────────────────────────────────────────
<?php if($page==='dashboard'): ?>
async function loadDashboard(){
  const s=await api('get_stats');
  if(s.ok){
    document.getElementById('s-total').textContent=s.data.total;
    document.getElementById('s-active').textContent=s.data.active;
    document.getElementById('s-pending').textContent=s.data.pending;
    document.getElementById('s-partners').textContent=s.data.partners;
  }
  const r=await api('get_applications');
  if(r.ok){
    const apps=r.data.slice(0,8);
    document.getElementById('recent-tbody').innerHTML=apps.map(a=>`<tr>
      <td><span class="inline-tag">${esc(a.app_code)}</span></td>
      <td>${esc(a.partner_name)}</td>
      <td style="color:var(--text);font-weight:500">${esc(a.cust_firstname)} ${esc(a.cust_lastname)}</td>
      <td>${esc(a.provider_name)} · ${esc(a.product_name)}</td>
      <td>${esc(a.created_at?.split(' ')[0])}</td>
      <td>${badge(a.status_name,a.status_color)}</td>
      <td><button class="btn btn-ghost btn-xs" onclick="viewApp(${a.id})"><?= $ic['eye'] ?> Προβολή</button></td>
    </tr>`).join('')||'<tr><td colspan="7" style="text-align:center;color:var(--text3);padding:30px">Δεν υπάρχουν αιτήσεις</td></tr>';
    // per-partner stats
    const partners=await api('get_partners');
    if(partners.ok){
      document.getElementById('partner-stats-tbody').innerHTML=partners.data.map(p=>{
        const pa=r.data.filter(a=>a.partner_id==p.id);
        return`<tr><td style="color:var(--text);font-weight:500">${esc(p.name)}</td><td>${pa.length}</td><td style="color:var(--green)">${pa.filter(a=>a.status_name==='Ενεργοποιημένη').length}</td><td style="color:var(--yellow)">${pa.filter(a=>a.status_name==='Εκκρεμότητα').length}</td><td style="color:var(--accent)">${pa.filter(a=>a.status_name==='Προς Έλεγχο').length}</td></tr>`;
      }).join('');
    }
  }
}
loadDashboard();
<?php endif; ?>

// ── Applications ──────────────────────────────────────
<?php if($page==='applications'): ?>
async function loadApps(){
  const [ar,sr,pr]=await Promise.all([api('get_applications'),api('get_statuses'),api('get_partners')]);
  ALL_APPS=ar.ok?ar.data:[];
  ALL_STATUSES=sr.ok?sr.data:[];
  ALL_PARTNERS=pr.ok?pr.data:[];
  const sf=document.getElementById('filter-status');
  ALL_STATUSES.forEach(s=>{ const o=new Option(s.name,s.id); sf.append(o); });
  const pf=document.getElementById('filter-partner');
  ALL_PARTNERS.forEach(p=>{ const o=new Option(p.name,p.id); pf.append(o); });
  renderApps(ALL_APPS);
}
function filterApps(){
  const q=document.getElementById('search-q').value.toLowerCase();
  const st=document.getElementById('filter-status').value;
  const pt=document.getElementById('filter-partner').value;
  let apps=ALL_APPS;
  if(q) apps=apps.filter(a=>(a.cust_firstname+' '+a.cust_lastname).toLowerCase().includes(q)||String(a.cust_afm).includes(q)||a.app_code.toLowerCase().includes(q));
  if(st) apps=apps.filter(a=>String(a.status_id)===st);
  if(pt) apps=apps.filter(a=>String(a.partner_id)===pt);
  renderApps(apps);
}
function renderApps(apps){
  document.getElementById('apps-title').textContent='Αιτήσεις ('+apps.length+')';
  document.getElementById('apps-tbody').innerHTML=apps.map(a=>`<tr>
    <td><span class="inline-tag">${esc(a.app_code)}</span></td>
    <td>${esc(a.partner_name)}</td>
    <td style="color:var(--text);font-weight:500">${esc(a.cust_firstname)} ${esc(a.cust_lastname)}</td>
    <td>${esc(a.cust_afm)}</td>
    <td>${esc(a.provider_name)} · ${esc(a.product_name)}</td>
    <td>${esc(a.created_at?.split(' ')[0])}</td>
    <td>
      <select onchange="changeStatus(${a.id},this.value)" style="background:var(--bg3);border:1px solid var(--border);border-radius:6px;padding:4px 8px;font-size:12px;color:var(--text);font-family:inherit;cursor:pointer;outline:none">
        ${ALL_STATUSES.map(s=>`<option value="${s.id}" ${s.id==a.status_id?'selected':''}>${esc(s.name)}</option>`).join('')}
      </select>
    </td>
    <td><button class="btn btn-ghost btn-xs" onclick="viewApp(${a.id})"><?= $ic['eye'] ?> Προβολή</button></td>
  </tr>`).join('')||'<tr><td colspan="8" style="text-align:center;color:var(--text3);padding:40px">Δεν βρέθηκαν αιτήσεις</td></tr>';
}
async function changeStatus(appId, statusId){
  const r=await api('update_status',{app_id:appId,status_id:statusId},'POST');
  showNotif(r.ok?'Στάτους ενημερώθηκε':'Σφάλμα: '+r.msg, r.ok);
  if(r.ok){ const a=ALL_APPS.find(x=>x.id==appId); if(a){ a.status_id=parseInt(statusId); const s=ALL_STATUSES.find(x=>x.id==statusId); if(s){a.status_name=s.name;a.status_color=s.color;} } }
}
loadApps();
<?php endif; ?>

// ── Partners ──────────────────────────────────────────
<?php if($page==='partners'): ?>
async function loadPartners(){
  const [pr,ar]=await Promise.all([api('get_partners'),api('get_applications')]);
  ALL_APPS=ar.ok?ar.data:[];
  ALL_PARTNERS=pr.ok?pr.data:[];
  document.getElementById('partners-title').textContent='Συνεργάτες ('+ALL_PARTNERS.length+')';
  document.getElementById('partners-tbody').innerHTML=ALL_PARTNERS.map(p=>`<tr>
    <td style="color:var(--text);font-weight:600">${esc(p.name)}</td>
    <td><span class="inline-tag">${esc(p.username)}</span></td>
    <td>${esc(p.email)}</td>
    <td>${esc(p.phone)}</td>
    <td>${ALL_APPS.filter(a=>a.partner_id==p.id).length}</td>
    <td><span class="badge ${p.active=='1'?'badge-green':'badge-red'}">${p.active=='1'?'Ενεργός':'Ανενεργός'}</span></td>
    <td style="display:flex;gap:6px">
      <button class="btn btn-ghost btn-xs" onclick="togglePartner(${p.id})">${p.active=='1'?'Απενεργοποίηση':'Ενεργοποίηση'}</button>
      <button class="btn btn-ghost btn-xs" onclick="openChangePass(${p.id},'${esc(p.name)}')"><?= $ic['edit'] ?> Κωδικός</button>
    </td>
  </tr>`).join('');
}
function openAddPartner(){
  showModal(`<div class="modal-header"><div class="modal-title">Νέος Συνεργάτης</div><button class="modal-close" onclick="closeModal()">×</button></div>
<div class="modal-body">
  <div class="form-row"><div class="form-group"><label>Ονοματεπώνυμο <span class="req">*</span></label><input id="np-name" type="text" placeholder="Γιώργης Παπαδόπουλος"></div><div class="form-group"><label>Username <span class="req">*</span></label><input id="np-user" type="text" placeholder="giorgis123"></div></div>
  <div class="form-row"><div class="form-group"><label>Email</label><input id="np-email" type="email" placeholder="email@example.com"></div><div class="form-group"><label>Τηλέφωνο</label><input id="np-phone" type="text" placeholder="69..."></div></div>
  <div class="form-group"><label>Κωδικός <span class="req">*</span></label><input id="np-pass" type="password" placeholder="Κωδικός πρόσβασης"></div>
</div>
<div class="modal-footer"><button class="btn btn-ghost" onclick="closeModal()">Ακύρωση</button><button class="btn btn-primary" onclick="addPartner()">Δημιουργία</button></div>`);
}
async function addPartner(){
  const r=await api('add_partner',{name:document.getElementById('np-name').value,username:document.getElementById('np-user').value,password:document.getElementById('np-pass').value,email:document.getElementById('np-email').value,phone:document.getElementById('np-phone').value},'POST');
  if(r.ok){ closeModal(); showNotif('Ο συνεργάτης δημιουργήθηκε'); loadPartners(); }
  else showNotif('Σφάλμα: '+r.msg, false);
}
async function togglePartner(id){
  const r=await api('toggle_partner',{id},'POST');
  if(r.ok) loadPartners(); else showNotif('Σφάλμα',false);
}
function openChangePass(id, name){
  showModal(`<div class="modal-header"><div class="modal-title">Αλλαγή Κωδικού: ${esc(name)}</div><button class="modal-close" onclick="closeModal()">×</button></div>
<div class="modal-body"><div class="form-group"><label>Νέος κωδικός <span class="req">*</span></label><input id="cp-pass" type="password" placeholder="Νέος κωδικός..."></div></div>
<div class="modal-footer"><button class="btn btn-ghost" onclick="closeModal()">Ακύρωση</button><button class="btn btn-primary" onclick="changePass(${id})">Αλλαγή</button></div>`);
}
async function changePass(id){
  const r=await api('change_password',{id,password:document.getElementById('cp-pass').value},'POST');
  if(r.ok){ closeModal(); showNotif('Κωδικός αλλάχθηκε'); } else showNotif('Σφάλμα: '+r.msg,false);
}
loadPartners();
<?php endif; ?>

// ── Settings ──────────────────────────────────────────
<?php if($page==='settings'): ?>
async function loadSettings(){
  const [cr,sr,ir]=await Promise.all([api('get_categories'),api('get_statuses'),api('get_info_companies')]);
  ALL_CATS=cr.ok?cr.data:[];
  ALL_STATUSES=sr.ok?sr.data:[];
  INFO_DATA=ir.ok?ir.data:[];
  renderCats(); renderStatuses(); renderInfoSettings();
  if(ALL_CATS.length){ loadProvidersForSettings(); }
}
function renderCats(){
  document.getElementById('cats-list').innerHTML=ALL_CATS.map(c=>`<div class="tag">${esc(c.name)}<button class="tag-del" onclick="deleteCat(${c.id})">×</button></div>`).join('')||'<span style="color:var(--text3);font-size:12px">Δεν υπάρχουν κατηγορίες</span>';
  const sel=document.getElementById('sel-cat-prov');
  sel.innerHTML=ALL_CATS.map(c=>`<option value="${c.id}">${esc(c.name)}</option>`).join('');
}
async function addCategory(){ const n=document.getElementById('new-cat').value.trim(); if(!n) return; const r=await api('add_category',{name:n},'POST'); if(r.ok){ALL_CATS.push(r.data);renderCats();document.getElementById('new-cat').value='';showNotif('Προστέθηκε');} else showNotif(r.msg,false); }
async function deleteCat(id){ const r=await api('delete_category',{id},'POST'); if(r.ok){ALL_CATS=ALL_CATS.filter(x=>x.id!=id);renderCats();} }
async function loadProvidersForSettings(){
  const catId=document.getElementById('sel-cat-prov').value;
  if(!catId) return;
  const r=await api('get_providers',{category_id:catId});
  ALL_PROVS=r.ok?r.data:[];
  document.getElementById('provs-list').innerHTML=ALL_PROVS.map(p=>`<div class="tag">${esc(p.name)}<button class="tag-del" onclick="deleteProv(${p.id})">×</button></div>`).join('')||'<span style="color:var(--text3);font-size:12px">Δεν υπάρχουν πάροχοι</span>';
  const sel=document.getElementById('sel-prov-prod');
  sel.innerHTML=ALL_PROVS.map(p=>`<option value="${p.id}">${esc(p.name)}</option>`).join('');
  loadProductsForSettings();
}
const loadProviders=loadProvidersForSettings;
async function addProvider(){ const n=document.getElementById('new-prov').value.trim(); const catId=document.getElementById('sel-cat-prov').value; if(!n||!catId) return; const r=await api('add_provider',{category_id:catId,name:n},'POST'); if(r.ok){document.getElementById('new-prov').value='';loadProvidersForSettings();showNotif('Προστέθηκε');} else showNotif(r.msg,false); }
async function deleteProv(id){ const r=await api('delete_provider',{id},'POST'); if(r.ok) loadProvidersForSettings(); }
async function loadProductsForSettings(){
  const provId=document.getElementById('sel-prov-prod').value;
  if(!provId) return;
  const r=await api('get_products',{provider_id:provId});
  ALL_PRODS=r.ok?r.data:[];
  document.getElementById('prods-list').innerHTML=ALL_PRODS.map(p=>`<div class="tag">${esc(p.name)}<button class="tag-del" onclick="deleteProd(${p.id})">×</button></div>`).join('')||'<span style="color:var(--text3);font-size:12px">Δεν υπάρχουν προϊόντα</span>';
}
const loadProducts=loadProductsForSettings;
async function addProduct(){ const n=document.getElementById('new-prod').value.trim(); const provId=document.getElementById('sel-prov-prod').value; if(!n||!provId) return; const r=await api('add_product',{provider_id:provId,name:n},'POST'); if(r.ok){document.getElementById('new-prod').value='';loadProductsForSettings();showNotif('Προστέθηκε');} else showNotif(r.msg,false); }
async function deleteProd(id){ const r=await api('delete_product',{id},'POST'); if(r.ok) loadProductsForSettings(); }
function renderStatuses(){
  const m={blue:'badge-blue',green:'badge-green',yellow:'badge-yellow',red:'badge-red'};
  document.getElementById('statuses-list').innerHTML=ALL_STATUSES.map(s=>`<div class="tag"><span class="badge ${m[s.color]||'badge-gray'}" style="font-size:11px">${esc(s.name)}</span><button class="tag-del" onclick="deleteStatus(${s.id})">×</button></div>`).join('')||'<span style="color:var(--text3);font-size:12px">Δεν υπάρχουν στάτους</span>';
}
async function addStatus(){ const n=document.getElementById('new-status').value.trim(); const color=document.getElementById('new-status-color').value; if(!n) return; const r=await api('add_status',{name:n,color},'POST'); if(r.ok){ALL_STATUSES.push(r.data);renderStatuses();document.getElementById('new-status').value='';showNotif('Προστέθηκε');} else showNotif(r.msg,false); }
async function deleteStatus(id){ const r=await api('delete_status',{id},'POST'); if(r.ok){ALL_STATUSES=ALL_STATUSES.filter(x=>x.id!=id);renderStatuses();} }
function renderInfoSettings(){
  document.getElementById('info-settings-body').innerHTML=INFO_DATA.map(c=>`
<div style="background:var(--bg3);border-radius:8px;padding:12px;margin-bottom:10px">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
    <span style="font-weight:700;font-size:13px">${esc(c.name)}</span>
    <div style="display:flex;gap:6px">
      <button class="btn btn-primary btn-xs" onclick="openAddPlan(${c.id},'${esc(c.name)}')">+ Πρόγραμμα</button>
      <button class="btn btn-danger btn-xs" onclick="deleteCompany(${c.id})">Διαγραφή</button>
    </div>
  </div>
  ${c.plans.map(p=>`<div style="display:flex;align-items:center;justify-content:space-between;padding:6px 0;border-top:1px solid var(--border)"><span style="font-size:12px;color:var(--text2)">${esc(p.name)} <span style="color:var(--accent)">${esc(p.price)}</span></span><button class="btn btn-danger btn-xs" onclick="deletePlan(${p.id})">×</button></div>`).join('')}
</div>`).join('')||'<span style="color:var(--text3);font-size:13px">Δεν υπάρχουν εταιρείες</span>';
}
function openAddCompany(){
  showModal(`<div class="modal-header"><div class="modal-title">Νέα Εταιρεία InfoPortal</div><button class="modal-close" onclick="closeModal()">×</button></div>
<div class="modal-body"><div class="form-group"><label>Όνομα Εταιρείας <span class="req">*</span></label><input id="ic-name" type="text" placeholder="π.χ. Nova"></div></div>
<div class="modal-footer"><button class="btn btn-ghost" onclick="closeModal()">Ακύρωση</button><button class="btn btn-primary" onclick="addCompany()">Αποθήκευση</button></div>`);
}
async function addCompany(){ const n=document.getElementById('ic-name').value.trim(); if(!n) return; const r=await api('add_info_company',{name:n},'POST'); if(r.ok){ closeModal(); const ir=await api('get_info_companies'); INFO_DATA=ir.ok?ir.data:[]; renderInfoSettings(); showNotif('Εταιρεία προστέθηκε'); } else showNotif(r.msg,false); }
function openAddPlan(compId, compName){
  showModal(`<div class="modal-header"><div class="modal-title">Νέο Πρόγραμμα: ${esc(compName)}</div><button class="modal-close" onclick="closeModal()">×</button></div>
<div class="modal-body">
  <div class="form-group"><label>Πρόγραμμα <span class="req">*</span></label><input id="ip-name" type="text" placeholder="π.χ. Fiber 200"></div>
  <div class="form-group"><label>Τιμή <span class="req">*</span></label><input id="ip-price" type="text" placeholder="π.χ. 24,90€/μήνα"></div>
  <div class="form-group"><label>Δικαιολογητικά (ένα ανά γραμμή, * για υποχρεωτικό)</label><textarea id="ip-docs" placeholder="Ταυτότητα*\nΒεβαίωση ΑΦΜ"></textarea></div>
</div>
<div class="modal-footer"><button class="btn btn-ghost" onclick="closeModal()">Ακύρωση</button><button class="btn btn-primary" onclick="addPlan(${compId})">Αποθήκευση</button></div>`);
}
async function addPlan(compId){ const r=await api('add_info_plan',{company_id:compId,name:document.getElementById('ip-name').value,price:document.getElementById('ip-price').value,docs:document.getElementById('ip-docs').value},'POST'); if(r.ok){ closeModal(); const ir=await api('get_info_companies'); INFO_DATA=ir.ok?ir.data:[]; renderInfoSettings(); showNotif('Πρόγραμμα προστέθηκε'); } else showNotif(r.msg,false); }
async function deletePlan(id){ if(!confirm('Να διαγραφεί το πρόγραμμα;')) return; const r=await api('delete_info_plan',{id},'POST'); if(r.ok){ const ir=await api('get_info_companies'); INFO_DATA=ir.ok?ir.data:[]; renderInfoSettings(); } }
async function deleteCompany(id){ if(!confirm('Να διαγραφεί η εταιρεία και όλα τα προγράμματά της;')) return; const r=await api('delete_info_company',{id},'POST'); if(r.ok){ const ir=await api('get_info_companies'); INFO_DATA=ir.ok?ir.data:[]; renderInfoSettings(); } }
async function changeAdminPass(){ const p=document.getElementById('new-admin-pass').value; if(p.length<6){showNotif('Τουλάχιστον 6 χαρακτήρες',false);return;} const r=await api('change_password',{id:<?= $user['id'] ?>,password:p},'POST'); showNotif(r.ok?'Κωδικός αλλάχθηκε':'Σφάλμα: '+r.msg,r.ok); if(r.ok) document.getElementById('new-admin-pass').value=''; }
loadSettings();
<?php endif; ?>

// ── InfoPortal ─────────────────────────────────────────
<?php if($page==='infoportal'): ?>
async function loadInfo(){
  const r=await api('get_info_companies');
  INFO_DATA=r.ok?r.data:[];
  renderInfo(INFO_DATA);
}
function renderInfo(data){
  const p=document.getElementById('info-panels');
  if(!data.length){ p.innerHTML='<div style="text-align:center;padding:60px;color:var(--text3)">Δεν υπάρχουν προγράμματα ακόμα</div>'; return; }
  p.innerHTML=data.map(c=>`<div class="info-company">
  <div class="company-header">
    <div class="user-avatar" style="width:38px;height:38px;border-radius:10px;font-size:13px;flex-shrink:0">${esc(c.name.substring(0,2).toUpperCase())}</div>
    <div class="company-name">${esc(c.name)}</div>
    <span style="margin-left:auto;font-size:12px;color:var(--text3)">${c.plans.length} προγράμματα</span>
  </div>
  <div class="company-body"><div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px">
  ${c.plans.map(pl=>{const docs=(pl.docs||'').split('\n').filter(Boolean);return`<div class="plan-card"><div class="plan-name">${esc(pl.name)}</div><div class="plan-price">${esc(pl.price)}</div>${docs.map(d=>`<div class="plan-doc-item"><span class="${d.includes('*')?'req-doc':''}">${esc(d)}</span></div>`).join('')}</div>`;}).join('')}
  </div></div></div>`).join('');
}
function filterInfo(){ const q=document.getElementById('info-search').value.toLowerCase(); renderInfo(INFO_DATA.filter(c=>c.name.toLowerCase().includes(q)||c.plans.some(p=>p.name.toLowerCase().includes(q)))); }
loadInfo();
<?php endif; ?>

// ── View Application modal (shared) ──────────────────
async function viewApp(id){
  const r=await api('get_application',{id});
  if(!r.ok){ showNotif('Σφάλμα: '+r.msg,false); return; }
  const a=r.data, docs=a.documents||[];
  const docTypes={identity:'Ταυτότητα *',logariasmos:'Λογαριασμός/ΔΕΚΟ',bebaiosi:'Βεβαίωση ΑΦΜ',extra:'Επιπλέον'};
  const statusSel=ALL_STATUSES.length?`<select onchange="changeStatusFromModal(${a.id},this.value)" style="background:var(--bg3);border:1px solid var(--border);border-radius:6px;padding:5px 10px;font-size:13px;color:var(--text);font-family:inherit;cursor:pointer;outline:none">
    ${ALL_STATUSES.map(s=>`<option value="${s.id}" ${s.id==a.status_id?'selected':''}>${esc(s.name)}</option>`).join('')}
  </select>`:badge(a.status_name,a.status_color);
  function row(k,v){ return v?`<div class="detail-row"><div class="detail-key">${k}</div><div class="detail-val">${esc(v)}</div></div>`:''; }
  const docHtml=Object.entries(docTypes).map(([t,label])=>{
    const grouped=docs.filter(d=>d.doc_type===t);
    if(!grouped.length) return t==='identity'?`<div class="doc-thumb-wrap"><div class="doc-missing">✗ ${label}</div><div class="doc-label">${label}</div></div>`:'';
    return grouped.map(d=>{
      const url='uploads/'+d.stored_name;
      const isImg=/\.(jpg|jpeg|png|gif|webp)$/i.test(d.stored_name);
      return`<div class="doc-thumb-wrap">${isImg?`<img class="doc-thumb" src="${esc(url)}" alt="${esc(d.original_name)}" onclick="openLightbox('${esc(url)}')" loading="lazy">`:
        `<a href="${esc(url)}" target="_blank" class="doc-missing" style="text-decoration:none;color:var(--accent)">📄 PDF<br>${esc(d.original_name)}</a>`}
      <div class="doc-label">${esc(docTypes[d.doc_type]||d.doc_type)}<br><small style="color:var(--text3)">${esc(d.original_name)}</small></div></div>`;
    }).join('');
  }).join('');
  showModal(`<div class="modal-header"><div class="modal-title">Αίτηση ${esc(a.app_code)}</div><button class="modal-close" onclick="closeModal()">×</button></div>
<div class="modal-body">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">${statusSel}<span style="font-size:12px;color:var(--text3)">${esc(a.created_at?.split(' ')[0])} · ${esc(a.category_name)} · ${esc(a.provider_name)} · ${esc(a.product_name)}</span></div>
  <div class="detail-section"><div class="detail-section-title">Στοιχεία Πελάτη</div>
    ${row('Ονοματεπώνυμο',a.cust_firstname+' '+a.cust_lastname)}${row('Πατρώνυμο',a.cust_patronimo)}${row('Α.Δ.Τ',a.cust_adt)}${row('ΑΦΜ',a.cust_afm)}${row('Δ.Ο.Υ',a.cust_doy)}${row('Ημ. Γέννησης',a.cust_birthdate)}${row('Νομός',a.cust_nomos)}${row('Πόλη / Περιοχή',a.cust_poli+' '+a.cust_periochi)}${row('Διεύθυνση',a.cust_address+' '+a.cust_number+', Τ.Κ '+a.cust_tk)}${row('Τηλέφωνο / Κινητό',a.cust_phone+' / '+a.cust_kinito)}${row('Email',a.cust_email)}${row('Κατηγορία',a.customer_type)}${row('Σύνδεση',a.connection_type)}${row('eBill',a.ebill)}</div>
  <div class="detail-section"><div class="detail-section-title">Υπεύθυνος Επικοινωνίας</div>
    ${row('Ονοματεπώνυμο',a.contact_name+' '+a.contact_lastname)}${row('Α.Δ.Τ',a.contact_adt)}${row('Τηλ./Κινητό',a.contact_phone+' / '+a.contact_kinito)}${row('Email',a.contact_email)}</div>
  <div class="detail-section"><div class="detail-section-title">Στοιχεία Προγράμματος</div>
    ${row('Πακέτο',a.prog_paketo)}${row('Τιμή',a.prog_timi?a.prog_timi+'€':'')}${row('Τύπος Λογαριασμού',a.prog_anypsos)}${row('Τηλ. Ενεργοποίησης',a.prog_phone_activation)}${row('SIM',a.prog_sim_activation)}${row('SIM Παραλήφθηκε',a.prog_sim_received)}${row('Τρόπος Υπογραφής',a.prog_signature_way)}${row('Consent 1',a.prog_consent1)}${row('Consent 2',a.prog_consent2)}</div>
  ${a.notes?`<div class="detail-section"><div class="detail-section-title">Σημειώσεις</div><div style="font-size:13px;color:var(--text2)">${esc(a.notes)}</div></div>`:''}
  <div class="detail-section"><div class="detail-section-title">Δικαιολογητικά</div><div class="doc-grid">${docHtml||'<span style="color:var(--text3);font-size:13px">Δεν έχουν ανέβει δικαιολογητικά</span>'}</div></div>
</div>`);
}
async function changeStatusFromModal(appId, statusId){
  const r=await api('update_status',{app_id:appId,status_id:statusId},'POST');
  showNotif(r.ok?'Στάτους ενημερώθηκε':'Σφάλμα',r.ok);
  <?php if($page==='applications'): ?>if(r.ok){ const a=ALL_APPS.find(x=>x.id==appId); if(a){ a.status_id=parseInt(statusId); const s=ALL_STATUSES.find(x=>x.id==statusId); if(s){a.status_name=s.name;a.status_color=s.color;} renderApps(ALL_APPS); } }<?php endif; ?>
}

// Preload statuses for modal
<?php if($page!=='settings'): ?>
api('get_statuses').then(r=>{ ALL_STATUSES=r.ok?r.data:[]; });
<?php endif; ?>
</script>
<?php renderFoot(); ?>
