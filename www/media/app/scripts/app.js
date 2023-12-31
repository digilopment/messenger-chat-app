class App {

    constructor() {
        this.messageFragment = $("#messageFragment");
        this.chat = $("#chat");
        this.signFragment = $(".signFragment");
        this.userList = $("#userListSection");
        this.chatMessages = $("#chat-messages");
        this.headerFragment = $(".headerFragment");
    }

    init() {
        $('.reloadApp').addClass("rotate");
        this.userName = this.getCookie("chat_app_name");
        this.channelName = this.getCookie("chat_app_channel");
        this.lastPartnerId = this.getCookie("chat_app_partner_id");
        this.userData = this.getUserData();
        this.partnerData = this.lastPartnerId ? this.getPartnerData(this.lastPartnerId) : '';

        if (this.partnerData) {
            $('.userImage.partnerImage').removeClass(function (index, className) {
                return (className.match(/\bfa-circle-\S+/g) || []).join(' ');
            });
            $('.userImage.partnerImage').addClass('fa-circle-' + getFirstAlphabet(this.partnerData.name));
            $('.userImage.partnerImage').css("color", stringToHexColor(this.partnerData.name));
        } else {
            $('.userImage.partnerImage').removeClass(function (index, className) {
                return (className.match(/\bfa-circle-\S+/g) || []).join(' ');
            });
            $('.userImage.partnerImage').addClass('fa-circle-user');
        }

        if (this.userData) {
            $('.userImage.meImage').removeClass(function (index, className) {
                return (className.match(/\bfa-circle-\S+/g) || []).join(' ');
            });
            $('.userImage.meImage').addClass('fa-circle-' + getFirstAlphabet(this.userData.name));
            $('.userImage.meImage').css("color", stringToHexColor(this.userData.name));
        } else {
            $('.userImage.meImage').removeClass(function (index, className) {
                return (className.match(/\bfa-circle-\S+/g) || []).join(' ');
            });
            $('.userImage.meImage').addClass('fa-circle-user');
        }

        this.lastMessageId = 0;
        if (this.userName && this.userData.id && this.channelName) {
            this.signFragment.hide();
            this.getUsers();
            this.headerFragment.show();
            this.userList.show();
            this.chat.show();
            this.messageFragment.show();
            this.refreshChat();
            scrollDown();
            console.log('selected channel');
        } else if (this.userName && this.userData.id) {
            this.signFragment.hide();
            this.getUsers();
            this.userList.show();
            this.headerFragment.show();
            this.messageFragment.hide();
            this.chat.show();
            console.log('logged');
        } else {
            this.chatMessages.hide();
            this.messageFragment.hide();
            this.headerFragment.hide();
            this.signFragment.fadeIn();
        }
    }

    getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ')
                c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0)
                return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    sendMessage() {
        const message = $("#message").val();
        if (message.length > 1) {
            $.post("driver.php?route=api/message", {channel: this.channelName, user: this.userData.id, message: message}, function (data) {
                $("#message").val('');
            });
        }
    }

    refreshChat() {
        if (this.channelName) {
            this.chatMessages.fadeIn();
            let requestUrl;
            if (this.lastMessageId === 0) {
                requestUrl = "driver.php?route=api/message";

            } else {
                requestUrl = "driver.php?route=api/message&fromId=" + this.lastMessageId;
            }
            $.get(requestUrl, {channel: this.channelName}, (data) => {
            const messages = JSON.parse(data);
                if (messages) {
                    const latestMessage = messages[messages.length - 1];
                    if (latestMessage && (latestMessage.id > this.lastMessageId || (latestMessage.channel_name !== this.channelName))) {
                        if (this.lastMessageId === 0) {
                            this.chatMessages.empty();
                        }
                        messages.forEach((msg) => {
                            const messageWithLinks = parseLinksInMessage(msg.message);
                            var userClass = msg.user == this.userData.id ? 'user-me' : 'user-partner';
                            var messageBGColor = msg.user == this.userData.id ? '#1877f2' : '#e4e6eb';
                            var messagePosition = msg.user == this.userData.id ? 'justify-content-end' : 'justify-content';
                            var userName = msg.user == this.userData.id ? this.userData.name : this.partnerData.name;

                            this.chatMessages.append(`<div class="${userClass}">
                                <div class="d-flex flex-row ${messagePosition} mb-4">
                                    <i class="fa-duotone fa-circle-${getFirstAlphabet(userName)} userImage" style="color: ${stringToHexColor(userName)}"></i>
                                <div>
                                <div class="p-3 ms-3" style="border-radius: 15px; background-color: ${messageBGColor};">
                                  <p class="small mb-0">${messageWithLinks}</p>
                                  <small>${formatTime(msg.created_at)}</small>
                                </div>
                            </div>`);
                        });
                        if (latestMessage.user !== this.userName) {
                            //sendNotification(latestMessage.user, latestMessage.message);
                        }
                        this.lastMessageId = latestMessage.id;
                        scrollDown();
                    }
                }
            });
        }
        $('.reloadApp').removeClass("rotate");
    }

    getUserData() {
        var userData;
        $.ajax({
            url: "driver.php?route=api/me",
            method: "GET",
            dataType: "json",
            async: false
        }).done((response) => {
            userData = response;
        }).fail((jqXHR, textStatus, errorThrown) => {
            console.error("Error:", textStatus, errorThrown);
        });
        return userData;
    }

    getPartnerData(id) {
        var userData;
        $.ajax({
            url: "driver.php?route=api/me&id=" + id,
            method: "GET",
            dataType: "json",
            async: false
        }).done((response) => {
            userData = response;
        }).fail((jqXHR, textStatus, errorThrown) => {
            console.error("Error:", textStatus, errorThrown);
        });
        return userData;
    }

    getUsers() {
        $.get("driver.php?route=api/users", (userData) => {
            var $userList = $("#userList");
            var $select = $userList.find("select");
            $select.empty();
            $select.append($('<option>', {
                value: '',
                text: 'Select user'
            }));
            $.each(userData, (k, item) => {
                var $option = $('<option>', {
                    value: item.id,
                    text: item.name
                });

                if (this.lastPartnerId == item.id) {
                    $option.prop('selected', true);
                }
                $select.append($option);
            });
            $userList.show();
        });
    }

    getUsersIds() {
        var userIds = [];
        $.ajax({
            url: "driver.php?route=api/users",
            method: "GET",
            dataType: "json",
            async: false,
            success: function (userData) {
                $.each(userData, function (k, item) {
                    userIds[item.id] = item.name;
                });
            },
            error: function (xhr, status, error) {
                console.error("Error fetching user ids:", error);
            }
        });

        return userIds;
    }

    setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + value + expires + "; path=/";
    }

    getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ')
                c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0)
                return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    register() {
        const name = $("#sectionRegistration .name").val();
        const email = $("#sectionRegistration .email").val();
        const password = $("#sectionRegistration .password").val();
        if (name && email && password) {
            $.post("driver.php?route=api/register", {name: name, email: email, password: password}, (data) => {
                if (data.success) {
                    console.log(data);
                    this.setCookie("chat_app_name", data.data.id, 30);
                    this.init();
                    console.log('registred');
                } else {
                    alert(data.message);
                }
            });
        } else {
            alert('Zle vyplnené údaje');
        }
    }

    login() {
        const email = $("#sectionLogin .email").val();
        const password = $("#sectionLogin .password").val();
        if (email && password) {
            $.post("driver.php?route=api/auth", {email: email, password: password}, (data) => {
                if (data.success) {
                    this.setCookie("chat_app_name", data.data.id, 30);
                    this.init();
                } else {
                    alert(data.message);
                }
            });
        } else {
            alert('Zle vyplnené údaje');
        }
    }

    fbLogin(data) {
        const email = data.id;
        const name = data.name;
        console.log('fb login');
        console.log(data);
        if (name && email) {
            $.post("driver.php?route=api/auth", {email: email, name: name, password: '', oauth: 'facebook'}, (data) => {
                if (data.success) {
                    this.setCookie("chat_app_name", data.data.id, 30);
                    this.init();
                } else {
                    alert(data.message);
                }
            });
        }
    }

    signOut() {
        this.setCookie("chat_app_name", '', 30);
        this.setCookie("chat_app_partner_id", '', 30);
        this.setCookie("chat_app_channel", '', 30);
        this.init();
    }

}


