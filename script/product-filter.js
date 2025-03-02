import { products, productsLoadFetch } from "../data/the-products.js"
import { Product } from "../data/parentClass-product.js"

class FilterSideProduct extends Product{
    storageProduct;

    constructor(productList) {
        super(productList);
        this.asdc();
        this.funckie();
    }

    productCardCreator(filterProduct){
        return super.productCardCreator(filterProduct);
    }

    cardFunction(){
        return super.cardFunction();
    }

    asdc(){
        this.storageProduct = JSON.parse(localStorage.getItem("selectedCategory"));
        if(!this.storageProduct){
            this.storageProduct = this.productList;
        }
    }

    selectedCategory() {
        document.querySelectorAll(".select-link").forEach((eachSideBtn) => {
            eachSideBtn.addEventListener("click", () => {
                const selectedCategory = eachSideBtn.getAttribute("data-category");
                console.log("Selected Category:", selectedCategory);
                const categoryFilter = selectedCategory;
    
                let filteredProducts = "";
                if (categoryFilter === 'all'){
                    filteredProducts = this.productList;
                } 
                else{
                    filteredProducts = this.productList.filter(product =>{
                        return product.productCategories.includes(categoryFilter);
                    });
                }
                localStorage.setItem("selectedCategory", JSON.stringify(filteredProducts));

                this.asdc();
                this.funckie();
            });
        });
    }

    funckie(){
        console.log(this.storageProduct);
        let abccc = "";
        this.storageProduct.forEach((storedProduct) =>{
            console.log(storedProduct)
            abccc += this.productCardCreator(storedProduct);

            console.log(storedProduct);
        });

        document.querySelector(".card-box").innerHTML = abccc;
        this.cardFunction();
    } 
    
}

document.addEventListener("DOMContentLoaded", () => {
    async function fetchProductFilterLoad() {
        try {
            await productsLoadFetch();
    
        } catch (error) {
            console.log(error);
        }
        
        const productSelect = new FilterSideProduct(products);
        productSelect.selectedCategory();
    }

    fetchProductFilterLoad();
});



