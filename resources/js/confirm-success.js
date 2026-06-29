window.showSuccess = function(message, title = 'تم بنجاح') {
    Swal.fire({
        icon: 'success',
        title: title,
        text: message,
        confirmButtonColor: '#0a5c36',
        confirmButtonText: 'حسناً',
        timer: 3000,
        timerProgressBar: true,
        customClass: {
            popup: 'rounded-3xl font-bold',
            confirmButton: 'rounded-xl px-6 py-2.5 text-sm',
        }
    });
}

window.showError = function(message, title = 'خطأ') {
    Swal.fire({
        icon: 'error',
        title: title,
        text: message,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'حسناً',
        customClass: {
            popup: 'rounded-3xl font-bold',
            confirmButton: 'rounded-xl px-6 py-2.5 text-sm',
        }
    });
}