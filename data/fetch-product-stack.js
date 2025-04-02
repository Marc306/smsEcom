export class StockHandler { 
    async fetchStockData() {
        try {
            // ecommerce.schoolmanagementsystem2.com
            const response = await fetch("https://ecommerce.schoolmanagementsystem2.com/php/monitoring-stacks.php");
            // const response = await fetch("http://localhost/smsEcommerce/php/monitoring-stacks.php");

            if (!response.ok) {
                throw new Error(`HTTP Error! Status: ${response.status}`);
            }

            const data = await response.json(); // ✅ Parse JSON directly

            if (!data || typeof data !== "object" || !Array.isArray(data.products)) {
                throw new Error("Invalid JSON format received");
            }

            console.log("✅ Fetched stock data:", data.products);

            return data.products; // ✅ Ensure we return only product data
        } catch (error) {
            console.error("❌ Error fetching product stock:", error);
            return [];
        }
    }    

    updateProductBorders(products) {
        products.forEach(product => {
            const card = document.querySelector(`[data-product-info="${product.productId}"]`);
            
            if (!card) {
                console.warn(`❌ Product card not found for ID: ${product.productId}`);
                return;
            }

            const stock = Number(product.stock); // ✅ Ensure stock is a number

            console.log(`📦 Updating UI for ${product.name} - Stock: ${stock}`);

            if (stock === 0) {
                card.style.border = "2px solid gray";
                card.style.pointerEvents = "none"; // Disable clicking
                card.style.opacity = "0.5"; // Gray out
            } else if (stock >= 1 && stock <= 5) {
                card.style.border = "2px solid red";
                card.style.pointerEvents = "auto";
                card.style.opacity = "1";
            } else if (stock >= 6 && stock <= 10) {
                card.style.border = "2px solid orange";
            } else {
                card.style.border = "1px solid #ddd"; // Normal state
            }
        });
    }

    handleAIAlerts(aiData) {
        if (Array.isArray(aiData) && aiData.length > 0) {
            aiData.forEach(alert => {
                console.warn("⚠️ AI Alert:", alert.type, alert.message);
                this.alertUser(alert.message);
            });
        }
    }

    alertUser(message) {
        const alertBox = document.createElement("div");
        alertBox.className = "ai-alert";
        alertBox.textContent = message;
        
        document.body.appendChild(alertBox);
        setTimeout(() => alertBox.remove(), 5000);
    }
}


// export class StockHandler {
//     constructor() {
//         // Start polling immediately and set interval to 30 seconds
//         this.fetchStockDataInterval = setInterval(() => this.fetchStockData(), 30000);

//         // Clean up interval when the window is unloaded
//         window.addEventListener('beforeunload', () => {
//             this.stopFetching();
//         });
//     }

//     async fetchStockData() {
//         try {
//             // Fetch stock data from the API
//             const response = await fetch("https://ecommerce.schoolmanagementsystem2.com/php/monitoring-stacks.php");

//             // Check if the response is OK (status 200-299)
//             if (!response.ok) {
//                 throw new Error(`HTTP Error! Status: ${response.status}`);
//             }

//             // Parse the response data as JSON
//             const data = await response.json();

//             // Validate the data format
//             if (!data || typeof data !== "object" || !Array.isArray(data.products)) {
//                 throw new Error("Invalid JSON format received");
//             }

//             console.log("✅ Fetched stock data:", data.products);

//             // Update the product UI based on the fetched stock data
//             this.updateProductBorders(data.products);

//             // Handle any AI alerts if present in the data
//             if (data.aiAlerts) {
//                 this.handleAIAlerts(data.aiAlerts);
//             }

//         } catch (error) {
//             // Log the error if fetching or processing the data fails
//             console.error("❌ Error fetching product stock:", error);

//             // Retry the request after a 5-second delay in case of failure
//             setTimeout(() => this.fetchStockData(), 5000);
//         }
//     }

//     updateProductBorders(products) {
//         products.forEach(product => {
//             // Find the product card in the DOM using the productId
//             const card = document.querySelector(`[data-product-info="${product.productId}"]`);

//             if (!card) {
//                 // Log a warning if no card is found for the product
//                 console.warn(`❌ Product card not found for ID: ${product.productId}`);
//                 return;
//             }

//             // Ensure stock is a number for comparison
//             const stock = Number(product.stock);

//             console.log(`📦 Updating UI for ${product.name} - Stock: ${stock}`);

//             // Update the product card's border and appearance based on the stock level
//             if (stock === 0) {
//                 card.style.border = "2px solid gray";
//                 card.style.pointerEvents = "none"; // Disable clicking
//                 card.style.opacity = "0.5"; // Gray out
//             } else if (stock >= 1 && stock <= 5) {
//                 card.style.border = "2px solid red";
//                 card.style.pointerEvents = "auto"; // Enable clicking
//                 card.style.opacity = "1"; // Full opacity
//             } else if (stock >= 6 && stock <= 10) {
//                 card.style.border = "2px solid orange";
//             } else {
//                 card.style.border = "1px solid #ddd"; // Normal state
//             }
//         });
//     }

//     handleAIAlerts(aiData) {
//         if (Array.isArray(aiData) && aiData.length > 0) {
//             aiData.forEach(alert => {
//                 console.warn("⚠️ AI Alert:", alert.type, alert.message);
//                 // Show an alert message to the user
//                 this.alertUser(alert.message);
//             });
//         }
//     }

//     alertUser(message) {
//         // Create an alert box element and append it to the body
//         const alertBox = document.createElement("div");
//         alertBox.className = "ai-alert";
//         alertBox.textContent = message;

//         document.body.appendChild(alertBox);

//         // Remove the alert box after 5 seconds
//         setTimeout(() => alertBox.remove(), 5000);
//     }

//     // Stop the polling when no longer needed
//     stopFetching() {
//         clearInterval(this.fetchStockDataInterval);
//         console.log("✅ Stopped polling for stock data.");
//     }
// }

