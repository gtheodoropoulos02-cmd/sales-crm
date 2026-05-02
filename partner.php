<?php
require_once 'config.php';
require_once 'layout.php';
$user = requireLogin();
if ($user['role'] === 'admin') { header('Location: dashboard.php'); exit; }

$page = $_GET['page'] ?? 'dashboard';
$ic   = icons();

$navItems = [
    ['url'=>'partner.php?page=dashboard',    'icon'=>$ic['home'], 'label'=>'Dashboard',        'active'=>$page==='dashboard'],
    ['url'=>'partner.php?page=applications', 'icon'=>$ic['list'], 'label'=>'Οι Αιτήσεις μου', 'active'=>$page==='applications'],
    ['url'=>'partner.php?page=new-app',      'icon'=>$ic['plus'], 'label'=>'Νέα Αίτηση',       'active'=>$page==='new-app'],
    ['url'=>'partner.php?page=infoportal',   'icon'=>$ic['info'], 'label'=>'InfoPortal',        'active'=>$page==='infoportal'],
];

$titles=['dashboard'=>'Dashboard','applications'=>'Οι Αιτήσεις μου','new-app'=>'Νέα Αίτηση','infoportal'=>'InfoPortal'];
$title=$titles[$page]??'Dashboard';

renderHead($title);
renderSidebar($user, $navItems);
?>
<div class="main">
  <div class="topbar">
    <div class="page-title"><?= htmlspecialchars($title) ?></div>
  </div>
  <div class="content">

<?php if($page==='dashboard'): ?>
<div class="welcome-banner">
  <div class="welcome-icon">👋</div>
  <div class="welcome-text">
    <h2>Καλωσόρισες, <?= htmlspecialchars(explode(' ',$user['name'])[0]) ?>!</h2>
    <p>Εδώ έχεις μια σύνοψη της δραστηριότητάς σου</p>
  </div>
</div>
<div class="stat-grid">
  <div class="stat-card blue"><div class="stat-label">Σύνολο Αιτήσεων</div><div class="stat-val" id="s-total">—</div><div class="stat-icon blue"><?= $ic['list'] ?></div></div>
  <div class="stat-card green"><div class="stat-label">Ενεργοποιημένες</div><div class="stat-val" id="s-active">—</div><div class="stat-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><polyline points="20,6 9,17 4,12"/></svg></div></div>
  <div class="stat-card yellow"><div class="stat-label">Εκκρεμότητες</div><div class="stat-val" id="s-pending">—</div><div class="stat-icon yellow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg></div></div>
  <div class="stat-card red"><div class="stat-label">Προς Έλεγχο</div><div class="stat-val" id="s-review">—</div><div class="stat-icon red"><?= $ic['eye'] ?></div></div>
</div>
<div class="card">
  <div class="card-header"><div class="card-title">Πρόσφατες Αιτήσεις</div><a href="partner.php?page=applications" class="btn btn-ghost btn-sm">Δες όλες →</a></div>
  <table><thead><tr><th>ID</th><th>Πελάτης</th><th>Πρόγραμμα</th><th>Ημ/νία</th><th>Στάτους</th></tr></thead>
  <tbody id="recent-tbody"><tr><td colspan="5" style="text-align:center;color:var(--text3);padding:30px">Φόρτωση...</td></tr></tbody></table>
</div>

<?php elseif($page==='applications'): ?>
<div class="card">
  <div class="card-header"><div class="card-title" id="apps-title">Οι Αιτήσεις μου</div><a href="partner.php?page=new-app" class="btn btn-primary btn-sm">+ Νέα Αίτηση</a></div>
  <div style="overflow-x:auto">
  <table><thead><tr><th>Ημ/νία</th><th>ID</th><th>Πελάτης</th><th>ΑΦΜ</th><th>Πρόγραμμα</th><th>Στάτους</th><th></th></tr></thead>
  <tbody id="apps-tbody"><tr><td colspan="7" style="text-align:center;color:var(--text3);padding:30px">Φόρτωση...</td></tr></tbody></table>
  </div>
</div>

<?php elseif($page==='new-app'):
  $step = (int)($_GET['step'] ?? 1);
  // Load any saved draft from session
  $draft = $_SESSION['app_draft'] ?? [];
