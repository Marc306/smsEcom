import { products, productsLoadFetch } from "../data/the-products.js";
import { Product } from "../data/parentClass-product.js";

const GEMINI_API_KEY = "AIzaSyCDi_pimz_P7z_HsEgv36A7OsL-ggNVEvI";  // Replace with your actual Gemini API key

const CACHE = new Map();  // 🔹 Store previous searches to reduce API calls

async function fetchGeminiSearchResults(userQuery, products) {
    if (CACHE.has(userQuery)) return CACHE.get(userQuery);  // ✅ Use cached results

    const apiUrl = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-pro-002:generateContent";
    const requestBody = {
        contents: [
            {
                parts: [{
                    text: `Find the most relevant product(s) based on this query: "${userQuery}". 
                    Available products: ${products.map(p => `${p.name} (Category: ${p.category}, Price: ${p.price}, Description: ${p.description || "N/A"})`).join("; ")}. 
                    Return product names as a comma-separated list.`
                }]
            }
        ]
    };

    try {
        const response = await fetch(`${apiUrl}?key=${GEMINI_API_KEY}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(requestBody)
        });

        if (response.status === 429) {  // 🔹 Handle rate limits
            console.warn("API quota exceeded! Using local search...");
            return localSearchFallback(userQuery, products);
        }

        const result = await response.json();
        console.log("AI Response:", result);

        if (result?.candidates?.length > 0) {
            const aiResponse = result.candidates[0]?.content?.parts[0]?.text || "";
            const matchedProducts = aiResponse.split(",").map(name => name.trim().toLowerCase());
            CACHE.set(userQuery, matchedProducts);  // ✅ Cache results
            return matchedProducts;
        }

        return [];
    } catch (error) {
        console.error("Error fetching AI search results:", error);
        return localSearchFallback(userQuery, products);
    }
}

// 🔹 Fallback search when AI API fails
function localSearchFallback(userQuery, products) {
    console.log("Using local search instead of AI.");
    return products
        .filter(p => p.name.toLowerCase().includes(userQuery.toLowerCase()))
        .map(p => p.name.toLowerCase());
}


class SearchEngine extends Product {
    constructor(productList) {
        super(productList);
    }

    productCardCreator(filteredProduct) {
        return super.productCardCreator(filteredProduct);
    }

    cardFunction() {
        return super.cardFunction();
    }

    async searchingTheProduct() {
        let productFilter = "";
        const url = new URL(window.location.href);
        const search = url.searchParams.get('search');
        
        if (!search) {
            document.querySelector(".card-box").innerHTML = `<p>Please enter a search query.</p>`;
            return;
        }

        localStorage.setItem("lastSearch", search);
        
        const searchLower = search.toLowerCase();
        const aiRecommendedProducts = await fetchGeminiSearchResults(searchLower, this.productList);
        let foundProduct = false;

        this.productList.forEach((productFiltered) => {
            const productName = productFiltered.name.toLowerCase();
            
            if (aiRecommendedProducts.includes(productFiltered.name) || productName.includes(searchLower)) {
                productFilter += this.productCardCreator(productFiltered);
                foundProduct = true;
            }
        });

        if (!foundProduct) {
            productFilter = `<p>No products found for "${search}". <br><br>Did you mean: <strong>${aiRecommendedProducts.join(", ")}</strong>?</p>`;
        }

        document.querySelector(".card-box").innerHTML = productFilter;
        this.cardFunction();
    }

    searchProduct() {
        const searchInput = document.querySelector('.js-search-input');
        let suggestionBox = document.querySelector(".autocomplete-box");

        // If `.autocomplete-box` doesn't exist, create it dynamically
        if (!suggestionBox) {
            suggestionBox = document.createElement("div");
            suggestionBox.classList.add("autocomplete-box");
            document.body.appendChild(suggestionBox);
        }

        document.querySelector(".js-search-button").addEventListener('click', () => {
            const search = searchInput.value;
            const newUrl = new URL(window.location.href);
            newUrl.searchParams.set('search', search);
            history.pushState(null, '', newUrl);
            this.searchingTheProduct();
            searchInput.value = "";
            suggestionBox.innerHTML = "";  // Clear suggestions
        });

        // AI-powered autocomplete suggestions
        searchInput.addEventListener("input", async () => {
            const userInput = searchInput.value.trim();
            if (userInput.length < 2) {
                suggestionBox.innerHTML = "";
                return;
            }
            
            const suggestions = await fetchGeminiSearchResults(userInput, this.productList);
            
            suggestionBox.innerHTML = suggestions.map(s => `<div class='suggestion-item'>${s}</div>`).join("");

            document.querySelectorAll(".suggestion-item").forEach(item => {
                item.addEventListener("click", () => {
                    searchInput.value = item.textContent;
                    suggestionBox.innerHTML = "";
                });
            });

            // Position suggestion box under search input
            const rect = searchInput.getBoundingClientRect();
            suggestionBox.style.top = `${rect.bottom + window.scrollY}px`;
            suggestionBox.style.left = `${rect.left + window.scrollX}px`;
            suggestionBox.style.width = `${rect.width}px`;
            suggestionBox.style.display = "block";
        });

        window.addEventListener('load', () => {
            const lastSearch = localStorage.getItem("lastSearch");
            if (lastSearch) {
                const newUrl = new URL(window.location.href);
                newUrl.searchParams.set('search', lastSearch);
                history.pushState(null, '', newUrl);
                this.searchingTheProduct();
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    async function fetchProductSearchEngineLoad() {
        try {
            await productsLoadFetch();
        } catch (error) {
            console.log(error);
        }
        
        const newSearch = new SearchEngine(products);
        newSearch.searchProduct(); 
    }

    fetchProductSearchEngineLoad();
});





// AI-Enhanced Autocomplete Suggestions
// As the user types in the search box, AI fetches smart suggestions based on partial input.
// Suggestions appear dynamically below the search box, helping users refine their queries.