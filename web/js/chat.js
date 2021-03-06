/**
 * Globals
 */
var messageList = [];
var activeTarget = -1;
var current_user = null;

function addFriendListener() {
    $("#friends-list li").click(function () {
        $("#friends-list li").removeClass("active-friend");

        $(this).removeClass("inactive-friend");
        $(this).addClass("active-friend");
        $("#delete-friend-link").remove();
        $(this).prepend("<a id=\"delete-friend-link\" href=\"#\"><span class=\"glyphicon glyphicon-remove\"></span></a>");

        $("#delete-friend-link").click(function () {
            var confirmation = confirm("Are you sure you want to remove " + $(this).parent().text() + " as a friend?\nWarning, all of your messages will be permanently deleted!");

            if (confirmation === true) {
                console.log($(this).parent().attr("user-id"));

                var request = $.ajax({
                    type: 'GET',
                    url: '/user_relationship/delete/' + $(this).parent().attr("user-id")
                });

                request.done(function () {
                    fetchFriendsList();
                });

                request.fail(function (jqXHR, textStatus) {
                    console.log(jqXHR.responseText);
                });
            }
        });

        activeTarget = $(this).attr("user-id");
        fetchLatestMessages();
    });

    $("#friend-request-list li").click(function () {
        $("#confirm-friend-link").remove();
        $("#delete-friend-link").remove();

        $(this).prepend("<a id=\"delete-request-link\" href=\"#\"><span class=\"glyphicon glyphicon-remove\"></span></a>");
        $(this).prepend("<a id=\"confirm-friend-link\" href=\"#\"><span class=\"glyphicon glyphicon-ok\"></span></a>");

        $("#confirm-friend-link").click(function () {
            var confirmation = confirm("Are you sure you want to accept " + $(this).parent().text() + " as a friend?");
            if (confirmation === true) {
                var request = $.ajax({
                    type: 'GET',
                    url: '/user_relationship/confirm/' + $(this).parent().attr("user-id")
                });

                request.done(function () {
                    fetchFriendsList();
                });

                request.fail(function (jqXHR, textStatus) {
                    console.log(jqXHR.responseText);
                })
            }
        });

        $("#delete-request-link").click(function () {
            var confirmation = confirm("Are you sure you want to delete the friend request with" + $(this).parent().text() + "?");
            if (confirmation === true) {
                var request = $.ajax({
                    type: 'GET',
                    url: '/user_relationship/delete/' + $(this).parent().attr("user-id")
                });

                request.done(function () {
                    fetchFriendsList();
                });

                request.fail(function (jqXHR, textStatus) {
                    console.log(jqXHR.responseText);
                })
            }
        });
    });
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
        $("#friends-list").html("");
        $("#friend-request-list").html("");

        for (var i in friends) {
            users = friends[i].users;

            for (var u in users) {
                user = users[u];

                if (user.id != current_user) {
                    if (friends[i].relationship_type == "FRIEND") {
                        $("#friends-list").append("<li user-id=\""
                            + user.id + "\" class=\"list-group-item inactive-friend\">" + user.username + "</li>");
                    }
                    else if (friends[i].relationship_type == "FRIEND_REQUEST") {
                        console.log("friend request");
                        $("#friend-request-list").append("<li user-id=\""
                            + user.id + "\" class=\"list-group-item inactive-friend\">" + user.username + "</li>");
                    }
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

            var request = $.ajax({
                type: 'POST',
                url: '/messages/send',
                data: params
            });

            request.done(function () {
                // Update the message list
                fetchLatestMessages();
            });

            request.fail(function (jqXHR, textStatus) {
                console.log(jqXHR.responseText);
            });

            $("#message-text").val("");
        }
    }
}

/*
 * Setup the global current_user from the users endpoint
 */
function getUserId() {
    var request = $.ajax({
        type: 'GET',
        url: '/users/me'
    });

    request.done(function (data) {
        current_user = data;
    });
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

        request.done(function (data) {
            fetchFriendsList();
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