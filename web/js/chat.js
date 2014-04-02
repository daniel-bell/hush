function fetchLatestMessages() {
    var xmlHttp = new XMLHttpRequest();

    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            var response = xmlHttp.responseText;
            var messages = JSON.parse(response);

            var chat_message = document.getElementById("chat-list");

            for (var i in messages) {
                var date = messages[i].sentTime.date;
                var messageContent = messages[i].messageContent;
              
              
                var el_li = document.createElement("li");
                el_li.innerHTML =   '<span class="date">' + date + '</span>' + 
                                    '<span class="message-content">' + messageContent + '</span>';

                // Append the converted object
                chat_message.appendChild(el_li); 
            }
        }
    }

    xmlHttp.open("GET", "/messages.json", true)
    xmlHttp.send()
}

function fetchFriendsList() {
    var xmlHttp = new XMLHttpRequest();

    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            var response = xmlHttp.responseText;
            var friends = JSON.parse(response);

            console.log(friends);
            var ul = document.getElementById('friends-list');
            for (var i in friends) {
                var username = friends[i].target_user.username;

                el = document.createElement("li");
                el.innerHTML = '<img src="http://placehold.it/75x75" alt="' + username +'" title="' + username + '"/>';

                ul.appendChild(el);
            }

        }
    }
    // TODO: HARDCODED
    xmlHttp.open("GET", "/users/1/relationships", true)
    xmlHttp.send() 
}
