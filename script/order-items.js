// import { ordersFetch, orders, deleteOrder } from "../data/orders-data.js";

// class Order{
//     constructor(orderListItem){
//         this.orderListItem = orderListItem;
//     }

//     toPayOrders(){
//         let orderAttributes = "";
//         let statusPayment;
//         this.orderListItem.forEach((eachOrder) =>{
//             statusPayment = eachOrder.payment.status

//             let getItem = [];
//             eachOrder.items.forEach((items) =>{
//                 getItem = items
//             });

//             orderAttributes = `
//             <div class="orders-container-${getItem.productId}">
//                 <div class="order-div">
//                     <div class="top">
//                         <div class="center-ordersId-div">
//                             <div class="orders-id">ProductId: <span>${getItem.productId}</span></div>
//                         </div>
//                         <div class="status-item">
//                             <div class="orders-date">${eachOrder.created_at}</div>
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
//                                 <span class="span-size"><span class="size-text">Size: </span>${getItem.size}</span>
//                                 <span class="span-gender"><span class="gender-text">Gender: </span>${getItem.gender}</span>
//                             </div>
//                             <span class="price-span">Total Price: <img src="image/icon/philippine-peso.png" alt=""><span class="price">${eachOrder.total_price}</span></span>
//                             <div class="button-div">
//                                 <button class="cancel-button" data-product-id="${getItem.id}">Cancel Order</button>
//                             </div>
//                         </div>
//                     </div>
//                 </div>
//             </div> 
//             `
//         });

//         if(!this.orderListItem){
//             const noOrder = `
//             <div class="completed-items-container">
//                 <div class="notFound-image-div">
//                     <img src="image/icon/no-results-img.png" alt="">  
//                     <div class="text-notFound">
//                         No orders yet
//                     </div>
//                 </div> 
//             </div>
//             `
//             document.querySelector(".main-content").innerHTML = noOrder;
//         }

//         document.querySelector(".main-content").innerHTML = orderAttributes;
//         this.cancelOrder(statusPayment);
//     }

//     cancelOrder(paymentStatus) {
//         const cancelButtons = document.querySelectorAll(".cancel-button");
//         cancelButtons.forEach((button) => {
//             const status = paymentStatus; // Assuming this is passed as a data attribute
    
//             if (status !== "Pending") {
//                 button.disabled = true;
//                 button.classList.add("disabled");
//             }
    
//             button.addEventListener("click", async () => {
//                 const idButton = button.dataset.productId;
    
//                 if (!idButton) {
//                     console.error("Error: No productId provided for deleteOrder()");
//                     return;
//                 }
    
//                 const response = await deleteOrder(idButton);
//                 if (response.success) {
//                     const ordersContainer = document.querySelector(`.orders-container-${idButton}`);
//                     if (ordersContainer) ordersContainer.remove();
//                 } else {
//                     alert(response.message);
//                 }
//             });
//         });
//     }
    
// }

// document.addEventListener("DOMContentLoaded", () => {
//     async function loadOrders(){
//         try{
//             await ordersFetch();
//         }
//         catch(error){
//             console.log(error);
//         }

//         const studentOrder = new Order(orders);
//         studentOrder.toPayOrders();  
//     } 

//     loadOrders();
// });


import { ordersFetch, orders, deleteOrder } from "../data/orders-data.js";
import { itemCartStorage } from "../data/checkout-cart.js";
import { cartCounter } from "./cart-counter.js";
import { loadReceipt } from "../data/fetch-receipt.js";
import { productsLoadFetch, products } from "../data/the-products.js";

class Order {
    constructor(orderListItem) {
        this.orderListItem = orderListItem;
    }

    toPayOrders() {
        let orderAttributes = "";
        this.orderListItem.forEach((eachOrder) => {
            const paymentStatus = eachOrder.payment.status;
            console.log(paymentStatus);
            let getItem = eachOrder.items.length > 0 ? eachOrder.items[0] : null;
            if (!getItem) return;
            
            let productsCategories;
            products.forEach((eachId) =>{
                if(getItem.productId === eachId.productId){
                    productsCategories = eachId;
                }
            });

            console.log(productsCategories.typeItem);
            console.log(eachOrder.payment_method);

            orderAttributes += `
            <div class="orders-container-${getItem.productId}">
                <div class="order-div">
                    <div class="top">
                        <div class="center-ordersId-div">
                            <div class="orders-id">ProductId: <span>${getItem.productId}</span></div>
                        </div>
                        <div class="status-item">
                            <div class="orders-date">${eachOrder.created_at}</div>
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
                                    `
                                    <span class="span-size"><span class="size-text">Size: </span>${getItem.size}</span>
                                    <span class="span-gender"><span class="gender-text">Gender: </span>${getItem.gender}</span>   
                                    ` : ""
                                }
                                
                            </div>
                            <span class="price-span">Total Price: <img src="image/icon/philippine-peso.png" alt=""><span class="price">${eachOrder.total_price}</span></span>

                            ${
                                eachOrder.payment_method === "Gcash Payment" ? 
                                `<form class="uploadReceiptForm" action="uploadReceipt.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="order_id" value="${eachOrder.id}">
                                    
                                    <label class="custom-file-upload1">
                                        Send Receipt
                                        <input type="file" class="file-image-input1" name="receipt_url" accept=".jpg,.jpeg,.png">
                                    </label> 
                                </form>` : ""
                            }

                            <div class="button-div">
                                <button class="cancel-button" data-product-id="${getItem.productId}" ${paymentStatus !== "Pending" ? "disabled" : ""}>Cancel Order</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `;

            loadReceipt();
        });

        if (this.orderListItem.length == 0) {
            document.querySelector(".main-content").innerHTML = `
            <div class="completed-items-container">
                <div class="notFound-image-div">
                    <img src="image/icon/no-results-img.png" alt="">  
                    <div class="text-notFound">No orders yet</div>
                </div> 
            </div>`;
        } 
        else{
            console.log("cops")
        }

        document.querySelector(".main-content").innerHTML = orderAttributes;
        this.cancelOrder();
        this.uploadReceiptImage();
    }

    uploadReceiptImage() {
        document.querySelectorAll(".file-image-input1").forEach((input) => {
            input.addEventListener("change", async function (e) {
                e.preventDefault();
                const form = this.closest("form");
                const formData = new FormData(form);

                try {
                    const response = await fetch("http://localhost/smsEcommerce/php/upload-receipt.php", {
                        method: "POST",
                        body: formData
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        // Update UI to show receipt was uploaded
                        const label = form.querySelector(".custom-file-upload1");
                        if (label) {
                            label.textContent = "Receipt Sent";
                            label.style.background = "#ccc";
                            label.style.cursor = "not-allowed";
                        }
                        
                        // Disable the input
                        this.disabled = true;
                        
                        // Show success message
                        alert("Receipt uploaded successfully!");
                    } else {
                        alert(data.message || "Error uploading receipt");
                    }
                } catch (error) {
                    console.error("Error:", error);
                    alert("Error uploading receipt. Please try again.");
                }
            });
        });
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
        } catch (error) {
            console.error("Error fetching orders:", error);
        }

        const studentOrder = new Order(orders);
        studentOrder.toPayOrders();
    }

    tryLoad();
});
