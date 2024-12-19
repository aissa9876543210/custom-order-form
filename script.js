document.addEventListener('DOMContentLoaded', function() {
    // استرجاع الإعدادات المحفوظة
    const savedSettings = JSON.parse(localStorage.getItem('formSettings') || '{}');
    
    // تطبيق الإعدادات على الفورم
    applySettings(savedSettings);

    const hasVariations = document.getElementById('hasVariations').value === '1';
    const countrySelect = document.getElementById('country');
    const citySelect = document.getElementById('city');
    const shippingPriceElement = document.getElementById('shippingPrice');
    const totalPriceElement = document.getElementById('totalPrice');
    const deliveryOptions = document.querySelectorAll('input[name="delivery_type"]');
    const variationsContainer = document.getElementById('variationsContainer');
    const basePrice = parseFloat(document.getElementById('basePrice').value);
    const productPriceElement = document.getElementById('productPrice');
    const quantityInput = document.getElementById('quantity');
    const increaseButton = document.getElementById('increaseQuantity');
    const decreaseButton = document.getElementById('decreaseQuantity');

    // تطبيق الإعدادات المحفوظة
    function applySettings(settings) {
        if (!settings) return;

        // تطبيق تسميات الحقول
        if (settings.fieldLabels) {
            Object.entries(settings.fieldLabels).forEach(([fieldId, label]) => {
                const labelElement = document.querySelector(`label[for="${fieldId}"]`);
                if (labelElement) {
                    labelElement.textContent = label;
                }
                const input = document.getElementById(fieldId);
                if (input) {
                    input.placeholder = label;
                }
            });
        }

        // تطبيق أسعار التوصيل
        if (settings.shippingPrices) {
            window.shippingPrices = settings.shippingPrices;
        }

        // تطبيق إعدادات التصميم
        if (settings.design) {
            document.documentElement.style.setProperty('--primary-color', settings.design.primaryColor);
            document.documentElement.style.setProperty('--button-color', settings.design.buttonColor);
            
            if (settings.design.fontFamily) {
                document.body.style.fontFamily = settings.design.fontFamily;
            }
        }
    }

    increaseButton.addEventListener('click', function() {
        let currentQuantity = parseInt(quantityInput.value) || 1;
        currentQuantity++;
        quantityInput.value = currentQuantity;
        updateTotalPrice();
    });
    
    decreaseButton.addEventListener('click', function() {
        let currentQuantity = parseInt(quantityInput.value) || 1;
        if (currentQuantity > 1) {
            currentQuantity--;
            quantityInput.value = currentQuantity;
            updateTotalPrice();
        }
    });

    if (document.getElementById('variableProduct').value === '1' && variationForm) {
        // استمع إلى تغييرات في نموذج المتغيرات
        variationForm.addEventListener('change', function() {
            // استخدم setTimeout لضمان أن WooCommerce قد قام بتحديث السعر
            setTimeout(function() {
                const newPriceElement = variationForm.querySelector('.woocommerce-variation-price .amount');
                if (newPriceElement) {
                    const newPrice = parseFloat(newPriceElement.textContent.replace(/[^0-9.-]+/g,""));
                    if (!isNaN(newPrice)) {
                        currentProductPrice = newPrice;
                        productPriceElement.innerHTML = newPriceElement.innerHTML;
                        updateTotalPrice();
                    }
                }
            }, 100);
        });

        // استمع إلى إعادة تعيين النموذج
        const resetButton = variationForm.querySelector('.reset_variations');
        if (resetButton) {
            resetButton.addEventListener('click', function() {
                const basePrice = parseFloat(document.getElementById('basePrice').value);
                currentProductPrice = basePrice;
                productPriceElement.innerHTML = basePrice.toFixed(2) + ' د.ج';
                updateTotalPrice();
            });
        }
    }

    let selectedDeliveryType = 'home';
    let currentProductPrice = basePrice;

    function updateShippingPrice() {
        const selectedState = countrySelect.value;
        if (selectedState && shippingPrices[selectedState]) {
            const price = shippingPrices[selectedState][selectedDeliveryType];
            shippingPriceElement.textContent = price.toFixed(2) + ' د.ج';
            updateTotalPrice();
        } else {
            shippingPriceElement.textContent = '0 د.ج';
            updateTotalPrice();
        }
    }

    function updateTotalPrice() {
        const shippingPrice = parseFloat(shippingPriceElement.textContent);
        const quantity = parseInt(quantityInput.value) || 1;
        total = (currentProductPrice * quantity) + shippingPrice;
        totalPriceElement.textContent = total.toFixed(2) + ' د.ج';
    }
    countrySelect.addEventListener('change', function() {
        updateShippingPrice();
        
        citySelect.innerHTML = '<option value="">اختر البلدية</option>';
        const selectedState = this.value;
        if (selectedState && algeriaStates[selectedState]) {
            algeriaStates[selectedState].forEach(city => {
                const option = document.createElement('option');
                option.value = city;
                option.textContent = city;
                citySelect.appendChild(option);
            });
        }
    });

    deliveryOptions.forEach(option => {
        option.addEventListener('change', function() {
            selectedDeliveryType = this.value;
            updateShippingPrice();
        });
    });

    document.getElementById('toggleSummary').addEventListener('click', function() {
        var content = document.getElementById('summaryContent');
        var icon = this.querySelector('i');
        if (content.style.display === 'none') {
            content.style.display = 'block';
            icon.classList.add('rotated');
        } else {
            content.style.display = 'none';
            icon.classList.remove('rotated');
        }
    });

    document.getElementById('orderForm').addEventListener('submit', submitForm);

    function submitForm(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        formData.append('action', 'place_custom_order');
        formData.append('product_id', woocommerce_params.product_id);
        formData.append('price', currentProductPrice);

        const fullName = document.getElementById('fullName').value;
        formData.append('full_name', fullName);

        const quantity = document.getElementById('quantity').value;
        formData.append('quantity', quantity);

        if (hasVariations) {
            const variationSelects = variationsContainer.querySelectorAll('select');
            variationSelects.forEach(select => {
                formData.append(`attribute_${select.name}`, select.value);  // تأكد من إضافة المتغيرات بالشكل الصحيح
            });
        }
        
        fetch(woocommerce_params.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.data.redirect_url;
            } else {
                alert('حدث خطأ أثناء إنشاء الطلب. يرجى المحاولة مرة أخرى.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء إنشاء الطلب. يرجى المحاولة مرة أخرى.');
        });
    }

    // تحديث الأسعار عند تحميل الصفحة
    updateShippingPrice();
    updateTotalPrice();
});

window.addEventListener('storage', function(e) {
    if (e.key === 'formSettings') {
        const newSettings = JSON.parse(e.newValue);
        applySettings(newSettings);
    }
});
