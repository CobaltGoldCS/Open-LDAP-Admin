/*
 * Javascript controlling input for attribute editing.
 * Referenced in: newaccount.tpl
 *  
 */

/////////////////
// Select2 JQuery OnLoad Classes for selection boxes.
$(document).ready(function() {

    // Org Unit selection
    $('.select2-org_unit').select2({
        theme: "bootstrap4"
    });

    // Groups selection
    $('.select2-groups').select2({
        theme: "bootstrap4"
    });
});

/////////////////
// Javascript function for controlling creation of new accounts.
function newaccount() {

    // Get form selector
    form = document.getElementById("newaccount");

    // Input validation array for all attributes in form
    var validation_array = [];
    var password_check = false;
    var org_check = false;

    attributes = document.getElementById("newaccount").elements;// Get form elements
    for (var i = 0, iLen = attributes.length; i < iLen; i++) {

        var attribute = attributes[i].name;
        selector = document.getElementById(attribute);
        // console.log(attribute);

        /////////////////
        // Building input validation error message below text input fields
        var message = alertMsg("message-"+attribute);
        
        /////////////////
        // Input validation
        if (selector != null) {
            validation = EditAttributeValidate(attribute,selector);// Do the validation
            validation_array.push(validation.validated);// Append validation state to array
            err_msg = document.getElementById("message-"+attribute);// Get any previous error messages

            if (validation.validated) {
                selector.style.border = "1px solid #ccc";// Reset border to normal
                if(err_msg) { err_msg.remove(); }// If previous error message exists, remove it
            } else if (selector.style.border != "2px solid red") {// If not already checked
                selector.style.border = "2px solid red";
                message.innerHTML = validation.message;
                selector.insertAdjacentElement('afterend',message);
            } 

        }

    }

    /////////////////
    // Password input validation
    passwd_err_msg = document.getElementById("password-verif");// Get any previous password error messages
    if(passwd_err_msg) { passwd_err_msg.remove(); }// If previous password error message exists, remove it

    if ( attributes.newpassword.value ===  attributes.confirmpassword.value && !isEmpty(attributes.newpassword.value) ) {
        password_check = true;
    } 
    else if ( isEmpty(attributes.newpassword.value) ) {
        message = alertMsg("password-verif","Password cannot be blank.");
        attributes.newpassword.style.border = "2px solid red";
        attributes.newpassword.parentNode.insertAdjacentElement('afterend',message);
    }
    else if (attributes.confirmpassword.style.border != "2px solid red") {
        message = alertMsg("password-verif","The passwords do not match.");
        attributes.confirmpassword.style.border = "2px solid red";
        attributes.confirmpassword.parentNode.insertAdjacentElement('afterend',message);
    } 

    /////////////////
    // Org Unit validation
    org_err_msg = document.getElementById("org-verif");// Get any previous password error messages
    org_panel = document.getElementById("org-unit-selection")
    if(org_err_msg) { org_err_msg.remove(); }// If previous password error message exists, remove it

    if ( !isEmpty(attributes.org_unit.value) ) {
        org_check = true;
        org_panel.style.border = "none";
    }
    else if (org_panel.style.border != "2px solid red") {
        message = alertMsg("org-verif","You must choose an Organizational Unit.");
        org_panel.style.border = "2px solid red";
        attributes.org_unit.insertAdjacentElement('afterend',message);
    } 

    /////////////////
    // Final form validation action
    if (validation_array.every(Boolean) && password_check && org_check) {// If all validation conditions are met, do the POST
        form.action = "index.php?page=newaccount";
        form.method = "post";
        return true;
    } else {// Otherwise prevent default
        return false;
    }
    
}


/////////////////
// Displayname auto-fill

// Get form selector
form = document.getElementById("newaccount");

attributes = document.getElementById("newaccount").elements;// Get all form elements on page load
for (var i = 0, iLen = attributes.length; i < iLen; i++) {
    var attribute = attributes[i].name;
}

AutoFill(attributes.givenname,attributes.displayname);
AutoFill(attributes.sn,attributes.displayname);
AutoFill(attributes.physicaldeliveryofficename,attributes.samaccountname);
