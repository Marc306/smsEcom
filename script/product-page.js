import { products, productsLoadFetch } from "../data/the-products.js";
import { pdl } from "../data/product-detail-list.js";

class Product{
    constructor(productList){
        this.productList = productList;
    }

    productCardCreator(product){
        // const idKupal = product.id;
        // console.log(product.id)
        return `
        <div class="card js-card" style="width: 18rem;">
            <img src="${product.image}" class="card-img-top js-card-image-top" data-product-info="${product.productId}">
            <div class="card-body">
                <p class="card-text limit-text-in-2line">${product.name}</p>
                <div class="bottom-option">
                    <span class="price-text">₱${product.price}</span>
                    <span class="btn-cart js-card-image-top" data-product-info="${product.productId}">🔍</span>
                </div>
            </div>
        </div>
        `;
    }

    cardFunction(){
        const allCard = document.querySelectorAll(".js-card-image-top");
        allCard.forEach((card) => {
            card.addEventListener("click", (event) => {
                event.preventDefault();
                const uniqueCard = card.dataset.productInfo; 
                window.location.href = `product-details.php?id=${uniqueCard}`;
                pdl.toProductDetails(uniqueCard);
            });
        });
    }

    displayProduct() {
        let eachProduct = "";
        this.productList.forEach((product2) =>{
            console.log(product2.id);
            eachProduct += this.productCardCreator(product2);
        });

        document.querySelector(".card-box").innerHTML = eachProduct;
        this.cardFunction();
    } 
}

document.addEventListener("DOMContentLoaded", () => {
    async function productPageLoad() {
        try{
            await productsLoadFetch();
        }
        catch(error){
            console.log(error);
        }

        const allProduct = new Product(products);
        allProduct.displayProduct();
    }

    productPageLoad();
});


