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
});
