import { ordersFetch } from "../data/orders-data.js";

async function buyNowItemAndIfDuplicate() {
    const storedProduct = sessionStorage.getItem("buyNowProduct");

    if (!storedProduct) {
        console.error("❌ Error: No product found in session storage.");
        return;
    }

    let product;
    try {
        product = JSON.parse(storedProduct);
    } catch (error) {
        console.error("❌ Error parsing JSON:", error);
        return;
    }

    if (!product || typeof product !== "object") {
        console.error("❌ Error: Invalid product data.");
        return;
    }

    // Assign values to the UI
    document.querySelector(".js-product-name").textContent = product.name;
    document.querySelector(".js-product-price").textContent = `₱ ${product.price}`;
    document.querySelector(".js-product-image").src = product.image;

    if (product.typeItem === "uniform") {
        document.querySelector(".js-product-size").textContent = `Size: ${product.size}`;
        document.querySelector(".js-product-gender").textContent = `Gender: ${product.gender}`;
    }

    const confirmPaymentBtn = document.querySelector(".js-confirm-payment");
    const paymentMethod = document.querySelector(".js-payment-method");

    if (!confirmPaymentBtn || !paymentMethod) return; 

    confirmPaymentBtn.addEventListener("click", async () => {
        if (!paymentMethod.value) {
            alert("Please select a payment method.");
            return;
        }

        try {
            // const response = await fetch("http://localhost/smsEcommerce/php/check-duplicate-orders.php", {
            //     method: "POST",
            //     headers: { "Content-Type": "application/json" },
            //     body: JSON.stringify({ productId: product.productId })
            // });
            const response = await fetch("https://ecommerce.schoolmanagementsystem2.com/php/check-duplicate-orders.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ productId: product.productId })
            });

            const data = await response.json();

            if (data.duplicate) {
                alert("You have already checked out this item. You can only purchase it twice.");
                return;
            }

            // ✅ Ensure product is a valid object before assigning paymentMethod
            if (typeof product === "object" && product !== null) {
                product.paymentMethod = paymentMethod.value;
            } else {
                console.error("❌ Error: Product is not a valid object.");
                return;
            }

            const checkoutResponse = await fetch("process-buy-now.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(product)
            });

            const checkoutData = await checkoutResponse.json();

            if (checkoutData.success) {
                sessionStorage.removeItem("buyNowProduct");
               // window.location.href = `/smsEcommerce/user-purchase.php?payment=${paymentMethod.value}`;
               window.location.href = `https://ecommerce.schoolmanagementsystem2.com/user-purchase.php?payment=${paymentMethod.value}`;
            } else {
                alert("Error processing order.");
            }
        } catch (error) {
            console.error("❌ Error:", error);
        }
    });
}


document.addEventListener("DOMContentLoaded", async () => {
    try {
        await ordersFetch(); // Load previous orders
    } catch (error) {
        console.error("Error fetching order data:", error);
    }

    buyNowItemAndIfDuplicate();
});