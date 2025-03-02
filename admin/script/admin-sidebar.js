document.addEventListener("DOMContentLoaded", function () {
    const sections = document.querySelectorAll(".section");
    document.querySelectorAll(".sidebar a").forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            sections.forEach(section => section.classList.add("hidden"));
            document.querySelector(link.getAttribute("href")).classList.remove("hidden");
        });
    });
    
    document.querySelectorAll(".view-order").forEach(button => {
        button.addEventListener("click", function () {
            const orderDetails = JSON.parse(this.dataset.details);
            let detailsHtml = "<h3>Order Details</h3><ul>";
            orderDetails.items.forEach(item => {
                detailsHtml += `<li>${item.product} (Size: ${item.size}, Gender: ${item.gender}) - Price: $${item.price}</li>`;
            });
            detailsHtml += "</ul>";
            document.getElementById("order-details").innerHTML = detailsHtml;
            document.querySelector(".modal").style.display = "block";
            document.querySelector(".modal-overlay").style.display = "block";
        });
    });
    
    document.querySelector(".modal-overlay").addEventListener("click", function () {
        document.querySelector(".modal").style.display = "none";
        document.querySelector(".modal-overlay").style.display = "none";
    });
});