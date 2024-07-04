// Function to show the notifications modal
function showModal() {
    document.getElementById('modal').style.display = 'block';
}

// Function to hide the notifications modal
function cancel() {
    document.getElementById('modal').style.display = 'none';
}

// Function to open a specific notification
function openNotification(element) {
    const email = element.getAttribute('data-email');
    const message = element.getAttribute('data-message');
    document.getElementById('sender-email').innerText = `Sender: ${email}`;
    document.getElementById('notification-message').innerText = `Message: ${message}`;
    document.getElementById('all-notifications').style.display = 'none';
    document.getElementById('opened-notification').style.display = 'block';
}
