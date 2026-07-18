<?php
/**
 * AJAX endpoint: receives selected symptom IDs, returns the
 * mapped clinical test recommendations as JSON (rule engine).
 */
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/json');

$ids = $_POST['symptoms'] ?? [];
if (!is_array($ids)) {
    $ids = [];
}

echo json_encode(analyze_symptoms($ids));
