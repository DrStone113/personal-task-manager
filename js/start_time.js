document.addEventListener('DOMContentLoaded', function() {
    const now = new Date();
    
    // Lấy giá trị theo múi giờ địa phương và định dạng chuẩn HTML datetime-local
    const localTime = now.toLocaleString('sv-SE', { timeZoneName: 'short' }).slice(0, 16);
    
    const datetimeInput = document.querySelector('input[name="start_time"]');
    datetimeInput.value = localTime;
    datetimeInput.min = localTime;
    
    function validateTime() {
        const selectedTime = new Date(datetimeInput.value);
        
        if (selectedTime < now) {
            datetimeInput.setCustomValidity("❌ Can't choose the pass!");
        } else {
            datetimeInput.setCustomValidity(""); // Xóa lỗi nếu hợp lệ
        }
        datetimeInput.reportValidity(); // Hiển thị lỗi ngay lập tức
    }

    function resetIfInvalid() {
        const selectedTime = new Date(datetimeInput.value);
        
        if (selectedTime < now) {
            datetimeInput.value = localTime; // Đặt lại thời gian về hiện tại
            datetimeInput.setCustomValidity(""); // Xóa lỗi sau khi đặt lại
        }
    }

    // Kiểm tra khi người dùng thay đổi giá trị
    datetimeInput.addEventListener('input', validateTime);

    // Kiểm tra khi người dùng rời khỏi ô nhập và đặt lại giá trị nếu cần
    datetimeInput.addEventListener('blur', function () {
        validateTime();
        resetIfInvalid();
    });
});