/**
 * Take a tree of a dom representation and add create a real DOM object
 */
var recursiveCreate = function(obj) {
    var el = document.createElement(obj.name);
    
    if (obj.content != "") {
        var textNode = document.createTextNode(obj.content);
        el.appendChild(textNode);
    }
    
    if (obj.children != undefined) {
        for (var child in obj.children) {
            el.appendChild(recursiveCreate(obj.children[child]));
        }
    }
    return el;
}

function fetchLatestMessages() {
    var xmlHttp = new XMLHttpRequest();

    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            var response = xmlHttp.responseText;
            var messages = JSON.parse(response);

            for (var i in messages) {
                var date = messages[i].sentTime.date;
                var messageContent = messages[i].messageContent
              
                var chat_message = document.getElementById("chat-list");
              
                // The <li> element to be added
                // <li><span>{{ date }}</span><span>{{ messageContent }}</span></li>
                domObject = {
                  "name": "li",
                  "content": "",
                  "children": [
                      { 
                          "name": "span",
                          "content": date,
                          "children": undefined
                      },
                      {
                          "name": "span",
                          "content": messageContent,
                          "children": undefined
                      }
                  ]
                }

                // Append the converted object
                chat_message.appendChild(recursiveCreate(domObject)); 
            }
        }
    }

    xmlHttp.open("GET", "/messages.json", true)
    xmlHttp.send()
}
