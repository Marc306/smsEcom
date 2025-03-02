import { itemCartStorage } from "../data/checkout-cart.js";
import { products, productsLoadFetch  } from "../data/the-products.js";
import { cartCounter } from "./cart-counter.js";

// class GetItemsFromCart {
//     constructor(productItems) {
//         this.productItems = productItems;
//         this.cartContainer = document.querySelector(".js-product-cart-box");
//         this.countItemsCart();
//         this.initEventListeners();
//     }

//     getItems() {
//         let itemFromCart = "";
//         itemCartStorage.cartItems.forEach((eachItemInCart) => {
//             const matchingItems = this.productItems.find(item => item.productId == eachItemInCart.productId);
//             if (!matchingItems) return;

//             itemFromCart += `
//                 <div class="grid-div js-grid-div-${matchingItems.productId}">
//                     <div class="products-div">
//                         <img class="exit-button js-delete-item-cart" src="image/icon/exit-button.png" data-delete-cartbtn="${matchingItems.productId}">
//                     </div>
//                     <div class="products-div">
//                         <img class="product-image-cart" src="${matchingItems.image}" alt="">
//                     </div>
//                     <div class="products-div">
//                         <div>
//                             <span class="product-name-cart limit-text-line">${matchingItems.name}</span>
//                             ${matchingItems.typeItem === "uniform" ? `
//                                 <div class="size-edit">
//                                     <span class="gender">${eachItemInCart.gender}</span>
//                                     <div class="size-box">
//                                         <span class="sizes js-size-${matchingItems.productId}">${eachItemInCart.size}</span>
//                                         <select class="selected-new-size js-new-size-${matchingItems.productId}" data-new-quantity="${matchingItems.productId}">
//                                             <option value="Small">Small</option>
//                                             <option value="Medium">Medium</option>
//                                             <option value="Large">Large</option>
//                                             <option value="XL">XL</option>
//                                             <option value="2XL">2XL</option>
//                                             <option value="3XL">3XL</option>
//                                             <option value="4XL">4XL</option>
//                                             <option value="5XL">5XL</option>
//                                         </select>
//                                     </div>
//                                     <span class="edit-button js-edit-button" data-update-size="${matchingItems.productId}">Edit</span>
//                                     <span class="save-button js-save-button" data-save-update="${matchingItems.productId}">Save</span>
//                                 </div>` : ""}
//                         </div>
//                     </div>
//                     <div class="products-div">  
//                         <span class="quantity-item">${eachItemInCart.quantity}</span>
//                     </div>
//                     <div class="products-div">
//                         <span class="product-price-cart"><img src="image/icon/philippine-peso.png" alt="">${matchingItems.price}</span>
//                     </div>
//                 </div>
//             `;
//         });

//         this.cartContainer.innerHTML = itemFromCart;
//         this.updateTotalPrice();
//     }

//     updateTotalPrice() {
//         let totalPrice = itemCartStorage.cartItems.reduce((sum, eachItemInCart) => {
//             let matchingItems = this.productItems.find(item => item.productId == eachItemInCart.productId);
//             return matchingItems ? sum + (matchingItems.price * eachItemInCart.quantity) : sum;
//         }, 0);
//         document.querySelector(".js-text-all-price").innerHTML = totalPrice.toFixed(2);
//     }

//     initEventListeners() {
//         this.cartContainer.addEventListener("click", (event) => {
//             if (event.target.matches(".js-delete-item-cart")) {
//                 this.deleteCartItem(event.target.dataset.deleteCartbtn);
//             } else if (event.target.matches(".js-edit-button")) {
//                 const id = event.target.dataset.updateSize;
//                 document.querySelector(`.js-grid-div-${id}`).classList.add("edit-size");
//             } else if (event.target.matches(".js-save-button")) {
//                 this.saveUpdateCartSize(event.target.dataset.saveUpdate);
//             }
//         });
//     }

