// Function to update purchase notifications
async function updatePurchaseNotifications() {
    try {
        const response = await fetch('https://ecommerce.schoolmanagementsystem2.com/php/get-orders.php');
        const data = await response.json();
        
        // Get all purchase notification elements
        const purchaseNotifs = document.querySelectorAll('.purchase-notif');
        
        // Check if there are any orders with 'To Pay' status
        const hasUnpaidOrders = data.success && data.orders && data.orders.some(order => order.status === 'To Pay');
        
        // Update all notification dots
        purchaseNotifs.forEach(notif => {
            if (hasUnpaidOrders) {
                notif.classList.add('active');
            } else {
                notif.classList.remove('active');
            }
        });
        
    } catch (error) {
        console.error('Error updating purchase notifications:', error);
    }
}

// Update notifications initially and every 30 seconds
updatePurchaseNotifications();
setInterval(updatePurchaseNotifications, 30000);
