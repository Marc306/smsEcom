import { products, productsLoadFetch } from "../data/the-products.js";
import { Product } from "../data/parentClass-product.js";

class ProductHomePage extends Product {
    constructor(productList) {
        super(productList);
    }

    productCardCreator(product1) {
        return super.productCardCreator(product1);
    }

    cardFunction() {
        return super.cardFunction();
    }

    displayProductHomePage() {
        let theSelectedProduct = "";
        this.productList.slice(0,8).forEach((product1) => {
            theSelectedProduct += this.productCardCreator(product1);
        });

        document.querySelector(".card-box2").innerHTML = theSelectedProduct;
        this.cardFunction();
    }
}

document.addEventListener("DOMContentLoaded", () => {
    async function fetchProductLoad() {
        try {
            await productsLoadFetch();
    
        } catch (error) {
            console.log(error);
        }
       
        const allProduct = new ProductHomePage(products);
        allProduct.displayProductHomePage();
    }

    fetchProductLoad();
});





