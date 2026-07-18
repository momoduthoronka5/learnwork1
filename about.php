<?php
require_once __DIR__ . '/includes/functions.php';
$currentPage = 'about';
$pageTitle   = 'About';
require __DIR__ . '/includes/header.php';
?>

<section class="mt-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <span class="mt-eyebrow">Our Core Purpose</span>
                <h1 class="fw-bold mt-3" style="letter-spacing:-1px;">Improving Sierra Leone Healthcare Access</h1>

                <p class="text-secondary mt-3" style="max-width:820px;">
                    My Trusted Care (MTCare) was founded in 2026 to solve the critical challenges of
                    distributed, uncoordinated health facility information, leading to delays in standard
                    diagnostics and bookings.
                </p>
                <p class="text-secondary" style="max-width:820px;">
                    We realize that many patients spend valuable hours traveling between local clinics, or are
                    unaware of what diagnostic tests they require before professional consultations. MTCare bridges
                    this information gap. By matching standard clinical guidelines with interactive diagnostic
                    mapping tools, we put power back in the hands of the patients.
                </p>

                <h3 class="fw-bold mt-5 mb-3">Our Primary Goals</h3>
                <ul class="list-unstyled">
                    <li class="mb-3 d-flex gap-2">
                        <i class="fa-solid fa-circle text-success mt-2" style="font-size:.5rem;"></i>
                        <span><strong>Connect Providers:</strong> Maintain an updated database of active registered clinical health doctors and health centers.</span>
                    </li>
                    <li class="mb-3 d-flex gap-2">
                        <i class="fa-solid fa-circle text-success mt-2" style="font-size:.5rem;"></i>
                        <span><strong>Explain Screening Tests:</strong> Demystify early screening methods such as CBC checks and Malaria Rapid Diagnostic tests.</span>
                    </li>
                    <li class="mb-3 d-flex gap-2">
                        <i class="fa-solid fa-circle text-success mt-2" style="font-size:.5rem;"></i>
                        <span><strong>Simplify Scheduling:</strong> Eliminate queue bottlenecks inside primary referral units.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
