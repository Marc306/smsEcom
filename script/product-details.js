import { pdl } from "../data/product-detail-list.js";
import { products, productsLoadFetch } from "../data/the-products.js";
import { itemCartStorage } from "../data/checkout-cart.js";
import { cartCounter } from "./cart-counter.js";
import { buyNowItemAndIfDuplicate } from "./buyNow-duplicate-item.js";

class Details {
    matchingIdProduct;

    constructor(allProduct, selectProductDetails, productAddToCart){
        this.allProduct = allProduct;
        this.selectProductDetails = selectProductDetails;
        this.productAddToCart = productAddToCart;
    }

    displayDetails(){
        const productID = this.selectProductDetails.productDetails;

        this.allProduct.forEach((productItemsId) => {
            if(productID === productItemsId.productId){
                this.matchingIdProduct = productItemsId;
            }
        });

        this.distributeDetails(this.matchingIdProduct);
    }

    distributeDetails(matchingIdProduct){
        let detailsOfProduct = `
        <div class="image-details">
            <div class="image-div">
                <div class="image-product">
                    <img class="image-product" src="${matchingIdProduct.image}" alt="">
                </div>
                
                ${
                    matchingIdProduct.typeItem === "books"
                        ? `<p>${matchingIdProduct.productDescription}</p>`
                        : `<div class="size-chart-div">
                            <img class="size-chart-image" src="/image/size-chart.jpg" alt="">
                        </div>`
                }
            </div>
        </div>

        <div class="other-details">
            <div class="details-text">
                <div class="product-name">
                    <p class="course-text limit-text-2line">${matchingIdProduct.name}</p>
                    ${matchingIdProduct.typeItem === "uniform" ? `<p class="year-text">${matchingIdProduct.productDescription}</p>` : ""}
                </div>

                <div class="pricing-div">
                    <p class="price-text"><img class="icon-peso" src="image/icon/philippine-peso2.png" alt=""> ${matchingIdProduct.price}</p>
                </div>

                ${
                    matchingIdProduct.typeItem === "uniform"
                        ? `
                    <div class="product-select-size-gender">
                        <div class="div-select-gender">
                            <p class="gender-text">Gender:</p>
                            <select class="selecting-gender" name="" id="">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="div-select-size">
                            <p class="size-text">Size:</p>
                            <select class="selecting-size" name="" id="">
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
                    </div>
                    `
                        : ""
                }  

                <div class="added-to-cart-icon js-added-to-cart-${matchingIdProduct.productId}">
                    <img src="image/icon/checkmark.png">
                    Added to the cart
                </div>
                <div class="buttons-buyNow-addtocart">
                    <button class="buy-now-btn js-buy-now-btn" data-buy-item="${matchingIdProduct.productId}">BUY NOW</button>
                    <button class="add-to-cart-btn js-button-cart" data-cart-button="${matchingIdProduct.productId}">ADD TO CART</button>
                </div>
            </div>
        </div>
        `;

        document.querySelector(".js-details-div").innerHTML = detailsOfProduct;

        this.buttonAddToCart(matchingIdProduct);
        this.buyNowButton();
    }

    buttonAddToCart(matchingIdProduct){
        const cartButton = document.querySelector(".js-button-cart");
        cartButton.addEventListener("click", () => {
            const idCartBtn = cartButton.dataset.cartButton;

            if(matchingIdProduct.productId == idCartBtn){
                this.sendToCart(matchingIdProduct); // Send data to backend
                cartCounter(); // Update cart count visually
            }
        });
    }

    selectSizeGender(){
        const genderSelect = document.querySelector(".selecting-gender");
        const sizeSelect = document.querySelector(".selecting-size");

        let gender = genderSelect ? genderSelect.value : "";
        let size = sizeSelect ? sizeSelect.value : "";

        return { gender, size };
    }


