document.addEventListener('DOMContentLoaded', function () {
    var batteryHealthInput = document.getElementById('battery_health_pct');
    var symptomFields = document.getElementById('battery-symptom-fields');

    function toggleSymptomFields() {
        if (!batteryHealthInput || !symptomFields) return;
        var hasHealth = batteryHealthInput.value.trim() !== '';
        symptomFields.style.display = hasHealth ? 'none' : 'block';
    }

    if (batteryHealthInput) {
        batteryHealthInput.addEventListener('input', toggleSymptomFields);
        toggleSymptomFields();
    }

    var ptaSelect = document.getElementById('pta_status');
    var ptaWarning = document.getElementById('pta-warning');
    function togglePtaWarning() {
        if (!ptaSelect || !ptaWarning) return;
        ptaWarning.style.display = ptaSelect.value === 'approved' ? 'none' : 'block';
    }
    if (ptaSelect) {
        ptaSelect.addEventListener('change', togglePtaWarning);
        togglePtaWarning();
    }

    function openSection(id) {
        var el = document.getElementById(id);
        if (!el || el.tagName !== 'DETAILS') return;
        el.open = true;
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    document.querySelectorAll('.next-section').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openSection(btn.getAttribute('data-next'));
        });
    });

    document.querySelectorAll('.step-nav a[data-target]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            openSection(link.getAttribute('data-target'));
        });
    });
});
