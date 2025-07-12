<!-- SweetAlert 2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- SweetAlert 2 Global Configuration -->
<script>
// Global SweetAlert 2 configuration
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    customClass: {
        popup: 'rounded-2xl shadow-lg'
    },
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

// Success Toast
window.showSuccessToast = function(message, title = 'Success!') {
    Toast.fire({
        icon: 'success',
        title: title,
        text: message
    });
};

// Error Toast
window.showErrorToast = function(message, title = 'Error!') {
    Toast.fire({
        icon: 'error',
        title: title,
        text: message
    });
};

// Info Toast
window.showInfoToast = function(message, title = 'Info') {
    Toast.fire({
        icon: 'info',
        title: title,
        text: message
    });
};

// Warning Toast
window.showWarningToast = function(message, title = 'Warning!') {
    Toast.fire({
        icon: 'warning',
        title: title,
        text: message
    });
};

// Confirmation Modal
window.showConfirmModal = function(options) {
    const defaults = {
        title: 'Are you sure?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, proceed',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-lg px-6 py-2 font-semibold',
            cancelButton: 'rounded-lg px-6 py-2 font-semibold'
        }
    };
    
    return Swal.fire({...defaults, ...options});
};

// Loading Modal
window.showLoadingModal = function(title = 'Loading...', text = 'Please wait') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'info',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        customClass: {
            popup: 'rounded-2xl'
        },
        didOpen: () => {
            Swal.showLoading();
        }
    });
};

// Success Modal
window.showSuccessModal = function(title, text, confirmButtonText = 'OK') {
    return Swal.fire({
        icon: 'success',
        title: title,
        text: text,
        confirmButtonText: confirmButtonText,
        confirmButtonColor: '#16a34a',
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-lg px-6 py-2 font-semibold'
        }
    });
};

// Error Modal
window.showErrorModal = function(title, text, confirmButtonText = 'OK') {
    return Swal.fire({
        icon: 'error',
        title: title,
        text: text,
        confirmButtonText: confirmButtonText,
        confirmButtonColor: '#dc2626',
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-lg px-6 py-2 font-semibold'
        }
    });
};

// Auto-show Laravel session messages
document.addEventListener('DOMContentLoaded', function() {
    @if (session('success'))
        showSuccessToast('{{ session('success') }}');
    @endif

    @if (session('error'))
        showErrorToast('{{ session('error') }}');
    @endif

    @if (session('info'))
        showInfoToast('{{ session('info') }}');
    @endif

    @if (session('warning'))
        showWarningToast('{{ session('warning') }}');
    @endif

    @if ($errors->any())
        let errorMessages = '';
        @foreach ($errors->all() as $error)
            errorMessages += '<p class="mb-1">{{ $error }}</p>';
        @endforeach
        
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: errorMessages,
            confirmButtonColor: '#dc2626',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-lg px-6 py-2 font-semibold'
            }
        });
    @endif
});
</script>

<style>
/* Custom SweetAlert 2 styling */
.swal2-popup {
    font-family: inherit !important;
}

.swal2-title {
    font-weight: 700 !important;
}

.swal2-html-container {
    font-size: 0.95rem !important;
}

.swal2-toast .swal2-title {
    font-size: 1rem !important;
    font-weight: 600 !important;
}

.swal2-toast .swal2-html-container {
    font-size: 0.875rem !important;
}

/* Toast positioning adjustments */
.swal2-container.swal2-top-end {
    top: 1rem !important;
    right: 1rem !important;
}

/* Button hover effects */
.swal2-confirm:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

.swal2-cancel:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

/* Loading spinner customization */
.swal2-loader {
    border-color: #3b82f6 transparent #3b82f6 transparent !important;
}
</style>