$(document).ready(function () {

    window.fbAsyncInit = function () {
        FB.init({
            appId: '357055923378845',
            cookie: true,
            xfbml: true,
            version: 'v18.0'
        });
        FB.getLoginStatus(function (response) {
            statusChangeCallback(response);
        });
    };

    app = new App();
    app.init();

    let chatInterval;
    function readMessages(app) {
        if (app.userName && app.userData.id && app.channelName) {
            if (chatInterval) {
                clearInterval(chatInterval);
            }

            chatInterval = setInterval(function () {
                app.refreshChat();
            }, 1000);
        }
    }

    readMessages(app);

    $('#userListSection select').on('change', function () {

        app.chatMessages.empty();
        var partnerId = $(this).val();
        if (partnerId) {
            app.setCookie("chat_app_channel", createChannelId(app.userData.id, partnerId), 30);
            app.setCookie("chat_app_partner_id", partnerId, 30);
            app.init();
            readMessages(app);
        } else {
            app.setCookie("chat_app_channel", '', 30);
            app.setCookie("chat_app_partner_id", '', 30);
            app.init();
        }
    });


    $("#register").click(function () {
        app.register();
    });

    $("#sectionLogin #login").click(function () {
        app.login();
    });

    $(".signOut").click(function () {
        app.signOut();
        if (FB) {
            FB.logout();
        }
    });


    $("#send").click(function () {
        app.sendMessage();
    });
    $("#message").on("keydown", function (event) {
        if (event.key === "Enter") {
            app.sendMessage();
        }
    });
    $(".scrollDown").click(function () {
        scrollDown();
    });



    $('.shareButton').on("click", async function () {
        try {
            const shareData = {
                title: 'Došla ti nová správa',
                text: 'Prečítaj si najnovšiu správu v chat aplikacii od používateľa ' + app.userData.name,
                url: window.location.href
            };
            await navigator.share(shareData);
        } catch (err) {
            //alert('Share API is not supported OK.');
        }

    });

    $('.reloadApp').on('click', function () {
        $(this).addClass("rotate");
        app = new App();
        app.init();
        readMessages(app);

    });
    
    $('.requestNotification').on('click', function () {
        if ('Notification' in window) {
            Notification.requestPermission().then((permissionResult) => {
                if (permissionResult === 'granted') {
                    //alert('Notifikácie sú povolené');
                    sendNotification('Quesenger', 'Notifikácie sú povolené');
                } else if (permissionResult === 'denied') {
                    alert('Notifikácie sú zamietnuté. Ak ich chcete povoliť, prejdite do nastavení vášho prehliadača.');
                } else {
                    alert('Váš prehliadač nepodporuje notifikácie.');
                }
            });
        } else {
            alert('Váš prehliadač nepodporuje notifikácie.');
        }
    });

    //WORKERS REGISTRATOR
    const worker = new Worker('worker.js');
    setWorker(worker, app);
});


