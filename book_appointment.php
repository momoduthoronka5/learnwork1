<?php
/**
 * Handles the 4-step appointment booking wizard submission.
 */
require_once __DIR__ . '/includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
    redirect('dashboard.php');
}

$user       = current_user();
$areaId     = (int) ($_POST['area_id'] ?? 0);
$hospitalId = (int) ($_POST['hospital_id'] ?? 0);
$doctorId   = (int) ($_POST['doctor_id'] ?? 0);
$date       = trim($_POST['appointment_date'] ?? '');
$slot       = trim($_POST['time_slot'] ?? '');

if (!$hospitalId || !$doctorId || $date === '' || $slot === '') {
    set_flash('error', 'Please complete all booking steps before confirming.');
    redirect('dashboard.php');
}

$stmt = db()->prepare(
    'INSERT INTO appointments (patient_id, doctor_id, hospital_id, appointment_date, time_slot, status)
     VALUES (?,?,?,?,?, "Pending")'
);
$stmt->execute([$user['id'], $doctorId, $hospitalId, $date, $slot]);

set_flash('success', 'Appointment request submitted! Status is now Pending admin confirmation.');
redirect('dashboard.php');
