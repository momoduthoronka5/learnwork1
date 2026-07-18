<?php
require_once __DIR__ . '/includes/functions.php';
require_admin();

$currentPage = 'admin';
$pageTitle   = 'Admin Panel';

/* -------------------------------------------------------------
 *  Admin actions (approve / delete appointment)
 * ----------------------------------------------------------- */
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    if ($_GET['action'] === 'approve') {
        db()->prepare('UPDATE appointments SET status = "Confirmed" WHERE id = ?')->execute([$id]);
        set_flash('success', 'Appointment #' . $id . ' confirmed.');
    } elseif ($_GET['action'] === 'complete') {
        db()->prepare('UPDATE appointments SET status = "Completed" WHERE id = ?')->execute([$id]);
        set_flash('success', 'Appointment #' . $id . ' marked completed.');
    } elseif ($_GET['action'] === 'delete') {
        db()->prepare('DELETE FROM appointments WHERE id = ?')->execute([$id]);
        set_flash('success', 'Appointment #' . $id . ' removed.');
    }
    redirect('admin.php');
}

/* -------------------------------------------------------------
 *  Data loads
 * ----------------------------------------------------------- */
$stats = [
    'users'        => count_rows('users'),
    'hospitals'    => count_rows('hospitals'),
    'doctors'      => count_rows('doctors'),
    'appointments' => count_rows('appointments'),
    'symptoms'     => count_rows('symptoms'),
    'rules'        => count_rows('symptom_rules'),
];

$users     = db()->query('SELECT id, full_name, email, phone, role FROM users ORDER BY id')->fetchAll();
$areas     = db()->query('SELECT id, name, description FROM areas ORDER BY id')->fetchAll();
$hospitals = db()->query('SELECT h.id, h.name, h.address, a.name AS area FROM hospitals h LEFT JOIN areas a ON a.id = h.area_id ORDER BY h.id')->fetchAll();
$doctors   = db()->query('SELECT d.id, d.name, d.specialty, h.name AS hospital, d.active FROM doctors d LEFT JOIN hospitals h ON h.id = d.hospital_id ORDER BY d.id')->fetchAll();
$symptoms  = db()->query('SELECT id, name FROM symptoms ORDER BY id')->fetchAll();
$rules     = db()->query('SELECT r.id, s.name AS symptom, r.recommended_test, r.advice FROM symptom_rules r JOIN symptoms s ON s.id = r.symptom_id ORDER BY r.id')->fetchAll();

