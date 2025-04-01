// import { itemCartStorage } from "../data/checkout-cart.js";
// import { cartCounter } from "./cart-counter.js";
// import { ordersFetch } from "../data/orders-data.js";

// function checkoutNowBtn() {
//     document.getElementById("confirmCheckout").addEventListener("click", function () { 
//         let selectedMethod = document.querySelector('input[name="payment_method"]:checked');
    
//         if (!selectedMethod) {
//             alert("Please select a payment method.");
//             return;
//         }
    
//         let paymentMethod = selectedMethod.value.trim();
//         console.log("Selected Payment Method (Before Sending):", paymentMethod); // Debugging

//         fetch("http://localhost/smsEcommerce/php/payment-logic.php", {
//             method: "POST",
//             headers: {
//                 "Content-Type": "application/json",
//             },
//             body: JSON.stringify({ payment_method: paymentMethod }),
//         })
//         .then(response => response.json())
//         .then(data => {
//             console.log("Server Response:", data); // Debugging
//             if (data.success) {
//                 cartCounter();
//                 alert("Checkout successful!");

//                 // Redirect based on payment method
//                 if (paymentMethod === "Gcash Payment") {
//                     window.location.href = "gcash-QrCode.php";
//                 } 
//                 else if (paymentMethod === "Walk-In Payment") {
//                     window.location.href = "user-purchase.php";
//                 } 
//                 else if (paymentMethod === "Kasunduan") {
//                     window.location.href = "kasunduan-form.php"; // New redirect for Kasunduan
//                 }
//             } else {
//                 alert("Checkout failed: " + data.error);
//             }
//         })
//         .catch(error => console.error("Error:", error));
//     });
// }

// document.addEventListener("DOMContentLoaded", () => {
//     async function loadOrders() {
//         try {
//             await ordersFetch();
//             itemCartStorage.cartStorage();
//         } catch (error) {
//             console.error("Error loading cart data:", error);
//         }

//         checkoutNowBtn(); 
//     }

//     loadOrders();
// });


import { itemCartStorage } from "../data/checkout-cart.js";
import { cartCounter } from "./cart-counter.js";
import { ordersFetch } from "../data/orders-data.js";

function checkoutNowBtn() {
    document.getElementById("confirmCheckout").addEventListener("click", function () {
        let selectedMethod = document.querySelector('input[name="payment_method"]:checked');

        if (!selectedMethod) {
            alert("Please select a payment method.");
            return;
        }

        let paymentMethod = selectedMethod.value.trim();
        console.log("🟢 Selected Payment Method (Before Sending):", paymentMethod);

        let buyNowProduct = JSON.parse(sessionStorage.getItem("buyNowProduct"));
        let cartItems = JSON.parse(localStorage.getItem("cartItems")) || []; // Fallback for cart checkout

        let requestData = buyNowProduct 
            ? {
                type: "buy_now",
                product: {
                    ...buyNowProduct,
                    size: buyNowProduct.typeItem === "uniform" ? buyNowProduct.size : null,
                    gender: buyNowProduct.typeItem === "uniform" ? buyNowProduct.gender : null,
                    payment_method: paymentMethod
                }
            }
            : {
                type: "cart",
                cart: cartItems.map(item => ({
                    productId: item.productId,
                    quantity: item.quantity,
                    size: item.typeItem === "uniform" ? item.size : null,
                    gender: item.typeItem === "uniform" ? item.gender : null,
                    payment_method: paymentMethod
                }))
            };

        console.log("🟡 Checkout Request Data:", JSON.stringify(requestData, null, 2));

        fetch("https://ecommerce.schoolmanagementsystem2.com/php/payment-logic.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(requestData),
        })
        .then(async response => {
            console.log("🔵 Response Headers:", response.headers);
            const data = await response.json();
            console.log("🔴 Server Response (Raw JSON):", data);
            return data;
        })
        .then(data => {
            if (data.success) {
                console.log("✅ Checkout Success:", data);
                cartCounter();
                alert("Checkout successful!");
                sessionStorage.removeItem("buyNowProduct");

                let redirectPage = {
                    "Gcash Payment": "gcash-QrCode.php",
                    "Walk-In Payment": "user-purchase.php",
                    "Kasunduan": "kasunduan-form.php"
                }[paymentMethod] || "user-purchase.php";

                window.location.href = redirectPage;
            } else {
                console.log(data.success);
                console.error("❌ Checkout Failed:", data.error);
                alert("Checkout failed: " + data.error);
            }
        })
        .catch(error => {
            console.error("⚠️ Fetch Error:", error);
            alert("An unexpected error occurred. Please try again.");
        });
    });
}

document.addEventListener("DOMContentLoaded", async () => {
    try {
        await ordersFetch();
        itemCartStorage.cartStorage();

        checkoutNowBtn();
    } catch (error) {
        console.error("🚨 Error loading cart data:", error);
    }
    // checkoutNowBtn();
});
