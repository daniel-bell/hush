/**
 * Globals
 */
var messageList = [];
var activeTarget = -1;
var friendLis;
var current_user = null;

function addFriendListener() {
    var friend;

    friendLis = document.getElementById('friends-list').children;

    for (var i in friendLis) {
        friend = friendLis[i];

        if (friend.nodeName === "LI") {
            friend.addEventListener("click", function () {

                if (this.classList) {
                    this.classList.add('active');
                }

                // TODO: How do I remove?
                this.setAttribute('class', "active");
                var user_id = parseInt(this.getAttribute('user-id'));
                activeTarget = user_id;
                fetchLatestMessages();
            });
        }
    }

}

/**
 * Given a number, add a zero if it is less than 2 in length
 */
function padNumber(num) {
    var newNum = '0' + num;

    if (newNum.length < 3) {
        return newNum;
    } else {
        return num;
    }
}

function fetchLatestMessages() {
    $.getJSON('/messages/inbox/' + activeTarget, function (messages) {
        var chat_message = document.getElementById("chat-list");

        // Reset the chat
        chat_message.innerHTML = "";
        messageList = [];

        for (var i in messages) {
            // Only add new messages
            if (messageList.indexOf(messages[i].id) < 0) {
                var date = new Date(messages[i].sentTime.date.split(" ").join("T"));
                var dateString = '' + padNumber(date.getHours()) + ':' + padNumber(date.getMinutes()) + ':' + padNumber(date.getSeconds());
                var messageContent = messages[i].messageContent;
                var sender = messages[i].sendUsername;

                var el_li = document.createElement("li");
                el_li.className = "message-ul";
                el_li.innerHTML = '<span class="message-time"><time date-time="' + date.toString() + '">' + dateString + '</time></span>' +
                    '<span class="sender">' + sender + ':</span>' +
                    '<span class="message-content">' + messageContent + '</span>';

                // Append the converted object
                chat_message.appendChild(el_li);

                messageList.push(messages[i].id);
            }
        }
    });
}

/**
 * Get the friend list for the current user
 */
function fetchFriendsList() {
    $.getJSON('/user_relationship', function (friends) {
        // Build up the new <li> elements
        var ul = document.getElementById('friends-list');

        for (var i in friends) {
            users = friends[i].users;

            for (var u in users) {
                user = users[u];

                if (user.id != current_user) {
                    el = document.createElement("li");
                    el.setAttribute('user-id', user.id);
                    // TODO: Add a wee checkmark for accepting?
                    el.innerHTML = user.username;

                    ul.appendChild(el);
                }
            }
        }

        // Load the friend listener
        addFriendListener();
    });
}

/**
 * Send the results of the send message form to the backend
 */
function sendMessage() {
    if (activeTarget > 0) {
        console.log("Send Message");

        var date, xmlHttp, params;

        var messageText = $("#message-text").val();
        console.log(messageText);

        // TODO: Test override

        // Check the box isn't just empty, or empty strings
        // TODO: Add some HTML5 validation
        if (messageText != "") {
            xmlHttp = new XMLHttpRequest();

            xmlHttp.open('POST', '/messages/send', true);
            xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

            // Parameters
            date = new Date();

            // Build up the parameters needed for the new Entity
            params = {
                "messageKey": "FIXME",
                "sentTime": {
                    "date": {
                        "month": date.getMonth() + 1,
                        "day": date.getDate(),
                        "year": date.getFullYear()
                    },
                    "time": {
                        "hour": date.getHours(),
                        "minute": date.getMinutes()
                    }
                },
                "messageContent": messageText,
                "targetUser": activeTarget
            }

            params = "json_str=" + JSON.stringify(params);
            xmlHttp.send(params);

            // Update the message list
            fetchLatestMessages();

            $("#message-text").val("");
        }
    }
}

/*
 * Setup the global current_user from the users endpoint
 */
function getUserId() {
    xmlHttp = new XMLHttpRequest();

    xmlHttp.onreadystatechange = function () {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            current_user = xmlHttp.responseText;
        }
    }

    xmlHttp.open('GET', "/users/me", true);
    xmlHttp.send();
}

function addFriend() {
    var params;
    var friend_name = $("#friend-name").val();

    if (friend_name != "") {
        params = {
            "target_username": friend_name
        }

        params = "json_str=" + JSON.stringify(params);

        var request = $.ajax({
            type: 'POST',
            url: '/user_relationship/new',
            data: params
        });

        request.fail(function (jqXHR, textStatus) {
            console.log(jqXHR.responseText);
        });


        $("#friend-name").val("");
    }
}

/**
 * Globally setup some listeners
 */
getUserId();

// Check messages every second
var intervalId = window.setInterval(fetchLatestMessages, 3000);