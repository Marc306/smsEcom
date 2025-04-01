// import { itemCartStorage } from "../data/checkout-cart.js";
// import { ordersFetch, orders } from "../data/orders-data.js";

// export function cartCounter(){
//     let itemCart = 0;
//     let hasItems = false; 

//     itemCartStorage.cartItems.forEach((eachItemCart) => {
//         const quantity = eachItemCart.quantity;
//         if (typeof quantity === 'number' && !isNaN(quantity)){
//             itemCart += quantity;
//             hasItems = true; // Set the flag to true if we have at least one item
//         }
//         else{
//             console.warn(`Invalid quantity detected for productId: ${eachItemCart.productId}`, quantity);
//         }
//     });

//     document.querySelector(".cart-count").innerHTML = itemCart || "";

//     const notifElement = document.querySelector(".notif");
//     const notifBurger = document.querySelector(".notif-burger");
//     const notifProfile = document.querySelector(".notif-profile");
//     if(hasItems){
//         notifElement.classList.add("notif-active");
//         notifBurger.classList.add("notif-burger-active");
//     }
//     else if(orders.length != 0){
//         notifElement.classList.add("notif-active");
//         notifBurger.classList.add("notif-burger-active");
//         notifProfile.classList.add("notif-profile-active");
//     }
//     else if(orders.length == 0){
//         notifElement.classList.remove("notif-active");
//         notifBurger.classList.remove("notif-burger-active");
//         notifProfile.classList.remove("notif-profile-active");
//     }
//     else if(hasItems && orders.length != 0){
//         notifElement.classList.add("notif-active");
//         notifBurger.classList.add("notif-burger-active");
//         notifProfile.classList.add("notif-profile-active");
//     } 
//     else if(hasItems && orders.length == 0){
//         notifElement.classList.remove("notif-active");
//         notifBurger.classList.remove("notif-burger-active");
//         notifProfile.classList.remove("notif-profile-active");
//     }
//     else{
//         notifElement.classList.remove("notif-active");
//         notifBurger.classList.remove("notif-burger-active");
//     }
// }

// document.addEventListener("DOMContentLoaded", async () => {
//     try{
//         await itemCartStorage.cartStorage();
//         await ordersFetch();
//     } 
//     catch(error){
//         console.error("Error loading cart data:", error);
//     }

//     cartCounter(); 
// });




// import { itemCartStorage } from "../data/checkout-cart.js";
// import { ordersFetch, orders } from "../data/orders-data.js";

// export function cartCounter() {
//     let itemCart = 0;
//     let hasItems = false; 

//     // Ensure elements exist to avoid errors
//     const cartCount = document.querySelector(".cart-count");
//     const notifElement = document.querySelector(".notif");
//     const notifBurger = document.querySelector(".notif-burger");
//     const notifProfile = document.querySelector(".notif-profile");

//     if (!cartCount || !notifElement || !notifBurger || !notifProfile) return;

//     itemCartStorage.cartItems.forEach((eachItemCart) => {
//         const quantity = eachItemCart.quantity;
//         if (typeof quantity === "number" && !isNaN(quantity)) {
//             itemCart += quantity;
//             hasItems = true;
//         } else {
//             console.warn(`Invalid quantity detected for productId: ${eachItemCart.productId}`, quantity);
//         }
//     });

//     cartCount.innerHTML = itemCart || "";

//     const hasOrders = orders.length !== 0;

//     // Toggle notification classes based on cart & orders
//     notifElement.classList.toggle("notif-active", hasItems || hasOrders);
//     notifBurger.classList.toggle("notif-burger-active", hasItems || hasOrders);
//     notifProfile.classList.toggle("notif-profile-active", hasOrders);
// }

// document.addEventListener("DOMContentLoaded", async () => {
//     try {
//         await itemCartStorage.cartStorage();
//         await ordersFetch();
//     } catch (error) {
//         console.error("Error loading cart data:", error);
//     }

//     cartCounter();
// });


import { itemCartStorage } from "../data/checkout-cart.js";
import { ordersFetch, orders } from "../data/orders-data.js";

export function cartCounter() {
    let itemCart = 0;
    let hasItems = false;

    // Ensure elements exist to avoid errors
    const cartCount = document.querySelector(".cart-count");
    const notifElement = document.querySelector(".notif");
    const notifBurger = document.querySelector(".notif-burger");
    const notifProfile = document.querySelector(".notif-profile");

    if (!cartCount || !notifElement || !notifBurger || !notifProfile) return;

    itemCartStorage.cartItems.forEach((eachItemCart) => {
        const quantity = eachItemCart.quantity;
        if (typeof quantity === "number" && !isNaN(quantity)) {
            itemCart += quantity;
            hasItems = true;
        } else {
            console.warn(`Invalid quantity detected for productId: ${eachItemCart.productId}`, quantity);
        }
    });

    cartCount.innerHTML = itemCart || "";

    // Ensure `orders` is defined before checking its length
    const hasOrders = Array.isArray(orders) && orders.length !== 0;

    // Toggle notification classes based on cart & orders
    notifElement.classList.toggle("notif-active", hasItems || hasOrders);
    notifBurger.classList.toggle("notif-burger-active", hasItems || hasOrders);
    notifProfile.classList.toggle("notif-profile-active", hasOrders);
}

document.addEventListener("DOMContentLoaded", async () => {
    try {
        // Wait for both cartStorage and ordersFetch to finish
        await itemCartStorage.cartStorage();
        await ordersFetch();  // Wait for ordersFetch to resolve

        // Now that both are done, run cartCounter
        cartCounter();
    } catch (error) {
        console.error("Error loading cart data:", error);
    }
});


