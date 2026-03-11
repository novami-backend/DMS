<!-- Common Scripts Component -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Common JavaScript functions
    function showSuccessMessage(message) {
        // Create alert dynamically
        const alertHtml = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.querySelector('.main-content').insertAdjacentHTML('afterbegin', alertHtml);
    }

    function showErrorMessage(message) {
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.querySelector('.main-content').insertAdjacentHTML('afterbegin', alertHtml);
    }

    // Auto-hide alerts after 5 seconds
    setTimeout(function () {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            setTimeout(() => bsAlert.close(), 5000);
        });
    }, 1000);

    // Initialize any Bootstrap dropdowns that use data-bs-toggle="dropdown".
    function initDropdowns() {
        var ddToggles = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        ddToggles.forEach(function(el) {
            new bootstrap.Dropdown(el);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDropdowns);
    } else {
        // DOMContentLoaded already fired
        initDropdowns();
    }
</script>