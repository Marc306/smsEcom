// Function to update cart notification
async function updateCartNotification() {
    try {
        // const response = await fetch('http://localhost/smsEcommerce/php/cartData.php?action=getCart');
        const response = await fetch('https://ecommerce.schoolmanagementsystem2.com/php/cartData.php?action=getCart');
        const data = await response.json();
        
        const cartCount = document.querySelector('.cart-count');
        const notifBurger = document.querySelector('.notif-burger');
        const notifDropdown = document.querySelector('.notif');
        
        if (data.success && data.cartItems && data.cartItems.length > 0) {
            // Update cart count
            cartCount.textContent = data.cartItems.length;
            cartCount.style.display = 'inline-flex';
            
            // Show notifications
            notifBurger.style.display = 'block';
            notifDropdown.style.display = 'block';
        } else {
            // Hide notifications if cart is empty
            cartCount.style.display = 'none';
            notifBurger.style.display = 'none';
            notifDropdown.style.display = 'none';
        }
    } catch (error) {
        console.error('Error updating cart notification:', error);
    }
}

// Function to update orders notification
async function updateOrdersNotification() {
    try {
        const response = await fetch('http://localhost/smsEcommerce/php/get-orders.php');
        const data = await response.json();
        
        const notifProfile = document.querySelector('.notif-profile');
        
        if (data.success && data.orders && data.orders.some(order => order.status === 'To Pay')) {
            notifProfile.style.display = 'inline-flex';
        } else {
            notifProfile.style.display = 'none';
        }
    } catch (error) {
        console.error('Error updating orders notification:', error);
    }
}

// Update notifications initially and every 30 seconds
updateCartNotification();
updateOrdersNotification();
setInterval(() => {
    updateCartNotification();
    updateOrdersNotification();
}, 30000);
