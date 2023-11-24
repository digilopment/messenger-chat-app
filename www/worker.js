var lastMessageId = 0;

self.onmessage = function (event) {
    setInterval(function () {
        const channelName = event.data.channelName;
        const userName = event.data.userName;
        const lastMessageId = event.data.lastMessageId;
        //console.log(event.data);
        const newMessage = loadChannelMessage(channelName, userName);
        if (newMessage) {
            self.postMessage(newMessage);
        }
        console.log('running');
    }, 5000);
};

function getNewMessage(message, user) {
    return {
        message: message,
        name: user,
        id: 123
    };
}

function loadChannelMessage(channelName, userName) {
    var newMessage = null;
    if (channelName) {
        fetch(`driver.php?route=api/message&channel=${channelName}`)
                .then(response => response.json())
                .then(messages => {
                    if (messages) {
                        const latestMessage = messages[messages.length - 1];
                        if (this.lastMessageId !== 0 && latestMessage && (latestMessage.id > this.lastMessageId || (latestMessage.channel_name !== channelName))) {
                            if (latestMessage.user !== userName) {
                                console.log('send message');
                                newMessage = getNewMessage(latestMessage.user, latestMessage.message);
                            }
                        } else {
                            console.log('no new message');
                        }
                        this.lastMessageId = latestMessage.id;
                    }
                })
                .catch(error => console.error('Error loading channel messages:', error));
    }
    return newMessage;
}
