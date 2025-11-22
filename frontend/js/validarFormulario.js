  // JavaScript for Bootstrap form validation (still useful for immediate visual feedback)
  (function() {
    'use strict';
    var form = document.getElementById('diplomaForm');
    if (form) {
      form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    }
  })();