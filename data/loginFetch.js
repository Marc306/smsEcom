const formLogin = document.querySelector("#form-page");
formLogin.addEventListener("submit", async (event) => {
    event.preventDefault(); 

    const studentId = document.querySelector("#student_id");
    const password = document.querySelector("#password");
    const errorDiv = document.querySelector(".error-div");

    await fetchDataLogin(studentId.value.trim(), password.value.trim(), errorDiv);

    studentId.value = "";
    password.value = "";
});

async function fetchDataLogin(studentId, password, errorDiv) {
    try {
        // const response = await fetch("http://localhost/smsEcommerce/php/loginData.php", {
        //     method: "POST",
        //     headers: {
        //         "Content-Type": "application/x-www-form-urlencoded",
        //     },
        //     body: `student_id=${encodeURIComponent(studentId)}&password=${encodeURIComponent(password)}`
        // });
        const response = await fetch("https://ecommerce.schoolmanagementsystem2.com/php/loginData.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `student_id=${encodeURIComponent(studentId)}&password=${encodeURIComponent(password)}`
        });

        const data = await response.json();

        if (data.success) {
            window.location.href = data.redirect;
        } else {
            errorDiv.innerHTML = `<div class="error-message" style="display: block">${data.error}</div>`; 
        }
    }
    catch(error) {
        errorDiv.innerHTML = `<div class="error-message" style="display: block">An error occurred. Please try again.</div>`;
    }
}


