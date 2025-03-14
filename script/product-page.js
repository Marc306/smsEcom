import { products, productsLoadFetch } from "../data/the-products.js";
import { pdl } from "../data/product-detail-list.js";
import { StockHandler } from "../data/fetch-product-stack.js";

class Product {
    constructor(productList) {
        this.productList = productList;
        this.stockHandler = new StockHandler(); //Initialize StockHandler
    }

    async productCardCreator(product, stockData) {
        // Find stock info for the product
        const stockInfo = stockData.find(item => item.name === product.name);
        const stockLevel = stockInfo ? Number(stockInfo.stock) : 0;

        console.log(`${product.name} - Stock Level:`, stockLevel);

        // Apply stock-based styling
        let borderColor = "border: 1px solid #ddd;"; // Default
        if (stockLevel === 0) {
            borderColor = "border: 2px solid gray; pointer-events: none; opacity: 0.5;";
        } else if (stockLevel >= 1 && stockLevel <= 5) {
            borderColor = "border: 2px solid red;";
        } else if (stockLevel >= 6 && stockLevel <= 10) {
            borderColor = "border: 2px solid orange;";
        }

        return `
        <div class="card js-card" style="width: 18rem; ${borderColor}" data-product-name="${product.name}">
            <img src="${product.image}" class="card-img-top js-card-image-top" data-product-info="${product.productId}">
            <div class="product-stock">Stock: ${stockLevel}</div>
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

    cardFunction() {
        document.querySelectorAll(".js-card-image-top").forEach((card) => {
            card.addEventListener("click", (event) => {
                event.preventDefault();
                const uniqueCard = card.dataset.productInfo;
                window.location.href = `product-details.php?id=${uniqueCard}`;
                pdl.toProductDetails(uniqueCard);
            });
        });
    }

    async displayProduct() {
        let eachProduct = "";

        try {
            //Fetch stock data before rendering
            const stockData = await this.stockHandler.fetchStockData();

            for (const product of this.productList) {
                eachProduct += await this.productCardCreator(product, stockData);
            }

            document.querySelector(".card-box").innerHTML = eachProduct;
            this.cardFunction();
        } catch (error) {
            console.error("Error loading stock data:", error);
        }
    }
}

//Load and display products when the DOM is ready
document.addEventListener("DOMContentLoaded", async () => {
    try {
        await productsLoadFetch();
        const allProduct = new Product(products);
        await allProduct.displayProduct(); //Wait for full product rendering
    } catch (error) {
        console.error("Error loading products:", error);
    }
});