//     deleteCartItem(productId) {
//         fetch("http://localhost/smsEcommerce/php/cart-action.php", {
//             method: "POST",
//             headers: { "Content-Type": "application/json" },
//             body: JSON.stringify({ action: "delete", productId }),
//         })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 itemCartStorage.deleteItemCartStorage(productId);
//                 document.querySelector(`.js-grid-div-${productId}`).remove();
//                 this.countItemsCart();
//                 this.updateTotalPrice();
//                 cartCounter();
//             } else {
//                 console.error("Error deleting item:", data.error);
//             }
//         })
//         .catch(error => console.error("Fetch error:", error));
//     }

//     saveUpdateCartSize(productId) {
//         const newSize = document.querySelector(`.js-new-size-${productId}`).value;
//         fetch("http://localhost/smsEcommerce/php/cart-action.php", {
//             method: "POST",
//             headers: { "Content-Type": "application/json" },
//             body: JSON.stringify({ action: "update_size", productId, newSize }),
//         })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 itemCartStorage.updateSizeUniform(productId, newSize);
//                 document.querySelector(`.js-size-${productId}`).textContent = newSize;
//                 document.querySelector(`.js-grid-div-${productId}`).classList.remove("edit-size");
//             }
//         })
//         .catch(error => console.error("Fetch error:", error));
//     }

//     countItemsCart() {
//         const itemCount = itemCartStorage.cartItems.reduce((sum, item) => sum + item.quantity, 0);
//         document.querySelector(".js-text-count-cart").textContent = itemCount;
//     }
// }

// document.addEventListener("DOMContentLoaded", async () => {
//     await productsLoadFetch();
//     new GetItemsFromCart(products).getItems();
// });



class GetItemsFromCart {
    constructor(productItems){
        this.productItems = productItems;
        this.countItemsCart();
    }

    getItems(){
        let itemFromCart = "";
        itemCartStorage.cartItems.forEach((eachItemInCart) =>{
            const productId = eachItemInCart.productId;

            let matchingItems;
            this.productItems.forEach((item) =>{
                if(item.productId == productId){
                    matchingItems = item;
                }
            });


            if(matchingItems.typeItem == "uniform"){
                itemFromCart += `
                <div class="grid-div js-grid-div-${matchingItems.productId}">
                    <div class="products-div">
                        <img class="exit-button js-delete-item-cart" src="image/icon/exit-button.png" data-delete-cartbtn="${matchingItems.productId}">
                    </div>
                    <div class="products-div">
                        <img class="product-image-cart" src="${matchingItems.image}" alt="">
                    </div>
                    <div class="products-div">
                        <div>
                            <span class="product-name-cart limit-text-line">${matchingItems.name}</span>
                            <div class="size-edit">
                                <span class="gender">${eachItemInCart.gender}</span>
                                <div class="size-box">
                                    <span class="sizes js-size-${matchingItems.productId}">${eachItemInCart.size}</span>
                                    <select class="selected-new-size js-new-size-${matchingItems.productId}" data-new-quantity="${matchingItems.productId}">
                                        <option value="Small">Small</option>
                                        <option value="Medium">Medium</option>
                                        <option value="Large">Large</option>
                                        <option value="XL">XL</option>
                                        <option value="2XL">2XL</option>
                                        <option value="3XL">3XL</option>
                                        <option value="4XL">4XL</option>
                                        <option value="5XL">5XL</option>
                                    </select>
                                </div>
                                <span class="edit-button js-edit-button" data-update-size="${matchingItems.productId}">Edit</span>
                                <span class="save-button js-save-button" data-save-update="${matchingItems.productId}">Save</span>
                            </div>
                        </div>
                    </div>
                    <div class="products-div">  
                        <span class="quantity-item">${eachItemInCart.quantity}</span>
                    </div>
                    <div class="products-div">
                        <span class="product-price-cart"><img src="image/icon/philippine-peso.png" alt="">${matchingItems.price}</span>
                    </div>
                </div>
                `;
            } 
            else{
                itemFromCart += `
                <div class="grid-div js-grid-div-${matchingItems.productId}">
                    <div class="products-div">
                        <img class="exit-button js-delete-item-cart" src="image/icon/exit-button.png" data-delete-cartbtn="${matchingItems.productId}">
                    </div>
                    <div class="products-div">
                        <img class="product-image-cart" src="${matchingItems.image}" alt="">
                    </div>
                    <div class="products-div">
                        <div>
                            <span class="product-name-cart limit-text-line">${matchingItems.name}</span>
                        </div>
                    </div>
                    <div class="products-div">  
                        <span class="quantity-item">${eachItemInCart.quantity}</span>
                    </div>
                    <div class="products-div">
                        <span class="product-price-cart"><img src="image/icon/philippine-peso.png" alt="">${matchingItems.price}</span>
                    </div>
                </div>
                `;
            } 
            
            document.querySelector(".js-product-cart-box").innerHTML = itemFromCart;
        });

       

        this.updateTotalPrice(); 
        this.updateCartSizeInUnif();

        this.deleteCartItems();
       
    }

