<?php
require_once __DIR__ . '/includes/functions.php';
$currentPage = 'contact';
$pageTitle   = 'Contact Us';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $errors[] = 'Session expired. Please try again.';
    } else {
        $name = trim($_POST['full_name'] ?? '');
        $mail = trim($_POST['email'] ?? '');
        $msg  = trim($_POST['message'] ?? '');
        if ($name === '' || $mail === '' || $msg === '') {
            $errors[] = 'All fields are required.';
        } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        } else {
            $stmt = db()->prepare('INSERT INTO contact_messages (full_name, email, message) VALUES (?,?,?)');
            $stmt->execute([$name, $mail, $msg]);
            set_flash('success', 'Thank you, ' . $name . '. Your message has reached the MTCare support desk.');
            redirect('contact.php');
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<section class="mt-page">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5">
                <h1 class="fw-bold" style="letter-spacing:-1px;">Contact MTCare Support</h1>
                <p class="text-secondary mt-3">
                    Our support team is on standby to assist with patient accounts, registration of new
                    health facilities, and clinical service coordination.
                </p>
                <div class="mt-contact-meta mt-4">
                    <p class="mb-3"><strong>Freetown HQ Address:</strong><br>
                        <span class="text-secondary">Percival Street, Central Freetown, Sierra Leone</span></p>
                    <p class="mb-3"><strong>Support Helpline:</strong>
                        <span class="text-secondary">+232 76 123456 / +232 30 456789</span></p>
                    <p class="mb-0"><strong>Email:</strong>
                        <a href="mailto:support@mtcare.com">support@mtcare.com</a></p>
                </div>
            </div>

            <div class="col-lg-6 offset-lg-1">
                <div class="mt-card">
                    <?php if ($errors): ?>
                        <div class="alert alert-danger"><?= e(implode(' ', $errors)) ?></div>
                    <?php endif; ?>
                    <form method="post" novalidate>
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="mt-label">Your Full Name</label>
                            <input type="text" name="full_name" class="mt-input" placeholder="e.g. Marie Koroma" required>
                        </div>
                        <div class="mb-3">
                            <label class="mt-label">Your Email</label>
                            <input type="email" name="email" class="mt-input" placeholder="e.g. marie@gmail.com" required>
                        </div>
                        <div class="mb-4">
                            <label class="mt-label">Message</label>
                            <textarea name="message" class="mt-input" placeholder="Describe how we can help you…" required></textarea>
                        </div>
                        <button type="submit" class="btn mt-btn-primary w-100 py-2">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
