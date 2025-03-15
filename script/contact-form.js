document.addEventListener('DOMContentLoaded', () => {
    const form = {
        nameInput: document.querySelector('.name-input'),
        emailInput: document.querySelector('.email-input'),
        subjectInput: document.querySelector('.subject-input'),
        messageInput: document.querySelector('.message-input'),
        sendButton: document.querySelector('.send-btn')
    };

    // Add loading state to button
    function setButtonLoading(isLoading) {
        form.sendButton.disabled = isLoading;
        form.sendButton.textContent = isLoading ? 'SENDING...' : 'SEND';
    }

    // Clear form inputs
    function clearForm() {
        form.nameInput.value = '';
        form.emailInput.value = '';
        form.subjectInput.value = '';
        form.messageInput.value = '';
    }

    // Show alert message
    function showAlert(message, isSuccess) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${isSuccess ? 'alert-success' : 'alert-danger'} mt-3`;
        alertDiv.textContent = message;
        
        const btnSendDiv = document.querySelector('.btn-send-div');
        btnSendDiv.insertAdjacentElement('afterend', alertDiv);

        // Remove alert after 3 seconds
        setTimeout(() => alertDiv.remove(), 3000);
    }

    // Handle form submission
    form.sendButton.addEventListener('click', async () => {
        try {
            // Get form values
            const formData = {
                name: form.nameInput.value.trim(),
                email: form.emailInput.value.trim(),
                subject: form.subjectInput.value.trim(),
                message: form.messageInput.value.trim()
            };

            // Basic validation
            if (!formData.name || !formData.email || !formData.subject || !formData.message) {
                showAlert('Please fill in all fields', false);
                return;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(formData.email)) {
                showAlert('Please enter a valid email address', false);
                return;
            }

            // Set loading state
            setButtonLoading(true);

            //Send data to server
            // const response = await fetch('http://localhost/smsEcommerce/php/submit-message.php', {
            //     method: 'POST',
            //     headers: {
            //         'Content-Type': 'application/json'
            //     },
            //     body: JSON.stringify(formData)
            // });
            const response = await fetch('https://ecommerce.schoolmanagementsystem2.com/php/submit-message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.success) {
                showAlert(result.message, true);
                clearForm();
            } else {
                throw new Error(result.message);
            }

        } catch (error) {
            showAlert(error.message || 'Failed to send message. Please try again.', false);
        } finally {
            setButtonLoading(false);
        }
    });
});
