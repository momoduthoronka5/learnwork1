/* =============================================================
   MTCare — Front-end interactivity
   ============================================================= */
(function () {
    "use strict";

    /* ----------------------------------------------------------
       Symptom checker (dashboard) — toggle chips + AJAX analyse
       -------------------------------------------------------- */
    const symptomChips = document.querySelectorAll(".mt-symptom");
    symptomChips.forEach(function (chip) {
        const input = chip.querySelector("input");
        chip.addEventListener("click", function (e) {
            if (e.target.tagName !== "INPUT") {
                input.checked = !input.checked;
            }
            chip.classList.toggle("selected", input.checked);
        });
        if (input && input.checked) chip.classList.add("selected");
    });

    const analyzeBtn = document.getElementById("analyzeBtn");
    if (analyzeBtn) {
        analyzeBtn.addEventListener("click", function () {
            const ids = Array.from(
                document.querySelectorAll(".mt-symptom input:checked")
            ).map((i) => i.value);

            const resultBox = document.getElementById("symptomResults");
            if (!ids.length) {
                resultBox.innerHTML =
                    '<div class="text-muted small py-2">Please select at least one symptom to analyze.</div>';
                return;
            }

            analyzeBtn.disabled = true;
            analyzeBtn.innerHTML =
                '<i class="fa-solid fa-spinner fa-spin me-2"></i>Analyzing…';

            const body = new URLSearchParams();
            ids.forEach((id) => body.append("symptoms[]", id));

            fetch("analyze_symptoms.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: body.toString(),
            })
                .then((r) => r.json())
                .then((data) => {
                    if (!data.length) {
                        resultBox.innerHTML =
                            '<div class="text-muted small py-2">No mapping rules matched your selection.</div>';
                    } else {
                        resultBox.innerHTML =
                            '<h6 class="fw-bold mb-3 mt-2"><i class="fa-solid fa-stethoscope me-2 text-success"></i>Recommended Clinical Screenings</h6>' +
                            data
                                .map(
                                    (r) =>
                                        '<div class="mt-result-item">' +
                                        "<h6>" + escapeHtml(r.symptom) + "</h6>" +
                                        '<div class="test"><i class="fa-solid fa-flask me-1"></i>' +
                                        escapeHtml(r.recommended_test) + "</div>" +
                                        '<p class="advice">' + escapeHtml(r.advice) + "</p>" +
                                        "</div>"
                                )
                                .join("");
                    }
                })
                .catch(function () {
                    resultBox.innerHTML =
                        '<div class="text-danger small py-2">Something went wrong. Please try again.</div>';
                })
                .finally(function () {
                    analyzeBtn.disabled = false;
                    analyzeBtn.innerHTML =
                        '<i class="fa-solid fa-wand-magic-sparkles me-2"></i>Analyze Symptom Mappings';
                });
        });
    }

    /* ----------------------------------------------------------
       Appointment booking wizard (dashboard) — 4 steps
       -------------------------------------------------------- */
    const wizard = document.getElementById("bookingWizard");
    if (wizard) {
        let step = 1;
        const totalSteps = 4;
        const bars = wizard.querySelectorAll(".mt-wizard-steps .bar");
        const panes = wizard.querySelectorAll("[data-step]");
        const stepLabel = document.getElementById("wizardStepLabel");
        const backBtn = document.getElementById("wizardBack");
        const nextBtn = document.getElementById("wizardNext");

        function render() {
            panes.forEach((p) => {
                p.style.display = Number(p.dataset.step) === step ? "block" : "none";
            });
            bars.forEach((b, i) => b.classList.toggle("active", i < step));
            if (stepLabel) stepLabel.textContent = "Step " + step + " of " + totalSteps;
            backBtn.style.visibility = step === 1 ? "hidden" : "visible";
            nextBtn.innerHTML =
                step === totalSteps
                    ? 'Confirm Booking <i class="fa-solid fa-check ms-1"></i>'
                    : 'Continue <i class="fa-solid fa-arrow-right ms-1"></i>';
        }

        // selectable tiles (single select within a step group)
        wizard.querySelectorAll("[data-group]").forEach(function (tile) {
            tile.addEventListener("click", function () {
                const group = tile.dataset.group;
                wizard
                    .querySelectorAll('[data-group="' + group + '"]')
                    .forEach((t) => t.classList.remove("selected"));
                tile.classList.add("selected");
                const radio = tile.querySelector("input");
                if (radio) radio.checked = true;
            });
        });

        nextBtn.addEventListener("click", function (e) {
            if (step < totalSteps) {
                e.preventDefault();
                step++;
                render();
            }
            // On final step the button submits the form naturally.
        });
        backBtn.addEventListener("click", function (e) {
            e.preventDefault();
            if (step > 1) { step--; render(); }
        });
        render();
    }

    /* ----------------------------------------------------------
       Admin tabs
       -------------------------------------------------------- */
    const tabs = document.querySelectorAll(".mt-tab");
    if (tabs.length) {
        tabs.forEach(function (tab) {
            tab.addEventListener("click", function () {
                tabs.forEach((t) => t.classList.remove("active"));
                tab.classList.add("active");
                const target = tab.dataset.target;
                document.querySelectorAll(".mt-tab-pane").forEach(function (pane) {
                    pane.style.display = pane.id === target ? "block" : "none";
                });
            });
        });
    }

    /* ----------------------------------------------------------
       Utility
       -------------------------------------------------------- */
    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
})();
