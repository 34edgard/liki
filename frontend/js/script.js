document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('passwordInput');
    const emailInput = document.getElementById('emailInput');
    const form = document.querySelector('form');

    // Toggle password visibility
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            // Toggle the type attribute
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle the eye icon (you might replace this with different SVG paths or classes)
            this.querySelector('svg').classList.toggle('bi-eye');
            this.querySelector('svg').classList.toggle('bi-eye-slash'); // Assuming you have a bi-eye-slash icon
        });
    }

    // Form validation (basic example)
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!emailInput.value) {
                emailInput.classList.add('is-invalid');
                event.preventDefault(); // Prevent form submission
            } else {
                emailInput.classList.remove('is-invalid');
            }

            if (!passwordInput.value) {
                passwordInput.classList.add('is-invalid');
                event.preventDefault(); // Prevent form submission
            } else {
                passwordInput.classList.remove('is-invalid');
            }

            // If you have more complex validation, add it here.
            // For a real application, you'd handle server-side validation too.
        });

        // Remove validation feedback when user starts typing
        if (emailInput) {
            emailInput.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                }
            });
        }

        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                }
            });
        }
    }
});