    updateTotalPrice(){
        let totalPrice = 0.00;
        itemCartStorage.cartItems.forEach((eachItemInCart) => {
            let matchingItems = this.productItems.find(item => item.productId == eachItemInCart.productId);
            if (matchingItems) {
                totalPrice += parseFloat(matchingItems.price) * eachItemInCart.quantity;
            }
        });

        document.querySelector(".js-text-all-price").innerHTML = totalPrice.toFixed(2);
    }

    deleteCartItems(){
        const deleteCartProduct = document.querySelectorAll(".js-delete-item-cart");
        deleteCartProduct.forEach((btnDeleteCart) => {
            btnDeleteCart.addEventListener("click", async () => {  // Make this an async function
                const btnDeleteId = btnDeleteCart.dataset.deleteCartbtn;
    
                await itemCartStorage.deleteItemCartStorage(btnDeleteId);
                const deleteItemCart = document.querySelector(`.js-grid-div-${btnDeleteId}`);
    
                if(deleteItemCart) deleteItemCart.remove();
                
                await itemCartStorage.cartStorage();  // Wait for cart update
                
                this.countItemsCart();
                this.updateTotalPrice();
                cartCounter();
            });
        });
    }
    
    
    updateCartSizeInUnif(){
        const editButtons = document.querySelectorAll(".js-edit-button");
        editButtons.forEach((eachEditButton) => {
            eachEditButton.addEventListener("click", () => {
                const idOfOption = eachEditButton.dataset.updateSize;
    
                const containerCart = document.querySelector(`.js-grid-div-${idOfOption}`);
                containerCart.classList.add("edit-size");
    
                this.saveUpdateCartSizeUnif();
            });  
        });  
    }
       
    saveUpdateCartSizeUnif(){
        const saveButton = document.querySelectorAll(".js-save-button");
        saveButton.forEach((eachSaveBtn) => {
            eachSaveBtn.addEventListener("click", async () => {  // Make this an async function
                const saveUpdateSizeId = eachSaveBtn.dataset.saveUpdate;
                const sizeDropdown = document.querySelector(`.js-new-size-${saveUpdateSizeId}`);
                const newSize = sizeDropdown.value;
    
                await itemCartStorage.updateSizeUniform(saveUpdateSizeId, newSize);
                this.saveUiUpdatedUniformSize(saveUpdateSizeId);
    
                const containerCart = document.querySelector(`.js-grid-div-${saveUpdateSizeId}`);
                containerCart.classList.remove("edit-size");
    
                await itemCartStorage.cartStorage();  // Wait for cart update
                cartCounter();
            });
        });
    }
    
    saveUiUpdatedUniformSize(saveUpdateSizeId){
        let updatedSize;
        const cartItem = itemCartStorage.cartItems.find(item => item.productId == saveUpdateSizeId);
        if(cartItem){
            updatedSize = cartItem.size; 
        }
        const sizeDisplay = document.querySelector(`.js-size-${saveUpdateSizeId}`);
        sizeDisplay.textContent = updatedSize;
    }
    

