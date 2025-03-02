class ProductDetailList {
    productDetails;

    constructor(loadStorageKey){
        this.loadStorageKey = loadStorageKey
        this.loadStorage();
    }

    loadStorage(){
        this.productDetails =  JSON.parse(localStorage.getItem(this.loadStorageKey)) || "";
    }

    saveToStorage(){
        localStorage.setItem(this.loadStorageKey, JSON.stringify(this.productDetails));
    }

    toProductDetails(uniqueProductContainer){
        this.productDetails = uniqueProductContainer;
    
        this.saveToStorage();
    }
}

export const pdl = new ProductDetailList("productDetailStorage");



// this.productDetails.push({
//     id: uniqueProductContainer
// });

// this.productDetails = [{
//     id: "123qwe", 
//  }, {
//     id: "456rty",
// }];

// if(!this.productDetails){
//     return this.productDetails = "123qwe";
// }