?>

<?php if($step===1): ?>
<div class="step-wizard">
  <div class="step active"><div class="step-num">1</div><div class="step-label">Επιλογή Προγράμματος</div></div>
  <div class="step-line"></div>
  <div class="step"><div class="step-num">2</div><div class="step-label">Στοιχεία Πελάτη</div></div>
  <div class="step-line"></div>
  <div class="step"><div class="step-num">3</div><div class="step-label">Δικαιολογητικά</div></div>
</div>
<div class="card" style="max-width:500px">
  <div class="card-header"><div class="card-title">Επιλογή Κατηγορίας & Προϊόντος</div></div>
  <div class="card-body">
    <div class="form-group"><label>Κατηγορία <span class="req">*</span></label><select id="sel-cat" onchange="loadProviders()"><option value="">Επιλέξτε κατηγορία...</option></select></div>
    <div class="form-group" id="prov-group" style="display:none"><label>Πάροχος <span class="req">*</span></label><select id="sel-prov" onchange="loadProducts()"><option value="">Επιλέξτε πάροχο...</option></select></div>
    <div class="form-group" id="prod-group" style="display:none"><label>Προϊόν <span class="req">*</span></label><select id="sel-prod"><option value="">Επιλέξτε προϊόν...</option></select></div>
    <div style="display:flex;gap:10px;margin-top:8px">
      <button class="btn btn-primary" onclick="step1Next()">Δημιουργία →</button>
      <a href="partner.php?page=applications" class="btn btn-ghost">Ακύρωση</a>
    </div>
  </div>
</div>

<?php elseif($step===2):
  $d=$draft;
  $nomoi=['Αττικής','Θεσσαλονίκης','Πατρών','Ηρακλείου','Λάρισας','Βόλου','Ιωαννίνων','Αλεξανδρούπολης','Καβάλας','Χανίων'];
?>
<div class="step-wizard">
  <div class="step done"><div class="step-num">✓</div><div class="step-label">Επιλογή Προγράμματος</div></div>
  <div class="step-line done"></div>
  <div class="step active"><div class="step-num">2</div><div class="step-label">Στοιχεία Πελάτη</div></div>
  <div class="step-line"></div>
  <div class="step"><div class="step-num">3</div><div class="step-label">Δικαιολογητικά</div></div>
</div>
<div class="alert alert-info" style="margin-bottom:20px">
  <strong><?= htmlspecialchars($draft['category_name']??'') ?></strong> · <?= htmlspecialchars($draft['provider_name']??'') ?> · <strong><?= htmlspecialchars($draft['product_name']??'') ?></strong>
</div>
<form method="POST" action="save_draft.php?page=new-app&step=2save">
<div class="card" style="margin-bottom:16px">
  <div class="card-header"><div class="card-title">Κατηγορία & Σύνδεση</div></div>
  <div class="card-body">
    <div class="form-row">
      <div class="form-group"><label>Κατηγορία Πελάτη <span class="req">*</span></label>
        <select name="customer_type" required><option>ΙΔΙΩΤΗΣ</option><option>ΕΠΙΧΕΙΡΗΣΗ</option></select></div>
      <div class="form-group"><label>Είδος Σύνδεσης <span class="req">*</span></label>
        <select name="connection_type" required><option>ΝΕΑ ΑΡΙΘΜΟΔΟΤΗΣΗ</option><option>ΑΛΛΑΓΗ ΠΑΡΟΧΟΥ</option><option>ΦΟΡΗΤΟΤΗΤΑ</option></select></div>
    </div>
    <div class="form-group"><label>eBill <span class="req">*</span></label>
      <select name="ebill"><option>Ναι</option><option>Όχι</option></select></div>
  </div>
</div>
<div class="card" style="margin-bottom:16px">
  <div class="card-header"><div class="card-title">Στοιχεία Πελάτη</div></div>
  <div class="card-body">
    <div class="form-row">
      <div class="form-group"><label>Όνομα <span class="req">*</span></label><input type="text" name="cust_firstname" required placeholder="Γιώργης" value="<?= htmlspecialchars($d['cust_firstname']??'') ?>"></div>
      <div class="form-group"><label>Επίθετο <span class="req">*</span></label><input type="text" name="cust_lastname" required placeholder="Παπαδόπουλος" value="<?= htmlspecialchars($d['cust_lastname']??'') ?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Πατρώνυμο <span class="req">*</span></label><input type="text" name="cust_patronimo" required value="<?= htmlspecialchars($d['cust_patronimo']??'') ?>"></div>
      <div class="form-group"><label>Α.Δ.Τ <span class="req">*</span></label><input type="text" name="cust_adt" required placeholder="ΑΒ123456" value="<?= htmlspecialchars($d['cust_adt']??'') ?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Ημ. Γέννησης <span class="req">*</span></label><input type="date" name="cust_birthdate" required value="<?= htmlspecialchars($d['cust_birthdate']??'') ?>"></div>
      <div class="form-group"><label>ΑΦΜ <span class="req">*</span></label><input type="text" name="cust_afm" required placeholder="123456789" value="<?= htmlspecialchars($d['cust_afm']??'') ?>"></div>
    </div>
    <div class="form-group"><label>Δ.Ο.Υ <span class="req">*</span></label><input type="text" name="cust_doy" required placeholder="Αθηνών" value="<?= htmlspecialchars($d['cust_doy']??'') ?>"></div>
    <div class="form-row3">
      <div class="form-group"><label>Τ.Κ <span class="req">*</span></label><input type="text" name="cust_tk" required placeholder="10561" value="<?= htmlspecialchars($d['cust_tk']??'') ?>"></div>
      <div class="form-group"><label>Νομός <span class="req">*</span></label>
        <select name="cust_nomos" required>
          <option value="">Επιλέξτε...</option>
          <?php foreach($nomoi as $n): ?><option value="<?= $n ?>" <?= ($d['cust_nomos']??'')===$n?'selected':'' ?>><?= $n ?></option><?php endforeach; ?>
        </select></div>
      <div class="form-group"><label>Πόλη <span class="req">*</span></label><input type="text" name="cust_poli" required value="<?= htmlspecialchars($d['cust_poli']??'') ?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Περιοχή <span class="req">*</span></label><input type="text" name="cust_periochi" required value="<?= htmlspecialchars($d['cust_periochi']??'') ?>"></div>
      <div class="form-group"><label>Διεύθυνση <span class="req">*</span></label><input type="text" name="cust_address" required value="<?= htmlspecialchars($d['cust_address']??'') ?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Αριθμός <span class="req">*</span></label><input type="text" name="cust_number" required value="<?= htmlspecialchars($d['cust_number']??'') ?>"></div>
      <div class="form-group"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Τηλέφωνο <span class="req">*</span></label><input type="text" name="cust_phone" required value="<?= htmlspecialchars($d['cust_phone']??'') ?>"></div>
      <div class="form-group"><label>Κινητό <span class="req">*</span></label><input type="text" name="cust_kinito" required value="<?= htmlspecialchars($d['cust_kinito']??'') ?>"></div>
    </div>
    <div class="form-group"><label>Email <span class="req">*</span></label><input type="email" name="cust_email" required value="<?= htmlspecialchars($d['cust_email']??'') ?>"></div>
  </div>
</div>
<div class="card" style="margin-bottom:16px">
  <div class="card-header"><div class="card-title">Υπεύθυνος Επικοινωνίας</div></div>
  <div class="card-body">
    <div class="form-row">
      <div class="form-group"><label>Όνομα <span class="req">*</span></label><input type="text" name="contact_name" required value="<?= htmlspecialchars($d['contact_name']??'') ?>"></div>
      <div class="form-group"><label>Επίθετο <span class="req">*</span></label><input type="text" name="contact_lastname" required value="<?= htmlspecialchars($d['contact_lastname']??'') ?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Πατρώνυμο <span class="req">*</span></label><input type="text" name="contact_patronimo" required value="<?= htmlspecialchars($d['contact_patronimo']??'') ?>"></div>
      <div class="form-group"><label>Α.Δ.Τ <span class="req">*</span></label><input type="text" name="contact_adt" required value="<?= htmlspecialchars($d['contact_adt']??'') ?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Τηλέφωνο <span class="req">*</span></label><input type="text" name="contact_phone" required value="<?= htmlspecialchars($d['contact_phone']??'') ?>"></div>
      <div class="form-group"><label>Κινητό <span class="req">*</span></label><input type="text" name="contact_kinito" required value="<?= htmlspecialchars($d['contact_kinito']??'') ?>"></div>
    </div>
    <div class="form-group"><label>Email <span class="req">*</span></label><input type="email" name="contact_email" required value="<?= htmlspecialchars($d['contact_email']??'') ?>"></div>
  </div>
</div>
<div class="card" style="margin-bottom:16px">
  <div class="card-header"><div class="card-title">Στοιχεία Αποστολής Λογαριασμού</div></div>
  <div class="card-body">
    <div class="form-group"><label>eBill <span class="req">*</span></label><select name="prog_ebill"><option>Ναι</option><option>Όχι</option></select></div>
  </div>
</div>
<div class="card" style="margin-bottom:16px">
  <div class="card-header"><div class="card-title">Στοιχεία Προγράμματος</div></div>
  <div class="card-body">
    <div class="form-row">
      <div class="form-group"><label>Τηλέφωνο Ενεργοποίησης</label><input type="text" name="prog_phone_activation" value="<?= htmlspecialchars($d['prog_phone_activation']??'') ?>"></div>
      <div class="form-group"><label>Sim Ενεργοποίησης</label><input type="text" name="prog_sim_activation" value="<?= htmlspecialchars($d['prog_sim_activation']??'') ?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Πακέτο <span class="req">*</span></label><input type="text" name="prog_paketo" required value="<?= htmlspecialchars($d['prog_paketo']??$d['product_name']??'') ?>"></div>
      <div class="form-group"><label>Τιμή (€) <span class="req">*</span></label><input type="text" name="prog_timi" required value="<?= htmlspecialchars($d['prog_timi']??'') ?>"></div>
    </div>
    <div class="form-group"><label>Αν. ύψος Λογαριασμού <span class="req">*</span></label>
      <select name="prog_anypsos" required><option>Μηνιαίος</option><option>Διμηνιαίος</option><option>Τριμηνιαίος</option></select></div>
    <div class="form-group"><label>Έχει παραλάβει κάρτα SIM ο πελάτης; <span class="req">*</span></label>
      <select name="prog_sim_received" required><option>Ναι</option><option>Όχι</option></select></div>
    <div class="form-group"><label>Δηλώνω ότι επιθυμώ ειδικά να δέχομαι τηλεφωνικές κλήσεις από την Εταιρεία με σκοπό την προώθηση των προϊόντων και υπηρεσιών της <span class="req">*</span></label>
      <select name="prog_consent1" required><option>Συμφωνώ</option><option>Δεν Συμφωνώ</option></select></div>
    <div class="form-group"><label>Δηλώνω ότι ενημερώθηκα και επιθυμώ να καταχωρηθεί η τηλεφωνική μου σύνδεση στο Μητρώο του άρθρου 11 που τηρεί η εταιρεία <span class="req">*</span></label>
      <select name="prog_consent2" required><option>Συμφωνώ</option><option>Δεν Συμφωνώ</option></select></div>
    <div class="form-group"><label>Τρόπος Υπογραφής <span class="req">*</span></label>
      <select name="prog_signature_way" required><option>Partner</option><option>Digital</option><option>Manual</option></select></div>
    <div class="form-group"><label>Σημειώσεις</label><textarea name="notes" placeholder="Προαιρετικές σημειώσεις..."><?= htmlspecialchars($d['notes']??'') ?></textarea></div>
  </div>
</div>
<div style="display:flex;gap:10px;margin-top:4px">
  <button type="submit" class="btn btn-primary">Αποθήκευση & Συνέχεια →</button>
  <a href="partner.php?page=applications" class="btn btn-ghost">Ακύρωση</a>
</div>
</form>

<?php elseif($step===3): ?>
<div class="step-wizard">
  <div class="step done"><div class="step-num">✓</div><div class="step-label">Επιλογή Προγράμματος</div></div>
  <div class="step-line done"></div>
  <div class="step done"><div class="step-num">✓</div><div class="step-label">Στοιχεία Πελάτη</div></div>
  <div class="step-line done"></div>
  <div class="step active"><div class="step-num">3</div><div class="step-label">Δικαιολογητικά</div></div>
</div>
<div class="alert alert-info" style="margin-bottom:20px">Αίτηση δημιουργήθηκε: <strong><?= htmlspecialchars($draft['app_code']??'') ?></strong> · <?= htmlspecialchars($draft['cust_firstname']??'') ?> <?= htmlspecialchars($draft['cust_lastname']??'') ?></div>
<div class="card" style="max-width:600px">
  <div class="card-header"><div class="card-title">Ανέβασμα Δικαιολογητικών</div></div>
  <div class="card-body">
    <?php
    $appId = $draft['app_id'] ?? 0;
    $docDefs = [
      ['key'=>'identity',    'label'=>'Ταυτότητα',               'req'=>true,  'icon'=>'🪪'],
      ['key'=>'logariasmos', 'label'=>'Λογαριασμός/ΔΕΚΟ/Μισθωτήριο','req'=>false,'icon'=>'📄'],
      ['key'=>'bebaiosi',    'label'=>'Βεβαίωση ΑΦΜ',            'req'=>false, 'icon'=>'📋'],
      ['key'=>'extra',       'label'=>'Επιπλέον Έγγραφα',         'req'=>false, 'icon'=>'📎', 'multi'=>true],
    ];
    foreach($docDefs as $doc): ?>
    <div style="margin-bottom:24px">
      <label><?= $doc['icon'] ?> <?= htmlspecialchars($doc['label']) ?><?php if($doc['req']): ?> <span class="req">*</span><?php endif; ?></label>
      <div class="upload-zone" id="uz-<?= $doc['key'] ?>">
        <input type="file" accept="image/*,application/pdf" <?= ($doc['multi']??false)?'multiple':'' ?> onchange="uploadDoc(event,'<?= $doc['key'] ?>',<?= $appId ?>)">
        <div style="font-size:24px;margin-bottom:8px;pointer-events:none"><?= $doc['icon'] ?></div>
        <div style="font-size:13px;color:var(--text2);pointer-events:none" id="ul-<?= $doc['key'] ?>">Κάντε κλικ ή σύρετε αρχείο<?= ($doc['multi']??false)?' (πολλαπλά)':'' ?></div>
      </div>
    </div>
    <?php endforeach; ?>
    <div style="display:flex;gap:10px;margin-top:8px">
      <button class="btn btn-primary" onclick="finishApp()" id="finish-btn">✓ Καταχώρηση Αίτησης</button>
      <a href="partner.php?page=applications" class="btn btn-ghost">Ακύρωση</a>
    </div>
    <div id="finish-status" style="margin-top:12px;font-size:13px;color:var(--text3)"></div>
  </div>
</div>
<?php endif; ?>

<?php elseif($page==='infoportal'): ?>
<div style="margin-bottom:20px">
  <div class="search-bar" style="max-width:400px">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;color:var(--text3)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="text" id="info-search" placeholder="Αναζήτηση..." oninput="filterInfo()">
  </div>
</div>
<div id="info-panels"></div>
<?php endif; ?>

  </div>
</div>

<!-- Modal (view app) -->
<div id="modal-overlay" class="modal-overlay" style="display:none" onclick="maybeCloseModal(event)">
  <div class="modal" id="modal-box"></div>
</div>

<script>
const API='api.php';
async function api(action,params={},method='GET'){
  let url=API+'?action='+action,opts={};
  if(method==='POST'){opts={method:'POST',body:new URLSearchParams({action,...params})};}
  else{Object.keys(params).forEach(k=>url+='&'+k+'='+encodeURIComponent(params[k]));}
  const r=await fetch(url,opts); return r.json();
}
function esc(s){const d=document.createElement('div');d.textContent=String(s??'');return d.innerHTML;}
function badge(name,color){const m={blue:'badge-blue',green:'badge-green',yellow:'badge-yellow',red:'badge-red'};return`<span class="badge ${m[color]||'badge-gray'}">${esc(name)}</span>`;}
function showModal(html){document.getElementById('modal-box').innerHTML=html;document.getElementById('modal-overlay').style.display='flex';}
function closeModal(){document.getElementById('modal-overlay').style.display='none';}
function maybeCloseModal(e){if(e.target.id==='modal-overlay')closeModal();}

let ALL_STATUSES=[], INFO_DATA=[];

<?php if($page==='dashboard'): ?>
async function loadDashboard(){
  const [s,r]=await Promise.all([api('get_stats'),api('get_applications')]);
  if(s.ok){
    document.getElementById('s-total').textContent=s.data.total;
    document.getElementById('s-active').textContent=s.data.active;
    document.getElementById('s-pending').textContent=s.data.pending;
    document.getElementById('s-review').textContent=s.data.review;
  }
  if(r.ok){
    const apps=r.data.slice(0,8);
    document.getElementById('recent-tbody').innerHTML=apps.map(a=>`<tr>
      <td><span class="inline-tag">${esc(a.app_code)}</span></td>
      <td style="color:var(--text);font-weight:500">${esc(a.cust_firstname)} ${esc(a.cust_lastname)}</td>
      <td>${esc(a.provider_name)} · ${esc(a.product_name)}</td>
      <td>${esc(a.created_at?.split(' ')[0])}</td>
      <td>${badge(a.status_name,a.status_color)}</td>
    </tr>`).join('')||'<tr><td colspan="5" style="text-align:center;color:var(--text3);padding:30px">Δεν υπάρχουν αιτήσεις</td></tr>';
  }
}
loadDashboard();
<?php endif; ?>

<?php if($page==='applications'): ?>
async function loadApps(){
  const r=await api('get_applications');
  if(!r.ok) return;
  const apps=r.data;
  document.getElementById('apps-title').textContent='Οι Αιτήσεις μου ('+apps.length+')';
  document.getElementById('apps-tbody').innerHTML=apps.map(a=>`<tr>
    <td>${esc(a.created_at?.split(' ')[0])}</td>
    <td><span class="inline-tag">${esc(a.app_code)}</span></td>
    <td style="color:var(--text);font-weight:500">${esc(a.cust_firstname)} ${esc(a.cust_lastname)}</td>
    <td>${esc(a.cust_afm)}</td>
    <td>${esc(a.provider_name)} · ${esc(a.product_name)}</td>
    <td>${badge(a.status_name,a.status_color)}</td>
    <td style="display:flex;gap:6px">
      <button class="btn btn-ghost btn-xs" onclick="viewApp(${a.id})"><?= $ic['eye'] ?> Προβολή</button>
      ${a.status_name==='Εκκρεμότητα'?`<button class="btn btn-primary btn-xs" onclick="editApp(${a.id})"><?= $ic['edit'] ?> Επεξεργασία</button>`:''}
    </td>
  </tr>`).join('')||'<tr><td colspan="7" style="text-align:center;color:var(--text3);padding:40px">Δεν υπάρχουν αιτήσεις ακόμα</td></tr>';
}
api('get_statuses').then(r=>{ALL_STATUSES=r.ok?r.data:[];});
loadApps();
function editApp(id){
  showModal(`<div class="modal-header"><div class="modal-title">Επεξεργασία Εκκρεμότητας</div><button class="modal-close" onclick="closeModal()">×</button></div>
<div class="modal-body">
  <div class="alert alert-warning">⚠️ Η αίτηση βρίσκεται σε εκκρεμότητα. Προσθέστε επιπλέον πληροφορίες ή δικαιολογητικά.</div>
  <div class="form-group"><label>Σημειώσεις / Επιπλέον Πληροφορίες</label><textarea id="edit-notes" placeholder="Προσθέστε σχόλια..."></textarea></div>
  <div class="form-group"><label>Επιπλέον Δικαιολογητικό</label>
    <div class="upload-zone"><input type="file" accept="image/*,application/pdf" multiple onchange="uploadExtraEdit(event,${id})">
      <div style="font-size:13px;color:var(--text2)">📎 Κάντε κλικ για ανέβασμα</div></div>
    <div id="edit-files" style="margin-top:8px;font-size:12px;color:var(--green)"></div>
  </div>
</div>
<div class="modal-footer"><button class="btn btn-ghost" onclick="closeModal()">Ακύρωση</button><button class="btn btn-primary" onclick="saveEdit(${id})">Αποθήκευση</button></div>`);
}
async function uploadExtraEdit(e,id){
  const files=Array.from(e.target.files); let ok=[];
  for(const f of files){ const fd=new FormData(); fd.append('file',f); fd.append('app_id',id); fd.append('doc_type','extra'); const r=await fetch(API+'?action=upload_document',{method:'POST',body:fd}); const j=await r.json(); if(j.ok) ok.push(f.name); }
  document.getElementById('edit-files').textContent=ok.length?'✓ Ανέβηκαν: '+ok.join(', '):'';
}
async function saveEdit(id){
  const r=await api('update_notes',{app_id:id,notes:document.getElementById('edit-notes').value},'POST');
  if(r.ok){closeModal();showNotif('Αποθηκεύτηκε');loadApps();}else showNotif('Σφάλμα: '+r.msg,false);
}
<?php endif; ?>

<?php if($page==='new-app' && $step===1): ?>
async function loadCats(){
  const r=await api('get_categories');
  if(!r.ok) return;
  const sel=document.getElementById('sel-cat');
  r.data.forEach(c=>{const o=new Option(c.name,c.id);sel.append(o);});
}
async function loadProviders(){
  const catId=document.getElementById('sel-cat').value;
  document.getElementById('prov-group').style.display=catId?'block':'none';
  document.getElementById('prod-group').style.display='none';
  if(!catId) return;
  const r=await api('get_providers',{category_id:catId});
  const sel=document.getElementById('sel-prov');
  sel.innerHTML='<option value="">Επιλέξτε πάροχο...</option>';
  (r.data||[]).forEach(p=>sel.append(new Option(p.name,p.id)));
}
async function loadProducts(){
  const provId=document.getElementById('sel-prov').value;
  document.getElementById('prod-group').style.display=provId?'block':'none';
  if(!provId) return;
  const r=await api('get_products',{provider_id:provId});
  const sel=document.getElementById('sel-prod');
  sel.innerHTML='<option value="">Επιλέξτε προϊόν...</option>';
  (r.data||[]).forEach(p=>sel.append(new Option(p.name,p.id)));
}
async function step1Next(){
  const catSel=document.getElementById('sel-cat');
  const provSel=document.getElementById('sel-prov');
  const prodSel=document.getElementById('sel-prod');
  if(!catSel.value||!provSel.value||!prodSel.value){showNotif('Παρακαλώ συμπληρώστε όλα τα πεδία',false);return;}
  // Save to session via fetch
  const fd=new URLSearchParams({action:'save_draft_step1',
    category_id:catSel.value, category_name:catSel.options[catSel.selectedIndex].text,
    provider_id:provSel.value, provider_name:provSel.options[provSel.selectedIndex].text,
    product_id:prodSel.value,  product_name:prodSel.options[prodSel.selectedIndex].text});
  const r=await(await fetch('save_draft.php',{method:'POST',body:fd})).json();
  if(r.ok) window.location.href='partner.php?page=new-app&step=2';
    category_id:catSel.value, category_name:catSel.options[catSel.selectedIndex].text,
    provider_id:provSel.value, provider_name:provSel.options[provSel.selectedIndex].text,
  window.location.href='partner.php?page=new-app&step=2';
}
loadCats();
<?php endif; ?>

<?php if($page==='new-app' && $step===3): ?>
const APP_ID=<?= (int)($draft['app_id']??0) ?>;
let identityUploaded=false;
async function uploadDoc(e,type,appId){
  const files=Array.from(e.target.files);
  const zone=document.getElementById('uz-'+type);
  const lbl=document.getElementById('ul-'+type);
  lbl.textContent='Ανεβάζεται...';
  let uploaded=[];
  for(const f of files){
    const fd=new FormData(); fd.append('file',f); fd.append('app_id',appId); fd.append('doc_type',type);
    const r=await fetch(API+'?action=upload_document',{method:'POST',body:fd});
    const j=await r.json();
    if(j.ok){uploaded.push(f.name);if(type==='identity')identityUploaded=true;}
    else{showNotif('Σφάλμα: '+j.msg,false);}
  }
  if(uploaded.length){zone.classList.add('uploaded');lbl.textContent='✓ '+uploaded.join(', ');}
}
async function finishApp(){
  if(!identityUploaded){showNotif('Η Ταυτότητα είναι υποχρεωτική',false);return;}
  const r=await fetch('save_draft.php',{method:'POST',body:new URLSearchParams({action:'clear_draft'})});
  showNotif('Η αίτηση καταχωρήθηκε επιτυχώς!');
  document.getElementById('finish-btn').disabled=true;
  setTimeout(()=>window.location.href='partner.php?page=applications',1500);
}
<?php endif; ?>

<?php if($page==='infoportal'): ?>
async function loadInfo(){
  const r=await api('get_info_companies');
  INFO_DATA=r.ok?r.data:[];
  renderInfo(INFO_DATA);
}
function renderInfo(data){
  const p=document.getElementById('info-panels');
  if(!data.length){p.innerHTML='<div style="text-align:center;padding:60px;color:var(--text3)">Δεν υπάρχουν προγράμματα ακόμα</div>';return;}
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
function filterInfo(){const q=document.getElementById('info-search').value.toLowerCase();renderInfo(INFO_DATA.filter(c=>c.name.toLowerCase().includes(q)||c.plans.some(p=>p.name.toLowerCase().includes(q))));}
loadInfo();
<?php endif; ?>

// ── View App modal ──────────────────────────────────────
async function viewApp(id){
  const r=await api('get_application',{id});
  if(!r.ok){showNotif('Σφάλμα: '+r.msg,false);return;}
  const a=r.data, docs=a.documents||[];
  const docTypes={identity:'Ταυτότητα *',logariasmos:'Λογαριασμός/ΔΕΚΟ',bebaiosi:'Βεβαίωση ΑΦΜ',extra:'Επιπλέον'};
  function row(k,v){return v?`<div class="detail-row"><div class="detail-key">${k}</div><div class="detail-val">${esc(v)}</div></div>`:''}
  const docHtml=Object.entries(docTypes).map(([t,label])=>{
    const grouped=docs.filter(d=>d.doc_type===t);
    if(!grouped.length) return t==='identity'?`<div class="doc-thumb-wrap"><div class="doc-missing">✗ ${label}</div></div>`:'';
    return grouped.map(d=>{
      const url='uploads/'+d.stored_name;
      const isImg=/\.(jpg|jpeg|png|gif|webp)$/i.test(d.stored_name);
      return`<div class="doc-thumb-wrap">${isImg?`<img class="doc-thumb" src="${esc(url)}" alt="${esc(d.original_name)}" onclick="openLightbox('${esc(url)}')" loading="lazy">`:
        `<a href="${esc(url)}" target="_blank" class="doc-missing" style="text-decoration:none;color:var(--accent)">📄 PDF<br><small>${esc(d.original_name)}</small></a>`}
      <div class="doc-label">${esc(label)}<br><small style="color:var(--text3)">${esc(d.original_name)}</small></div></div>`;
    }).join('');
  }).join('');
  showModal(`<div class="modal-header"><div class="modal-title">Αίτηση ${esc(a.app_code)}</div><button class="modal-close" onclick="closeModal()">×</button></div>
<div class="modal-body">
  <div style="margin-bottom:16px">${badge(a.status_name,a.status_color)}</div>
  <div class="detail-section"><div class="detail-section-title">Στοιχεία Πελάτη</div>
    ${row('Ονοματεπώνυμο',a.cust_firstname+' '+a.cust_lastname)}${row('ΑΦΜ',a.cust_afm)}${row('Α.Δ.Τ',a.cust_adt)}${row('Τηλ./Κινητό',a.cust_phone+' / '+a.cust_kinito)}</div>
  <div class="detail-section"><div class="detail-section-title">Πρόγραμμα</div>
    ${row('Πάροχος',a.provider_name)}${row('Πακέτο',a.prog_paketo)}${row('Τιμή',a.prog_timi?a.prog_timi+'€':'')}${row('Τρόπος Υπογραφής',a.prog_signature_way)}</div>
  ${a.notes?`<div class="detail-section"><div class="detail-section-title">Σημειώσεις</div><div style="font-size:13px;color:var(--text2)">${esc(a.notes)}</div></div>`:''}
  <div class="detail-section"><div class="detail-section-title">Δικαιολογητικά</div><div class="doc-grid">${docHtml||'<span style="color:var(--text3);font-size:13px">Δεν έχουν ανέβει</span>'}</div></div>
</div>`);
}
</script>
<?php renderFoot(); ?>