    async sendToCart(matchingIdProduct){
        const { gender, size } = this.selectSizeGender();
    
        if(matchingIdProduct.typeItem === "uniform"){
            await itemCartStorage.addToCartUnifroms(matchingIdProduct.productId, gender, size);
        } 
        else{
            await itemCartStorage.addToCartBooks(matchingIdProduct.productId);
        }

        await itemCartStorage.cartStorage();
        cartCounter();
    }


    buyNowButton() {
        const buyNowBtn = document.querySelector(".js-buy-now-btn");
    
        buyNowBtn.addEventListener("click", () => {
            const buyNowId = buyNowBtn.dataset.buyItem;
    
            // Find the product
            const selectedProduct = this.allProduct.find(product => product.productId == buyNowId);
            if (!selectedProduct) {
                alert("Product not found.");
                return;
            }
    
            // Store product details in sessionStorage
            let productDetails = {
                productId: selectedProduct.productId,
                name: selectedProduct.name,
                price: selectedProduct.price,
                image: selectedProduct.image,
                typeItem: selectedProduct.typeItem
            };
    
            // If it's a uniform, store size and gender
            if (selectedProduct.typeItem === "uniform") {
                const { gender, size } = this.selectSizeGender();
                productDetails.gender = gender;
                productDetails.size = size;
            }
    
            sessionStorage.setItem("buyNowProduct", JSON.stringify(productDetails));

            // Call the `buyNowItemAndIfDuplicate()` function and wait for it to complete
            buyNowItemAndIfDuplicate().then(() => {
                // ✅ Ensure correct redirection only after `buyNowItemAndIfDuplicate` is complete
                window.location.href = "https://ecommerce.schoolmanagementsystem2.com/payment-option.php";
            }).catch(error => {
                // Handle any errors that occurred during the duplication check
                console.error('Error during the duplicate check:', error);
                alert('An error occurred. Please try again later.');
            });
            // window.location.href = "http://localhost/smsEcommerce/payment-option.php";
        });
    }
     

}

document.addEventListener("DOMContentLoaded", () => {
    async function fetchProductDetailsLoad() {
        try {
            await productsLoadFetch();
            await itemCartStorage.cartStorage();

            await buyNowItemAndIfDuplicate();

            const displayDetailsOfProduct = new Details(products, pdl);
            displayDetailsOfProduct.displayDetails();
        } catch (error) {
            console.log(error);
        }

        // const displayDetailsOfProduct = new Details(products, pdl);
        // displayDetailsOfProduct.displayDetails();
    }

    fetchProductDetailsLoad();
});







// import { pdl } from "../data/product-detail-list.js";
// import { products, productsLoadFetch } from "../data/the-products.js";
// import { itemCartStorage } from "../data/checkout-cart.js";
// import { cartCounter } from "./cart-counter.js";

// class Details {
//     matchingIdProduct;

//     constructor(allProduct, selectProductDetails, productAddToCart){
//         this.allProduct = allProduct
//         this.selectProductDetails = selectProductDetails;
//         this.productAddToCart = productAddToCart;
//     }

//     displayDetails(){
//         const productID = this.selectProductDetails.productDetails;

//         this.allProduct.forEach((productItemsId) =>{
//             if(productID === productItemsId.productId){
//                 this.matchingIdProduct = productItemsId;
//             }
//         });

//         this.distributeDetails(this.matchingIdProduct);
//     }

//     distributeDetails(matchingIdProduct){
//         let detailsOfProduct = `
//         <div class="image-details">
//             <div class="image-div">
//                 <div class="image-product">
//                     <img class="image-product" src="${matchingIdProduct.image}" alt="">
//                 </div>
                
//                 ${
//                     matchingIdProduct.typeItem === "books"
//                     ? ""

//                     :`<div class="size-chart-div">
//                         <img class="size-chart-image" src="${matchingIdProduct.productDescription}" alt="">
//                     </div>`
//                 }
//             </div>
//         </div>

//         <div class="other-details">
//             <div class="details-text">
//                 <div class="product-name">
//                     <p class="course-text limit-text-2line">${matchingIdProduct.name}</p>
//                     <p class="year-text">(1st year to 4th year)</p>
//                 </div>

