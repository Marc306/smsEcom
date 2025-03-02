import { itemCartStorage } from "../data/checkout-cart.js";
import { ordersFetch, orders } from "../data/orders-data.js";

function checkoutItemOrderIfDuplicate() {
    const checkoutBtn = document.querySelector(".js-checkout-btn");
    const paymentOptionLink = document.querySelector(".js-payment-option-link");

    if (!checkoutBtn || !paymentOptionLink) return; // Prevent errors if elements are missing

    checkoutBtn.addEventListener("click", () => {
        let duplicateFound = false;

        itemCartStorage.cartItems.forEach((cartItem) => {
            const alreadyCheckedOut = orders.some(order =>
                order.items.some(orderItem => orderItem.productId === cartItem.productId)
            );

            if (alreadyCheckedOut) {
                duplicateFound = true;
            }
        });

        if (duplicateFound) {
            alert("You have already checked out one or more of these items.");
        } else {
            paymentOptionLink.href = "payment-option.php";
        }
    });
}



// Fetch cart and orders before running functions
document.addEventListener("DOMContentLoaded", async () => {
    try {
        await itemCartStorage.cartStorage(); // Load cart items
        await ordersFetch(); // Load previous orders
    } catch (error) {
        console.error("Error fetching order data:", error);
    }

    checkoutItemOrderIfDuplicate();
});



