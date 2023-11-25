var lastMessageId = 0;

self.onmessage = function (event) {
    setInterval(function () {
        const channelName = event.data.channelName;
        const userId = event.data.userId;
        const userName = event.data.userName;
        loadChannelMessage(channelName, userId, userName);
    }, 5000);
};

function loadChannelMessage(channelName, userId, userName) {
    if (channelName) {
        fetch(`driver.php?route=api/message&channel=${channelName}&push=1`)
                .then(response => response.json())
                .then(messages => {
                    if (messages) {
                        const latestMessage = messages[messages.length - 1];
                        if (latestMessage && (latestMessage.id > this.lastMessageId || (latestMessage.channel_name !== channelName))) {
                            if (this.lastMessageId !== 0 && latestMessage.user !== userId) {
                                console.log(`new message, sent notifikation ${channelName} lastMessageID ${lastMessageId} `);
                                sendNotification(latestMessage.user, latestMessage.message, userName);
                            }
                            this.lastMessageId = latestMessage.id;
                        } else {
                            //console.log(`no new message for channel`);
                        }
                    }
                })
                .catch(error => console.error('Error loading channel messages:', error));
    }
}

function sendNotification(user, message, userName) {
    if (Notification.permission === 'granted') {
        var notification = new Notification(userName + ' vám píše správu', {
            icon: '/media/app/images/180.png',
            body: message
        });
    }
}
