window.confirmLogout = function() {
    Swal.fire({
        title: 'تسجيل الخروج',
        text: 'هل أنت متأكد من رغبتك في تسجيل الخروج؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'نعم، خروج',
        cancelButtonText: 'إلغاء',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-3xl font-bold',
            confirmButton: 'rounded-xl px-6 py-2.5 text-sm',
            cancelButton: 'rounded-xl px-6 py-2.5 text-sm',
        }
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
        }
    });
}