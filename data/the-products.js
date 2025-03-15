export let products = [];

export function productsLoadFetch() {
    // ecommerce.schoolmanagementsystem2.com
    // const promiseProduct = fetch("http://localhost/smsEcommerce/php/products-database.php").then((response) => {
    //     return response.json(); 
    // })
    const promiseProduct = fetch("http://ecommerce.schoolmanagementsystem2.com/php/products-database.php").then((response) => {
        return response.json(); 
    })
    .then((productsData) => {
        products = productsData;
        console.log(productsData)
    })
    .catch((error) => {
        console.log(`Unexpected ${error}. Please try again later.`);
    });

    return promiseProduct;
}




