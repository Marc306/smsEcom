import { products, productsLoadFetch } from "../data/the-products.js";
import { Product } from "../data/parentClass-product.js";

class FilterSideProduct extends Product {
    storageProduct;
    stockData = []; // Store fetched stock data

    constructor(productList) {
        super(productList);
        this.asdc();
        this.funckie();
    }

    async productCardCreator(filterProduct, stockData) { // Pass stockData
        return super.productCardCreator(filterProduct, stockData);
    }

    cardFunction() {
        return super.cardFunction();
    }

    asdc() {
        this.storageProduct = JSON.parse(localStorage.getItem("selectedCategory"));
        if (!this.storageProduct) {
            this.storageProduct = this.productList;
        }
    }

    selectedCategory() {
        document.querySelectorAll(".select-link").forEach((eachSideBtn) => {
            eachSideBtn.addEventListener("click", async () => {
                const selectedCategory = eachSideBtn.getAttribute("data-category");
                console.log("Selected Category:", selectedCategory);
                const categoryFilter = selectedCategory;

                let filteredProducts = [];
                if (categoryFilter === "all") {
                    filteredProducts = this.productList;
                } else {
                    filteredProducts = this.productList.filter((product) =>
                        product.productCategories.includes(categoryFilter)
                    );
                }
                localStorage.setItem("selectedCategory", JSON.stringify(filteredProducts));

                this.asdc();
                await this.funckie(); // ✅ Ensure stock updates when filtering
            });
        });
    }

    async funckie() {
        console.log(this.storageProduct);
        let abccc = "";

        for (const storedProduct of this.storageProduct) {
            console.log(storedProduct);
            abccc += await this.productCardCreator(storedProduct, this.stockData); // ✅ Pass stockData
        }

        document.querySelector(".card-box").innerHTML = abccc;
        this.cardFunction();
    }
}

// document.addEventListener("DOMContentLoaded", async () => {
//     async function fetchProductFilterLoad() {
//         try {
//             await productsLoadFetch();
//             const productSelect = new FilterSideProduct(products);

//             // ✅ Fetch stock data and store it
//             productSelect.stockData = await productSelect.stockHandler.fetchStockData();
            
//             // ✅ Ensure stock data is used
//             await productSelect.funckie(); 
//             productSelect.selectedCategory();
//         } catch (error) {
//             console.log(error);
//         }
//     }

//     fetchProductFilterLoad();
// });
// Function to refresh products and stock data in real-time
async function refreshProductData() {
    try {
        await productsLoadFetch(); // Reload products data
        const productSelect = new FilterSideProduct(products);

        // Fetch stock data
        productSelect.stockData = await productSelect.stockHandler.fetchStockData();

        // Update the product list with stock data
        await productSelect.funckie();
        productSelect.selectedCategory();
    } catch (error) {
        console.error("Error fetching or refreshing products:", error);
    }
}

document.addEventListener("DOMContentLoaded", async () => {
    // Initial product load
    await refreshProductData();

    // Set interval to refresh products and stock data every 10 seconds
    setInterval(async () => {
        await refreshProductData();
    }, 10000); // Refresh every 10 seconds
});




