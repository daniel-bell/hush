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
    var xmlHttp = new XMLHttpRequest();

    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            var response = xmlHttp.responseText;
            var messages = JSON.parse(response);

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
                    el_li.innerHTML =   '<span class="message-time"><time date-time="' + date.toString() +'">' + dateString + '</time></span>' + 
                                        '<span class="sender">' + sender + ':</span>' +
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
                        // TODO: Add a wee checkmark for accepting?
                        el.innerHTML = user.username;

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

function addFriend(){
  console.log("Add friend");

  var form, friend_box, friend_name, xmlHttp, params;

  form = document.getElementById("add-friend").children[0];
  friend_box = form.elements["friend-name"];

  friend_name = friend_box.value;

  console.log(friend_name)

  if (friend_name != "") {
      xmlHttp = new XMLHttpRequest();
      xmlHttp.open('POST', '/user_relationship/new', true);
      xmlHttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');

      params = {
           "target_username": friend_name
       }

       xmlHttp.onreadystatechange = function() {
          if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
              var response = xmlHttp.responseText;

            }
       }

       params = "json_str=" + JSON.stringify(params);
       xmlHttp.send(params);

       friend_box.value = "";
  }
}

/**
 * Globally setup some listeners
 */
getUserId();

var chat_form = document.getElementById('send-messages').elements[1];
chat_form.addEventListener('click', sendMessage);
messageBox = chat_form.elements["chat-text"];
messageBox.addEventListener('keypress', function(event) {
    console.log("Enter");
    if (event.which == 13 || event.keyCode == 13) {
        
        chat_form.submit();
        return true;
    }

    return false;
})

var friend_form = document.getElementById('add-friend-form').elements[1];
friend_form.addEventListener('click', addFriend);

// Check messages every second
var intervalId = window.setInterval(fetchLatestMessages, 1000);