$appts = db()->query(
    'SELECT a.*, u.full_name AS patient, d.name AS doctor, h.name AS hospital
     FROM appointments a
     LEFT JOIN users u     ON u.id = a.patient_id
     LEFT JOIN doctors d   ON d.id = a.doctor_id
     LEFT JOIN hospitals h ON h.id = a.hospital_id
     ORDER BY a.id'
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="mt-page">
    <div class="container">

        <!-- ===================== HERO ===================== -->
        <div class="mt-admin-hero mb-4">
            <div class="eyebrow">Back-Office Command Hub</div>
            <h2>MTCare System Administration</h2>
            <p>Manage database objects, control clinical mappings, review symptom test rules, and verify active appointments.</p>
        </div>

        <!-- ===================== STATS ===================== -->
        <div class="row g-3 mb-4">
            <?php
            $cards = [
                ['TOTAL USERS', $stats['users']],
                ['HOSPITALS',   $stats['hospitals']],
                ['DOCTORS',     $stats['doctors']],
                ['APPOINTMENTS',$stats['appointments']],
                ['SYMPTOMS',    $stats['symptoms']],
                ['RULES',       $stats['rules']],
            ];
            foreach ($cards as $c): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="mt-stat">
                        <div class="lbl"><?= e($c[0]) ?></div>
                        <div class="val"><?= (int)$c[1] ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- ===================== TABS ===================== -->
        <div class="mt-tabs mb-4">
            <button class="mt-tab active" data-target="tab-dashboard">Dashboard Control</button>
            <button class="mt-tab" data-target="tab-users">Users</button>
            <button class="mt-tab" data-target="tab-areas">Areas</button>
            <button class="mt-tab" data-target="tab-hospitals">Hospitals</button>
            <button class="mt-tab" data-target="tab-doctors">Doctors</button>
            <button class="mt-tab" data-target="tab-symptoms">Symptoms</button>
            <button class="mt-tab" data-target="tab-rules">Symptom Rule Mappings</button>
            <button class="mt-tab" data-target="tab-appointments">Appointments Status</button>
        </div>

        <!-- ===================== DASHBOARD CONTROL ===================== -->
        <div class="mt-tab-pane" id="tab-dashboard">
            <div class="mt-panel">
                <h5 class="mb-3">Dashboard Control</h5>
                <p class="text-secondary">Welcome to the MTCare back office. Use the tabs above to inspect every database object: registered users, provincial areas, health facilities, resident doctors, tracked symptoms, the clinical rule mappings, and live appointment statuses.</p>
                <div class="row g-3 mt-2">
                    <div class="col-md-4"><div class="p-3 rounded-3" style="background:#ecfdf5;"><strong><?= $stats['appointments'] ?></strong> appointments in the system.</div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3" style="background:#eff6ff;"><strong><?= $stats['doctors'] ?></strong> active resident doctors.</div></div>
                    <div class="col-md-4"><div class="p-3 rounded-3" style="background:#fef9e7;"><strong><?= $stats['rules'] ?></strong> symptom-to-test rules configured.</div></div>
                </div>
            </div>
        </div>

        <!-- ===================== USERS ===================== -->
        <div class="mt-tab-pane" id="tab-users" style="display:none;">
            <div class="mt-panel">
                <h5 class="mb-3">Registered Users</h5>
                <div class="table-responsive">
                    <table class="mt-table">
                        <thead><tr><th>ID</th><th>Full Name</th><th>Email</th><th>Phone</th><th>Role</th></tr></thead>
                        <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><strong><?= (int)$u['id'] ?></strong></td>
                                <td><?= e($u['full_name']) ?></td>
                                <td><?= e($u['email']) ?></td>
                                <td><?= e($u['phone']) ?></td>
                                <td><span class="mt-badge <?= $u['role'] === 'admin' ? 'badge-pending' : 'badge-completed' ?>"><?= strtoupper(e($u['role'])) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ===================== AREAS ===================== -->
        <div class="mt-tab-pane" id="tab-areas" style="display:none;">
            <div class="mt-panel">
                <h5 class="mb-3">Provinces &amp; Areas</h5>
                <div class="table-responsive">
                    <table class="mt-table">
                        <thead><tr><th>ID</th><th>Area / Province</th><th>Description</th></tr></thead>
                        <tbody>
                        <?php foreach ($areas as $a): ?>
                            <tr><td><strong><?= (int)$a['id'] ?></strong></td><td><?= e($a['name']) ?></td><td class="text-secondary"><?= e($a['description']) ?></td></tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ===================== HOSPITALS ===================== -->
        <div class="mt-tab-pane" id="tab-hospitals" style="display:none;">
            <div class="mt-panel">
                <h5 class="mb-3">Registered Health Facilities</h5>
                <div class="table-responsive">
                    <table class="mt-table">
                        <thead><tr><th>ID</th><th>Hospital</th><th>Area</th><th>Address</th></tr></thead>
                        <tbody>
                        <?php foreach ($hospitals as $h): ?>
                            <tr><td><strong><?= (int)$h['id'] ?></strong></td><td class="mt-hosp-link"><?= e($h['name']) ?></td><td><?= e($h['area']) ?></td><td class="text-secondary"><?= e($h['address']) ?></td></tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ===================== DOCTORS ===================== -->
        <div class="mt-tab-pane" id="tab-doctors" style="display:none;">
            <div class="mt-panel">
                <h5 class="mb-3">Resident Doctors</h5>
                <div class="table-responsive">
                    <table class="mt-table">
                        <thead><tr><th>ID</th><th>Doctor</th><th>Specialty</th><th>Hospital</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($doctors as $d): ?>
                            <tr>
                                <td><strong><?= (int)$d['id'] ?></strong></td>
                                <td><?= e($d['name']) ?></td>
                                <td class="text-secondary"><?= e($d['specialty']) ?></td>
                                <td class="mt-hosp-link"><?= e($d['hospital']) ?></td>
                                <td><span class="mt-badge <?= $d['active'] ? 'badge-confirmed' : 'badge-cancelled' ?>"><?= $d['active'] ? 'ACTIVE' : 'INACTIVE' ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ===================== SYMPTOMS ===================== -->
        <div class="mt-tab-pane" id="tab-symptoms" style="display:none;">
            <div class="mt-panel">
                <h5 class="mb-3">Tracked Symptoms</h5>
                <div class="row g-2">
                    <?php foreach ($symptoms as $s): ?>
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-3 text-center fw-semibold" style="background:#f0fdf4;border:1px solid #d1fae5;color:#047857;">
                                <i class="fa-solid fa-notes-medical me-1"></i> <?= e($s['name']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ===================== RULE MAPPINGS ===================== -->
        <div class="mt-tab-pane" id="tab-rules" style="display:none;">
            <div class="mt-panel">
                <h5 class="mb-3">Symptom Rule Mappings</h5>
                <div class="table-responsive">
                    <table class="mt-table">
                        <thead><tr><th>ID</th><th>Symptom</th><th>Recommended Test</th><th>Clinical Advice</th></tr></thead>
                        <tbody>
                        <?php foreach ($rules as $r): ?>
                            <tr>
                                <td><strong><?= (int)$r['id'] ?></strong></td>
                                <td class="fw-semibold"><?= e($r['symptom']) ?></td>
                                <td class="mt-hosp-link"><?= e($r['recommended_test']) ?></td>
                                <td class="text-secondary"><?= e($r['advice']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ===================== APPOINTMENTS ===================== -->
        <div class="mt-tab-pane" id="tab-appointments" style="display:none;">
            <div class="mt-panel">
                <h5 class="mb-3">Pending and Confirmed Consultations</h5>
                <div class="table-responsive">
                    <table class="mt-table">
                        <thead>
                            <tr>
                                <th>ID</th><th>Patient</th><th>Doctor</th><th>Hospital Location</th>
                                <th>Appointment Date</th><th>Time Slot</th><th>Status</th><th>Approve Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($appts as $a): ?>
                            <tr>
                                <td><strong><?= (int)$a['id'] ?></strong></td>
                                <td class="fw-semibold"><?= e($a['patient']) ?></td>
                                <td><?= e($a['doctor'] ?? '—') ?></td>
                                <td class="mt-hosp-link"><?= e($a['hospital'] ?? '—') ?></td>
                                <td><?= e($a['appointment_date']) ?></td>
                                <td class="fw-semibold"><?= e($a['time_slot']) ?></td>
                                <td><?= status_badge($a['status']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 justify-content-end">
                                        <?php if ($a['status'] === 'Pending'): ?>
                                            <a href="admin.php?action=approve&id=<?= (int)$a['id'] ?>" class="mt-approve-btn">Approve</a>
                                        <?php endif; ?>
                                        <a href="admin.php?action=delete&id=<?= (int)$a['id'] ?>" class="mt-del-btn" onclick="return confirm('Delete this appointment?');" title="Delete">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