    countItemsCart(){
        let itemInCart = 0;
        itemCartStorage.cartItems.forEach((eachItemCart) => {
            const quantity = eachItemCart.quantity;
            if(typeof quantity === 'number' && !isNaN(quantity)){
                itemInCart += quantity;
            } 
            else{
                console.warn(`Invalid quantity detected for productId: ${eachItemCart.productId}`, quantity);
            }
        });

        document.querySelector(".js-text-count-cart").innerHTML = itemInCart;
    }
}

document.addEventListener("DOMContentLoaded", () => {
    async function fetchProductCart() {
        try{
            await productsLoadFetch();
            await itemCartStorage.cartStorage()
    
        } 
        catch(error){
            console.log(error);
        }

        const itemsInCart = new GetItemsFromCart(products); 
        itemsInCart.getItems();
    } 
    
    fetchProductCart();
});




 // saveUpdateCartSizeUnif(){
    //     const saveButton = document.querySelectorAll(".js-save-button");
    //     saveButton.forEach((eachSaveBtn) => {
    //         eachSaveBtn.addEventListener("click", () => {
    //             const saveUpdateSizeId = eachSaveBtn.dataset.saveUpdate;
    //             const sizeDropdown = document.querySelector(`.js-new-size-${saveUpdateSizeId}`);
    //             console.log(saveUpdateSizeId);
    //             const newSize = sizeDropdown.value;

    //             itemCartStorage.updateSizeUniform(saveUpdateSizeId, newSize);
    //             this.saveUiUpdatedUniformSize(saveUpdateSizeId);
    //             const containerCart = document.querySelector(`.js-grid-div-${saveUpdateSizeId}`);
    //             containerCart.classList.remove("edit-size");

    //             // // Send a request to update size in the database
    //             fetch("http://localhost/smsEcommerce/php/cart-action.php", {
    //                 method: "POST",
    //                 headers: { "Content-Type": "application/json" },
    //                 body: JSON.stringify({ action: "update_size", productId: saveUpdateSizeId, newSize: newSize }),
    //             })
    //             .then(response => response.json())
    //             .then( async (data) => {
    //                 if(data.success){
                        
    //                     await itemCartStorage.cartStorage();
    //                     cartCounter()

    //                     itemCartStorage.updateSizeUniform(saveUpdateSizeId, newSize);
    //                     this.saveUiUpdatedUniformSize(saveUpdateSizeId);
    //                     const containerCart = document.querySelector(`.js-grid-div-${saveUpdateSizeId}`);
    //                     containerCart.classList.remove("edit-size");

    //                 } 
    //                 else{
    //                     console.error("Error updating size:", data.error);
    //                 }
    //             })
    //             .catch(error => console.error("Fetch error:", error));
    //         });
    //     });
    // } 


    // deleteCartItems(){
    //     const deleteCartProduct = document.querySelectorAll(".js-delete-item-cart");
    //     deleteCartProduct.forEach((btnDeleteCart) => {
    //         btnDeleteCart.addEventListener("click", () => {
    //             const btnDeleteId = btnDeleteCart.dataset.deleteCartbtn;

    //             //Send a request to delete item from database
    //             fetch("http://localhost/smsEcommerce/php/cart-action.php", {
    //                 method: "POST",
    //                 headers: { "Content-Type": "application/json" },
    //                 body: JSON.stringify({ action: "delete", productId: btnDeleteId }),
    //             })
    //             .then(response => response.json())
    //             .then( async (data) => {
    //                 if(data.success){
                        
    //                     await itemCartStorage.cartStorage();
    //                     cartCounter();

    //                     itemCartStorage.deleteItemCartStorage(btnDeleteId);
    //                     const deleteItemCart = document.querySelector(`.js-grid-div-${btnDeleteId}`);

    //                     if(deleteItemCart) deleteItemCart.remove();
    //                     this.countItemsCart();
    //                     this.updateTotalPrice();

    //                 } 
    //                 else{ 
    //                     console.error("Error deleting item:", data.error);
    //                 }
    //             })
    //             .catch(error => console.error("Fetch error:", error));
    //         });
    //     });
    // }