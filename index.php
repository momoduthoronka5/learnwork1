<?php
require_once __DIR__ . '/includes/functions.php';
$currentPage = 'home';
$pageTitle   = 'Home';
require __DIR__ . '/includes/header.php';
?>

<!-- ============================ HERO ============================ -->
<section class="mt-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="mt-hero-badge">
                    <i class="fa-solid fa-shield-heart"></i> SL &middot; Tailored for Sierra Leone Health Access
                </span>
                <h1>Your Reliable Pathway to <span class="hl">Quality Care</span> and Diagnoses.</h1>
                <p class="lead">
                    Check critical medical symptoms, view highly recommended screening tests,
                    look up verified hospitals across all provinces, and book primary care
                    appointments seamlessly.
                </p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="register.php" class="btn mt-btn-hero-primary">Get Started Instantly</a>
                    <a href="about.php" class="btn mt-btn-hero-outline">Learn How It Works</a>
                </div>

                <div class="mt-4 pt-2">
                    <div class="mt-demo-access mb-2">Quick Mock Demo Access:</div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="login.php?demo=patient" class="mt-demo-chip"><span class="dot-green"></span> Login as Demo Patient</a>
                        <a href="login.php?demo=admin" class="mt-demo-chip"><span class="dot-amber"></span> Login as Demo Admin</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="mt-hero-steps">
                    <div class="mt-hero-steps-title">
                        <span class="chk"><i class="fa-solid fa-check"></i></span> Easy Steps to Access
                    </div>
                    <div class="mt-step">
                        <span class="mt-step-num">1</span>
                        <div><h6>Select Symptoms</h6><p>Pick symptoms like Fever, Cough, or Stomach Pain to get matching recommendations.</p></div>
                    </div>
                    <div class="mt-step">
                        <span class="mt-step-num">2</span>
                        <div><h6>Identify Recommended Tests</h6><p>See if you might require Malaria RDT, CBC, or Typhoid testing before visiting.</p></div>
                    </div>
                    <div class="mt-step">
                        <span class="mt-step-num">3</span>
                        <div><h6>Locate Certified Facilities</h6><p>Discover hospitals with active staff in Freetown, Bo, Kenema, Makeni, or Port Loko.</p></div>
                    </div>
                    <div class="mt-step">
                        <span class="mt-step-num">4</span>
                        <div><h6>Secure Doctor Appointment</h6><p>Complete booking without manually visiting the clinic and waiting for hours.</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======================== FEATURES ========================= -->
<section class="mt-section">
    <div class="container">
        <h2 class="mt-section-title">Empowering Every Patient</h2>
        <p class="mt-section-sub">MTCare coordinates scattered healthcare resources directly into an accessible workspace.</p>

        <div class="row g-4 mt-3">
            <div class="col-md-4">
                <div class="mt-feature-card">
                    <span class="mt-feature-icon mt-icon-green"><i class="fa-solid fa-clipboard-list"></i></span>
                    <h5>Smart Symptom Review</h5>
                    <p>Avoid guesswork. Select specific health changes and find appropriate diagnostic tests mapped according to standardized clinical rules.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mt-feature-card">
                    <span class="mt-feature-icon mt-icon-blue"><i class="fa-solid fa-location-dot"></i></span>
                    <h5>Location-Based Discovery</h5>
                    <p>Find nearest healthcare providers across the Western Area and Eastern, Northern, Southern, or North-West Provinces without needing constant GPS.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mt-feature-card">
                    <span class="mt-feature-icon mt-icon-green"><i class="fa-solid fa-calendar-check"></i></span>
                    <h5>Real-Time Bookings</h5>
                    <p>Book direct visits with active resident doctors, and track progress status (Pending, Confirmed, Completed) straight from your custom portal.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======================== ADVISORY ========================= -->
<section class="mt-advisory">
    <div class="container">
        <div class="mt-advisory-inner">
            <span class="mt-advisory-icon"><i class="fa-solid fa-circle-info"></i></span>
            <div>
                <h6>MTCare Informational Advisory</h6>
                <p>This system maps malaria rapid diagnostic assays, typhoid examinations, and other standard diagnostic regimens. It is intended to guide you towards correct health facilities and is not a replacement for immediate emergency clinical interventions.</p>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
