document.getElementById("copyNumber").addEventListener("click", function () {
    const gcashNumber = "09XXXXXXXXX";
    navigator.clipboard.writeText(gcashNumber).then(() => {
        alert("GCash Number copied to clipboard!");
    }).catch(err => {
        console.error("Failed to copy: ", err);
    });
});
