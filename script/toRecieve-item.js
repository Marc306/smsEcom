import { ordersFetch, orders, deleteOrder } from "../data/orders-data.js";
import { cartCounter } from "./cart-counter.js";
import { itemCartStorage } from "../data/checkout-cart.js";
import { productsLoadFetch, products } from "../data/the-products.js";

class Order {
    // constructor(orderListItem) {
    //     this.orderListItem = orderListItem;
    // }
    constructor(orderListItem = []) {
        this.orderListItem = Array.isArray(orderListItem) ? orderListItem : [];
    }    

    // toRecieveOrders() {
    //     let orderAttributes = "";
    //     let displayedProducts = new Set(); // Track unique products

    //     this.orderListItem.forEach((eachOrder) => {
    //         if (eachOrder.status !== "To Receive") return;

    //         console.log(eachOrder);

    //         eachOrder.items.forEach((getItem) => {
    //             if (!getItem) return;

    //             // Ensure a product is displayed only once
    //             if (displayedProducts.has(getItem.productId)) return;
    //             displayedProducts.add(getItem.productId);

    //             let productsCategories = products.find(eachId => getItem.productId === eachId.productId) || {};

    //             orderAttributes += `
    //             <div class="orders-container-${getItem.productId}">
    //                 <div class="order-div">
    //                     <div class="top">
    //                         <div class="center-ordersId-div">
    //                             <div class="orders-id">ProductId: <span>${getItem.productId}</span></div>
    //                         </div>
    //                         <div class="status-item">
    //                             <div class="orders-date">${eachOrder.created_at}</div>
    //                             <div class="schedule-date">Scheduled Date: ${eachOrder.schedule_date ?? 'Not scheduled'}</div>
    //                             <span class="item-status">Status: <span>${eachOrder.status}</span></span>
    //                         </div>
    //                     </div>
    //                     <div class="middle-part">
    //                         <div class="left-side">
    //                             <div class="image-name">
    //                                 <img class="item-img" src="${getItem.image}" alt="">
    //                                 <span class="item-naming">${getItem.name}</span>
    //                             </div>
    //                             <div class="methods">
    //                                 <span>Quantity: <span>${getItem.quantity}</span></span>
    //                                 <span>Payment Method: <span>${eachOrder.payment_method}</span></span>
    //                                 <span>Payment Status: <span>${eachOrder.payment.status}</span></span>
    //                             </div>
    //                         </div>
    //                         <div class="right-side">
    //                             <div class="size-gender">
    //                                 ${
    //                                     productsCategories.typeItem === "uniform" ? 
    //                                     `<span class="span-size"><span class="size-text">Size: </span>${getItem.size}</span>
    //                                     <span class="span-gender"><span class="gender-text">Gender: </span>${getItem.gender}</span>` : ""
    //                                 }
    //                             </div>
    //                             <span class="price-span">Total Price: <img src="image/icon/philippine-peso.png" alt=""><span class="price">${eachOrder.total_price}</span></span>

    //                             <div class="button-div">
    //                                 <button class="cancel-button" data-product-id="${getItem.productId}" ${eachOrder.payment.status !== "Pending" ? "disabled" : ""}>Cancel Order</button>
    //                             </div>
    //                         </div>
    //                     </div>
    //                 </div>
    //             </div>`;
    //         });
    //     });

    //     const mainContent = document.querySelector(".main-content");
    //     if (orderAttributes) {
    //         mainContent.innerHTML = orderAttributes;
    //     } else {
    //         mainContent.innerHTML = `
    //         <div class="completed-items-container">
    //             <div class="notFound-image-div">
    //                 <img src="image/icon/no-results-img.png" alt="">  
    //                 <div class="text-notFound">No orders yet</div>
    //             </div> 
    //         </div>`;
    //     }

