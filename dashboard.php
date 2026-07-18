<?php
require_once __DIR__ . '/includes/functions.php';
require_login();
if (is_admin()) {
    redirect('admin.php');
}

$currentPage = 'dashboard';
$pageTitle   = 'Patient Portal';
$user        = current_user();

// Data for symptom checker + booking wizard.
$symptoms  = db()->query('SELECT id, name FROM symptoms ORDER BY id')->fetchAll();
$areas     = db()->query('SELECT id, name FROM areas ORDER BY id')->fetchAll();
$hospitals = db()->query('SELECT id, name, area_id FROM hospitals ORDER BY name')->fetchAll();
$doctors   = db()->query('SELECT id, name, specialty, hospital_id FROM doctors WHERE active = 1 ORDER BY name')->fetchAll();

// Patient's appointment history.
$apptStmt = db()->prepare(
    'SELECT a.*, d.name AS doctor, d.specialty, h.name AS hospital
     FROM appointments a
     LEFT JOIN doctors d   ON d.id = a.doctor_id
     LEFT JOIN hospitals h ON h.id = a.hospital_id
     WHERE a.patient_id = ?
     ORDER BY a.appointment_date DESC'
);
$apptStmt->execute([$user['id']]);
$appointments = $apptStmt->fetchAll();

// Fresh copy of profile.
$pStmt = db()->prepare('SELECT full_name, phone, email FROM users WHERE id = ?');
$pStmt->execute([$user['id']]);
$profile = $pStmt->fetch();

require __DIR__ . '/includes/header.php';
?>

