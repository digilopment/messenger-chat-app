// Define the lastMessageId variable outside of the event listeners
let lastMessageId = 0;

self.addEventListener('message', event => {
  // Handle messages from the main application
  if (event.data && event.data.type === 'startMessageLoader') {
    startMessageLoader(event.data);
  }
});

function startMessageLoader(data) {
  setInterval(function () {
    const channelName = data.channelName;
    const userId = data.userId;
    const userName = data.userName;
    loadChannelMessage(channelName, userId, userName);
  }, 5000);
}

function loadChannelMessage(channelName, userId, userName) {
  if (channelName) {
    fetch(`driver.php?route=api/message&channel=${channelName}&push=1`)
      .then(response => response.json())
      .then(messages => {
        if (messages) {
          const latestMessage = messages[messages.length - 1];
          if (latestMessage && (latestMessage.id > lastMessageId || (latestMessage.channel_name !== channelName))) {
            if (lastMessageId !== 0 && latestMessage.user !== userId) {
              // Post a message to the main application to trigger a notification
              self.clients.matchAll().then(clients => {
                clients.forEach(client => {
                  client.postMessage({
                    type: 'newMessage',
                    user: latestMessage.user,
                    message: latestMessage.message,
                    userName: userName,
                  });
                });
              });
            }
            lastMessageId = latestMessage.id;
          } else {
            // console.log(`no new message for channel`);
          }
        }
      })
      .catch(error => console.error('Error loading channel messages:', error));
  }
}

// Note: You can't use the Notification API directly in the Service Worker.
// Instead, the main application will handle the notification based on messages sent from the Service Worker.
