import { pdl } from "./product-detail-list.js";

export class Product {
    constructor(productList){
        this.productList = productList;
    }

    productCardCreator(product){
        // const idKupal = product.id;
        // console.log(product.id)
        return `
        <div class="card js-card" style="width: 18rem;">
            <img src="${product.image}" class="card-img-top card-picture js-card-image-top" data-product-info="${product.productId}">
            <div class="product-stock">${product.stock}</div>
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
}
