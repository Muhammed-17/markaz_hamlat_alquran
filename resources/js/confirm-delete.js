window.confirmDelete = function(event, { name, type = 'العنصر', form = null }) {
    event.preventDefault();
    const targetForm = form ?? event.currentTarget;

    Swal.fire({
        title: `تأكيد الحذف`,
        html: `
            <p style="font-size:14px; color:#6b7280; margin-bottom:12px; font-family: inherit;">
                لحذف <strong style="color:#e11d48">${type}: ${name}</strong> نهائياً بما يحتويه من بيانات،<br>
                اكتب الاسم في الحقل أدناه لتأكيد الحذف:
            </p>
            <input id="swal-delete-name" type="text"
                placeholder="اكتب الاسم بدقة..."
                class="swal2-input" dir="rtl"
                style="text-align:right; font-size:14px; width:80%; margin-top:10px;">
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'حذف',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        focusConfirm: false,
        customClass: {
            popup: 'rounded-3xl font-bold',
            confirmButton: 'rounded-xl px-6 py-2.5 text-sm',
            cancelButton: 'rounded-xl px-6 py-2.5 text-sm',
        },
        preConfirm: () => {
            const input = document.getElementById('swal-delete-name').value.trim();
            if (input !== name.trim()) {
                Swal.showValidationMessage('❌ الاسم غير مطابق');
                return false;
            }
            return true;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            targetForm.submit();
        }
    });
}