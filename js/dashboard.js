/**
 * Dashboard JavaScript
 * Handles user list, chat functionality, and call initiation
 */

let users = [];
let pollInterval = null;
let messagesInterval = null;

// Load users on page load
document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
    updateUserStatus('online');
    
    // Poll for users every 3 seconds
    pollInterval = setInterval(loadUsers, 3000);
    
    // Update status before leaving page
    window.addEventListener('beforeunload', function() {
        updateUserStatus('offline');
    });
});

/**
 * Load all users from database
 */
async function loadUsers() {
    try {
        const response = await fetch('api/get_users.php');
        const data = await response.json();
        
        if (data.success) {
            users = data.users;
            displayUsers();
        }
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

/**
 * Display users in the sidebar
 */
function displayUsers() {
    const usersList = document.getElementById('usersList');
    
    if (users.length === 0) {
        usersList.innerHTML = '<div class="loading">No other users found</div>';
        return;
    }
    
    let html = '';
    users.forEach(user => {
        const statusClass = `status-${user.status}`;
        const statusText = user.status === 'online' ? 'Online' : 
                          user.status === 'on_call' ? 'On a call' : 'Offline';
        
        html += `
            <div class="user-item" data-user-id="${user.id}">
                <img src="uploads/${user.profile_picture}" alt="${user.username}">
                <div class="user-item-info">
                    <h4>${user.username}</h4>
                    <p>
                        <span class="status-indicator ${statusClass}"></span>
                        ${statusText}
                    </p>
                </div>
                <div class="user-actions">
                    <button class="action-btn video" onclick="initiateCall(${user.id}, '${user.username}', 'video')" 
                            ${user.status === 'on_call' ? 'disabled' : ''} title="Video Call">
                        📹
                    </button>
                    <button class="action-btn audio" onclick="initiateCall(${user.id}, '${user.username}', 'audio')" 
                            ${user.status === 'on_call' ? 'disabled' : ''} title="Audio Call">
                        📞
                    </button>
                    <button class="action-btn chat" onclick="openChat(${user.id}, '${user.username}', '${user.profile_picture}')" 
                            title="Chat">
                        💬
                    </button>
                </div>
            </div>
        `;
    });
    
    usersList.innerHTML = html;
}

/**
 * Update current user's status
 */
async function updateUserStatus(status) {
    try {
        await fetch('api/update_status.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({status})
        });
    } catch (error) {
        console.error('Error updating status:', error);
    }
}

/**
 * Initiate a call
 */
function initiateCall(userId, username, callType) {
    // Check if user is busy
    const user = users.find(u => u.id === userId);
    if (user && user.status === 'on_call') {
        showBusyModal();
        return;
    }
    
    // Show calling modal
    showCallingModal(username, user.profile_picture);
    
    // Update own status
    updateUserStatus('on_call');
    
    // Redirect to call page as initiator
    setTimeout(() => {
        window.location.href = `call.php?user_id=${userId}&type=${callType}&initiator=true`;
    }, 1000);
}

/**
 * Show calling modal
 */
function showCallingModal(username, profilePic) {
    const modal = document.getElementById('callingModal');
    document.getElementById('callingModalName').textContent = username;
    document.getElementById('callingModalAvatar').src = 'uploads/' + profilePic;
    modal.classList.add('active');
}

/**
 * Cancel call
 */
function cancelCall() {
    document.getElementById('callingModal').classList.remove('active');
    updateUserStatus('online');
}

/**
 * Show busy modal
 */
function showBusyModal() {
    document.getElementById('busyModal').classList.add('active');
}

/**
 * Close busy modal
 */
function closeBusyModal() {
    document.getElementById('busyModal').classList.remove('active');
}

/**
 * Open chat with a user
 */
function openChat(userId, username, profilePic) {
    selectedUserId = userId;
    selectedUsername = username;
    
    // Update chat header
    document.getElementById('chatHeader').innerHTML = `
        <img src="uploads/${profilePic}" alt="${username}" class="profile-pic-small">
        <span>${username}</span>
    `;
    
    // Show chat input
    document.getElementById('chatInput').style.display = 'flex';
    
    // Load messages
    loadMessages();
    
    // Start polling for new messages
    if (messagesInterval) {
        clearInterval(messagesInterval);
    }
    messagesInterval = setInterval(loadMessages, 2000);
    
    // Highlight selected user
    document.querySelectorAll('.user-item').forEach(item => {
        item.classList.remove('active');
    });
    document.querySelector(`[data-user-id="${userId}"]`).classList.add('active');
}

/**
 * Load messages with selected user
 */
async function loadMessages() {
    if (!selectedUserId) return;
    
    try {
        const response = await fetch(`api/messages.php?user_id=${selectedUserId}`);
        const data = await response.json();
        
        if (data.success) {
            displayMessages(data.messages);
        }
    } catch (error) {
        console.error('Error loading messages:', error);
    }
}

/**
 * Display messages in chat
 */
function displayMessages(messages) {
    const chatMessages = document.getElementById('chatMessages');
    
    if (messages.length === 0) {
        chatMessages.innerHTML = '<div class="loading">No messages yet. Start chatting!</div>';
        return;
    }
    
    let html = '';
    messages.forEach(msg => {
        const isSent = msg.from_user_id === currentUserId;
        const messageClass = isSent ? 'sent' : '';
        
        html += `
            <div class="message ${messageClass}">
                <img src="uploads/${msg.profile_picture}" alt="${msg.username}">
                <div class="message-content">
                    <p>${msg.message}</p>
                    <span>${formatTime(msg.created_at)}</span>
                </div>
            </div>
        `;
    });
    
    chatMessages.innerHTML = html;
    
    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

/**
 * Send a message
 */
async function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message || !selectedUserId) return;
    
    try {
        const response = await fetch('api/messages.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                to_user_id: selectedUserId,
                message: message
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            input.value = '';
            loadMessages();
        }
    } catch (error) {
        console.error('Error sending message:', error);
    }
}

/**
 * Format timestamp
 */
function formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;
    
    // If less than 1 minute
    if (diff < 60000) {
        return 'Just now';
    }
    
    // If today
    if (date.toDateString() === now.toDateString()) {
        return date.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'});
    }
    
    // Otherwise
    return date.toLocaleDateString('en-US', {month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'});
}

// Allow Enter key to send message
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('messageInput');
    if (input) {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }
});