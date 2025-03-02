import { products, productsLoadFetch } from "../data/the-products.js";
import { Product } from "../data/parentClass-product.js";

class ProductPage extends Product{
    constructor(productList){
        super(productList);
    }

    productCardCreator(product2){
        return super.productCardCreator(product2);
    }

    cardFunction(){
        return super.cardFunction();
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

        const allProduct = new ProductPage(products);
        allProduct.displayProduct();
    }

    productPageLoad();
});


