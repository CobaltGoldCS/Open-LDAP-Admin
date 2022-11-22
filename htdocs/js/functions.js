/*
 * Custom Javascript Functions
 */

/////////////////
// Custom input validation function
// Expects an LDAP 'attribute' as a string and a DOM selector.
function EditAttributeValidate(attribute,selector){          

    switch (attribute) {
        case "mobile":
            var regex = /^$|\d{10}]$/;
            validated = selector.value.match(regex) ? true : false;
            message = "Input must be a 10-digit phone number without spaces, dashes, or parenthesis.";
            break;
        case "mail":
            var regex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
            validated = selector.value.match(regex) ? true : false;
            message = "You have not entered a valid email address.";
            break;
        case "physicaldeliveryofficename":
            var regex = /^\d{7}$/;
            validated = selector.value.match(regex) ? true : false;
            message = "Input must be a 7-digit ID.";
            break;
        case "org_unit":
            validated = !isEmpty(selector.value) ? true : false;
            message = "Please choose an Organizational Unit.";
            break;
        case ("samaccountname" || "uid"):
            var regex = /^[a-zA-Z0-9_\.]*$/;
            validated = selector.value.match(regex) ? true : false;
            message = "Input contains invalid characters";
            break;
        default:
            validated = !isEmpty(selector.value) ? true : false;
            message = "Field cannot be blank.";
            break;
    }

    // Queries LDAP for existence of attribute in database
    if (js_config_obj.check_unique.includes(attribute)) {
        validated = (query_ldap(selector.value,attribute).count === 1) ? false : true;
        message = "Cannot be the same as existing value.";
    }

    return { validated, message };

};

/////////////////
// Simple string isEmpty checker
// 
function isEmpty(str) {
    return !str.trim().length;
}

/////////////////
// Alert message <div> builder function
// Creates simple <div></div> element with customizable message text
function alertMsg(id,messagetext) {
        /////////////////
        // Building input validation error message below text input fields
        var message = document.createElement('div');
        message.style = "font-size:10px;display:table-footer-group;width:inherit";
        message.id = id;
        message.className = "alert alert-warning";
        message.role = "alert";
        message.innerHTML = messagetext;
        return message;
}

/////////////////
// Custom auto-fill function
// Expects an input field (text) and a target field. Auto-fills in real-time
function AutoFill(input,target) {
    
    // Listen for text highlight events
    let selection = null;
    document.addEventListener('selectionchange', event => {
        selection = document.getSelection ? document.getSelection().toString() :  document.selection.createRange().toString();
    });
    
    // Listen for and handle keydown events
    let upper = false; let ctrl = false;
    input.addEventListener('keydown', event => {
        const allowedChars = 'abcdefghijklmnopqrstuvwxyz0123456789.\' ';
        const key = event.key.toLowerCase();

        switch (key) {
            case 'shift':
                upper = true; break;// Set to uppercase on shift keydown
            case 'capslock':
                upper = upper ? false : true; break;// Toggle between uppercase and lowercase
            case 'tab':
                target.value += " "; break;// Add space between fields on "tab" key
            case 'control':
                ctrl = true; break;// Ignore ctrl+ key combinations
            case 'backspace':
                target.value = (selection) ? target.value.replace(selection,"") : target.value.slice(0, -1); break;// Remove highlighted text or trim by 1
            default:
                if(allowedChars.indexOf(key) === -1) { break; }// If key is NOT in allowed character list
                if(selection) { target.value = target.value.replace(selection,""); }// Remove highlighted text
                if(!ctrl) { // If ctrl key is not active
                    target.value += upper ? key.toUpperCase() : key;// Append keystrokes
                    target.animate([{'border': '2px solid blue'}], 200);// Animate target border color
                }
        }

        // Simulate 'input' event in target element so eventListeners can be triggered at the target
        // More info: https://stackoverflow.com/questions/35659430/how-do-i-programmatically-trigger-an-input-event-without-jquery
        var event = document.createEvent('Event');
        event.initEvent('input', true, true);
        target.dispatchEvent(event);
     
    });

    // Listen for and handle keyup events
    input.addEventListener('keyup', event => {
        const key = event.key.toLowerCase();
        switch (key) {
            case 'shift':
                upper = false; break;// Set text back to lowercase
            case 'control':
                ctrl = false; break;// Set text back to lowercase
        }

    });

}

/////////////////
// Simple delay function
// Call it like: delay(1000).then(() => console.log('ran after 1 second1 passed'));
// https://masteringjs.io/tutorials/fundamentals/wait-1-second-then
function delay(time) {
    return new Promise(resolve => setTimeout(resolve, time));
}

/////////////////
// Attribute editing initialization function
// Note: This function is very specific to display.js
function hidePreviousMessages() {

    /////////////////
    // Hide any messages from previous edits
    message = document.getElementById('editattributeresult');
    if (message) { message.style.display = "none"; }

    /////////////////
    // Show the welcome bar again
    document.getElementById('welcome-bar').style.display = "block";

}

/////////////////
// Remove GET editattributeresult from URL
function clearGET(variable) {
    const regex = new RegExp(`&${variable}=(.+?)(?=(\&|$))`);
    if (window.location.href.match(regex) != null){
        window.location = window.location.href.replace(regex, "");
    }
}

/////////////////
// Simple delay function (thanks https://stackoverflow.com/questions/1909441/how-to-delay-the-keyup-handler-until-the-user-stops-typing)
// Example usage:
//    $('#input').keyup(delay(function (e) {
//      console.log('Time elapsed!', this.value);
//    }, 500));
function delay(callback, ms) {
    var timer = 0;
    return function() {
      var context = this, args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        callback.apply(context, args);
      }, ms || 0);
    };
  }

/////////////////
// Query LDAP
// Example usage:
//   query = query_ldap(this.value,'samaccountname');
// Returns (from ajax.php):
//   array('count'=>$count,'entries'=>$sub_array,'attr'=>$attr,'success'=>$success,'input'=>$input,'filter'=>$filter);
  function query_ldap(val,attr) {
    var response = '';
    let req = {
        request: 'query_ldap',
        input: val,
        attribute: attr,
    }
    $.ajax({
        type: 'GET',
        url: 'ajax.php',
        data: req,
        async: false,
        success: function(data){
            response = data;
            // console.log(data);
        },
    })
    return response ? JSON.parse(response) : null;// Return null if response is empty
}

/////////////////
// Simple capitalize first letter of string
function ucFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }