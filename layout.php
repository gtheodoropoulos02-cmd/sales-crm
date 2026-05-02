<?php
// layout.php — shared header/footer partials

function renderHead(string $title = 'Sales CRM'): void { ?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title) ?> — Sales CRM</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{
  --bg:#0f1117;--bg2:#161b27;--bg3:#1e2535;--bg4:#252d40;
  --accent:#4f8ef7;--accent2:#7c3aed;--green:#22c55e;--yellow:#f59e0b;--red:#ef4444;
  --text:#e8ecf4;--text2:#8b95aa;--text3:#5a6478;
  --border:#2a3347;--border2:#3a4560;
  --card:#1e2535;
  font-family:'Plus Jakarta Sans',sans-serif;
}
body{background:var(--bg);color:var(--text);min-height:100vh;display:flex;flex-direction:column}
.app{display:flex;height:100vh;overflow:hidden}
.sidebar{width:240px;background:var(--bg2);border-right:1px solid var(--border);display:flex;flex-direction:column;flex-shrink:0}
.logo{padding:20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px}
.logo-icon{width:36px;height:36px;background:linear-gradient(135deg,var(--accent),var(--accent2));border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:#fff;flex-shrink:0}
.logo-text{font-size:15px;font-weight:700}.logo-sub{font-size:10px;color:var(--text3);margin-top:1px}
.nav{padding:12px 8px;flex:1;overflow-y:auto}
.nav-section{font-size:10px;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.08em;padding:8px 12px 4px}
.nav-item{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;cursor:pointer;color:var(--text2);font-size:13.5px;font-weight:500;margin-bottom:2px;text-decoration:none;transition:.15s}
.nav-item:hover{background:var(--bg3);color:var(--text)}
.nav-item.active{background:rgba(79,142,247,.15);color:var(--accent)}
.nav-item svg{width:16px;height:16px;flex-shrink:0}
.user-panel{padding:12px;border-top:1px solid var(--border)}
.user-card{background:var(--bg3);border-radius:8px;padding:10px 12px;display:flex;align-items:center;gap:10px;margin-bottom:8px}
.user-avatar{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;flex-shrink:0}
.user-name{font-size:13px;font-weight:600}.user-role{font-size:11px;color:var(--text3)}
.logout-btn{width:100%;background:var(--bg3);border:1px solid var(--border2);color:var(--text2);padding:8px;border-radius:8px;cursor:pointer;font-family:inherit;font-size:12px;font-weight:500;transition:.15s}
.logout-btn:hover{background:rgba(239,68,68,.15);border-color:rgba(239,68,68,.4);color:var(--red)}
.main{flex:1;display:flex;flex-direction:column;overflow:hidden}
.topbar{padding:0 24px;height:60px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);background:var(--bg2);flex-shrink:0}
.page-title{font-size:15px;font-weight:700}
.content{flex:1;overflow-y:auto;padding:24px}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:.15s;border:none;font-family:inherit;text-decoration:none}
.btn-primary{background:var(--accent);color:#fff}.btn-primary:hover{background:#3d7ef5}
.btn-ghost{background:var(--bg3);color:var(--text2);border:1px solid var(--border)}.btn-ghost:hover{background:var(--bg4);color:var(--text)}
.btn-danger{background:rgba(239,68,68,.15);color:var(--red);border:1px solid rgba(239,68,68,.3)}.btn-danger:hover{background:rgba(239,68,68,.25)}
.btn-success{background:rgba(34,197,94,.15);color:var(--green);border:1px solid rgba(34,197,94,.3)}
.btn-sm{padding:5px 12px;font-size:12px}
.btn-xs{padding:3px 9px;font-size:11.5px}
.card{background:var(--card);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:20px}
.card-header{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.card-title{font-size:14px;font-weight:700}
.card-body{padding:20px}
table{width:100%;border-collapse:collapse}
th{text-align:left;font-size:11.5px;font-weight:600;color:var(--text3);text-transform:uppercase;letter-spacing:.05em;padding:10px 16px;border-bottom:1px solid var(--border)}
td{padding:11px 16px;border-bottom:1px solid var(--border);font-size:13.5px;color:var(--text2)}
tr:last-child td{border-bottom:none}
tr:hover td{background:rgba(255,255,255,.02)}
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:600}
.badge::before{content:'';width:5px;height:5px;border-radius:50%}
.badge-blue{background:rgba(79,142,247,.15);color:#7ab3fa}.badge-blue::before{background:#4f8ef7}
.badge-green{background:rgba(34,197,94,.15);color:#4ade80}.badge-green::before{background:#22c55e}
.badge-yellow{background:rgba(245,158,11,.15);color:#fbbf24}.badge-yellow::before{background:#f59e0b}
.badge-red{background:rgba(239,68,68,.15);color:#f87171}.badge-red::before{background:#ef4444}
.badge-gray{background:rgba(255,255,255,.07);color:var(--text2)}.badge-gray::before{background:var(--text3)}
.stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px}
.stat-card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:20px;position:relative;overflow:hidden}
.stat-card::before{content:'';position:absolute;top:0;right:0;width:80px;height:80px;border-radius:50%;opacity:.08;transform:translate(20px,-20px)}
.stat-card.blue::before{background:var(--accent)}.stat-card.green::before{background:var(--green)}.stat-card.yellow::before{background:var(--yellow)}.stat-card.red::before{background:var(--red)}
.stat-label{font-size:12px;color:var(--text2);margin-bottom:8px;font-weight:500}
.stat-val{font-size:28px;font-weight:700;line-height:1}
.stat-icon{position:absolute;top:16px;right:16px;width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center}
.stat-icon.blue{background:rgba(79,142,247,.15);color:var(--accent)}.stat-icon.green{background:rgba(34,197,94,.15);color:var(--green)}.stat-icon.yellow{background:rgba(245,158,11,.15);color:var(--yellow)}.stat-icon.red{background:rgba(239,68,68,.15);color:var(--red)}
.welcome-banner{background:linear-gradient(135deg,rgba(79,142,247,.15),rgba(124,58,237,.15));border:1px solid rgba(79,142,247,.25);border-radius:12px;padding:20px 24px;margin-bottom:24px;display:flex;align-items:center;gap:16px}
.welcome-icon{width:48px;height:48px;background:linear-gradient(135deg,var(--accent),var(--accent2));border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
.welcome-text h2{font-size:18px;font-weight:700;margin-bottom:2px}.welcome-text p{font-size:13px;color:var(--text2)}
.form-group{margin-bottom:18px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-row3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}
label{display:block;font-size:12.5px;font-weight:600;color:var(--text2);margin-bottom:6px}
.req{color:var(--red);margin-left:2px}
input[type=text],input[type=email],input[type=date],input[type=password],input[type=number],select,textarea{width:100%;background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:9px 12px;font-size:13.5px;color:var(--text);font-family:inherit;outline:none;transition:.15s}
input:focus,select:focus,textarea:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(79,142,247,.12)}
textarea{resize:vertical;min-height:90px}
input::placeholder,textarea::placeholder{color:var(--text3)}
select{cursor:pointer}
.step-wizard{display:flex;align-items:center;margin-bottom:28px}
.step{display:flex;align-items:center;gap:8px}
.step-num{width:28px;height:28px;border-radius:50%;border:2px solid var(--border2);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--text3);flex-shrink:0;transition:.3s}
.step.done .step-num{background:var(--green);border-color:var(--green);color:#fff}
.step.active .step-num{background:var(--accent);border-color:var(--accent);color:#fff}
.step-label{font-size:12px;font-weight:600;color:var(--text3)}
.step.active .step-label{color:var(--accent)}.step.done .step-label{color:var(--green)}
.step-line{flex:1;height:2px;background:var(--border);margin:0 8px}
.step-line.done{background:var(--green)}
.upload-zone{border:2px dashed var(--border2);border-radius:12px;padding:24px;text-align:center;cursor:pointer;transition:.2s;background:var(--bg3);position:relative}
.upload-zone:hover{border-color:var(--accent);background:rgba(79,142,247,.05)}
.upload-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
.upload-zone.uploaded{border-color:var(--green);background:rgba(34,197,94,.05)}
.inline-tag{display:inline-flex;align-items:center;background:rgba(79,142,247,.1);color:#7ab3fa;padding:2px 8px;border-radius:4px;font-size:11.5px;font-weight:600}
.search-bar{display:flex;align-items:center;gap:8px;background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:8px 12px}
.search-bar input{background:none;border:none;outline:none;font-size:13px;font-family:inherit;flex:1;color:var(--text)}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:100;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px);padding:20px}
.modal{background:var(--bg2);border:1px solid var(--border2);border-radius:16px;width:100%;max-width:620px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 60px rgba(0,0,0,.6)}
.modal-header{padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:var(--bg2);z-index:1}
.modal-title{font-size:16px;font-weight:700}
.modal-close{background:none;border:none;color:var(--text3);cursor:pointer;font-size:22px;line-height:1;transition:.15s;padding:2px 6px;border-radius:4px}
.modal-close:hover{background:var(--bg3);color:var(--text)}
.modal-body{padding:24px}
.modal-footer{padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;position:sticky;bottom:0;background:var(--bg2)}
.detail-section{margin-bottom:20px}
.detail-section-title{font-size:11px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border)}
.detail-row{display:flex;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.04)}
.detail-row:last-child{border-bottom:none}
.detail-key{font-size:12.5px;color:var(--text3);width:180px;flex-shrink:0}
.detail-val{font-size:12.5px;color:var(--text);font-weight:500}
.doc-grid{display:flex;flex-wrap:wrap;gap:12px;margin-top:10px}
.doc-thumb-wrap{position:relative;display:flex;flex-direction:column;align-items:center;gap:6px}
.doc-thumb{width:110px;height:90px;object-fit:cover;border-radius:8px;border:1px solid var(--border);cursor:pointer;transition:.2s}
.doc-thumb:hover{border-color:var(--accent);transform:scale(1.03)}
.doc-label{font-size:11px;color:var(--text3);text-align:center;max-width:110px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.doc-missing{width:110px;height:90px;border-radius:8px;border:2px dashed var(--border2);display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--text3);text-align:center;padding:8px}
.alert{border-radius:8px;padding:11px 14px;font-size:13px;margin-bottom:16px}
.alert-warning{background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.3);color:#fbbf24}
.alert-info{background:rgba(79,142,247,.1);border:1px solid rgba(79,142,247,.25);color:#7ab3fa}
.tag-manager{display:flex;flex-wrap:wrap;gap:8px;padding:12px;background:var(--bg3);border:1px solid var(--border);border-radius:8px;min-height:50px}
.tag{display:flex;align-items:center;gap:6px;background:var(--bg4);border:1px solid var(--border2);padding:4px 10px;border-radius:20px;font-size:12px;color:var(--text2)}
.tag-del{background:none;border:none;color:var(--red);cursor:pointer;font-size:14px;line-height:1;font-weight:700;padding:0 2px}
.add-tag-row{display:flex;gap:8px;margin-top:8px}
.add-tag-row input{flex:1}
.info-company{background:var(--card);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:20px}
.company-header{padding:14px 18px;background:var(--bg3);border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px}
.company-name{font-size:14px;font-weight:700}
.company-body{padding:18px}
.plan-card{background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:14px 16px;margin-bottom:10px}
.plan-name{font-size:13.5px;font-weight:700;margin-bottom:4px}
.plan-price{font-size:20px;font-weight:700;color:var(--accent);margin-bottom:6px}
.plan-doc-item{display:flex;align-items:center;gap:6px;margin-top:4px;font-size:12px;color:var(--text2)}
.plan-doc-item::before{content:'';width:4px;height:4px;border-radius:50%;background:var(--text3);flex-shrink:0}
.req-doc{color:var(--accent)}
.notif{position:fixed;top:18px;right:18px;z-index:9999;padding:12px 20px;border-radius:8px;font-size:13.5px;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.5);display:none;animation:slideIn .3s ease}
.notif-ok{background:var(--green);color:#fff}
.notif-err{background:var(--red);color:#fff}
@keyframes slideIn{from{transform:translateX(120%);opacity:0}to{transform:translateX(0);opacity:1}}
.sep{border-color:var(--border);margin:0}
.lightbox{position:fixed;inset:0;background:rgba(0,0,0,.9);z-index:200;display:flex;align-items:center;justify-content:center}
.lightbox img{max-width:90vw;max-height:90vh;border-radius:8px;box-shadow:0 24px 60px rgba(0,0,0,.8)}
.lightbox-close{position:fixed;top:20px;right:24px;background:rgba(255,255,255,.15);border:none;color:#fff;font-size:28px;cursor:pointer;border-radius:8px;padding:4px 12px;line-height:1;transition:.15s}
.lightbox-close:hover{background:rgba(255,255,255,.25)}
</style>
</head>
<body>
<div id="notif" class="notif"></div>
<div id="lightbox" class="lightbox" style="display:none" onclick="closeLightbox()">
  <button class="lightbox-close" onclick="closeLightbox()">×</button>
  <img id="lightbox-img" src="" alt="">
</div>
<div class="app">
<?php }

function renderSidebar(array $user, array $navItems): void { ?>
<div class="sidebar">
  <div class="logo">
    <div class="logo-icon">S</div>
    <div>
      <div class="logo-text">Sales CRM</div>
      <div class="logo-sub"><?= $user['role'] === 'admin' ? 'Admin Panel' : 'Partner Portal' ?></div>
    </div>
  </div>
  <div class="nav">
    <div class="nav-section">Μενού</div>
    <?php foreach ($navItems as $item): ?>
    <a href="<?= $item['url'] ?>" class="nav-item <?= $item['active'] ? 'active' : '' ?>">
      <?= $item['icon'] ?> <?= htmlspecialchars($item['label']) ?>
    </a>
    <?php endforeach; ?>
  </div>
  <div class="user-panel">
    <div class="user-card">
      <div class="user-avatar"><?= htmlspecialchars(mb_substr($user['name'], 0, 1) . (strpos($user['name'], ' ') !== false ? mb_substr(explode(' ', $user['name'])[1], 0, 1) : '')) ?></div>
      <div>
        <div class="user-name"><?= htmlspecialchars($user['name']) ?></div>
        <div class="user-role"><?= $user['role'] === 'admin' ? 'Διαχειριστής' : 'Εξωτερικός Πωλητής' ?></div>
      </div>
    </div>
    <form method="POST" action="logout.php">
      <button type="submit" class="logout-btn">⎋ Αποσύνδεση</button>
    </form>
  </div>
</div>
<?php }

function icons(): array {
    return [
        'home'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>',
        'list'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>',
        'plus'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>',
        'info'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
        'users'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>',
        'settings' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M4.93 19.07l1.41-1.41M19.07 19.07l-1.41-1.41M12 2v2M12 20v2M2 12h2M20 12h2"/></svg>',
        'eye'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>',
        'edit'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
    ];
}

function renderFoot(): void { ?>
</div><!-- .app -->
<script>
function showNotif(msg, ok=true){
  const n=document.getElementById('notif');
  n.textContent=msg; n.className='notif '+(ok?'notif-ok':'notif-err');
  n.style.display='block';
  setTimeout(()=>n.style.display='none', 3200);
}
function openLightbox(src){
  document.getElementById('lightbox-img').src=src;
  document.getElementById('lightbox').style.display='flex';
}
function closeLightbox(){
  document.getElementById('lightbox').style.display='none';
  document.getElementById('lightbox-img').src='';
}
document.addEventListener('keydown',e=>{if(e.key==='Escape') closeLightbox();});
</script>
</body></html>
<?php }
