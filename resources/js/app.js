document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            // Confirmation forms intentionally prevent their first submit event.
            if (event.defaultPrevented || !form.checkValidity()) return;

            const button = event.submitter ?? form.querySelector('button[type="submit"]');
            if (!(button instanceof HTMLButtonElement) || button.disabled) return;

            form.setAttribute('aria-busy', 'true');
            button.classList.add('is-loading');
            button.disabled = true;
        });
    });
});

// Restore controls when a browser returns to this page from its back-forward cache.
window.addEventListener('pageshow', () => {
    document.querySelectorAll('form[aria-busy="true"]').forEach((form) => {
        form.removeAttribute('aria-busy');
        form.querySelectorAll('button.is-loading').forEach((button) => {
            button.classList.remove('is-loading');
            button.disabled = false;
        });
    });
});
