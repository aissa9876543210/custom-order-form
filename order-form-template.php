<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="custom-order-form-container">
    <h2 class="mb-4">
        <i class="fas fa-shopping-cart text-primary mb-3 text-3xl block"></i>
        أضف معلوماتك في الأسفل لطلب هذا المنتج
    </h2>
    
    <form id="orderForm" class="needs-validation" novalidate>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user text-primary"></i>
                        </span>
                        <input type="text" id="fullName" name="fullName" class="form-control" placeholder="الاسم بالكامل" required>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-phone-alt text-primary"></i>
                        </span>
                        <input type="tel" id="phone" name="phone" class="form-control text-right" placeholder="رقم الهاتف" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-map-marker-alt text-primary"></i>
                </span>
                <select id="country" name="country" class="form-select" required>
                    <option value="">اختر الولاية</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-city text-primary"></i>
                </span>
                <select id="city" name="city" class="form-select" required>
                    <option value="">اختر البلدية</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-home text-primary"></i>
                </span>
                <input type="text" id="address" name="address" class="form-control" placeholder="العنوان بالتفصيل">
            </div>
        </div>

        <?php if ($has_variations): ?>
            <div id="variationsContainer" class="form-group" style="display: none;">
                <?php foreach ($variations as $attribute => $options): ?>
                    <label for="<?php echo esc_attr($attribute); ?>" class="mb-2">
                        <?php echo esc_html(wc_attribute_label($attribute)); ?>:
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-tags text-primary"></i>
                        </span>
                        <select name="<?php echo esc_attr($attribute); ?>" id="<?php echo esc_attr($attribute); ?>" class="form-select" required>
                            <option value="">اختر <?php echo esc_html(wc_attribute_label($attribute)); ?></option>
                            <?php foreach ($options as $option): ?>
                                <option value="<?php echo esc_attr($option); ?>">
                                    <?php echo esc_html($option); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="delivery-type-group">
            <label class="d-block mb-3">نوع التوصيل:</label>
            <div class="btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="delivery_type" id="home_delivery" value="home" checked required>
                <label class="btn btn-outline-primary" for="home_delivery">
                    <i class="fas fa-home me-2"></i>
                    التوصيل للمنزل
                </label>

                <input type="radio" class="btn-check" name="delivery_type" id="office_delivery" value="office" required>
                <label class="btn btn-outline-primary" for="office_delivery">
                    <i class="fas fa-building me-2"></i>
                    التوصيل للمكتب
                </label>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="custom-quantity-control d-flex align-items-center">
                <button type="button" id="decreaseQuantity" class="btn">
                    <i class="fas fa-minus"></i>
                </button>
                <input type="number" id="quantity" name="quantity" value="1" min="1" class="form-control">
                <button type="button" id="increaseQuantity" class="btn">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            
            <button id="confirmOrder" type="submit" class="btn btn-primary flex-grow-1">
                <span id="confirmOrderText">
                    <i class="fas fa-check"></i>
                    تأكيد الطلب
                </span>
                <span id="confirmOrderLoading" class="d-none">
                    <i class="fas fa-spinner fa-spin"></i>
                    جاري التأكيد...
                </span>
            </button>
        </div>

        <button type="button" class="btn btn-success w-100 mt-3" onclick="orderViaWhatsApp()">
            <i class="fab fa-whatsapp me-2"></i>
            طلب عبر الواتساب
        </button>

        <div class="order-summary mt-4">
            <h3 id="toggleSummary">
                ملخص الطلب
                <i class="fas fa-chevron-down rotate-icon"></i>
            </h3>
            <div id="summaryContent">
                <p>
                    <i class="fas fa-box text-primary"></i>
                    <?php echo get_the_title(); ?>
                </p>
                <p>
                    <i class="fas fa-tag text-primary"></i>
                    سعر المنتج: <span id="productPrice" class="fw-bold"><?php echo $product->get_price_html(); ?></span>
                </p>
                <p>
                    <i class="fas fa-truck text-primary"></i>
                    سعر التوصيل: <span id="shippingPrice" class="fw-bold">0 د.ج</span>
                </p>
                <p class="border-top pt-2 mt-2">
                    <i class="fas fa-calculator text-primary"></i>
                    السعر الإجمالي: <span id="totalPrice" class="fw-bold">0 د.ج</span>
                </p>
            </div>
        </div>
    </form>

    <input type="hidden" id="basePrice" value="<?php echo $product->get_price(); ?>">
    <input type="hidden" id="variableProduct" value="<?php echo $product->is_type('variable') ? '1' : '0'; ?>">
    <input type="hidden" id="hasVariations" value="<?php echo $has_variations ? '1' : '0'; ?>">
    <input type="hidden" id="productName" value="<?php echo esc_attr(get_the_title()); ?>">
</div>