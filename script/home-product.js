import { products, productsLoadFetch } from "../data/the-products.js"; 
import { Product } from "../data/parentClass-product.js";

class ProductHomePage extends Product {
    constructor(productList) {
        super(productList);
    }

    async productCardCreator(product1, stockData) { // Pass stockData
        return super.productCardCreator(product1, stockData);
    }

    cardFunction() {
        return super.cardFunction();
    }

    async displayProductHomePage(stockData) { // Accept stockData
        let theSelectedProduct = "";
    
        for (const product1 of this.productList.slice(0, 8)) {
            theSelectedProduct += await this.productCardCreator(product1, stockData); //Pass stockData
        }
    
        const productContainer = document.querySelector(".card-box2");
        if (productContainer) {
            productContainer.innerHTML = theSelectedProduct;
            this.cardFunction();
        } else {
            console.error("Error: .card-box2 not found in the DOM.");
        }
    }    
}

document.addEventListener("DOMContentLoaded", async () => {
    try {
        await productsLoadFetch();
        const allProduct = new ProductHomePage(products);

        //Fetch stock data once and pass it to both display methods
        const stockData = await allProduct.stockHandler.fetchStockData(); 
        
        //Parent method
        await allProduct.displayProduct(stockData); 
        //Child method
        await allProduct.displayProductHomePage(stockData); 
    } catch (error) {
        console.error("Error loading products:", error);
    }
});