function setWorker(worker, app) {
    if (window.Worker) {
        $.each(app.getUsersIds(), (partnerId, partnerName) => {
            const appData = {
                channelName: createChannelId(app.userData.id, partnerId),
                userId: app.userName,
                userName: partnerName
            };
            worker.postMessage(appData);
        });
    } else {
        console.error('Web workers are not supported in this browser');
    }

}

function createChannelId(userId, partnerId) {
    var sortedIds = [userId, partnerId].sort(function (a, b) {
        return a - b;
    });
    return sortedIds.join('-');
}
function statusChangeCallback(response) {
    if (response.status === 'connected') {
        FB.api('/me', function (response) {
            console.log(response);
            app.fbLogin(response);
        });
    } else {
        document.getElementById('status').innerHTML = 'Please log ' +
                'into this webpage.';
    }
}
function checkLoginState() {
    FB.getLoginStatus(function (response) {
        statusChangeCallback(response);
    });
}
function parseLinksInMessage(message) {
    // Regulárny výraz na hľadanie odkazov v texte
    var linkRegex = /(?:https?|ftp):\/\/[^\s/$.?#].[^\s]*/g;

    // Nahradenie všetkých odkazov v texte
    var messageWithLinks = message.replace(linkRegex, function (url) {
        // Získať len textovú časť URL bez "www" a s veľkým počiatočným písmenom
        var linkText = getLinkText(url);
        return '<a href="' + url + '" target="_blank">' + linkText + '</a>';
    });

    return messageWithLinks;
}

function getLinkText(url) {
    // Získať len textovú časť URL bez "www" a s veľkým počiatočným písmenom
    var domain = url.split('/')[2];
    domain = domain.replace('www.', ''); // Odstrániť "www" z URL
    domain = domain.charAt(0).toUpperCase() + domain.slice(1); // Urobiť prvé písmeno veľkým
    return domain;
}

function nameUrl(inputString) {
    // Remove special characters and convert to lowercase
    let cleanedString = inputString.toLowerCase().replace(/[^a-z0-9]+/g, '-');

    // Remove leading and trailing hyphens
    cleanedString = cleanedString.replace(/^-+|-+$/g, '');

    return cleanedString;
}

function sendNotification(user, message) {
    if (Notification.permission === 'granted') {
        var notification = new Notification('Nová správa od ' + user, {
            icon: '/media/app/images/180.png', // Môžete pridať vlastný obrázok
            body: message
        });
    }
}

function stringToHexColor(str) {
    // Hash the string to get a number
    if (!str) {
        return '#000000';
    }
    let hash = 0;
    for (let i = 0; i < str.length; i++) {
        hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }

    // Convert the number to a hexadecimal color
    const color = (hash & 0x00FFFFFF).toString(16).toUpperCase();

    // Ensure the color is 6 characters long by adding leading zeros if needed
    const paddedColor = "000000".substring(0, 6 - color.length) + color;

    return `#${paddedColor}`;
}

function formatTime(inputDateString) {
    if (!inputDateString) {
        return '';
    }
    const [datePart, timePart] = inputDateString.split(' ');
    const [year, month, day] = datePart.split('-');
    const [hours, minutes, seconds] = timePart.split(':');

    const dateTime = new Date(year, month - 1, day, hours, minutes, seconds);

    if (!isNaN(dateTime.getTime())) {
        const timeString = dateTime.toLocaleTimeString('sk-SK', {
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });
        return timeString;
    } else {
        return '';
    }
}

function getFirstAlphabet(str) {
    if (!str) {
        return '';
    }
    const match = str.match(/[A-Za-z]/);
    if (match) {
        return match[0].toLowerCase();
    } else {
        return '';
    }
}

function getPartnerId(channelName, userId) {
    const parts = channelName.split(userId);
    const lastPart = parts[parts.length - 1];
    const digits = lastPart.match(/\d+/);

    if (digits) {
        return digits[0];
    } else {
        return null;
    }
}

function scrollDown() {
    window.scrollBy(0, document.body.scrollHeight);
}