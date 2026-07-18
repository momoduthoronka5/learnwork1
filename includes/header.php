<?php
require_once __DIR__ . '/functions.php';
$currentPage = $currentPage ?? '';
$pageTitle   = $pageTitle ?? 'My Trusted Care (MTCare)';
$user        = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> &middot; MTCare SL</title>
    <meta name="description" content="My Trusted Care (MTCare) - Sierra Leone's digital health gateway for symptom checks, clinical test suggestions, hospital discovery and appointment booking.">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- App CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg mt-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <span class="mt-logo"><i class="fa-solid fa-heart"></i></span>
            <span class="d-flex flex-column lh-1">
                <span class="mt-brand-name">MTCare <span class="mt-brand-badge">SL</span></span>
                <span class="mt-brand-sub">My Trusted Care</span>
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mtNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mtNav">
            <ul class="navbar-nav mx-auto mt-nav-links gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'about' ? 'active' : '' ?>" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'contact' ? 'active' : '' ?>" href="contact.php">Contact Us</a>
                </li>
                <?php if ($user && $user['role'] === 'patient'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="dashboard.php">Patient Portal</a>
                    </li>
                <?php elseif ($user && $user['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'admin' ? 'active' : '' ?>" href="admin.php">Admin Panel</a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-2 mt-nav-actions">
                <?php if ($user): ?>
                    <span class="mt-user-pill">
                        <span class="mt-dot"></span> <?= e($user['full_name']) ?>
                    </span>
                    <a href="logout.php" class="btn mt-btn-outline btn-sm">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> Sign Out
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn mt-btn-ghost btn-sm">Sign In</a>
                    <a href="register.php" class="btn mt-btn-primary btn-sm px-3">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<?php foreach (get_flashes() as $flash): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : e($flash['type']) ?> alert-dismissible fade show mb-0" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endforeach; ?>