<section class="mt-page">
    <div class="container">

        <!-- ===================== HERO ===================== -->
        <div class="mt-dash-hero mb-4">
            <div class="row align-items-center">
                <div class="col-lg-9">
                    <div class="eyebrow">Sierra Leone Patient Portal</div>
                    <h2>Hello, <?= e($profile['full_name']) ?></h2>
                    <p>Configure symptoms, seek matching clinical tests, locate regional hospitals, and control appointments.</p>
                </div>
                <div class="col-lg-3 text-lg-end mt-3 mt-lg-0">
                    <a href="#bookingWizard" class="btn btn-light fw-bold px-4 py-2" style="color:#065f46;border-radius:10px;">
                        <i class="fa-solid fa-calendar-plus me-1"></i> Book Appointment
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- ===================== LEFT COLUMN ===================== -->
            <div class="col-lg-7">

                <!-- Symptom checker -->
                <div class="mt-card mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h3 class="mt-card-title"><span class="ic"><i class="fa-solid fa-clipboard-list"></i></span> Interactive Symptom Checker</h3>
                        <span class="mt-card-link">Clinical Mappings</span>
                    </div>
                    <p class="text-secondary small mt-2">
                        Select your current health symptoms below. Our custom rule engine maps them against
                        standard clinical screenings like Malaria rapid diagnostics, Widal assays, and ultrasounds.
                    </p>

                    <div class="row g-2 mt-1">
                        <?php foreach ($symptoms as $s): ?>
                            <div class="col-6 col-md-3">
                                <label class="mt-symptom">
                                    <input type="checkbox" value="<?= (int)$s['id'] ?>">
                                    <?= e($s['name']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button id="analyzeBtn" class="btn mt-btn-primary mt-4 px-3">
                        <i class="fa-solid fa-wand-magic-sparkles me-2"></i>Analyze Symptom Mappings
                    </button>

                    <div id="symptomResults" class="mt-4"></div>
                </div>

                <!-- Booking wizard -->
                <div class="mt-card" id="bookingWizard">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="mt-card-title"><span class="ic" style="background:#dbeafe;color:#2563eb;"><i class="fa-solid fa-calendar-days"></i></span> Book Doctor Appointment</h3>
                        <span id="wizardStepLabel" class="mt-card-link">Step 1 of 4</span>
                    </div>

                    <div class="mt-wizard-steps mb-4">
                        <span class="bar active"></span><span class="bar"></span><span class="bar"></span><span class="bar"></span>
                    </div>

                    <form method="post" action="book_appointment.php">
                        <?= csrf_field() ?>

                        <!-- Step 1: Area -->
                        <div data-step="1">
                            <p class="text-secondary small mb-3">Select your current residential Area or Province across Sierra Leone:</p>
                            <div class="row g-2">
                                <?php foreach ($areas as $a): ?>
                                    <div class="col-md-6">
                                        <div class="mt-select-tile" data-group="area">
                                            <i class="fa-solid fa-location-crosshairs"></i>
                                            <span><?= e($a['name']) ?></span>
                                            <input type="radio" name="area_id" value="<?= (int)$a['id'] ?>" hidden>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Step 2: Hospital -->
                        <div data-step="2" style="display:none;">
                            <p class="text-secondary small mb-3">Choose an available health facility:</p>
                            <div class="row g-2">
                                <?php foreach ($hospitals as $h): ?>
                                    <div class="col-md-6">
                                        <div class="mt-select-tile" data-group="hospital">
                                            <i class="fa-solid fa-hospital"></i>
                                            <span><?= e($h['name']) ?></span>
                                            <input type="radio" name="hospital_id" value="<?= (int)$h['id'] ?>" hidden>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Step 3: Doctor -->
                        <div data-step="3" style="display:none;">
                            <p class="text-secondary small mb-3">Select a resident doctor for your consultation:</p>
                            <div class="row g-2">
                                <?php foreach ($doctors as $d): ?>
                                    <div class="col-md-6">
                                        <div class="mt-select-tile" data-group="doctor">
                                            <i class="fa-solid fa-user-doctor"></i>
                                            <span><?= e($d['name']) ?> &middot; <small class="text-secondary"><?= e($d['specialty']) ?></small></span>
                                            <input type="radio" name="doctor_id" value="<?= (int)$d['id'] ?>" hidden>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Step 4: Date & Time -->
                        <div data-step="4" style="display:none;">
                            <p class="text-secondary small mb-3">Pick your preferred appointment date and time slot:</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="mt-label">Appointment Date</label>
                                    <input type="date" name="appointment_date" class="mt-input" min="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="mt-label">Time Slot</label>
                                    <select name="time_slot" class="mt-input" required>
                                        <option value="">Select a slot…</option>
                                        <option>09:00 AM</option>
                                        <option>10:30 AM</option>
                                        <option>12:00 PM</option>
                                        <option>02:00 PM</option>
                                        <option>03:30 PM</option>
                                        <option>05:00 PM</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top">
                            <button id="wizardBack" type="button" class="btn mt-btn-ghost btn-sm" style="visibility:hidden;">&larr; Back</button>
                            <button id="wizardNext" type="submit" class="btn mt-btn-primary px-4">Continue <i class="fa-solid fa-arrow-right ms-1"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ===================== RIGHT COLUMN ===================== -->
            <div class="col-lg-5">

                <!-- Appointment history -->
                <div class="mt-card mb-4">
                    <h3 class="mt-card-title mb-1"><span class="ic"><i class="fa-solid fa-clock-rotate-left"></i></span> Appointment History</h3>
                    <p class="text-secondary small mt-2 mb-3">View and track clinical consult requests.</p>

                    <?php if (!$appointments): ?>
                        <div class="text-secondary small py-3">No appointments booked yet. Use the wizard to request one.</div>
                    <?php else: ?>
                        <?php foreach ($appointments as $ap): ?>
                            <div class="mt-appt">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="doc"><?= e($ap['doctor'] ?? 'Unassigned') ?></div>
                                        <div class="spec"><?= e($ap['specialty'] ?? '') ?></div>
                                    </div>
                                    <?= status_badge($ap['status']) ?>
                                </div>
                                <div class="meta"><i class="fa-solid fa-hospital"></i> <?= e($ap['hospital'] ?? '—') ?></div>
                                <div class="meta"><i class="fa-solid fa-calendar"></i> <?= e($ap['appointment_date']) ?> at <?= e($ap['time_slot']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Security & profile -->
                <div class="mt-card">
                    <h3 class="mt-card-title mb-1"><span class="ic" style="background:#ede9fe;color:#7c3aed;"><i class="fa-solid fa-user-shield"></i></span> Security &amp; Profile</h3>
                    <p class="text-secondary small mt-2 mb-3">Update registered contact details instantly.</p>

                    <form method="post" action="update_profile.php">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="mt-label">Your Full Name</label>
                            <input type="text" name="full_name" class="mt-input" value="<?= e($profile['full_name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="mt-label">Contact Phone</label>
                            <input type="text" name="phone" class="mt-input" value="<?= e($profile['phone']) ?>">
                        </div>
                        <div class="mb-4">
                            <label class="mt-label">Edit Password</label>
                            <input type="password" name="password" class="mt-input" placeholder="••••••••••">
                            <small class="text-secondary" style="font-size:.72rem;">Leave blank to keep current password.</small>
                        </div>
                        <button type="submit" class="btn mt-btn-dark w-100 py-2">Update Profile Details</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
