<?php
require_once 'config.php';
startSession();

if (empty($_SESSION['user'])) {
    jsonResponse(false, null, 'Μη εξουσιοδοτημένη πρόσβαση');
}

$user   = $_SESSION['user'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$db     = getDB();

switch ($action) {

    // ── Categories ──────────────────────────────────────────────
    case 'get_categories':
        jsonResponse(true, $db->query("SELECT * FROM categories ORDER BY sort_order,name")->fetchAll());

    case 'add_category':
        requireAdminApi();
        $name = trim($_POST['name'] ?? '');
        if (!$name) jsonResponse(false, null, 'Απαιτείται όνομα');
        $db->prepare("INSERT INTO categories (name) VALUES (?)")->execute([$name]);
        jsonResponse(true, ['id' => $db->lastInsertId(), 'name' => $name]);

    case 'delete_category':
        requireAdminApi();
        $db->prepare("DELETE FROM categories WHERE id=?")->execute([(int)$_POST['id']]);
        jsonResponse(true);

    // ── Providers ───────────────────────────────────────────────
    case 'get_providers':
        $catId = (int)($_GET['category_id'] ?? 0);
        $stmt = $db->prepare("SELECT * FROM providers WHERE category_id=? ORDER BY name");
        $stmt->execute([$catId]);
        jsonResponse(true, $stmt->fetchAll());

    case 'add_provider':
        requireAdminApi();
        $catId = (int)($_POST['category_id'] ?? 0);
        $name  = trim($_POST['name'] ?? '');
        if (!$name || !$catId) jsonResponse(false, null, 'Απαιτούνται όλα τα πεδία');
        $db->prepare("INSERT INTO providers (category_id, name) VALUES (?,?)")->execute([$catId, $name]);
        jsonResponse(true, ['id' => $db->lastInsertId(), 'name' => $name]);

    case 'delete_provider':
        requireAdminApi();
        $db->prepare("DELETE FROM providers WHERE id=?")->execute([(int)$_POST['id']]);
        jsonResponse(true);

    // ── Products ────────────────────────────────────────────────
    case 'get_products':
        $provId = (int)($_GET['provider_id'] ?? 0);
        $stmt = $db->prepare("SELECT * FROM products WHERE provider_id=? ORDER BY name");
        $stmt->execute([$provId]);
        jsonResponse(true, $stmt->fetchAll());

    case 'add_product':
        requireAdminApi();
        $provId = (int)($_POST['provider_id'] ?? 0);
        $name   = trim($_POST['name'] ?? '');
        if (!$name || !$provId) jsonResponse(false, null, 'Απαιτούνται όλα τα πεδία');
        $db->prepare("INSERT INTO products (provider_id, name) VALUES (?,?)")->execute([$provId, $name]);
        jsonResponse(true, ['id' => $db->lastInsertId(), 'name' => $name]);

    case 'delete_product':
        requireAdminApi();
        $db->prepare("DELETE FROM products WHERE id=?")->execute([(int)$_POST['id']]);
        jsonResponse(true);

    // ── Statuses ────────────────────────────────────────────────
    case 'get_statuses':
        jsonResponse(true, $db->query("SELECT * FROM statuses")->fetchAll());

    case 'add_status':
        requireAdminApi();
        $name  = trim($_POST['name'] ?? '');
        $color = $_POST['color'] ?? 'gray';
        if (!$name) jsonResponse(false, null, 'Απαιτείται όνομα');
        $db->prepare("INSERT INTO statuses (name, color) VALUES (?,?)")->execute([$name, $color]);
        jsonResponse(true, ['id' => $db->lastInsertId(), 'name' => $name, 'color' => $color]);

    case 'delete_status':
        requireAdminApi();
        $db->prepare("DELETE FROM statuses WHERE id=?")->execute([(int)$_POST['id']]);
        jsonResponse(true);

    // ── Partners ────────────────────────────────────────────────
    case 'get_partners':
        requireAdminApi();
        jsonResponse(true, $db->query("SELECT id,name,username,email,phone,active,created_at FROM users WHERE role='partner' ORDER BY name")->fetchAll());

    case 'add_partner':
        requireAdminApi();
        $name  = trim($_POST['name'] ?? '');
        $uname = trim($_POST['username'] ?? '');
        $pass  = $_POST['password'] ?? '';
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        if (!$name || !$uname || !$pass) jsonResponse(false, null, 'Απαιτούνται: όνομα, username, κωδικός');
        $exists = $db->prepare("SELECT id FROM users WHERE username=?");
        $exists->execute([$uname]);
        if ($exists->fetch()) jsonResponse(false, null, 'Το username υπάρχει ήδη');
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $db->prepare("INSERT INTO users (name,username,password,email,phone,role) VALUES (?,?,?,?,?,'partner')")
           ->execute([$name, $uname, $hash, $email, $phone]);
        jsonResponse(true, ['id' => $db->lastInsertId()]);

    case 'toggle_partner':
        requireAdminApi();
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare("UPDATE users SET active = 1 - active WHERE id=? AND role='partner'")->execute([$id]);
        jsonResponse(true);

    case 'change_password':
        requireAdminApi();
        $id   = (int)($_POST['id'] ?? 0);
        $pass = $_POST['password'] ?? '';
        if (!$pass) jsonResponse(false, null, 'Απαιτείται κωδικός');
        $db->prepare("UPDATE users SET password=? WHERE id=?")->execute([password_hash($pass, PASSWORD_DEFAULT), $id]);
        jsonResponse(true);

    // ── Applications ────────────────────────────────────────────
    case 'get_applications':
        if ($user['role'] === 'admin') {
            $rows = $db->query("
                SELECT a.*, u.name AS partner_name, s.name AS status_name, s.color AS status_color,
                       c.name AS category_name, p.name AS provider_name, pr.name AS product_name
                FROM applications a
                JOIN users u ON a.partner_id=u.id
                JOIN statuses s ON a.status_id=s.id
                LEFT JOIN categories c ON a.category_id=c.id
                LEFT JOIN providers p ON a.provider_id=p.id
                LEFT JOIN products pr ON a.product_id=pr.id
                ORDER BY a.created_at DESC
            ")->fetchAll();
        } else {
            $stmt = $db->prepare("
                SELECT a.*, u.name AS partner_name, s.name AS status_name, s.color AS status_color,
                       c.name AS category_name, p.name AS provider_name, pr.name AS product_name
                FROM applications a
                JOIN users u ON a.partner_id=u.id
                JOIN statuses s ON a.status_id=s.id
                LEFT JOIN categories c ON a.category_id=c.id
                LEFT JOIN providers p ON a.provider_id=p.id
                LEFT JOIN products pr ON a.product_id=pr.id
                WHERE a.partner_id=?
                ORDER BY a.created_at DESC
            ");
            $stmt->execute([$user['id']]);
            $rows = $stmt->fetchAll();
        }
        jsonResponse(true, $rows);

    case 'get_application':
        $id = (int)($_GET['id'] ?? 0);
        $stmt = $db->prepare("
            SELECT a.*, u.name AS partner_name, s.name AS status_name, s.color AS status_color,
                   c.name AS category_name, p.name AS provider_name, pr.name AS product_name
            FROM applications a
            JOIN users u ON a.partner_id=u.id
            JOIN statuses s ON a.status_id=s.id
            LEFT JOIN categories c ON a.category_id=c.id
            LEFT JOIN providers p ON a.provider_id=p.id
            LEFT JOIN products pr ON a.product_id=pr.id
            WHERE a.id=?
        ");
        $stmt->execute([$id]);
        $app = $stmt->fetch();
        if (!$app) jsonResponse(false, null, 'Δεν βρέθηκε');
        // Only partner can see own apps
        if ($user['role'] === 'partner' && $app['partner_id'] != $user['id']) jsonResponse(false, null, 'Άρνηση πρόσβασης');
        // Get documents
        $dStmt = $db->prepare("SELECT * FROM documents WHERE application_id=? ORDER BY doc_type,uploaded_at");
        $dStmt->execute([$id]);
        $app['documents'] = $dStmt->fetchAll();
        jsonResponse(true, $app);

    case 'submit_application':
        // Step 1: create application record
        $d = $_POST;
        // Get status "Προς Έλεγχο"
        $sRow = $db->query("SELECT id FROM statuses WHERE name='Προς Έλεγχο' LIMIT 1")->fetch();
        if (!$sRow) jsonResponse(false, null, 'Δεν βρέθηκε στάτους "Προς Έλεγχο"');

        $code = generateAppCode();
        $stmt = $db->prepare("INSERT INTO applications (
            app_code, partner_id, status_id, category_id, provider_id, product_id,
            customer_type, connection_type, ebill,
            cust_firstname, cust_lastname, cust_patronimo, cust_adt, cust_birthdate,
            cust_afm, cust_doy, cust_tk, cust_nomos, cust_poli, cust_periochi,
            cust_address, cust_number, cust_phone, cust_kinito, cust_email,
            contact_name, contact_lastname, contact_patronimo, contact_adt,
            contact_phone, contact_kinito, contact_email,
            prog_phone_activation, prog_sim_activation, prog_paketo, prog_timi,
            prog_anypsos, prog_sim_received, prog_consent1, prog_consent2,
            prog_signature_way, prog_ebill, notes
        ) VALUES (
            ?,?,?,?,?,?,
            ?,?,?,
            ?,?,?,?,?,
            ?,?,?,?,?,?,
            ?,?,?,?,?,
            ?,?,?,?,
            ?,?,?,
            ?,?,?,?,
            ?,?,?,?,
            ?,?,?
        )");
        $stmt->execute([
            $code, $user['id'], $sRow['id'],
            (int)($d['category_id'] ?? 0) ?: null,
            (int)($d['provider_id'] ?? 0) ?: null,
            (int)($d['product_id'] ?? 0) ?: null,
            $d['customer_type'] ?? '', $d['connection_type'] ?? '', $d['ebill'] ?? '',
            $d['cust_firstname'] ?? '', $d['cust_lastname'] ?? '', $d['cust_patronimo'] ?? '',
            $d['cust_adt'] ?? '', $d['cust_birthdate'] ?? null,
            $d['cust_afm'] ?? '', $d['cust_doy'] ?? '', $d['cust_tk'] ?? '',
            $d['cust_nomos'] ?? '', $d['cust_poli'] ?? '', $d['cust_periochi'] ?? '',
            $d['cust_address'] ?? '', $d['cust_number'] ?? '',
            $d['cust_phone'] ?? '', $d['cust_kinito'] ?? '', $d['cust_email'] ?? '',
            $d['contact_name'] ?? '', $d['contact_lastname'] ?? '', $d['contact_patronimo'] ?? '',
            $d['contact_adt'] ?? '', $d['contact_phone'] ?? '', $d['contact_kinito'] ?? '', $d['contact_email'] ?? '',
            $d['prog_phone_activation'] ?? '', $d['prog_sim_activation'] ?? '',
            $d['prog_paketo'] ?? '', $d['prog_timi'] ?? '',
            $d['prog_anypsos'] ?? '', $d['prog_sim_received'] ?? '',
            $d['prog_consent1'] ?? '', $d['prog_consent2'] ?? '',
            $d['prog_signature_way'] ?? '', $d['prog_ebill'] ?? '',
            $d['notes'] ?? '',
        ]);
        $appId = $db->lastInsertId();
        jsonResponse(true, ['id' => $appId, 'code' => $code]);

    case 'upload_document':
        $appId   = (int)($_POST['app_id'] ?? 0);
        $docType = $_POST['doc_type'] ?? '';
        $allowed = ['identity','logariasmos','bebaiosi','extra'];
        if (!in_array($docType, $allowed)) jsonResponse(false, null, 'Μη έγκυρος τύπος εγγράφου');
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            jsonResponse(false, null, 'Σφάλμα ανεβάσματος αρχείου');
        }
        $file = $_FILES['file'];
        if ($file['size'] > MAX_FILE_SIZE) jsonResponse(false, null, 'Το αρχείο υπερβαίνει τα 10MB');
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        if (!in_array($mime, ALLOWED_TYPES)) jsonResponse(false, null, 'Μη επιτρεπτός τύπος αρχείου');
        $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
        $stored = $appId . '_' . $docType . '_' . uniqid() . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $stored)) {
            jsonResponse(false, null, 'Σφάλμα αποθήκευσης αρχείου');
        }
        $db->prepare("INSERT INTO documents (application_id, doc_type, original_name, stored_name) VALUES (?,?,?,?)")
           ->execute([$appId, $docType, $file['name'], $stored]);
        jsonResponse(true, ['stored_name' => $stored, 'original_name' => $file['name']]);

    case 'update_status':
        requireAdminApi();
        $appId    = (int)($_POST['app_id'] ?? 0);
        $statusId = (int)($_POST['status_id'] ?? 0);
        $db->prepare("UPDATE applications SET status_id=? WHERE id=?")->execute([$statusId, $appId]);
        jsonResponse(true);

    case 'update_notes':
        $appId = (int)($_POST['app_id'] ?? 0);
        // partner can only edit own pending apps
        if ($user['role'] === 'partner') {
            $check = $db->prepare("SELECT a.id FROM applications a JOIN statuses s ON a.status_id=s.id WHERE a.id=? AND a.partner_id=? AND s.name='Εκκρεμότητα'");
            $check->execute([$appId, $user['id']]);
            if (!$check->fetch()) jsonResponse(false, null, 'Δεν επιτρέπεται');
        }
        $db->prepare("UPDATE applications SET notes=? WHERE id=?")->execute([$_POST['notes'] ?? '', $appId]);
        jsonResponse(true);

    // ── InfoPortal ──────────────────────────────────────────────
    case 'get_info_companies':
        $companies = $db->query("SELECT * FROM info_companies ORDER BY name")->fetchAll();
        foreach ($companies as &$c) {
            $pStmt = $db->prepare("SELECT * FROM info_plans WHERE company_id=?");
            $pStmt->execute([$c['id']]);
            $c['plans'] = $pStmt->fetchAll();
        }
        jsonResponse(true, $companies);

    case 'add_info_company':
        requireAdminApi();
        $name = trim($_POST['name'] ?? '');
        if (!$name) jsonResponse(false, null, 'Απαιτείται όνομα');
        $db->prepare("INSERT INTO info_companies (name) VALUES (?)")->execute([$name]);
        jsonResponse(true, ['id' => $db->lastInsertId()]);

    case 'add_info_plan':
        requireAdminApi();
        $compId = (int)($_POST['company_id'] ?? 0);
        $name   = trim($_POST['name'] ?? '');
        $price  = trim($_POST['price'] ?? '');
        $docs   = trim($_POST['docs'] ?? '');
        if (!$compId || !$name || !$price) jsonResponse(false, null, 'Απαιτούνται όλα τα πεδία');
        $db->prepare("INSERT INTO info_plans (company_id, name, price, docs) VALUES (?,?,?,?)")
           ->execute([$compId, $name, $price, $docs]);
        jsonResponse(true, ['id' => $db->lastInsertId()]);

    case 'delete_info_plan':
        requireAdminApi();
        $db->prepare("DELETE FROM info_plans WHERE id=?")->execute([(int)$_POST['id']]);
        jsonResponse(true);

    case 'delete_info_company':
        requireAdminApi();
        $db->prepare("DELETE FROM info_companies WHERE id=?")->execute([(int)$_POST['id']]);
        jsonResponse(true);

    // ── Stats ────────────────────────────────────────────────────
    case 'get_stats':
        if ($user['role'] === 'admin') {
            $total   = $db->query("SELECT COUNT(*) FROM applications")->fetchColumn();
            $active  = $db->query("SELECT COUNT(*) FROM applications a JOIN statuses s ON a.status_id=s.id WHERE s.name='Ενεργοποιημένη'")->fetchColumn();
            $pending = $db->query("SELECT COUNT(*) FROM applications a JOIN statuses s ON a.status_id=s.id WHERE s.name='Εκκρεμότητα'")->fetchColumn();
            $review  = $db->query("SELECT COUNT(*) FROM applications a JOIN statuses s ON a.status_id=s.id WHERE s.name='Προς Έλεγχο'")->fetchColumn();
            $partners= $db->query("SELECT COUNT(*) FROM users WHERE role='partner' AND active=1")->fetchColumn();
            jsonResponse(true, compact('total','active','pending','review','partners'));
        } else {
            $pid = $user['id'];
            $stmt = $db->prepare("SELECT COUNT(*) FROM applications WHERE partner_id=?"); $stmt->execute([$pid]);
            $total = $stmt->fetchColumn();
            $stmt = $db->prepare("SELECT COUNT(*) FROM applications a JOIN statuses s ON a.status_id=s.id WHERE a.partner_id=? AND s.name='Ενεργοποιημένη'"); $stmt->execute([$pid]);
            $active = $stmt->fetchColumn();
            $stmt = $db->prepare("SELECT COUNT(*) FROM applications a JOIN statuses s ON a.status_id=s.id WHERE a.partner_id=? AND s.name='Εκκρεμότητα'"); $stmt->execute([$pid]);
            $pending = $stmt->fetchColumn();
            $stmt = $db->prepare("SELECT COUNT(*) FROM applications a JOIN statuses s ON a.status_id=s.id WHERE a.partner_id=? AND s.name='Προς Έλεγχο'"); $stmt->execute([$pid]);
            $review = $stmt->fetchColumn();
            jsonResponse(true, compact('total','active','pending','review'));
        }

    default:
        jsonResponse(false, null, 'Άγνωστη ενέργεια: ' . htmlspecialchars($action));
}

function requireAdminApi(): void {
    global $user;
    if ($user['role'] !== 'admin') jsonResponse(false, null, 'Απαιτείται δικαίωμα διαχειριστή');
}
