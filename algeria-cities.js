const algeriaStates = {

    "01: ولاية أدرار": ["أدرار", "تامست", "شروين", "رقان", "إن زغمير", "تيت", "قصر قدور", "تسابيت", "تيميمون", "أولاد السعيد", "زاوية كونتة", "أولف", "تيمقتن", "تامنطيت", "فنوغيل", "تنركوك", "دلدول", "سالي", "أقبلي", "المطارفة", "أولاد أحمد تيمي", "بودة", "أوقروت", "طالمين", "برج باجي مختار", "السبع", "أولاد عيسى", "تيمياوين"],

    "02: ولاية الشلف": ["الشلف", "تنس", "بنايرية", "الكريمية", "تاجنة", "تاوقريت", "بني حواء", "صبحة", "حرشون", "أولاد فارس", "سيدي عكاشة", "بوقادير", "بني راشد", "تلعصة", "الهرنفة", "وادى قوسين", "الظهرة", "أولاد عباس", "السنجاس", "الزبوجة", "وادي سلي", "أبو الحسن", "المرسى", "الشطية", "سيدي عبد الرحمان", "مصدق", "الحجاج", "الأبيض مجاجة", "وادي الفضة", "أولاد بن عبد القادر", "بوزغاية", "عين مران", "أم الذروع", "بريرة", "بني بوعتاب"],
    
    "03: ولاية الأغواط": ["الأغواط", "قصر الحيران", "بن ناصر بن شهرة", "سيدي مخلوف", "حاسي الدلاعة", "حاسي الرمل", "عين ماضي", "تاجموت", "الخنق", "قلتة سيدي سعد", "عين سيدي علي", "البيضاء", "بريدة", "الغيشة", "الحاج المشري", "سبقاق", "تاويالة", "تاجرونة", "أفلو", "العسافية", "وادي مرة", "وادي مزي", "الحويطة", "سيدي بوزيد"],
         // ...    باقي الولايات هنا

};




const shippingPrices = {
 

    "01: ولاية أدرار": { home: 1300, office: 1040 },
    "02: ولاية الشلف": { home: 600, office: 480 },
    "03: ولاية الأغواط": { home: 700, office: 560 },
    // ... أضف باقي الولايات هنا
};



document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('country');
    const citySelect = document.getElementById('city');

    // ملء قائمة الولايات
    for (let state in algeriaStates) {
        const option = document.createElement('option');
        option.value = state;
        option.textContent = state;
        countrySelect.appendChild(option);
    }

    // تحديث قائمة البلديات عند اختيار ولاية
    countrySelect.addEventListener('change', function() {
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
});