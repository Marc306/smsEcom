const API_URL = "http://localhost/smsEcommerce/php/schedule-handler.php"; 

async function assignPickupSchedule(orderId, studentId, paymentMethod) {
    try {
        const response = await fetch(API_URL, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                order_id: orderId,
                student_id: studentId,
                payment_method: paymentMethod,
            }),
        });

        const data = await response.json();

        if (data.success) {
            alert(`Pickup scheduled: ${data.message}`);
            document.querySelector(`.schedule-info-${orderId}`).innerText = `Pickup Date: ${data.message}`;
        } else {
            console.warn("AI Scheduling Failed:", data.message);
        }
    } catch (error) {
        console.error("Error assigning schedule:", error);
    }
}

// Automatically schedule for Walk-in Payment
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".order-container").forEach(order => {
        const orderId = order.dataset.orderId;
        const studentId = order.dataset.studentId;
        const paymentMethod = order.dataset.paymentMethod;

        if (paymentMethod === "Walk-In Payment") {
            assignPickupSchedule(orderId, studentId, paymentMethod);
        }
    });
});

// Schedule Gcash/Kasunduan after payment confirmation
async function checkPaymentAndSchedule(orderId, studentId, paymentMethod) {
    const paymentStatus = await fetch(`http://localhost/smsEcommerce/php/get-payment-status.php?order_id=${orderId}`)
        .then(res => res.json());

    if (paymentStatus.status === "Confirmed") {
        await assignPickupSchedule(orderId, studentId, paymentMethod);
    } else {
        console.warn("Payment is not confirmed yet.");
    }
}
