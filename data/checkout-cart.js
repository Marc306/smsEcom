// class AddToCart {
//     cartItems;

//     constructor() {
//         this.cartStorage();
//     }

//     cartStorage() {
//         this.cartItems = JSON.parse(localStorage.getItem("cartItemStorage"));
//         if (!this.cartItems) {
//             this.cartItems = [{
//                 productId: "123qwe",
//                 gender: "Male",
//                 size: "Large",
//                 quantity: 1,
//             }, {
//                 productId: "456rty",
//                 quantity: 1,
//             }];
//         }

//         console.log(this.cartItems);
//     }

//     storageCartSet() {
//         localStorage.setItem("cartItemStorage", JSON.stringify(this.cartItems));
//     }

//     addToCartUnifroms(itemId, itemGender, itemSize) {
//         let matchingCartItem = "";
//         this.cartItems.forEach((cartItem) => {
//             if (itemId === cartItem.productId) {
//                 matchingCartItem = cartItem;
//             }
//         });

//         const addedNotif = document.querySelector(`.js-added-to-cart-${itemId}`);
//         if (matchingCartItem) {
//             alert("You already added this to the cart.");
//         } else {
//             this.cartItems.push({
//                 productId: itemId,
//                 gender: itemGender,
//                 size: itemSize,
//                 quantity: 1,
//             });

//             setTimeout(() => {
//                 addedNotif.classList.add("added-active");
//                 setTimeout(() => {
//                     addedNotif.classList.remove("added-active");
//                 }, 2000);
//             });
//         }

//         this.storageCartSet();
//     }

//     addToCartBooks(itemID) {
//         console.log(itemID)
//         let matchingCartItem = "";
//         this.cartItems.forEach((cartItem) => {
//             if (itemID === cartItem.productId) {
//                 matchingCartItem = cartItem;
//             }
//         });

//         const addedNotif = document.querySelector(`.js-added-to-cart-${itemID}`);
//         if (matchingCartItem) {
//             alert("You already added this to the cart.");
//         } else {
//             this.cartItems.push({
//                 productId: itemID,
//                 quantity: 1,
//             });

//             setTimeout(() => {
//                 addedNotif.classList.add("added-active");
//                 setTimeout(() => {
//                     addedNotif.classList.remove("added-active");
//                 }, 2000);
//             });
//         }

//         this.storageCartSet();
//     }

//     updateSizeUniform(productId, newUniformSize) {
//         let itemInCart;
//         this.cartItems.forEach((eachItemInCart) => {
//             if (eachItemInCart.productId === productId) {
//                 itemInCart = eachItemInCart
//             }
//         });

//         if (itemInCart) {
//             itemInCart.size = newUniformSize;
//         }

//         this.storageCartSet();
//     }

//     deleteItemCartStorge(idOfTheItem) {
//         let newCart = [];
//         this.cartItems.forEach((items) => {
//             if (items.productId !== idOfTheItem) {
//                 newCart.push(items);
//             }
//         });
//         this.cartItems = newCart;

//         this.storageCartSet();
//     }

   
// }

// export const itemCartStorage = new AddToCart();


class AddToCart {
    cartItems = [];

    constructor() {
        this.cartStorage();
    }

    async cartStorage(){
        try{
            const response = await fetch("https://ecommerce.schoolmanagementsystem2.com/php/cartData.php?action=getCart");
            // const response = await fetch("http://localhost/smsEcommerce/php/cartData.php?action=getCart");
            const data = await response.json();
            this.cartItems = data.cartItems || [];
            localStorage.setItem("cartItems", JSON.stringify(this.cartItems)); 
            return this.cartItems;
        } 
        catch(error){
            console.error("Error fetching cart:", error);
            this.cartItems = [];
            return [];
        }
    }

    async addToCartUnifroms(itemId, itemGender, itemSize){
        try{
            const response = await fetch("https://ecommerce.schoolmanagementsystem2.com/php/cartData.php?action=add", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    productId: itemId,
                    gender: itemGender,
                    size: itemSize,
                    quantity: 1,
                }),
            });
            // const response = await fetch("http://localhost/smsEcommerce/php/cartData.php?action=add", {
            //     method: "POST",
            //     headers: {
            //         "Content-Type": "application/json",
            //     },
            //     body: JSON.stringify({
            //         productId: itemId,
            //         gender: itemGender,
            //         size: itemSize,
            //         quantity: 1,
            //     }),
            // });

            const addedNotif = document.querySelector(`.js-added-to-cart-${itemId}`);
            const data = await response.json();
            if(data.success){
                setTimeout(() => {
                    addedNotif.classList.add("added-active");
                    setTimeout(() => {
                        addedNotif.classList.remove("added-active");
                    }, 2000);
                });
                this.cartStorage(); // Refresh cart
            } 
            else{
                alert(data.message);
            }
        } 
        catch(error){
            console.error("Error adding to cart:", error);
        }
    }

    async addToCartBooks(itemID){
        try{
            const response = await fetch("https://ecommerce.schoolmanagementsystem2.com/php/cartData.php?action=add", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    productId: itemID,
                    quantity: 1,
                }),
            });
            // const response = await fetch("http://localhost/smsEcommerce/php/cartData.php?action=add", {
            //     method: "POST",
            //     headers: {
            //         "Content-Type": "application/json",
            //     },
            //     body: JSON.stringify({
            //         productId: itemID,
            //         quantity: 1,
            //     }),
            // });

            const addedNotif = document.querySelector(`.js-added-to-cart-${itemID}`);
            const data = await response.json();
            if(data.success){
                setTimeout(() => {
                    addedNotif.classList.add("added-active");
                    setTimeout(() => {
                        addedNotif.classList.remove("added-active");
                    }, 2000);
                }); 
                this.cartStorage(); // Refresh cart
            } 
            else{
                alert(data.message);
            }
        } 
        catch(error){ 
            console.error("Error adding book to cart:", error);
        }
    }

    async deleteItemCartStorage(idOfTheItem){
        try {
            const response = await fetch("https://ecommerce.schoolmanagementsystem2.com/php/cart-action.php?action=delete", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ action: "delete", productId: idOfTheItem }),
            });
            // const response = await fetch("http://localhost/smsEcommerce/php/cart-action.php?action=delete", {
            //     method: "POST",
            //     headers: {
            //         "Content-Type": "application/json",
            //     },
            //     body: JSON.stringify({ action: "delete", productId: idOfTheItem }),
            // });

            const data = await response.json();
            if(data.success){
                this.cartStorage(); // Refresh cart
            }
            else{
                alert(data.message);
            }
        } 
        catch(error){
            console.error("Error deleting item:", error);
        }
    }

    async updateSizeUniform(productId, newUniformSize){
        try{
            const response = await fetch("https://ecommerce.schoolmanagementsystem2.com/php/cart-action.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    action: "update_size",
                    productId: productId,
                    newSize: newUniformSize,
                }),
            });
            // const response = await fetch("http://localhost/smsEcommerce/php/cart-action.php", {
            //     method: "POST",
            //     headers: {
            //         "Content-Type": "application/json",
            //     },
            //     body: JSON.stringify({
            //         action: "update_size",
            //         productId: productId,
            //         newSize: newUniformSize,
            //     }),
            // });
    
            const data = await response.json();
            if (data.success){
                await this.cartStorage(); // Refresh cart data
            } 
            else{
                alert(data.error || "Failed to update size.");
            }
        } 
        catch(error){
            console.error("Error updating size:", error);
        }
    }
    
}



export const itemCartStorage = new AddToCart();
