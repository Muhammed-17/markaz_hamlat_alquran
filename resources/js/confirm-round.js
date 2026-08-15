window.confirmRoundAmount = function(totalAmount, onConfirmed) {
    Swal.fire({
        title: 'تأكيد التحصيل',
        html: `
            <p style="font-size:14px; color:#6b7280; margin-bottom:12px; font-family: inherit;">
                لتأكيد هذه العملية نهائياً بإجمالي
                <strong style="color:#059669">${totalAmount.toLocaleString('ar-EG', { minimumFractionDigits: 2 })} جنيه</strong>،<br>
                اكتب المبلغ الإجمالي في الحقل أدناه للتأكيد:
            </p>
            <input id="swal-confirm-amount" type="number" step="0.01"
                placeholder="اكتب المبلغ الإجمالي..."
                class="swal2-input" dir="rtl"
                style="text-align:right; font-size:14px; width:80%; margin-top:10px;">
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'تأكيد',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        focusConfirm: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        customClass: {
            popup: 'rounded-3xl font-bold',
            confirmButton: 'rounded-xl px-6 py-2.5 text-sm',
            cancelButton: 'rounded-xl px-6 py-2.5 text-sm',
        },
        preConfirm: () => {
            const input = parseFloat(document.getElementById('swal-confirm-amount').value);
            if (isNaN(input) || Math.abs(input - totalAmount) > 0.001) {
                Swal.showValidationMessage('❌ المبلغ غير مطابق للإجمالي');
                return false;
            }
            return true;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            onConfirmed();
        }
    });
};

// ✅ خاص بصفحة confirm.blade.php (يعرض أنيميشن تحميل ثم submit عادي للفورم)
window.confirmRoundAmountAndSubmitForm = function(totalAmount, form) {
    window.confirmRoundAmount(totalAmount, () => {
        Swal.fire({
            title: 'جاري التأكيد...',
            html: `
                <div style="display:flex; flex-direction:column; align-items:center; gap:16px; padding:10px 0;">
                    <div style="
                        width: 50px; height: 50px;
                        border: 4px solid #d1fae5;
                        border-top: 4px solid #059669;
                        border-radius: 50%;
                        animation: spin 0.8s linear infinite;
                    "></div>
                    <p style="font-size:14px; color:#6b7280; margin:0; font-family:inherit;">
                        يتم تأكيد التحصيل الآن
                    </p>
                </div>
                <style>
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                </style>
            `,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            showCancelButton: false,
            didOpen: () => {
                Swal.showLoading();
                setTimeout(() => {
                    form.submit();
                }, 800);
            }
        });
    });
};