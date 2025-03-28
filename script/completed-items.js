async function fetchCompletedOrders() {
    try {
        // const response = await fetch('http://localhost/smsEcommerce/php/get-completed-orders.php');
        const response = await fetch('https://ecommerce.schoolmanagementsystem2.com/php/get-completed-orders.php');
        const data = await response.json();
        
        const mainContent = document.querySelector('.main-content');
        
        if (!data.success || !data.orders || data.orders.length === 0) {
            mainContent.innerHTML = `
                <div class="completed-items-container">
                    <div class="notFound-image-div">
                        <img src="image/icon/no-results-img.png" alt="">  
                        <div class="text-notFound">
                            No orders yet
                        </div>
                    </div>
                </div>
            `;
            return;
        }

        // Create container for completed orders
        const ordersHTML = data.orders.map(order => `
            <div class="completed-items">
                <div class="completed-item-header">
                    <div class="order-info">
                        <span class="order-id">Order #${order.order_id}</span>
                        <span class="completed-date">${new Date(order.completed_at).toLocaleDateString()}</span>
                    </div>
                    <div class="order-status">Completed</div>
                </div>
                <div class="completed-item-content">
                    <img src="${order.product_image}" alt="${order.product_name}" class="product-image">
                    <div class="product-details">
                        <h3 class="product-name">${order.product_name}</h3>
                        <div class="order-details">
                            <span class="quantity">Quantity: ${order.quantity}</span>
                            <span class="total-price">₱${parseFloat(order.total_price).toLocaleString()}</span>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        mainContent.innerHTML = `
            <div class="completed-items-container">
                ${ordersHTML}
            </div>
        `;
    } catch (error) {
        console.error('Error fetching completed orders:', error);
        document.querySelector('.main-content').innerHTML = `
            <div class="completed-items-container">
                <div class="error-message">
                    Failed to load completed orders. Please try again later.
                </div>
            </div>
        `;
    }
}

// Call the function when the page loads
document.addEventListener('DOMContentLoaded', fetchCompletedOrders);

