/* ================================================
   SPMS LOGIN PAGE - JAVASCRIPT FUNCTIONALITY
   ================================================ */

$(document).ready(function() {

    // =====================================================
    // 1. PASSWORD VISIBILITY TOGGLE
    // =====================================================
    $('#togglePassword').on('click', function(e) {
        e.preventDefault();
        
        const passwordInput = $('#password');
        const toggleIcon = $(this).find('i');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            toggleIcon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            toggleIcon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // =====================================================
    // 2. FORM VALIDATION & SUBMISSION
    // =====================================================
    $('#loginForm').on('submit', function(e) {
        const email = $.trim($('#email').val());
        const password = $.trim($('#password').val());

        // Client-side validation
        if (!email) {
            e.preventDefault();
            showAlert('Email or username is required', 'danger');
            $('#email').focus();
            return false;
        }

        if (!password) {
            e.preventDefault();
            showAlert('Password is required', 'danger');
            $('#password').focus();
            return false;
        }

        if (password.length < 6) {
            e.preventDefault();
            showAlert('Password must be at least 6 characters', 'danger');
            return false;
        }

        // Show loading state
        showLoadingState(true);
    });

    // =====================================================
    // 3. FORM INPUT HANDLERS
    // =====================================================
    $('#email, #password').on('input', function() {
        // Remove error styling when user starts typing
        $(this).removeClass('is-invalid');
    });

    // Real-time email validation
    $('#email').on('blur', function() {
        const email = $(this).val();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        // Allow both email and username format
        if (email && email.includes('@') && !emailRegex.test(email)) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    // =====================================================
    // 4. REMEMBER ME FUNCTIONALITY
    // =====================================================
    function saveCredentials() {
        const rememberChecked = $('#remember').is(':checked');
        const email = $('#email').val();

        if (rememberChecked && email) {
            localStorage.setItem('spms_email', email);
            localStorage.setItem('spms_remember', 'true');
        } else {
            localStorage.removeItem('spms_email');
            localStorage.removeItem('spms_remember');
        }
    }

    function loadSavedCredentials() {
        const savedEmail = localStorage.getItem('spms_email');
        const isRemembered = localStorage.getItem('spms_remember');

        if (savedEmail && isRemembered === 'true') {
            $('#email').val(savedEmail);
            $('#remember').prop('checked', true);
        }
    }

    // Save credentials on form submission
    $('#loginForm').on('submit', function() {
        saveCredentials();
    });

    // Load saved credentials on page load
    loadSavedCredentials();

    // =====================================================
    // 5. AUTO-DISMISS ALERTS
    // =====================================================
    const alertTimeout = setTimeout(function() {
        $('#errorAlert, #successAlert').fadeOut('slow', function() {
            $(this).alert('close');
        });
    }, 5000);

    // Manual dismiss
    $('.alert .btn-close').on('click', function() {
        clearTimeout(alertTimeout);
    });

    // =====================================================
    // 6. KEYBOARD NAVIGATION
    // =====================================================
    $('#email, #password').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            
            if ($(this).attr('id') === 'email') {
                $('#password').focus();
            } else {
                $('#loginForm').submit();
            }
        }
    });

    // =====================================================
    // 7. HELPER FUNCTIONS
    // =====================================================
    function showAlert(message, type = 'danger') {
        const alertHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert" id="dynamicAlert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('#loginForm').before(alertHTML);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('#dynamicAlert').fadeOut('slow', function() {
                $(this).alert('close');
            });
        }, 5000);
    }

    function showLoadingState(isLoading) {
        const loginBtn = $('#loginBtn');
        const loginSpinner = $('#loginSpinner');
        const loginBtnText = $('#loginBtnText');

        if (isLoading) {
            loginBtn.prop('disabled', true).addClass('loading');
            loginSpinner.removeClass('d-none');
            loginBtnText.text('Signing In...');
        } else {
            loginBtn.prop('disabled', false).removeClass('loading');
            loginSpinner.addClass('d-none');
            loginBtnText.text('Sign In');
        }
    }

    // =====================================================
    // 8. CSRF TOKEN VALIDATION
    // =====================================================
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // =====================================================
    // 9. DEMO CREDENTIALS QUICK FILL (Development Only)
    // =====================================================
    function setupDemoCredentials() {
        const demoRoles = {
            'admin': { email: 'admin@spms.local', password: 'password123' },
            'supervisor': { email: 'supervisor@spms.local', password: 'password123' },
            'employee': { email: 'employee@spms.local', password: 'password123' }
        };

        // Note: Remove this in production
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            window.demoCredentials = demoRoles;
        }
    }

    setupDemoCredentials();

    // =====================================================
    // 10. FORM RESET ON PAGE LOAD (BACK BUTTON)
    // =====================================================
    if (sessionStorage.getItem('loginAttempted') === 'true') {
        sessionStorage.removeItem('loginAttempted');
    }

    // =====================================================
    // 11. BROWSER BACK BUTTON HANDLING
    // =====================================================
    $(window).on('pageshow', function() {
        // Reload form state after back navigation
        loadSavedCredentials();
    });

    // =====================================================
    // 12. ACCESSIBILITY ENHANCEMENTS
    // =====================================================
    // Focus management
    $('#email').on('keyup', function(e) {
        if (e.key === 'Tab') {
            $(this).attr('aria-expanded', 'true');
        }
    });

    // Screen reader announcements
    function announceToScreenReader(message) {
        const announcement = $('<div>')
            .attr('role', 'status')
            .attr('aria-live', 'polite')
            .attr('aria-atomic', 'true')
            .addClass('visually-hidden')
            .text(message)
            .appendTo('body');

        setTimeout(function() {
            announcement.remove();
        }, 3000);
    }

    // =====================================================
    // 13. PERFORMANCE OPTIMIZATION
    // =====================================================
    // Debounce email validation
    let emailValidationTimeout;
    $('#email').on('blur', function() {
        clearTimeout(emailValidationTimeout);
        emailValidationTimeout = setTimeout(function() {
            // Validation logic here
        }, 300);
    });

    // =====================================================
    // 14. ERROR LOGGING (Optional)
    // =====================================================
    function logError(errorMessage) {
        if (window.console) {
            console.error('Login Error:', errorMessage);
        }
    }

    // =====================================================
    // 15. SESSION MANAGEMENT
    // =====================================================
    // Warn user before session expires
    let sessionWarningTimeout;
    const SESSION_WARNING_TIME = 14 * 60 * 1000; // 14 minutes
    const SESSION_TIMEOUT_TIME = 15 * 60 * 1000; // 15 minutes

    function resetSessionTimer() {
        clearTimeout(sessionWarningTimeout);
        sessionWarningTimeout = setTimeout(function() {
            announceToScreenReader('Your session is about to expire. Please log in again.');
        }, SESSION_WARNING_TIME);
    }

    // Reset timer on user activity
    $(document).on('mousemove keypress click', resetSessionTimer);

    resetSessionTimer();

    console.log('SPMS Login Page - Initialized Successfully');
});