<?php
/**
 * MTCare - Core helpers, session bootstrap & auth utilities
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* -------------------------------------------------------------
 *  Output / security helpers
 * ----------------------------------------------------------- */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function base_url(string $path = ''): string
{
    return $path;
}

/* -------------------------------------------------------------
 *  Session / auth helpers
 * ----------------------------------------------------------- */
function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_admin(): bool
{
    return is_logged_in() && ($_SESSION['user']['role'] ?? '') === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function require_admin(): void
{
    if (!is_admin()) {
        redirect('login.php');
    }
}

/**
 * Attempt to authenticate a user. Returns the user row or null.
 */
function attempt_login(string $email, string $password): ?array
{
    $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        return $user;
    }
    return null;
}

/**
 * Store the authenticated user in the session.
 */
function login_user(array $user): void
{
    $_SESSION['user'] = [
        'id'        => (int) $user['id'],
        'full_name' => $user['full_name'],
        'email'     => $user['email'],
        'phone'     => $user['phone'],
        'role'      => $user['role'],
    ];
}

/* -------------------------------------------------------------
 *  CSRF protection
 * ----------------------------------------------------------- */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function verify_csrf(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

/* -------------------------------------------------------------
 *  Flash messages
 * ----------------------------------------------------------- */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

/* -------------------------------------------------------------
 *  Symptom rule engine
 *  Maps selected symptom IDs to their recommended clinical tests.
 * ----------------------------------------------------------- */
function analyze_symptoms(array $symptomIds): array
{
    $symptomIds = array_values(array_filter(array_map('intval', $symptomIds)));
    if (!$symptomIds) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($symptomIds), '?'));
    $sql = "SELECT s.id AS symptom_id, s.name AS symptom,
                   r.recommended_test, r.advice
            FROM symptoms s
            JOIN symptom_rules r ON r.symptom_id = s.id
            WHERE s.id IN ($placeholders)
            ORDER BY s.id";
    $stmt = db()->prepare($sql);
    $stmt->execute($symptomIds);
    return $stmt->fetchAll();
}

/* -------------------------------------------------------------
 *  Status badge helper (Bootstrap + custom colours)
 * ----------------------------------------------------------- */
function status_badge(string $status): string
{
    $map = [
        'Confirmed' => 'badge-confirmed',
        'Pending'   => 'badge-pending',
        'Completed' => 'badge-completed',
        'Cancelled' => 'badge-cancelled',
    ];
    $class = $map[$status] ?? 'badge-pending';
    return '<span class="mt-badge ' . $class . '">' . strtoupper(e($status)) . '</span>';
}

/* -------------------------------------------------------------
 *  Dashboard counters (admin)
 * ----------------------------------------------------------- */
function count_rows(string $table): int
{
    $allowed = ['users', 'hospitals', 'doctors', 'appointments', 'symptoms', 'symptom_rules', 'areas'];
    if (!in_array($table, $allowed, true)) {
        return 0;
    }
    return (int) db()->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
}