//                 <div class="pricing-div">
//                     <p class="price-text"><img class="icon-peso" src="image/icon/philippine-peso2.png" alt=""> ${matchingIdProduct.price}</p>
//                 </div>

//                 ${
//                     matchingIdProduct.typeItem === "uniform"
//                     ? `
//                     <div class="product-select-size-gender">
//                         <div class="div-select-gender">
//                             <p class="gender-text">Gender:</p>
//                             <select class="selecting-gender" name="" id="">
//                                 <option value="Male">Male</option>
//                                 <option value="Female">Female</option>
//                             </select>
//                         </div>

//                         <div class="div-select-size">
//                             <p class="size-text">Size:</p>
//                             <select class="selecting-size" name="" id="">
//                                 <option value="Small">Small</option>
//                                 <option value="Medium">Medium</option>
//                                 <option value="Large">Large</option>
//                                 <option value="XL">XL</option>
//                                 <option value="2XL">2XL</option>
//                                 <option value="3XL">3XL</option>
//                                 <option value="4XL">4XL</option>
//                                 <option value="5XL">5XL</option>
//                             </select>
//                         </div>
//                     </div>
//                     ` 
//                     : ""
//                 }  
                
//                 <div class="added-to-cart-icon js-added-to-cart-${matchingIdProduct.productId}">
//                     <img src="image/icon/checkmark.png">
//                     Added to the cart
//                 </div>
//                 <div class="buttons-buyNow-addtocart">
//                     <button class="buy-now-btn">BUY NOW</button>
//                     <button class="add-to-cart-btn js-button-cart" data-cart-button="${matchingIdProduct.productId}">ADD TO CART</button>
//                 </div>
//             </div>
//         </div>
//         `;

//         document.querySelector(".js-details-div").innerHTML = detailsOfProduct;

//         this.buttonAddToCart(matchingIdProduct);
//     }

//     buttonAddToCart(matchingIdProduct){
//         const cartButton = document.querySelector(".js-button-cart");
//         cartButton.addEventListener("click", () =>{
//             const idCartBtn = cartButton.dataset.cartButton;

//             if(matchingIdProduct.productId == idCartBtn){
//                 this.selectSizeGender(matchingIdProduct);

//                 cartCounter();
//             }
//         });
//     }

//     selectSizeGender(matchingIdProduct) {
//         const genderSelect = document.querySelector(".selecting-gender");
//         const sizeSelect = document.querySelector(".selecting-size");
    
//         // Get default selected values
//         let gender = genderSelect ? genderSelect.value : "";
//         let size = sizeSelect ? sizeSelect.value : "";
    
//         if (matchingIdProduct.typeItem == "uniform" || matchingIdProduct.typeItem == "others") {
//             // Event listeners to update values on change
//             genderSelect.addEventListener("change", () => {
//                 gender = genderSelect.value;
//             });
    
//             sizeSelect.addEventListener("change", () => {
//                 size = sizeSelect.value;
//             });
    
//             // Ensure default values are stored even if user doesn't change selection
//             itemCartStorage.addToCartUnifroms(matchingIdProduct.productId, gender, size);
//         } else {
//             itemCartStorage.addToCartBooks(matchingIdProduct.productId);
//         }
//     }
    
// }


// document.addEventListener("DOMContentLoaded", () => {
//     async function fetchProductDetailsLoad() {
//         try {
//             await productsLoadFetch();
    
//         } catch (error) {
//             console.log(error);
//         }
       
//         const displayDetailsOfProduct = new Details(products, pdl);
//         displayDetailsOfProduct.displayDetails()
//     }

//     fetchProductDetailsLoad();
// });



// this.selectProductDetails.productDetails.forEach((detailProduct) =>{
//     const productId = detailProduct.id;
    
//     this.allProduct.forEach((productItemsId) =>{
//         if(productId === productItemsId.id){
//             this.matchingIdProduct = productItemsId;
//         }
//     });

//     this.distributeDetails(this.matchingIdProduct)
// });