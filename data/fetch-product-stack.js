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



