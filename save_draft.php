<?php
// save_draft.php — handles session draft + step2 form submission
require_once 'config.php';
startSession();

$user = requireLogin();
if ($user['role'] === 'admin') { header('Location: dashboard.php'); exit; }

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ── Save step1 draft to session (called via fetch from JS) ──
if ($action === 'save_draft_step1') {
    header('Content-Type: application/json');
    $_SESSION['app_draft'] = [
        'category_id'   => (int)($_POST['category_id'] ?? 0),
        'category_name' => $_POST['category_name'] ?? '',
        'provider_id'   => (int)($_POST['provider_id'] ?? 0),
        'provider_name' => $_POST['provider_name'] ?? '',
        'product_id'    => (int)($_POST['product_id'] ?? 0),
        'product_name'  => $_POST['product_name'] ?? '',
    ];
    echo json_encode(['ok' => true]);
    exit;
}

// ── Clear draft ──
if ($action === 'clear_draft') {
    header('Content-Type: application/json');
    unset($_SESSION['app_draft']);
    echo json_encode(['ok' => true]);
    exit;
}

// ── Step2: form POST submission → save to DB → redirect to step3 ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['page']) && $_GET['page'] === 'new-app' && isset($_GET['step']) && $_GET['step'] === '2save') {
    $draft = $_SESSION['app_draft'] ?? [];
    if (empty($draft['category_id'])) {
        header('Location: partner.php?page=new-app&step=1');
        exit;
    }

    $db = getDB();

    // Get "Προς Έλεγχο" status
    $sRow = $db->query("SELECT id FROM statuses WHERE name='Προς Έλεγχο' LIMIT 1")->fetch();
    if (!$sRow) {
        die('Σφάλμα: δεν βρέθηκε στάτους "Προς Έλεγχο". Παρακαλώ ελέγξτε τη βάση δεδομένων.');
    }

    $code = generateAppCode();
    $p    = $_POST;

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
        $code,
        $user['id'],
        $sRow['id'],
        $draft['category_id'],
        $draft['provider_id'],
        $draft['product_id'],

        $p['customer_type']  ?? 'ΙΔΙΩΤΗΣ',
        $p['connection_type']?? 'ΝΕΑ ΑΡΙΘΜΟΔΟΤΗΣΗ',
        $p['ebill']          ?? 'Ναι',

        trim($p['cust_firstname']  ?? ''),
        trim($p['cust_lastname']   ?? ''),
        trim($p['cust_patronimo']  ?? ''),
        trim($p['cust_adt']        ?? ''),
        $p['cust_birthdate']       ?? null,
        trim($p['cust_afm']        ?? ''),
        trim($p['cust_doy']        ?? ''),
        trim($p['cust_tk']         ?? ''),
        trim($p['cust_nomos']      ?? ''),
        trim($p['cust_poli']       ?? ''),
        trim($p['cust_periochi']   ?? ''),
        trim($p['cust_address']    ?? ''),
        trim($p['cust_number']     ?? ''),
        trim($p['cust_phone']      ?? ''),
        trim($p['cust_kinito']     ?? ''),
        trim($p['cust_email']      ?? ''),

        trim($p['contact_name']     ?? ''),
        trim($p['contact_lastname'] ?? ''),
        trim($p['contact_patronimo']?? ''),
        trim($p['contact_adt']      ?? ''),
        trim($p['contact_phone']    ?? ''),
        trim($p['contact_kinito']   ?? ''),
        trim($p['contact_email']    ?? ''),

        trim($p['prog_phone_activation'] ?? ''),
        trim($p['prog_sim_activation']   ?? ''),
        trim($p['prog_paketo']           ?? $draft['product_name'] ?? ''),
        trim($p['prog_timi']             ?? ''),
        $p['prog_anypsos']               ?? 'Μηνιαίος',
        $p['prog_sim_received']          ?? 'Ναι',
        $p['prog_consent1']              ?? 'Συμφωνώ',
        $p['prog_consent2']              ?? 'Συμφωνώ',
        $p['prog_signature_way']         ?? 'Partner',
        $p['prog_ebill']                 ?? 'Ναι',
        trim($p['notes']                 ?? ''),
    ]);

    $appId = $db->lastInsertId();

    // Update session draft with app info for step 3
    $_SESSION['app_draft'] = array_merge($draft, [
        'app_id'         => $appId,
        'app_code'       => $code,
        'cust_firstname' => trim($p['cust_firstname'] ?? ''),
        'cust_lastname'  => trim($p['cust_lastname']  ?? ''),
    ]);

    header('Location: partner.php?page=new-app&step=3');
    exit;
}

// Fallback
header('Location: partner.php');
exit;
