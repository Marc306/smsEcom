export function loadReceipt() { 
    fetch("https://ecommerce.schoolmanagementsystem2.com/php/get-receipt.php")
    .then(response => response.json())
    .then(data => {
        console.log("Full Response from PHP:", data);

        if (data.receipts && data.receipts.length > 0) {
            data.receipts.forEach(receipt => {
                console.log("Receipt Found:", receipt);
                const orderId = receipt.order_id;

                // ✅ Ensure receipt_url is NOT null before disabling input
                if (receipt.receipt_url !== null && receipt.receipt_url !== "") {
                    document.querySelectorAll('.uploadReceiptForm').forEach(form => {
                        const fileInput = form.querySelector('input[name="order_id"]');
                        
                        if (fileInput && fileInput.value === orderId.toString()) {
                            console.log(`Disabling input for order ID: ${orderId}`);
                            fileInput.disabled = true;
                            
                            const label = form.querySelector(".custom-file-upload1");
                            console.log("Checking label for order ID:", orderId, label);
                            
                            if (label) {
                                label.textContent = "Receipt Sent";
                                label.style.background = "#ccc";
                                label.style.cursor = "not-allowed";
                            } else {
                                console.warn(`Label not found for order ID: ${orderId}`);
                            }
                        }
                    });
                } else {
                    console.warn(`Skipping order ID: ${orderId}, because receipt_url is NULL`);
                }
            });
        } else {
            console.warn("No receipts found for this user.");
        }
    })
    .catch(error => console.error("Error fetching receipt:", error));
}