    //     this.cancelOrder();
    // }
    toRecieveOrders() {
        if (!Array.isArray(this.orderListItem) || this.orderListItem.length === 0) {
            console.warn("No orders found.");
            document.querySelector(".main-content").innerHTML = `
            <div class="completed-items-container">
                <div class="notFound-image-div">
                    <img src="image/icon/no-results-img.png" alt="">  
                    <div class="text-notFound">No orders yet</div>
                </div> 
            </div>`;
            return;
        }
    
        let orderAttributes = "";
        let displayedProducts = new Set();
    
        this.orderListItem.forEach((eachOrder) => {
            if (eachOrder.status !== "To Receive") return;
    
            eachOrder.items?.forEach((getItem) => { // Optional chaining (?.) to avoid undefined error
                if (!getItem || displayedProducts.has(getItem.productId)) return;
                displayedProducts.add(getItem.productId);
    
                let productsCategories = products.find(eachId => getItem.productId === eachId.productId) || {};
    
                orderAttributes += `
                <div class="orders-container-${getItem.productId}">
                    <div class="order-div">
                        <div class="top">
                            <div class="center-ordersId-div">
                                <div class="orders-id">ProductId: <span>${getItem.productId}</span></div>
                            </div>
                            <div class="status-item">
                                <div class="orders-date">${eachOrder.created_at}</div>
                                <div class="schedule-date">Scheduled Date: ${eachOrder.schedule_date ?? 'Not scheduled'}</div>
                                <span class="item-status">Status: <span>${eachOrder.status}</span></span>
                            </div>
                        </div>
                        <div class="middle-part">
                            <div class="left-side">
                                <div class="image-name">
                                    <img class="item-img" src="${getItem.image}" alt="">
                                    <span class="item-naming">${getItem.name}</span>
                                </div>
                                <div class="methods">
                                    <span>Quantity: <span>${getItem.quantity}</span></span>
                                    <span>Payment Method: <span>${eachOrder.payment_method}</span></span>
                                    <span>Payment Status: <span>${eachOrder.payment.status}</span></span>
                                </div>
                            </div>
                            <div class="right-side">
                                <div class="size-gender">
                                    ${
                                        productsCategories.typeItem === "uniform" ? 
                                        `<span class="span-size"><span class="size-text">Size: </span>${getItem.size}</span>
                                        <span class="span-gender"><span class="gender-text">Gender: </span>${getItem.gender}</span>` : ""
                                    }
                                </div>
                                <span class="price-span">Total Price: <img src="image/icon/philippine-peso.png" alt=""><span class="price">${eachOrder.total_price}</span></span>

                                <div class="button-div">
                                    <button class="cancel-button" data-product-id="${getItem.productId}" ${eachOrder.payment.status !== "Pending" ? "disabled" : ""}>Cancel Order</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            });
        });
    
        document.querySelector(".main-content").innerHTML = orderAttributes || `
        <div class="completed-items-container">
            <div class="notFound-image-div">
                <img src="image/icon/no-results-img.png" alt="">  
                <div class="text-notFound">No orders yet</div>
            </div> 
        </div>`;

        this.cancelOrder()
    }

    cancelOrder() {
        document.querySelectorAll(".cancel-button").forEach((button) => {
            button.addEventListener("click", async () => {
                const productId = button.dataset.productId;
                if (!productId) {
                    console.error("Error: No productId provided for deleteOrder()");
                    return;
                }

                try {
                    const response = await deleteOrder(productId);
                    alert("Order successfully canceled."); // Debugging
                    
                    
                    if (response && response.success) {
                        document.querySelector(`.orders-container-${productId}`).remove();

                        await ordersFetch(); // Refetch updated order list
                        await itemCartStorage.cartStorage(); // Refresh cart data
                        cartCounter();
                        
                    } else {
                        alert(response?.message || "Failed to delete order");
                    }
                } catch (error) {
                    console.error("Delete order failed:", error);
                    alert("An error occurred while deleting the order.");
                }
            });
        });
    }
    
}

document.addEventListener("DOMContentLoaded", () => {
    async function tryLoad(){
        try {
            await ordersFetch();
            await productsLoadFetch();
            await itemCartStorage.cartStorage();

            console.log("Orders after fetch:", orders); // Debugging

            if (!Array.isArray(orders)) {
                throw new Error("Orders is not an array.");
            }
            
            const studentOrder = new Order(orders);
            studentOrder.toRecieveOrders();

        } catch (error) {
            console.error("Error fetching orders:", error);
        }

        // const studentOrder = new Order(orders);
        // studentOrder.toRecieveOrders();
    }

    tryLoad();

    // Re-fetch orders every 10 seconds
    setInterval(async () => {
        await ordersFetch();
        await productsLoadFetch();
        await itemCartStorage.cartStorage();
        const studentOrder = new Order(orders);
        studentOrder.toRecieveOrders();
    }, 10000); // Refresh every 10 seconds
});