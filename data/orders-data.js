export let orders = [];

export async function ordersFetch() {
    try {
        const response = await fetch("http://localhost/smsEcommerce/php/get-orders.php");
        const ordersItem = await response.json();

        orders = ordersItem.orders;
        return orders; 

    } catch (error) {
        console.error(`Unexpected error: ${error.message}. Please try again later.`);
        return [];
    }
}

export async function deleteOrder(productId) {
    try {
        const response = await fetch("http://localhost/smsEcommerce/php/order-cancel.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ action: "delete", productId }),
        });

        console.log("Raw response:", response);

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        console.log("Parsed JSON response:", data);  // Debugging

        return data;  // ✅ Ensure return of parsed JSON
    } catch (error) {
        console.error("Error in deleteOrder:", error);
        return { success: false, message: "Request failed" };
    }
}





// export async function deleteOrder(idButton){
//     try{
//         const response = await fetch("http://localhost/smsEcommerce/php/order-cancel.php", {
//             method: "POST",
//             headers: {
//                 "Content-Type": "application/json",
//             },
//             body: JSON.stringify({ action: "delete", productId: idButton }),
//         });

//         const data = await response.json();
//         if(data.success){
//             await ordersFetch();
//         }
//         else{
//             alert(data.message);
//         }
//     }
//     catch(error){
//         console.log(error);
//     }
// }