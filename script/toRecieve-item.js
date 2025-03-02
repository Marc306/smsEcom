import { ordersFetch, orders} from "../data/orders-data.js";
import { itemCartStorage } from "../data/checkout-cart.js";
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

            if(eachOrder.status === "To Receive"){
                orderAttributes += `
                    <div class="orders-container-${getItem.productId}">
                        <div class="order-div">
                            <div class="top">
                                <div class="center-ordersId-div">
                                    <div class="orders-id">ProductId: <span>${getItem.productId}</span></div>
                                </div>
                                <div class="status-item">
                                    <div class="orders-date">${eachOrder.created_at}</div>
                                    <div class="schedule-date">Scheduled Date: ${eachOrder.schedule_date || 'Not scheduled'}</div>
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

                                </div>
                            </div>
                        </div>
                    </div>
                    `;

            loadReceipt();
            }
            else{
                document.querySelector(".main-content").innerHTML = `
                <div class="completed-items-container">
                    <div class="notFound-image-div">
                        <img src="image/icon/no-results-img.png" alt="">  
                        <div class="text-notFound">No orders yet</div>
                    </div> 
                </div>`;
            }
        });

        if (this.orderListItem.length === 0) {
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