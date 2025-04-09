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

    // Step navigation
    function goToStep(step) {
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
                s.classList.add('active');
            } else {
                // Future step
                stepCircle.classList.remove('bg-primary', 'text-white', 'bg-green-500');
                stepCircle.classList.add('bg-gray-200', 'text-gray-600');
                stepCircle.textContent = stepNumber;
                s.classList.remove('active', 'completed');
            }
        });

        // Update step lines
        const stepLines = document.querySelectorAll('.step-line');
        stepLines.forEach((line, index) => {
            if (index < step - 1) {
                line.classList.remove('bg-gray-200');
                line.classList.add('bg-green-500');
            } else {
                line.classList.remove('bg-green-500');
                line.classList.add('bg-gray-200');
            }
        });

        // Update current step
        currentStep = step;
        document.querySelector('input[name="current_step"]').value = currentStep;

        // Scroll to top of form
        document.querySelector('.bg-white.rounded-xl').scrollIntoView({ behavior: 'smooth' });
    }

    // Email validation
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Phone number validation
    function isValidPhone(phone) {
        // Remove any non-digit characters
        const cleanPhone = phone.replace(/\D/g, '');
        // Check if length is between 12 and 14 digits
        return cleanPhone.length >= 12 && cleanPhone.length <= 14;
    }

    // Step 1 Next Button
    document.getElementById('step1Next').addEventListener('click', function() {
        // Validate Step 1
        const firstName = document.getElementById('first_name').value.trim();
        const lastName = document.getElementById('last_name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone_number').value.trim();
        const password = document.getElementById('password').value;
        const passwordConfirmation = document.getElementById('password_confirmation').value;

        // Field validation
        if (!firstName || !lastName || !email || !phone || !password || !passwordConfirmation) {
            alert('Please fill in all required fields');
            return;
        }

        // Email validation
        if (!isValidEmail(email)) {
            alert('Please enter a valid email address');
            return;
        }

        // Phone validation
        if (!isValidPhone(phone)) {
            alert('Please enter a valid Zambian phone number (e.g., +260971234567)');
            return;
        }

        // Password validation
        if (password.length < 8) {
            alert('Password must be at least 8 characters long');
            return;
        }

        if (password !== passwordConfirmation) {
            alert('Passwords do not match');
            return;
        }

        goToStep(2);
    });

    // Step 2 Back Button
    document.getElementById('step2Back').addEventListener('click', function() {
        goToStep(1);
    });

    // Step 2 Next Button
    document.getElementById('step2Next').addEventListener('click', function() {
        // Validate Step 2
        const companyName = document.getElementById('company_name').value.trim();
        const registrationNumber = document.getElementById('registration_number').value.trim();
        const companyAddress = document.getElementById('company_address').value.trim();
        const companyCity = document.getElementById('company_city').value.trim();
        const companyPhone = document.getElementById('company_phone').value.trim();
        const companyEmail = document.getElementById('company_email').value.trim();
        const industry = document.getElementById('industry').value;

        // Field validation
        if (!companyName || !registrationNumber || !companyAddress || !companyCity || !companyPhone || !companyEmail || !industry) {
            alert('Please fill in all required fields');
            return;
        }

        // Email validation
        if (!isValidEmail(companyEmail)) {
            alert('Please enter a valid company email address');
            return;
        }

        // Phone validation
        if (!isValidPhone(companyPhone)) {
            alert('Please enter a valid Zambian phone number for the company');
            return;
        }

        // Website validation (if provided)
        const website = document.getElementById('company_website').value.trim();
        if (website && !website.startsWith('http://') && !website.startsWith('https://')) {
            alert('Website URL must start with http:// or https://');
            return;
        }

        goToStep(3);
    });

    // Step 3 Back Button
    document.getElementById('step3Back').addEventListener('click', function() {
        goToStep(2);
    });

    // Step 3 Next Button
    document.getElementById('step3Next').addEventListener('click', function() {
        // Validate Step 3
        const certificateFile = document.getElementById('certificate_file').files.length;
        const businessLicenseFile = document.getElementById('business_license_file').files.length;
        const directorIdFile = document.getElementById('director_id_file').files.length;

        // Required document validation
        if (!certificateFile || !businessLicenseFile || !directorIdFile) {
            alert('Please upload all required documents');
            return;
        }

        // Update review section
        updateReviewSection();

        goToStep(4);
    });

    // Step 4 Back Button
    document.getElementById('step4Back').addEventListener('click', function() {
        goToStep(3);
    });

    // Update Review Section
    function updateReviewSection() {
        // Personal Information
        document.getElementById('review-name').textContent =
            document.getElementById('first_name').value + ' ' + document.getElementById('last_name').value;
        document.getElementById('review-email').textContent = document.getElementById('email').value;
        document.getElementById('review-phone').textContent = document.getElementById('phone_number').value;

        // Company Information
        document.getElementById('review-company-name').textContent = document.getElementById('company_name').value;
        document.getElementById('review-registration-number').textContent = document.getElementById('registration_number').value;
        document.getElementById('review-tax-id').textContent = document.getElementById('tax_id').value || 'Not provided';
        document.getElementById('review-industry').textContent = document.getElementById('industry').options[document.getElementById('industry').selectedIndex].text;
        document.getElementById('review-company-address').textContent = document.getElementById('company_address').value;
        document.getElementById('review-company-city').textContent = document.getElementById('company_city').value;
        document.getElementById('review-company-country').textContent = document.getElementById('company_country').value;
        document.getElementById('review-company-phone').textContent = document.getElementById('company_phone').value;
        document.getElementById('review-company-email').textContent = document.getElementById('company_email').value;
        document.getElementById('review-company-website').textContent = document.getElementById('company_website').value || 'Not provided';

        // Documents
        document.getElementById('review-certificate').textContent =
            document.getElementById('certificate_file').files.length ? document.getElementById('certificate_file').files[0].name : 'Not uploaded';
        document.getElementById('review-tax-clearance').textContent =
            document.getElementById('tax_clearance_file').files.length ? document.getElementById('tax_clearance_file').files[0].name : 'Not uploaded';
        document.getElementById('review-business-license').textContent =
            document.getElementById('business_license_file').files.length ? document.getElementById('business_license_file').files[0].name : 'Not uploaded';
        document.getElementById('review-director-id').textContent =
            document.getElementById('director_id_file').files.length ? document.getElementById('director_id_file').files[0].name : 'Not uploaded';
    }

    // Form Submission
    const form = document.getElementById('corporateRegisterForm');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate final step
        if (!document.getElementById('terms').checked || !document.getElementById('verification').checked) {
            alert('Please accept the terms and confirm your information');
            return;
        }

        // Show processing state
        document.getElementById('step-4').classList.add('hidden');
        document.getElementById('processing-state').classList.remove('hidden');

        // Create FormData object
        const formData = new FormData(form);

        // Add file data explicitly to ensure proper upload
        const fileInputs = ['certificate_file', 'tax_clearance_file', 'business_license_file', 'director_id_file'];
        fileInputs.forEach(inputId => {
            const fileInput = document.getElementById(inputId);
            if (fileInput.files.length > 0) {
                formData.set(inputId, fileInput.files[0]);
            }
        });

        // Send AJAX request
        fetch('/corporate/register', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
            credentials: 'same-origin',
        })
        .then(response => {
            if (!response.ok) {
                // Handle HTTP errors
                if (response.status === 422) {
                    // Validation errors
                    return response.json().then(data => {
                        throw new Error(Object.values(data.errors).flat().join('\n'));
                    });
                }
                throw new Error('Something went wrong during registration. Please try again later.');
            }
            return response.json();
        })
        .then(data => {
            // Hide processing state
            document.getElementById('processing-state').classList.add('hidden');

            if (data.success) {
                // Show success modal
                document.getElementById('success-modal').classList.remove('hidden');
            } else {
                // Show error message
                alert(data.message || 'An error occurred during registration. Please try again.');
                document.getElementById('step-4').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Hide processing state and show form again
            document.getElementById('processing-state').classList.add('hidden');
            document.getElementById('step-4').classList.remove('hidden');
            alert(error.message || 'An error occurred during registration. Please try again.');
        });
    });

    // Close success modal when clicking outside
    document.getElementById('success-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});
