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
            friend.addEventListener("click", function() {

                if (this.classList) {
                    this.classList.add('active');
                }

                // TODO: How do I remove?
                this.setAttribute('class', "active");
                user_id = parseInt(this.getAttribute('user-id'));
                activeTarget = user_id;
            });
        }
    }

}

function fetchLatestMessages() {
    var xmlHttp = new XMLHttpRequest();

    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            var response = xmlHttp.responseText;
            var messages = JSON.parse(response);

            var chat_message = document.getElementById("chat-list");

            for (var i in messages) {
                // Only add new messages
                if (messageList.indexOf(messages[i].id) < 0) {
                    var date = messages[i].sentTime.date;
                    var messageContent = messages[i].messageContent;
                  
                  
                    var el_li = document.createElement("li");
                    el_li.innerHTML =   '<span class="date">' + date + '</span>' + 
                                        '<span class="message-content">' + messageContent + '</span>';

                    // Append the converted object
                    chat_message.appendChild(el_li); 

                    messageList.push(messages[i].id);
                }
            }

        } 

    }

    xmlHttp.open("POST", "/messages/mylatest.json", true);
    xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    xmlHttp.send("friend_id=" + activeTarget);
}

/**
 * Get the friend list for the current user
 */
function fetchFriendsList() {
    var xmlHttp = new XMLHttpRequest();

    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            var response = xmlHttp.responseText;
            var friends = JSON.parse(response);

            // Build up the new <li> elements
            var ul = document.getElementById('friends-list');

            for (var i in friends) {
                users = friends[i].users;

                for (var u in users) {
                    user = users[u];

                    if (user.id != current_user.id) {
                        el = document.createElement("li");
                        el.setAttribute('user-id', user.id);
                        el.innerHTML = '<img src="http://placehold.it/75x75" alt="' + user.username +'" title="' + user.username + '"/>';

                        ul.appendChild(el);
                    }
                }
            }

            // Load the friend listener
            addFriendListener();
        }
    }
    xmlHttp.open("GET", "/user_relationship", true)
    xmlHttp.send() 
}

/**
 * Send the results of the send message form to the backend
 */
function sendMessage() {
    if (activeTarget > 0) {
        console.log("Send Message");

        var form, messageBox, messageText, xmlHttp, params;

        form = document.getElementById('chat-controls').children[0];

        messageBox = form.elements["chat-text"];

        messageText = messageBox.value;
        // TODO: Test override

        // Check the box isn't just empty, or empty strings
        // TODO: Add some HTML5 validation
        if (messageText != "") {
           xmlHttp = new XMLHttpRequest();

           xmlHttp.open('POST', '/messages/', true);
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

            messageBox.value = "";
        }
    }
}

/*
 * Setup the global current_user from the users endpoint
 */
function getUserId() {
    xmlHttp = new XMLHttpRequest();

    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            current_user = JSON.parse(xmlHttp.responseText);
        }
    }

    xmlHttp.open('GET', "/users/me", true);
    xmlHttp.send();
}

/**
 * Globally setup some listeners
 */
var chat_form = document.getElementById('send-messages').elements[1];

chat_form.addEventListener('click', sendMessage);

getUserId();
