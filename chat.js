document.addEventListener('DOMContentLoaded', function () {
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const chatMessages = document.getElementById('chat-messages');

    // Handle message sending
    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const message = messageInput.value.trim();
        if (message) {
            fetch('chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    messageInput.value = '';
                    loadMessages();
                } else {
                    alert('Message could not be sent');
                }
            });
        }
    });

    // Load messages from the server
    function loadMessages() {
        fetch('chat.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    chatMessages.innerHTML = '';
                    data.messages.forEach(msg => {
                        const messageElement = document.createElement('div');
                        messageElement.textContent = `${msg.username}: ${msg.message}`;
                        chatMessages.appendChild(messageElement);
                    });
                    chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll to bottom
                } else {
                    chatMessages.innerHTML = '<p>Error loading messages</p>';
                }
            });
    }

    // Initial load of messages
    loadMessages();

    // Poll for new messages every 5 seconds
    setInterval(loadMessages, 5000);
});
