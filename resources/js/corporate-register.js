document.addEventListener('DOMContentLoaded', function() {
    // Variables for step navigation
    const steps = document.querySelectorAll('.step');
    const stepContents = document.querySelectorAll('.registration-step');
    let currentStep = 1;

    // File upload handling
    setupFileUpload('certificate_file', 'certificate_file_name');
    setupFileUpload('tax_clearance_file', 'tax_clearance_file_name');
    setupFileUpload('business_license_file', 'business_license_file_name');
    setupFileUpload('director_id_file', 'director_id_file_name');

    function setupFileUpload(inputId, displayId) {
        const input = document.getElementById(inputId);
        const display = document.getElementById(displayId);
        const nameSpan = display.querySelector('span');

        input.addEventListener('change', function() {
            if (input.files.length > 0) {
                // Validate file size (max 5MB)
                if (input.files[0].size > 5 * 1024 * 1024) {
                    alert('File size exceeds 5MB limit. Please select a smaller file.');
                    input.value = '';
                    display.classList.add('hidden');
                    return;
                }

                // Validate file type
                const fileType = input.files[0].type;
                if (!['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'].includes(fileType)) {
                    alert('Invalid file type. Please upload PDF, JPG, or PNG files only.');
                    input.value = '';
                    display.classList.add('hidden');
                    return;
                }

                nameSpan.textContent = input.files[0].name;
                display.classList.remove('hidden');
            } else {
                display.classList.add('hidden');
            }
        });
    }

    // Password visibility toggle
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const type = input.getAttribute('type');

            if (type === 'password') {
                input.setAttribute('type', 'text');
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                input.setAttribute('type', 'password');
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    });

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const strengthIndicators = document.querySelectorAll('.strength-indicator');
    const strengthText = document.querySelector('.strength-text span');
    const strengthContainer = document.querySelector('.password-strength');

    passwordInput.addEventListener('input', function() {
        const password = this.value;

        if (password.length > 0) {
            strengthContainer.classList.remove('hidden');

            // Reset indicators
            strengthIndicators.forEach(indicator => {
                indicator.className = 'h-1 w-1/4 rounded bg-gray-200 strength-indicator';
            });

            // Calculate strength
            let strength = 0;

            // Length check
            if (password.length >= 8) strength++;

            // Contains lowercase
            if (/[a-z]/.test(password)) strength++;

            // Contains uppercase
            if (/[A-Z]/.test(password)) strength++;

            // Contains number or special char
            if (/[0-9]/.test(password) || /[^a-zA-Z0-9]/.test(password)) strength++;

            // Update UI
            for (let i = 0; i < strength; i++) {
                strengthIndicators[i].classList.remove('bg-gray-200');

                if (strength === 1) {
                    strengthIndicators[i].classList.add('bg-red-500');
                    strengthText.textContent = 'Weak';
                    strengthText.className = 'text-red-500';
                } else if (strength === 2) {
                    strengthIndicators[i].classList.add('bg-yellow-500');
                    strengthText.textContent = 'Fair';
                    strengthText.className = 'text-yellow-500';
                } else if (strength === 3) {
                    strengthIndicators[i].classList.add('bg-blue-500');
                    strengthText.textContent = 'Good';
                    strengthText.className = 'text-blue-500';
                } else if (strength === 4) {
                    strengthIndicators[i].classList.add('bg-green-500');
                    strengthText.textContent = 'Strong';
                    strengthText.className = 'text-green-500';
                }
            }
        } else {
            strengthContainer.classList.add('hidden');
        }
    });

    // Step navigation function
    window.goToStep = function(step) {
        // Hide all steps
        stepContents.forEach(content => content.classList.add('hidden'));

        // Show the current step
        document.getElementById(`step-${step}`).classList.remove('hidden');

        // Update step indicators
        steps.forEach((s, index) => {
            const stepNumber = index + 1;
            const stepCircle = s.querySelector('div:first-child');

            if (stepNumber < step) {
                // Completed step
                stepCircle.classList.remove('bg-gray-200', 'text-gray-600');
                stepCircle.classList.add('bg-green-500', 'text-white');
                stepCircle.innerHTML = '<i class="fas fa-check"></i>';
                s.classList.add('completed');
            } else if (stepNumber === step) {
                // Current step
                stepCircle.classList.remove('bg-gray-200', 'text-gray-600', 'bg-green-500');
                stepCircle.classList.add('bg-primary', 'text-white');
                stepCircle.textContent = stepNumber;
            }
        });
    };
});