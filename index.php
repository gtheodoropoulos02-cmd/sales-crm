<?php
require_once 'config.php';
startSession();

// If already logged in, redirect
if (!empty($_SESSION['user'])) {
    header('Location: ' . ($_SESSION['user']['role'] === 'admin' ? 'dashboard.php' : 'partner.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND active = 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id'       => $user['id'],
                    'name'     => $user['name'],
                    'username' => $user['username'],
                    'role'     => $user['role'],
                ];
                header('Location: ' . ($user['role'] === 'admin' ? 'dashboard.php' : 'partner.php'));
                exit;
            } else {
                $error = 'Λανθασμένο username ή κωδικός.';
            }
        } catch (Exception $e) {
            $error = 'Σφάλμα σύνδεσης με τη βάση δεδομένων. Ελέγξτε το config.php.';
        }
    } else {
        $error = 'Συμπληρώστε username και κωδικό.';
    }
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sales CRM — Σύνδεση</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{
  --bg:#0f1117;--bg2:#161b27;--bg3:#1e2535;--bg4:#252d40;
  --accent:#4f8ef7;--accent2:#7c3aed;--green:#22c55e;--red:#ef4444;
  --text:#e8ecf4;--text2:#8b95aa;--text3:#5a6478;
  --border:#2a3347;--border2:#3a4560;
  font-family:'Plus Jakarta Sans',sans-serif;
}
body{background:var(--bg);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.login-wrap{width:100%;max-width:420px}
.logo-area{text-align:center;margin-bottom:32px}
.logo-icon{width:56px;height:56px;background:linear-gradient(135deg,var(--accent),var(--accent2));border-radius:16px;display:inline-flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:#fff;margin-bottom:14px}
.logo-title{font-size:22px;font-weight:800;color:var(--text)}
.logo-sub{font-size:13px;color:var(--text3);margin-top:3px}
.card{background:var(--bg2);border:1px solid var(--border);border-radius:16px;padding:32px}
.form-group{margin-bottom:18px}
label{display:block;font-size:12.5px;font-weight:600;color:var(--text2);margin-bottom:7px}
input{width:100%;background:var(--bg3);border:1px solid var(--border);border-radius:9px;padding:11px 14px;font-size:14px;color:var(--text);font-family:inherit;outline:none;transition:.15s}
input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(79,142,247,.12)}
input::placeholder{color:var(--text3)}
.btn{width:100%;background:var(--accent);color:#fff;border:none;border-radius:9px;padding:12px;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;transition:.15s;margin-top:6px}
.btn:hover{background:#3d7ef5;transform:translateY(-1px)}
.btn:active{transform:translateY(0)}
.error{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);border-radius:9px;padding:11px 14px;font-size:13px;color:#f87171;margin-bottom:18px}
.footer-note{text-align:center;margin-top:20px;font-size:12px;color:var(--text3)}
</style>
</head>
<body>
<div class="login-wrap">
  <div class="logo-area">
    <div class="logo-icon">S</div>
    <div class="logo-title">Sales CRM</div>
    <div class="logo-sub">Partner Management Portal</div>
  </div>
  <div class="card">
    <?php if ($error): ?>
    <div class="error">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Εισάγετε username..." value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" autocomplete="username" required>
      </div>
      <div class="form-group">
        <label>Κωδικός</label>
        <input type="password" name="password" placeholder="Εισάγετε κωδικό..." autocomplete="current-password" required>
      </div>
      <button type="submit" class="btn">Σύνδεση →</button>
    </form>
  </div>
  <div class="footer-note">Sales CRM © <?= date('Y') ?></div>
</div>
</body>
</html>
