function goToStep(step) {
    document.querySelectorAll('.form-step').forEach(function (panel) {
        panel.hidden = panel.id !== 'formStep' + step;
    });

    document.querySelectorAll('.stepper-step').forEach(function (stepEl) {
        const stepNumber = parseInt(stepEl.dataset.step, 10);
        stepEl.classList.remove('active', 'completed');
        if (stepNumber === step) {
            stepEl.classList.add('active');
        } else if (stepNumber < step) {
            stepEl.classList.add('completed');
        }
    });

    window.scrollTo({ top: 0, behavior: 'smooth' });
}