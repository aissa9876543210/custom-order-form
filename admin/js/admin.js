jQuery(document).ready(function($) {
    // تهيئة مختار الألوان
    $('.color-picker').wpColorPicker({
        change: function(event, ui) {
            updatePreview();
        }
    });

    // التبديل بين علامات التبويب
    $('.settings-tab').click(function() {
        $('.settings-tab').removeClass('active');
        $(this).addClass('active');
        
        const tabId = $(this).data('tab');
        $('.settings-panel').removeClass('active');
        $(`#${tabId}-panel`).addClass('active');
    });

    // حفظ الإعدادات
    $('#custom-order-form-settings').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'save_form_settings');
        formData.append('nonce', customOrderFormAdmin.nonce);

        $.ajax({
            url: customOrderFormAdmin.ajaxUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showNotification('تم حفظ الإعدادات بنجاح', 'success');
                    updatePreview();
                } else {
                    showNotification(response.data || 'حدث خطأ أثناء حفظ الإعدادات', 'error');
                }
            },
            error: function(xhr, status, error) {
                showNotification('حدث خطأ في الاتصال: ' + error, 'error');
            }
        });
    });

    function showNotification(message, type) {
        const notificationClass = type === 'success' ? 'updated' : 'error';
        const notification = $(`<div class="notice ${notificationClass} is-dismissible"><p>${message}</p></div>`);
        
        $('.wrap').first().prepend(notification);
        
        setTimeout(function() {
            notification.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }

    function updatePreview() {
        const settings = {
            fieldLabels: {},
            shippingPrices: {},
            design: {}
        };

        // جمع تسميات الحقول
        $('input[name^="field_labels"]').each(function() {
            const fieldName = $(this).attr('name').match(/\[([^\]]+)\]/)[1];
            settings.fieldLabels[fieldName] = $(this).val();
        });

        // جمع أسعار التوصيل
        $('.shipping-price-group').each(function() {
            const state = $(this).find('h3').text();
            settings.shippingPrices[state] = {
                home: $(this).find('input[name$="[home]"]').val(),
                office: $(this).find('input[name$="[office]"]').val()
            };
        });

        // جمع إعدادات التصميم
        settings.design = {
            primaryColor: $('input[name="design[primaryColor]"]').val(),
            buttonColor: $('input[name="design[buttonColor]"]').val(),
            fontFamily: $('select[name="design[fontFamily]"]').val()
        };

        // حفظ الإعدادات في localStorage للاستخدام في الواجهة الأمامية
        localStorage.setItem('formSettings', JSON.stringify(settings));
    }

    // تحديث المعاينة عند تغيير أي إعداد
    $('input, select').on('change', updatePreview);

    // تحديث المعاينة عند تحميل الصفحة
    updatePreview();
});